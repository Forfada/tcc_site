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

$work_start = '08:00:00';
$work_end   = '18:00:00';
$default_step = 30;

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

        $start_ts = strtotime($date . ' ' . $work_start);
        $end_ts   = strtotime($date . ' ' . $work_end);
        $step = $duration_minutes * 60;

        // no possible slot if duration exceeds working window
        if (($start_ts + $step) > $end_ts) {
            $unavailable[] = $date;
            continue;
        }

        // build candidate slots
        $slots = [];
        for ($t = $start_ts; ($t + $step) <= $end_ts; $t += $step) {
            $slots[] = ['start' => $t, 'end' => $t + $step];
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