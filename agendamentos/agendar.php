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
    $id_p = $_POST['procedimento'] ?? null;
    $data = $_POST['data'] ?? null;
    $hora = $_POST['horario'] ?? null;

    if ($id_p && $data && $hora) {
        $a_dia = date('Y-m-d', strtotime($data));
        $a_hora = date('H:i:s', strtotime($hora));

        // Verifica se horário já está ocupado
        $existe = filter('agendamento', "a_dia = '$a_dia' AND a_hora = '$a_hora'");
        if ($existe && count($existe) > 0) {
            $_SESSION['message'] = 'Este horário já foi agendado.';
            $_SESSION['type'] = 'danger';
        } else {
            $dados = [
                'a_dia' => $a_dia,
                'a_hora' => $a_hora,
                'id_u' => $id_u,
                'id_p' => $id_p
            ];
            save('agendamento', $dados);
            $_SESSION['message'] = 'Agendamento realizado com sucesso!';
            $_SESSION['type'] = 'success';
        }

        header('Location: historico.php');
        exit;
    } else {
        $_SESSION['message'] = 'Preencha todos os campos corretamente.';
        $_SESSION['type'] = 'danger';
        header('Location: agendamento.php');
        exit;
    }
} else {
    header('Location: agendamento.php');
    exit;
}
