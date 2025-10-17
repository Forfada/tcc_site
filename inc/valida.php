<?php
include("../config.php");
require_once(DBAPI);
session_start();

try {
    $bd = open_database();
    $bd->exec("USE " . DB_NAME);

    $usuario = trim($_POST['login'] ?? '');
    $senha = trim($_POST['password'] ?? '');

    if (empty($usuario) || empty($senha)) {
        throw new Exception("Preencha todos os campos.");
    }

    $senha = cri($senha);

    $stmt = $bd->prepare("SELECT id, u_user, u_num, u_senha, foto FROM usuarios WHERE u_num = :usuario AND u_senha = :senha");
    $stmt->bindParam(':usuario', $usuario);
    $stmt->bindParam(':senha', $senha);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['id'] = $dados['id'];
        $_SESSION['nome'] = $dados['u_user'];
        $_SESSION['user'] = $dados['u_num'];
        $_SESSION['foto'] = $dados['foto'];

        $_SESSION['message'] = "Bem-vindo(a) " . $_SESSION['nome'] . "!";
        $_SESSION['type'] = "info";

        header("Location: " . BASEURL . "index.php");
        exit;
    } else {
        $_SESSION['message'] = "Usuário ou senha incorretos.";
        $_SESSION['type'] = "danger";
        header("Location: login.php");
        exit;
    }

} catch (Exception $e) {
    $_SESSION['message'] = "Erro: " . $e->getMessage();
    $_SESSION['type'] = "danger";
    header("Location: login.php");
    exit;
}
?>
