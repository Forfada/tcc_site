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
remove('usuarios', $id);

session_destroy();

session_start();
$_SESSION['message'] = "Conta excluída com sucesso!";
$_SESSION['type'] = "danger";
header("Location: " . BASEURL . "index.php");
exit;
?>
