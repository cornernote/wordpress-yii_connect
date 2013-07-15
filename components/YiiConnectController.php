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
        if (!$file) {
            $error = 'View not found: ' . $file;
            // throw new Exception($error); // causes the wordpress page to render an empty page
            if ($return) {
                return $error;
            }
            else {
                echo $error;
                return false;
            }
        }
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
        $file = self::$basePath . 'views/' . $view . '.php';
        if (!file_exists($file)) {
            return false;
        }
        return $file;
    }

}