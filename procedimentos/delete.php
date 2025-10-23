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

// iniciar sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_GET['id'])) {
    try {
        $proc = find("procedimentos", $_GET['id']);
        $success = delete($_GET['id']); // assume que delete() retorna true/false

        // se a exclusão foi bem-sucedida e não é a imagem padrão, apaga o arquivo
        if ($success && $proc['p_foto'] !== "noimg.jpg") {
            unlink("imagens/" . $proc['p_foto']);
        }

        if ($success) {
            $_SESSION['message'] = "Erro ao excluir procedimento.";
            $_SESSION['type'] = "danger";
        } else {         
            $_SESSION['message'] = "Procedimento excluído com sucesso!";
            $_SESSION['type'] = "success";
        }

    } catch (Exception $e) {
        $_SESSION['message'] = "Não foi possível realizar a operação: " . $e->getMessage();
        $_SESSION['type'] = "danger";
    }
} else {
    $_SESSION['message'] = "ID não definido.";
    $_SESSION['type'] = "danger";
}

// redireciona sempre para o index
header("Location: index.php");
exit;
?>
