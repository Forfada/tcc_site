<?php 
include("../config.php");
require_once("cookie_handler.php");
session_start();

// Remove os cookies de login
CookieHandler::removeLoginCookies();

// Define a mensagem antes de remover os dados do login
$_SESSION['message'] = "Você saiu da conta!";
$_SESSION['type'] = "danger";

// Remove apenas os dados do usuário logado
unset($_SESSION['id']);
unset($_SESSION['nome']);
unset($_SESSION['user']);
unset($_SESSION['foto']);

// Redireciona para o index
header("Location: " . BASEURL . "index.php");
exit;
?>
