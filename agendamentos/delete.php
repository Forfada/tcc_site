<?php
include '../config.php';
include(DBAPI);
if (!isset($_SESSION)) session_start();

// Must be logged in
if (!isset($_SESSION['id'])) {
    $_SESSION['message'] = 'Você precisa estar logado para excluir agendamentos.';
    $_SESSION['type'] = 'warning';
    header('Location: agendamento.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $user = $_SESSION['id'];

    try {
        $db = open_database();
        $stmt = $db->prepare('DELETE FROM agendamento WHERE id = :id AND id_u = :id_u');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':id_u', $user, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = 'Agendamento excluído com sucesso.';
            $_SESSION['type'] = 'success';
        } else {
            $_SESSION['message'] = 'Agendamento não encontrado ou não pertence a você.';
            $_SESSION['type'] = 'warning';
        }

        close_database($db);
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Erro ao excluir agendamento: ' . $e->getMessage();
        $_SESSION['type'] = 'danger';
    }

    header('Location: agendamento.php');
    exit;
}

header('Location: agendamento.php');
exit;
