<?php
include 'config.php';
include(DBAPI);
if (!isset($_GET['data'])) {
    echo json_encode([]);
    exit;
}
$data = $_GET['data'];
$agendamentos = filter('agendamento', "DATE(a_dia) = '" . $data . "'");
$horarios = [];
if ($agendamentos) {
    foreach ($agendamentos as $ag) {
        $horarios[] = date('H:i', strtotime($ag['a_hora']));
    }
}
echo json_encode($horarios);
