<?php

// Detectar ambiente (desenvolvimento ou produção)
$is_production = false;
if (isset($_SERVER['SERVER_NAME'])) {
    $is_production = $_SERVER['SERVER_NAME'] !== 'localhost' && 
                    $_SERVER['SERVER_NAME'] !== '127.0.0.1';
}

// Configurações do banco de dados
if ($is_production) {
    // Configurações para ambiente de produção
    define("DB_NAME", "bancolu");
    define("DB_USER", "root"); // Alterar para usuário de produção
    define("DB_PASSWORD", ""); // Alterar para senha de produção
    define("DB_HOST", "localhost");
} else {
    // Configurações para ambiente de desenvolvimento
    define("DB_NAME", "bancolu");
    define("DB_USER", "root");
    define("DB_PASSWORD", "");
    define("DB_HOST", "127.0.0.1");
}

// Caminhos do sistema (mantidos constantes em ambos ambientes)
if (!defined("ABSPATH")) {
    define("ABSPATH", dirname(__FILE__) . "/");
}

if (!defined("BASEURL")) {
    define("BASEURL", ($is_production ? "https://seusite.com/" : "/tcc_site/"));
}

// Caminhos dos arquivos do sistema
if (!defined("DBAPI")) {
    define("DBAPI", ABSPATH . "inc/database.php");
}

// Caminhos dos templates
define("HEADER_TEMPLATE", ABSPATH . "inc/header.php");
define("FOOTER_TEMPLATE", ABSPATH . "inc/footer.php");
define("CSS", ABSPATH . "inc/style.css");
define("INIT", ABSPATH . "inc/init.php");

// Verificação adicional de segurança
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Acesso Direto Não Permitido');
}
?>