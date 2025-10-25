<?php
include '../config.php';
include(DBAPI);

if (session_status() === PHP_SESSION_NONE) session_start();

// proteção: só usuários logados podem consultar indisponibilidades
if (empty($_SESSION['id'])) {
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([]);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

$start = $_GET['start'] ?? date('Y-m-d');
$end   = $_GET['end']   ?? date('Y-m-d', strtotime('+60 days'));
$duration_override = isset($_GET['duration']) ? intval($_GET['duration']) : 0;

$default_step = 30;
// weekly schedule with possible multiple working windows per day
// weekday: 1=Monday .. 7=Sunday
$weekly_schedule = [
    1 => [['09:30:00','13:00:00'], ['14:00:00','16:50:00']], // Monday
    2 => [['09:30:00','12:00:00'], ['13:00:00','18:30:00']], // Tuesday
    3 => [['09:30:00','12:00:00'], ['13:00:00','16:50:00']], // Wednesday
    4 => [['09:30:00','12:00:00'], ['13:00:00','18:30:00']], // Thursday
    5 => [['09:30:00','12:00:00'], ['13:00:00','18:30:00']], // Friday
    6 => [['10:30:00','12:00:00'], ['13:00:00','19:30:00']], // Saturday
    7 => [] // Sunday closed
];

try {
    $db = open_database();

    $begin = new DateTime($start);
    $last  = new DateTime($end);
    $interval = new DateInterval('P1D');

    $unavailable = [];

    for ($dt = clone $begin; $dt <= $last; $dt->add($interval)) {
        $date = $dt->format('Y-m-d');

        // não permitir hoje nem datas passadas: marcar como indisponível para consistência no calendário
        if ($date <= date('Y-m-d')) {
            $unavailable[] = $date;
            continue;
        }

        // determine duration to check (minutes)
        $duration_minutes = $default_step;
        if ($duration_override > 0) $duration_minutes = $duration_override;

        // build candidate slots using the weekly schedule for this weekday
        $weekday = (int) $dt->format('N'); // 1 (Mon) .. 7 (Sun)
        $windows = $weekly_schedule[$weekday] ?? [];
        $step = $duration_minutes * 60;

        // if no windows (e.g. Sunday), mark unavailable
        if (empty($windows)) {
            $unavailable[] = $date;
            continue;
        }

        // build candidate slots across all windows; if no window can fit the duration, day is unavailable
        $slots = [];
        foreach ($windows as $win) {
            $w_start_ts = strtotime($date . ' ' . $win[0]);
            $w_end_ts = strtotime($date . ' ' . $win[1]);
            if (($w_start_ts + $step) > $w_end_ts) {
                // this particular window cannot fit the duration; skip it
                continue;
            }
            for ($t = $w_start_ts; ($t + $step) <= $w_end_ts; $t += $step) {
                $slots[] = ['start' => $t, 'end' => $t + $step];
            }
        }

        if (empty($slots)) {
            $unavailable[] = $date;
            continue;
        }

        // fetch existing appointments for the date
        $occupied = [];
        $sql = "SELECT a.a_hora, p.p_duracao FROM agendamento a JOIN procedimentos p ON a.id_p = p.id WHERE a.a_dia = :a_dia";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':a_dia', $date);
        $stmt->execute();
        $existing = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($existing as $e) {
            $s_ts = strtotime($date . ' ' . $e['a_hora']);
            $d_minutes = $default_step;
            if (!empty($e['p_duracao'])) {
                list($eh, $em) = array_pad(explode(':', $e['p_duracao']), 2, '00');
                $d_minutes = intval($eh) * 60 + intval($em);
            }
            if ($d_minutes <= 0) $d_minutes = $default_step;
            $occupied[] = ['start' => $s_ts, 'end' => $s_ts + $d_minutes * 60];
        }

        // check if there is any free slot
        $hasFree = false;
        foreach ($slots as $s) {
            $conflict = false;
            foreach ($occupied as $o) {
                if ($o['start'] < $s['end'] && $o['end'] > $s['start']) {
                    $conflict = true;
                    break;
                }
            }
            if (!$conflict) { $hasFree = true; break; }
        }

        if (!$hasFree) $unavailable[] = $date;
    }

    close_database($db);
    echo json_encode($unavailable);
    exit;
} catch (Exception $e) {
    // on error, return empty array to avoid blocking UI
    echo json_encode([]);
    exit;
}
?>