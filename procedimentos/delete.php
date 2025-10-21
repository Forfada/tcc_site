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
            $proc = find("procedimentos", $_GET['id']);
            delete($_GET['id']);
            
            // Verifica se o nome do arquivo é semimagem.png | Se for ele não vai excluir a imagem, pq usamos dela em outros lugares!
            if ($proc['p_foto'] !== "noimg.jpg") {
                unlink("imagens/" . $proc['p_foto']);
            }

        } catch (Exception $e) {
            $_SESSION ['message'] = "Não foi possivel realizar a operação: " . $e->getMessage();
            $_SESSION['type'] = "danger";
        }
    }
    else {
        die("ERRO: ID não definido.");
    }
?>
