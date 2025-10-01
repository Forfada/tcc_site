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
    $id_usuario = $_SESSION['id'];
    $id_procedimento = isset($_POST['procedimento']) ? $_POST['procedimento'] : null;
    $data = isset($_POST['data']) ? $_POST['data'] : null;
    $hora = isset($_POST['horario']) ? $_POST['horario'] : null;
    if ($id_procedimento && $data && $hora) {
        $dados = [
            'a_dia' => $data,
            'a_hora' => $data . ' ' . $hora,
            'id_usuario' => $id_usuario,
            'id_procedimento' => $id_procedimento
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
