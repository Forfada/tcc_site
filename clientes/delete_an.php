<?php
include 'functions.php';

if (isset($_GET['anid'])) {
    $anid = intval($_GET['anid']);
    // remove anamnese
    anamnese_delete($anid);
    // redirect back to client view if client_id provided
    if (isset($_GET['client_id'])) {
        header('Location: view.php?id=' . intval($_GET['client_id']));
    } else {
        header('Location: index.php');
    }
    exit;
}

header('Location: index.php');
