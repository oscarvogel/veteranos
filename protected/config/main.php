<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
Yii::setPathOfAlias('editable', dirname(__FILE__).'/../extensions/x-editable');
Yii::setPathOfAlias('bootstrap', dirname(__FILE__).'/../extensions/bootstrap');

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
	'name'=>'Veteranos Ldor Gral San Martin',
	'language'=>'es',
	'theme'=>'classic',
	// preloading 'log' component
	'preload'=>array(
		'log',
		),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.extensions.coco.*',
		'application.modules.cruge.components.*',
		'application.modules.cruge.interfaces.*',
		'application.modules.cruge.models.data.*',
		'application.modules.cruge.extensions.crugemailer.*',
		'editable.*' //easy include of editable classes
	),
	'modules'=>array(
		// uncomment the following to enable the Gii tool
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'Roman',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
			'generatorPaths'=>array('application.modules.gii'),
		),
		'cruge'=>array(
				'tableprefix'=>'cruge_',

				// para que utilice a protected.modules.cruge.models.auth.CrugeAuthDefault.php
				//
				// en vez de 'default' pon 'authdemo' para que utilice el demo de autenticacion alterna
				// para saber mas lee documentacion de la clase modules/cruge/models/auth/AlternateAuthDemo.php
				//
				'availableAuthMethods'=>array('default'),

				'availableAuthModes'=>array('username','email'),
				'baseUrl'=>'http://www.veteranoslgsm.com.ar/',

				 // NO OLVIDES PONER EN FALSE TRAS INSTALAR
				 'debug'=>true,
				 'rbacSetupEnabled'=>false,
				 'allowUserAlways'=>false,

				// MIENTRAS INSTALAS..PONLO EN: false
				// lee mas abajo respecto a 'Encriptando las claves'
				//
				'useEncryptedPassword' => false,

				// Algoritmo de la función hash que deseas usar
				// Los valores admitidos están en: http://www.php.net/manual/en/function.hash-algos.php
				'hash' => 'md5',

				// a donde enviar al usuario tras iniciar sesion, cerrar sesion o al expirar la sesion.
				//
				// esto va a forzar a Yii::app()->user->returnUrl cambiando el comportamiento estandar de Yii
				// en los casos en que se usa CAccessControl como controlador
				//
				// ejemplo:
				//		'afterLoginUrl'=>array('/site/welcome'),  ( !!! no olvidar el slash inicial / )
				//		'afterLogoutUrl'=>array('/site/page','view'=>'about'),
				//
				'afterLoginUrl'=>null,
				'afterLogoutUrl'=>null,
				'afterSessionExpiredUrl'=>null,

				// manejo del layout con cruge.
				//
				'loginLayout'=>'//layouts/column2',
				'registrationLayout'=>'//layouts/column2',
				'activateAccountLayout'=>'//layouts/column2',
				'editProfileLayout'=>'//layouts/column2',
				// en la siguiente puedes especificar el valor "ui" o "column2" para que use el layout
				// de fabrica, es basico pero funcional.  si pones otro valor considera que cruge
				// requerirá de un portlet para desplegar un menu con las opciones de administrador.
				//
				'generalUserManagementLayout'=>'ui',
				'buttonStyle'=>'jQuery',
			),
		
	),

	// application components
	'components'=>array(
			//  IMPORTANTE:  asegurate de que la entrada 'user' (y format) que por defecto trae Yii
			//               sea sustituida por estas a continuación:
			//
		'user'=>array(
				'allowAutoLogin'=>true,
				'class' => 'application.modules.cruge.components.CrugeWebUser',
				'loginUrl' => array('/cruge/ui/login'),
			),
		'authManager' => array(
				'class' => 'application.modules.cruge.components.CrugeAuthManager',
			),
		'crugemailer'=>array(
				'class' => 'application.modules.cruge.components.CrugeMailer',
				'mailfrom' => 'oscarvogel@gmail.com',
				'subjectprefix' => 'Consulta desde la Web - ',
				'debug' => true,
			),
		'format' => array(
				'datetimeFormat'=>"d M, Y h:m:s a",
			),
  		
        //X-editable config
        'editable' => array(
            'class'     => 'editable.EditableConfig',
            'form'      => 'bootstrap', 
            'mode'      => 'inline',      
            'defaults'  => array(        
               'emptytext' => 'Click para editar',
               //'ajaxOptions' => array('dataType' => 'json') //usefull for json exchange with server
            )
        ),        
        'bootstrap'=>array(
            'class'=>'bootstrap.components.Bootstrap',
        ),
        
        'mobileDetect' => array(
            'class' => 'ext.MobileDetect.MobileDetect'
        ),

		// uncomment the following to enable URLs in path-format
		/*
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		
		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
			'enableParamLogging' => true,
		),
		// uncomment the following to use a MySQL database
		*/
		'db'=>$dbConfig,
		
		'coreMessages'=>array(
			'basePath'=>'protected/messages',
		),
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning, trace, log',
					'categories' => 'system.db.CDbCommand',
					'logFile' => 'db.log',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
		'ePdf' => array(
			'class'			=> 'ext.yii-pdf.EYiiPdf',
			'params'		=> array(
				'mpdf'	   => array(
					'librarySourcePath' => 'application.vendors.mpdf.*',
					'constants'			=> array(
						'_MPDF_TEMP_PATH' => Yii::getPathOfAlias('application.runtime'),
					),
					'class'=>'mpdf', // the literal class filename to be loaded from the vendors folder.
					/*'defaultParams'	  => array( // More info: http://mpdf1.com/manual/index.php?tid=184
						'mode'				=> '', //  This parameter specifies the mode of the new document.
						'format'			=> 'A4', // format A4, A5, ...
						'default_font_size' => 0, // Sets the default document font size in points (pt)
						'default_font'		=> '', // Sets the default font-family for the new document.
						'mgl'				=> 15, // margin_left. Sets the page margins for the new document.
						'mgr'				=> 15, // margin_right
						'mgt'				=> 16, // margin_top
						'mgb'				=> 16, // margin_bottom
						'mgh'				=> 9, // margin_header
						'mgf'				=> 9, // margin_footer
						'orientation'		=> 'P', // landscape or portrait orientation
					)*/
				),
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'oscarvogel@gmail.com',
		'Sistema'=>'Veteranos LGSM',
		'EsMovil'=>false,
	),
);
