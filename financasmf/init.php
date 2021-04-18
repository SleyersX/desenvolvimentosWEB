<?php
// define the autoloader
require_once 'lib/adianti/core/AdiantiCoreLoader.php';
spl_autoload_register(array('Adianti\Core\AdiantiCoreLoader', 'autoload'));
Adianti\Core\AdiantiCoreLoader::loadClassMap();

$loader = require 'vendor/autoload.php';
$loader->register();

// read configurations
$ini = parse_ini_file('app/config/application.ini', true);
date_default_timezone_set($ini['general']['timezone']);
AdiantiCoreTranslator::setLanguage( $ini['general']['language'] );
ApplicationTranslator::setLanguage( $ini['general']['language'] );
AdiantiApplicationConfig::load($ini);

// define constants
define('APPLICATION_NAME', $ini['general']['application']);
define('OS', strtoupper(substr(PHP_OS, 0, 3)));
define('PATH', dirname(__FILE__));
define('LANG', $ini['general']['language']);

if (version_compare(PHP_VERSION, '5.5.0') == -1) {
    die(AdiantiCoreTranslator::translate('The minimum version required for PHP is ^1', '5.5.0'));
}

// Constantes gerais
define('APP_SHORTNAME',	'BL');
define('APP_VERSION', 	'1.5.0.0');
define('APP_LOGOSYS',	'logo.png');
define('APP_LW',		220);			// width
define('APP_LH',		110);			// height
define('EMPRESA_ID', 1);
define('COLORBGFORM', 	'#e5eae3');
define('_NIVEISPC_', '9.99.99');    // 3 niveis
define('_DATABASE_', 		'dbfinancas');
define('_PERMISSION_', 		'dbfinancas');
define('_COMMUNICATION_',	'dbfinancas');
