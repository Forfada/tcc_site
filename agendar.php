<?php
include 'config.php';
include(DBAPI);
if (!isset($_SESSION)) session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['nome'])) {
    $_SESSION['message'] = 'Você precisa estar logado para agendar.';
    $_SESSION['type'] = 'danger';
    header('Location: inc/login.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_u = $_SESSION['id'];
    $id_p = isset($_POST['procedimento']) ? $_POST['procedimento'] : null;
    $data = isset($_POST['data']) ? $_POST['data'] : null;
    $hora = isset($_POST['horario']) ? $_POST['horario'] : null;
    if ($id_p && $data && $hora) {
        // Garante formato correto para o banco: a_dia = 'Y-m-d', a_hora = 'H:i:00'
        $a_dia = date('Y-m-d', strtotime($data));
        $a_hora = date('H:i:00', strtotime($hora));
        $dados = [
            'a_dia' => $a_dia,
            'a_hora' => $a_hora,
            'id_u' => $id_u,
            'id_p' => $id_p
        ];
        save('agendamento', $dados);
        $_SESSION['message'] = 'Agendamento realizado com sucesso!';
        $_SESSION['type'] = 'success';
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['message'] = 'Preencha todos os campos.';
        $_SESSION['type'] = 'danger';
        header('Location: agendamento.php');
        exit;
    }
} else {
    header('Location: agendamento.php');
    exit;
}
