<?php
/*
Plugin Name: Yii Connect
Plugin URI: https://github.com/cornernote/wordpress-yii_connect/
Description: Allows simple integration of Yii directly from your Wordpress site.
Version: 0.1.0
Author: Zain Ul abidin and Brett O'Donnell
Author URI: http://mrphp.com.au
License: CC-by-nc-nd
*/

/*
Copyright 2013, Zain Ul abidin <zainengineer@gmail.com>, Brett O'Donnell <cornernote@gmail.com>

This work is licensed under the Creative Commons Attribution-NonCommercial-NoDerivs 3.0 Unported License.
To view a copy of this license, visit http://creativecommons.org/licenses/by-nc-nd/3.0/.
*/

// ensure we cannot load directly
if (!function_exists('add_action')) {
    echo 'Yii Connect cannot be called directly.';
    exit;
}

if (!isset($GLOBALS['yiiConnectWPGlobal'])){
    $GLOBALS['yiiConnectWPGlobal'] = array();
}

defined('YII_DEBUG') or define('YII_DEBUG', true);
// define constants
define('YC_VERSION', '0.1.0');
define('YC_URL', plugin_dir_url(__FILE__));
define('YC_PATH', plugin_dir_path(__FILE__));

function yii_connect_admin_notice($message,$class='error') {
    ?>
<div class="<?php echo $class; ?>">
    <p><?php echo $message; ?></p>
</div>
<?php
}

function yii_connect_path_not_found()
{
    global $yiiConnectWPGlobal;
    $message = "Could not find yii path " . $yiiConnectWPGlobal['yii_path'];
    yii_connect_admin_notice($message);
}

// load YiiConnect
require_once(YC_PATH . 'components/YiiConnect.php');
if (YiiConnect::init()){
    define('YC_LOADED', true);
}
else{
    define('YC_LOADED', false);
}