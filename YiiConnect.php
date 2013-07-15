<?php

/**
 * Class YiiConnect
 */
class YiiConnect
{
    /**
     * @var array
     */
    public static $autoloadExclude = array();

    /**
     *
     */
    public static function init()
    {
        // set error reporting
        if (WP_DEBUG) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            defined('YII_DEBUG') or define('YII_DEBUG', true);
            defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);
        }
        else {
            //error_reporting(E_ALL);
            //ini_set('log_errors', 1);
            //ini_set('display_errors', 0);
            defined('YII_DEBUG') or define('YII_DEBUG', false);
        }

        // yii config array
        $config = dirname(__FILE__) . '/config/main.php';

        // yii
        require_once(dirname(__FILE__) . '/YiiConnect.php');
        require_once(YII_CONNECT_FRAMEWORK);
        require_once(dirname(__FILE__) . '/components/RawApplication.php');
        Yii::createApplication('RawApplication', $config);

        // fix autoload
        spl_autoload_unregister(array('YiiBase', 'autoload'));
        spl_autoload_register(array('YiiConnect', 'autoload'));

    }

    /**
     * @param $className
     */
    public static function autoload($className)
    {
        if (in_array($className, self::$autoloadExclude)) {
            return;
        }
        if (stripos($className, 'wp_') === 0) {
            return;
        }
        YiiBase::autoload($className);
    }
}