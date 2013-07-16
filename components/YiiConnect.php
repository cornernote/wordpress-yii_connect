<?php

/**
 * Class YiiConnect
 */
class YiiConnect
{

    /**
     * @var
     */
    public static $loaded;

    /**
     *
     */
    public static function init()
    {
        // set as loaded
        if (self::$loaded)
            return true;
        self::$loaded = true;

        // add the options
        add_option('yii_path', str_replace('\\', '/', realpath(YC_PATH . '../../../../yii/framework/yii.php')));

        // set debug level reporting
        defined('YII_DEBUG') or define('YII_DEBUG', WP_DEBUG);
        defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

        // admin settings page
        if (is_admin()) {
            add_action('admin_menu', 'YC::adminMenu');
            add_action('admin_init', 'YC::adminInit');
        }

        // yii config array
        $config = YC_PATH . 'config/main.php';
        if (!file_exists($config)) {
            return false;
        }

        // yii path
        $yii = get_option('yii_path');
        if (!self::validYiiPath($yii)) {
            return false;
        }

        // add output buffers
        YC::bufferStart();
        add_action('shutdown', 'YC::bufferEnd');

        // require yii and create application
        require_once($yii);
        require_once(YC_PATH . 'components/YCWebApplication.php');
        Yii::$enableIncludePath = false;
        $app = Yii::createApplication('YCWebApplication', $config);
        $app->controller = new CController('site');
        $app->controller->setAction(new CInlineAction($app->controller, 'index'));

        return true;
    }

    /**
     *
     */
    public static function bufferStart()
    {
        ob_start();
    }

    /**
     *
     */
    public static function bufferEnd()
    {
        $output = ob_get_clean();
        Yii::app()->getClientScript()->render($output);
        echo $output;
    }

    /**
     *
     */
    public static function adminMenu()
    {
        add_options_page('Yii Connect Options', 'Yii Connect', 'manage_options', 'yii-connect', 'YC::adminOptions');
    }

    /**
     *
     */
    public static function adminInit()
    {
        register_setting('yii_connect', 'yii_path', 'YC::validateYiiPath');
    }

    /**
     *
     */
    public static function validateYiiPath($path)
    {
        if (!self::validYiiPath($path)) {
            add_settings_error('yii_path', 'invalid_yii_path', __('The Yii Path entered does not appear to be valid.'));
        }
        return $path;
    }

    /**
     *
     */
    public static function validYiiPath($path)
    {
        if (!$path) {
            return false;
        }
        if (!file_exists($path)) {
            return false;
        }
        $contents = file_get_contents($path);
        if (!strpos($contents, 'extends YiiBase')) {
            return false;
        }
        return true;
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
        echo '<th scope="row"><label for="yii_path"><strong>Yii Path</strong></label><br/>Enter the full path to your Yii Framework\'s yii.php file.</th>';
        echo '<td>';
        echo '<input type="text" name="yii_path" id="yii_path" value="' . get_option('yii_path') . '" class="regular-text code error">';
        if (!$_POST) {
            if (self::validYiiPath(get_option('yii_path'))) {
                echo '<p style="color:green">appears to be valid</p>';
            }
            else {
                echo '<p style="color:red">does not appear to be valid</p>';
            }
        }
        echo '</td>';
        echo '</tr>';
        echo '</table>';
        submit_button();
        echo '</form>';
        echo '</div>';
    }

    /**
     * @param $path
     */
    public static function addIncludePath($path)
    {
        ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . $path);
    }


}