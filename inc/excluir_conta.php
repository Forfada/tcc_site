
<?php
    session_start();
    require_once('../config.php');
    require_once(DBAPI);

    // Verifica se está logado
    if (!isset($_SESSION['nome'])) {
        header("Location: " . BASEURL . "inc/login.php");
        exit;
    }

    // Obtém o ID do usuário logado
    $id_u = $_SESSION['id_u'];

    // Remove o usuário
    remove('usuarios', $id_u);

    // Destroi sessão e redireciona
    session_destroy();
    header("Location: " . BASEURL . "index.php");
    exit;
?>
