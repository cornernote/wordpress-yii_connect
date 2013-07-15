<?php

/**
 * Class YiiConnect
 */
class YiiConnect
{
    /**
     * @var array
     */
    public static $autoloadExclude = array(
        'Jetpack',
        'comment',
    );

    /**
     *
     */
    public static function init()
    {
        // set debug level reporting
        defined('YII_DEBUG') or define('YII_DEBUG', WP_DEBUG);
        defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

        // yii config array
        $config = dirname(__FILE__) . '/config/main.php';

        // require yii and create application
        require_once(YII_CONNECT_FRAMEWORK);
        require_once(dirname(__FILE__) . '/components/RawApplication.php');
        Yii::createApplication('RawApplication', $config);

        // fix autoload
        spl_autoload_unregister(array('YiiBase', 'autoload'));
        spl_autoload_register(array('YiiConnect', 'autoload'));

    }

    /**
     * @param $className
     * @throws Exception
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