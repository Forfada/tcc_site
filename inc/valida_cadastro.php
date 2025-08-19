<?php
include("../config.php");
require_once(DBAPI);
session_start();

try {
    $bd = open_database();
    $bd->exec("USE " . DB_NAME);

    // Coleta e sanitiza os dados
    $nome = trim($_POST['nome'] ?? '');
    $numero = trim($_POST['numero'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    // Validação segura
    if ($nome === '' || $numero === '' || $senha === '') {
        throw new Exception("Todos os campos são obrigatórios.");
    }

    // Verifica duplicidade do número
    $sql_verifica = "SELECT COUNT(*) FROM usuarios WHERE u_num = :numero";
    $stmt = $bd->prepare($sql_verifica);
    $stmt->bindParam(':numero', $numero);
    $stmt->execute();

    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Este número já está cadastrado.");
    }

    // Criptografar senha
    $senhaCripto = cri($senha);

    // Etapa 3: escolher um avatar aleatório
    $avatares = ['avatar1.jpg', 'avatar2.jpg', 'avatar3.jpg', 'avatar4.jpg', 'avatar5.jpg'];
    $foto = $avatares[array_rand($avatares)];

    // Inserir no banco com foto
    $sql_insert = "INSERT INTO usuarios (u_num, u_user, u_senha, foto) VALUES (:numero, :nome, :senha, :foto)";
    $stmt = $bd->prepare($sql_insert);
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
