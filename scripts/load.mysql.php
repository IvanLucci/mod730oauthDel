<?php
/**
 * 
 * load.mysql.php, 
 * 
 * @author framework.zend.com
 * @version 0.1
 * 
 */

/**
 * Script for creating and loading database
 * 
 * will load schema.mysql.sql, creating the DB schema
 * with --withdata flag will also load data.mysql.sql, populating the DB
 * 
 * usage:
 * php scripts/load.sqlite.php [--withdata]
 * 
 * 
 */
// Initialize the application path and autoloading
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
set_include_path(implode(PATH_SEPARATOR, array(
            APPLICATION_PATH . '/../library',
            get_include_path(),
        )));
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

// Define some CLI options
// Define some CLI options
$getopt = new Zend_Console_Getopt(array(
		'dbschema|s-s'  =>  'Loads the database schema from file provided',
		'dbdata|d-s'    =>  'Loads the default data from the file provided',
		'withdata|w'    =>  'Load database with sample data',
		'env|e-s'       =>  'Application environment for which to create '.
		                    'database (defaults to development)',
		'help|h'        =>  'Help -- usage message'));
try {
    $getopt->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    // Bad options passed: report usage
    echo $e->getUsageMessage();
    return false;
}

// If help requested, report usage message
if ($getopt->getOption('h')) {
    echo $getopt->getUsageMessage();
    return true;
}

// Initialize values based on presence or absence of CLI options
$withData = $getopt->getOption('w');
$env      = $getopt->getOption('e');
$db_schema_file = $getopt->getOption('s');
$db_data_file = $getopt->getOption('d');

defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (null === $env) ? 'development' : $env);

// Initialize Zend_Application
try {
	$application = new Zend_Application(APPLICATION_ENV, 
										APPLICATION_PATH.'/configs/application.ini');
} catch (Zend_Config_Exception $e) {
	echo 'The application environment is invalid "'.$env.'"'.PHP_EOL;
	return false;
}

// Initialize and retrieve DB resource
$bootstrap = $application->getBootstrap();
$bootstrap->bootstrap('db');
$dbAdapter = $bootstrap->getResource('db');


echo 'Writing Authorization Database in (control-c to cancel): ' . PHP_EOL;
for ($x = 5; $x > 0; $x--) {
    echo $x . "\r";
    sleep(1);
}

try {
	$schemaSql = file_get_contents($db_schema_file);
	if (!$schemaSql)
		throw new Exception("The db schema file cannot be readed.");
	if ($dbAdapter->getConnection()->exec($schemaSql))
		throw new Exception("The db schema contains invalid queries");	
	
	echo PHP_EOL.'Database Created.'.PHP_EOL;

	if ($withData) {
		$dataSql = file_get_contents($db_data_file);
		if (!$dataSql)
			throw new Exception("The db data file cannot be readed.");
		if ($dbAdapter->getConnection()->exec($dataSql))
			throw new Exception("The db data file contains invalid queries");
		echo 'Data Loaded.'.PHP_EOL;
	}
} catch (Exception $e) {
	echo 'An error has occured:' . PHP_EOL;
	echo $e->getMessage() . PHP_EOL;
	return false;
}

return true;
