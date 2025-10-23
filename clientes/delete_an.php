<?php
include 'functions.php';

// somente administrador pode acessar a área de clientes
if (!function_exists('is_admin') || !is_admin()) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['message'] = "Você não pode acessar essa funcionalidade.";
    $_SESSION['type'] = "danger";
    header("Location: " . BASEURL . "index.php");
    exit;
}

if (isset($_GET['anid'])) {
    if (session_status() === PHP_SESSION_NONE) session_start();

    $anid = intval($_GET['anid']);
    $success = anamnese_delete($anid); // assumindo que retorna true/false

    if ($success) {       
        $_SESSION['message'] = "Erro ao excluir anamnese.";
        $_SESSION['type'] = "danger";
    } else {
        $_SESSION['message'] = "Anamnese excluída com sucesso!";
        $_SESSION['type'] = "success";
    }

    if (isset($_GET['client_id'])) {
        header('Location: view.php?id=' . intval($_GET['client_id']));
    } else {
        header('Location: index.php');
    }
    exit;
}

// caso não tenha passado o ID
if (session_status() === PHP_SESSION_NONE) session_start();
$_SESSION['message'] = "ID da anamnese não definido.";
$_SESSION['type'] = "danger";
header('Location: index.php');
exit;
?>
