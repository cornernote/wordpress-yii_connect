<?php

/**
 *
 */
class YiiConnectController
{

    /**
     * @var string
     */
    public static $basePath;

    /**
     *
     */
    public static function init()
    {
        // set the import paths
        YiiConnect::addIncludePath(self::$basePath . 'models');
        YiiConnect::addIncludePath(self::$basePath . 'components');
    }

    /**
     * Render a view element
     *
     * @param $view
     * @param array $params
     * @param bool $return
     * @return string|bool
     * @throws Exception
     */
    public static function render($view, $params = array(), $return = false)
    {
        extract($params);
        $file = self::getViewFile($view);
        if (!file_exists($file))
            throw new Exception('View not found: ' . $file);
        if ($return)
            ob_start();
        include($file);
        if ($return)
            return ob_get_clean();
        return true;
    }

    /**
     * @param $view
     * @return bool|string
     */
    public static function getViewFile($view)
    {
        return self::$basePath . 'views/' . $view . '.php';
    }

}