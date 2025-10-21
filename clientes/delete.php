<?php 
    include("functions.php"); 

    // somente administrador pode executar exclusão
    if (!function_exists('is_admin') || !is_admin()) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['message'] = "Você não pode acessar essa funcionalidade.";
        $_SESSION['type'] = "danger";
        header("Location: " . BASEURL . "index.php");
        exit;
    }
?>
<?php 
    if (isset($_GET['id'])) {
        try {
            $cli = find("clientes", $_GET['id']);
            delete($_GET['id']);
        } catch (Exception $e) {
            $_SESSION ['message'] = "Não foi possivel realizar a operação: " . $e->getMessage();
            $_SESSION['type'] = "danger";
        }
    }
    else {
        die("ERRO: ID não definido.");
    }
?>