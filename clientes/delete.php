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

// inicia a sessão caso não esteja ativa
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_GET['id'])) {
    try {
        $cli = find("clientes", $_GET['id']);
        $success = delete($_GET['id']); // se sua função delete() retorna true/false

        if ($success) {
            $_SESSION['message'] = "Erro ao excluir cliente.";
            $_SESSION['type'] = "danger";
        } else {
            $_SESSION['message'] = "Cliente excluído com sucesso!";
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

// redireciona para a listagem de clientes
header("Location: index.php");
exit;
?>
