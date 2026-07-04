<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
$localDbConfigFile = dirname(__FILE__).'/db-local.php';
$dbConfig = is_file($localDbConfigFile) ? require($localDbConfigFile) : array(
	'connectionString' => 'mysql:host=localhost;dbname=ye000174_veteranos',
	'emulatePrepare' => true,
	'username' => 'ye000174_vet',
	'password' => 'Veteranos2013',
	'charset' => 'utf8',
);

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Console Application',
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.modules.cruge.components.*',
		'application.modules.cruge.interfaces.*',
		'application.modules.cruge.models.data.*',
	),
	// application components
	'components'=>array(
		'db'=>$dbConfig,
		// uncomment the following to use a MySQL database
		/*
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=testdrive',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
		),
		*/
	),
);
