<?php
include("../config.php");
require_once(DBAPI);
session_start();

try {
    $bd = open_database();
    $bd->exec("USE " . DB_NAME);

    $nome = trim($_POST['nome'] ?? '');
    $numero = trim($_POST['numero'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if ($nome === '' || $numero === '' || $senha === '') {
        throw new Exception("Todos os campos são obrigatórios.");
    }

    // Verifica duplicidade
    $stmt = $bd->prepare("SELECT COUNT(*) FROM usuarios WHERE u_num = :numero");
    $stmt->bindParam(':numero', $numero);
    $stmt->execute();

    if ($stmt->fetchColumn() > 0) {
        $_SESSION['message'] = "Erro: Este número já está cadastrado.";
        $_SESSION['type'] = "danger";
        header("Location: cadastro.php");
        exit();
    }

    $senhaCripto = cri($senha);
    $avatares = ['avatar1.png','avatar2.png','avatar3.png','avatar4.png','avatar5.png'];
    $foto = $avatares[array_rand($avatares)];

    $stmt = $bd->prepare("INSERT INTO usuarios (u_num, u_user, u_senha, foto) VALUES (:numero, :nome, :senha, :foto)");
    $stmt->bindParam(':numero', $numero);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':senha', $senhaCripto);
    $stmt->bindParam(':foto', $foto);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Cadastro realizado com sucesso!";
        $_SESSION['type'] = "success";
        header("Location: login.php");
        exit();
    } else {
        throw new Exception("Erro ao inserir usuário.");
    }

} catch (Exception $e) {
    $_SESSION['message'] = "Erro: " . $e->getMessage();
    $_SESSION['type'] = "danger";
    header("Location: cadastro.php");
    exit();
}
?>
