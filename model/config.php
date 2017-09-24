<?php
	//SITE_ROOT contains full path to the tshirts
	define('SITE_ROOT', dirname(dirname(__FILE__)));
	
	// Name of the virtual directory the site runs in
	define('VIRTUAL_LOCATION', '/');
	
	//These should be true while developing the site
	define('IS_WARNING_FATAL', false);
	define('DEBUGGING', false);
	
	//The error types to be reported
	define('ERROR_TYPES', E_ALL);
	
	//Settings about mailing the error message to admin
	define('SEND_ERROR_MAIL', true);
	define('ADMIN_ERROR_MAIL', 'ugochukwu95@gmail.com');
	define('SENDMAIL_FROM', 'ERRORS@example.com');
	ini_set('sendmail_from', 'SENDMAIL_FROM');
	
	//By default we don't log errors to file
	define('LOG_ERRORS', true);
	define('LOG_ERRORS_FILE', '/errors.log');
	define('SITE_GENERIC_ERROR_MESSAGE', 'ERROR! occured');
	
	//Database connectivity setup
	define('DB_PERSISTENCY', 'true');
	define('DB_SERVER', 'localhost');
	const DB_USERNAME = 'root';
	define('DB_PASSWORD', 'mysql');
	define('DB_DATABASE', 'portfolio_chat');
	define('PDO_DSN', 'mysql:host='.DB_SERVER.';dbname='.DB_DATABASE);
?>