<?php
	/** O nome do banco de dados*/
	define("DB_NAME", "bancolu");

	/** Usuário do banco de dados MySQL */
	define("DB_USER", "root");

	/** Senha do banco de dados MySQL */
	define("DB_PASSWORD", "");

	/** nome do host do MySQL Localhost */
	define("DB_HOST", "127.0.0.1");

	/** caminho absoluto para a pasta do sistema **/
	if ( !defined("ABSPATH") )
		define("ABSPATH", dirname(__FILE__) . "/");
		
	/** caminho no server para o sistema **/
	if ( !defined("BASEURL") )
		define("BASEURL", "/tcc_site/");
		
	/** caminho do arquivo de banco de dados **/
	if ( !defined("DBAPI") )
		define("DBAPI", ABSPATH . "inc/database.php");

	/** caminhos dos templates de header e footer **/
	define("HEADER_TEMPLATE", ABSPATH . "inc/header.php");
	define("FOOTER_TEMPLATE", ABSPATH . "inc/footer.php");
	define("CSS", ABSPATH . "inc/style.css");
	define("INIT", ABSPATH . "inc/init.php");

	// SMTP / Mail settings (configure antes de usar em produção)
	if (!defined('SMTP_HOST')) define('SMTP_HOST', 'smtp.mailgun.org');
	if (!defined('SMTP_USER')) define('SMTP_USER', 'lunaristest@sandbox09789730a53747a79f95f02c08f33a40.mailgun.org');
	if (!defined('SMTP_PASS')) define('SMTP_PASS', 'a6cf56e2a6e8d4e52974380c5477432e-ba8a60cd-6834d29f');
	if (!defined('SMTP_PORT')) define('SMTP_PORT', 587);
	if (!defined('SMTP_SECURE')) define('SMTP_SECURE', 'tls'); // 'tls', 'ssl' or ''
	if (!defined('MAIL_FROM')) define('MAIL_FROM', 'lunaristest@sandbox09789730a53747a79f95f02c08f33a40.mailgun.org');
	if (!defined('MAIL_FROM_NAME')) define('MAIL_FROM_NAME', 'Lunaris');
	
	require_once __DIR__ . '/vendor/autoload.php';
?>