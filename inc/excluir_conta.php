<?php
session_start();
require_once('../config.php');
require_once(DBAPI);

if (!isset($_SESSION['nome'])) {
    $_SESSION['message'] = "Faça login para excluir a conta.";
    $_SESSION['type'] = "warning";
    header("Location: login.php");
    exit;
}

$id = $_SESSION['id'];
// remove related appointments first to avoid foreign-key constraints
try {
    $db = open_database();
    $stmt = $db->prepare('DELETE FROM agendamento WHERE id_u = :id_u');
    $stmt->bindParam(':id_u', $id, PDO::PARAM_INT);
    $stmt->execute();
    close_database($db);
} catch (Exception $e) {
    // If we can't remove related records, set a helpful message and do not proceed
    $_SESSION['message'] = 'Não foi possível limpar agendamentos relacionados: ' . $e->getMessage();
    $_SESSION['type'] = 'danger';
    header("Location: " . BASEURL . "index.php");
    exit;
}

$deleted = remove('usuarios', $id);

if ($deleted) {
    // only destroy session (log out) if deletion actually happened
    session_destroy();
    // start a fresh session to show the flash message on redirect
    session_start();
    $_SESSION['message'] = "Conta excluída com sucesso.";
    $_SESSION['type'] = "danger";
    header("Location: " . BASEURL . "index.php");
    exit;
} else {
    // deletion failed (message already set by remove()). Keep the user logged
    // redirect back to home/profile so they can see the error message.
    header("Location: " . BASEURL . "index.php");
    exit;
}
?>
