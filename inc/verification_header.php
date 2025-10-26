<?php
if (!isset($pageTitle)) {
    $pageTitle = 'Verificação';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Lunaris</title>
    <link rel="stylesheet" href="<?php echo BASEURL; ?>css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>css/fontawesome/all.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>css/verification.css">
    <link rel="icon" type="image/x-icon" href="<?php echo BASEURL; ?>img/icone.png">
</head>
<body class="verification-page">