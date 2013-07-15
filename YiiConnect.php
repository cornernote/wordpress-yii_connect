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
        'dashboard',
        '1',
    );

    /**
     *
     */
    public static function init()
    {
        // add the options
        add_option('yii_path', str_replace('\\', '/', realpath(YII_CONNECT_PATH . '../../../../yii/yii-1.1.13.e9e4a0/framework/yii.php')));

        // set debug level reporting
        defined('YII_DEBUG') or define('YII_DEBUG', WP_DEBUG);
        defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

        // yii config array
        $config = dirname(__FILE__) . '/config/main.php';

        // yii path
        $yii = get_option('yii_path');
        if (file_exists($yii)) {
            // require yii and create application
            require_once($yii);
            require_once(dirname(__FILE__) . '/components/RawApplication.php');
            $app = Yii::createApplication('RawApplication', $config);
            $app->controller = new Controller('site');

            // fix autoload
            spl_autoload_unregister(array('YiiBase', 'autoload'));
            spl_autoload_register(array('YiiConnect', 'autoload'));
        }

        // admin settings page
        if (is_admin()) {
            add_action('admin_menu', 'YiiConnect::adminMenu');
            add_action('admin_init', 'YiiConnect::adminInit');
        }

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
        if (stripos($className, '_wp_') === 0) {
            return;
        }
        YiiBase::autoload($className);
    }

    /**
     *
     */
    public static function adminMenu()
    {
        add_options_page('Yii Connect Options', 'Yii Connect', 'manage_options', 'yii-connect', 'YiiConnect::adminOptions');
    }

    /**
     *
     */
    public static function adminInit()
    {
        register_setting('yii_connect', 'yii_path', 'YiiConnect::validYiiPath');
    }

    /**
     *
     */
    public static function validYiiPath($path)
    {
        if (!file_exists($path)) {
            add_settings_error('yii_path', 'invalid_yii_path', __('The Yii Path entered does not appear to be valid.'));
        }
        return $path;
    }

    /**
     *
     */
    public static function adminOptions()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        echo '<div class="wrap">';
        screen_icon();
        echo '<h2>Yii Connect</h2>';
        echo '<form method="post" action="options.php">';
        settings_fields('yii_connect');
        echo '<table class="form-table">';
        echo '<tr valign="top">';
        echo '<th scope="row"><label for="yii_path">Yii Path</label></th>';
        echo '<td class="error">';
        echo '<input type="text" name="yii_path" id="yii_path" value="' . get_option('yii_path') . '" class="regular-text code error">';
        echo '</td>';
        echo '</tr>';
        echo '</table>';
        submit_button();
        echo '</form>';
        echo '</div>';
    }
}