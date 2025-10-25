<?php
include '../config.php';
include(DBAPI);
if (session_status() === PHP_SESSION_NONE) session_start();

// proteção: só usuários logados podem consultar horários
if (empty($_SESSION['id'])) {
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([]);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
if (!isset($_GET['data'])) {
    echo json_encode([]);
    exit;
}
$data = $_GET['data'];
// Optional procedure id or explicit duration (in minutes) to standardize slots
$id_p = isset($_GET['id_p']) ? intval($_GET['id_p']) : null;
$duration_override = isset($_GET['duration']) ? intval($_GET['duration']) : null;

$db = open_database();

// bloquear hoje e datas passadas (não retornar horários)
$today = date('Y-m-d');
if ($data <= $today) {
    close_database($db);
    echo json_encode([]);
    exit;
}

// weekly schedule with possible multiple working windows per day
// weekday: 1=Monday .. 7=Sunday
$default_step = 30; // fallback minutes
$weekly_schedule = [
    1 => [['09:30:00','13:00:00'], ['14:00:00','16:50:00']], // Monday
    2 => [['09:30:00','12:00:00'], ['13:00:00','18:30:00']], // Tuesday
    3 => [['09:30:00','12:00:00'], ['13:00:00','16:50:00']], // Wednesday
    4 => [['09:30:00','12:00:00'], ['13:00:00','18:30:00']], // Thursday
    5 => [['09:30:00','12:00:00'], ['13:00:00','18:30:00']], // Friday
    6 => [['10:30:00','12:00:00'], ['13:00:00','19:30:00']], // Saturday
    7 => [] // Sunday closed
];

$duration_minutes = $default_step;
// if a duration is explicitly provided, use it (this handles multiple procedures combined)
if ($duration_override && $duration_override > 0) {
    $duration_minutes = $duration_override;
} elseif ($id_p) {
    try {
        $stmt = $db->prepare("SELECT p_duracao FROM procedimentos WHERE id = :id");
        $stmt->bindParam(':id', $id_p, PDO::PARAM_INT);
        $stmt->execute();
        $proc = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($proc && !empty($proc['p_duracao'])) {
            // expect format HH:MM or H:MM
            list($h, $m) = array_pad(explode(':', $proc['p_duracao']), 2, '00');
            $duration_minutes = intval($h) * 60 + intval($m);
            if ($duration_minutes <= 0) $duration_minutes = $default_step;
        }
    } catch (Exception $e) {
        $duration_minutes = $default_step;
    }
}

// build candidate slots stepping by procedure duration using the weekly schedule
$slots = [];
$step = $duration_minutes * 60;
$weekday = (int) date('N', strtotime($data)); // 1 (Mon) .. 7 (Sun)
$windows = $weekly_schedule[$weekday] ?? [];
foreach ($windows as $win) {
    $w_start_ts = strtotime($data . ' ' . $win[0]);
    $w_end_ts = strtotime($data . ' ' . $win[1]);
    for ($t = $w_start_ts; ($t + $step) <= $w_end_ts; $t += $step) {
        $slots[] = date('H:i', $t);
    }
}

// fetch existing appointments for the date and compute their occupied intervals
$occupied = [];
try {
    $sql = "SELECT a.a_hora, p.p_duracao FROM agendamento a JOIN procedimentos p ON a.id_p = p.id WHERE a.a_dia = :a_dia";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':a_dia', $data);
    $stmt->execute();
    $existing = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($existing as $e) {
        $s_ts = strtotime($data . ' ' . $e['a_hora']);
        $d_minutes = 0;
        if (!empty($e['p_duracao'])) {
            list($eh, $em) = array_pad(explode(':', $e['p_duracao']), 2, '00');
            $d_minutes = intval($eh) * 60 + intval($em);
        }
        if ($d_minutes <= 0) $d_minutes = $default_step;
        $occupied[] = ['start' => $s_ts, 'end' => $s_ts + $d_minutes * 60];
    }
} catch (Exception $e) {
    // ignore and treat as no occupied
}

// filter slots that overlap occupied intervals
$available = [];
foreach ($slots as $s) {
    $s_ts = strtotime($data . ' ' . $s);
    $s_end = $s_ts + $step;
    $conflict = false;
    foreach ($occupied as $o) {
        if ($o['start'] < $s_end && $o['end'] > $s_ts) {
            $conflict = true;
            break;
        }
    }
    if (!$conflict) $available[] = $s;
}

close_database($db);

echo json_encode($available);
