<?php

/**
 *
 */
class YCPlugin
{

    /**
     * @var string
     */
    public static $id;

    /**
     * @var string
     */
    public static $basePath;

    /**
     *
     */
    public static function init()
    {
        self::$id = basename(self::$basePath);
        YiiBase::setPathOfAlias(self::$id, self::$basePath);
        Yii::import(self::$id . '.components.*');
        Yii::import(self::$id . '.models.*');
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