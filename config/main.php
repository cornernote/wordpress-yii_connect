<?php
/**
 * @var array $setting
 */

//a quick fix

$config = array(
    'id' => 'yii_connect',
    'name' => 'Yii Connect',

    'basePath' => YC_PATH,
    'runtimePath' => YC_PATH . 'runtime',

    // preload log as per yii requirements
    'preload' => array(
        'log',
        'fatalErrorCatch',
    ),

    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.controllers.*',
        'application.components.*',
    ),

    // application components
    'components' => array(
        'cacheFile' => array(
            'class' => 'CFileCache',
        ),
        'cache' => array(
            'class' => 'CMemCache',
            'keyPrefix' => $_SERVER['HTTP_HOST'],
            'servers' => array(
                array(
                    'host' => '127.0.0.1',
                    'port' => 11211,
                    'weight' => 10,
                ),
            ),
        ),
        'assetManager' => array(
            'basePath' => YC_PATH . 'assets',
            'baseUrl' => YC_URL . 'assets',
        ),
        'clientScript' => array(
            'class' => 'YCClientScript',
        ),
        'db' => array(
            'connectionString' => 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
            'emulatePrepare' => true,
            'username' => DB_USER,
            'password' => DB_PASSWORD,
            'charset' => DB_CHARSET,
            'schemaCachingDuration' => 3600,
            //'enableProfiling' => true,
            //'enableParamLogging' => true,
        ),
        'fatalErrorCatch' => array(
            'class' => 'FatalErrorCatch',
        )
    ),

    // application-level parameters that can be accessed using Yii::app()->params['paramName']
    'params' => array(
        'email' => 'test@example.com',
    ),
);

// local web config overrides
$local = array();
$localFile = dirname(__FILE__) . '/main.local.php';
if (file_exists($localFile)) {
    $local = include($localFile);
}
return CMap::mergeArray($config, $local);