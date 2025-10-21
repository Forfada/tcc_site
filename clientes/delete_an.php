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
    $anid = intval($_GET['anid']);
    anamnese_delete($anid);
    if (isset($_GET['client_id'])) {
        header('Location: view.php?id=' . intval($_GET['client_id']));
    } else {
        header('Location: index.php');
    }
    exit;
}

header('Location: index.php');
?>
