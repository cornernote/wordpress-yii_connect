<?php
/*
Plugin Name: Yii Connect
Plugin URI: https://github.com/cornernote/wordpress-yii_connect/
Description: Allows simple integration of Yii directly from your Wordpress site.
Version: 0.0.1
Author: Zain Ul abidin and Brett O'Donnell
Author URI: http://mrphp.com.au
License: CC-by-nc-nd
*/

/*
Copyright 2013, Zain Ul abidin <zainengineer@gmail.com>, Brett O'Donnell <cornernote@gmail.com>

This work is licensed under the Creative Commons Attribution-NonCommercial-NoDerivs 3.0 Unported License.
To view a copy of this license, visit http://creativecommons.org/licenses/by-nc-nd/3.0/.
*/

// ensure we dont get loaded directly
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

// define constants
define('YII_CONNECT_VERSION', '0.0.1');
define('YII_CONNECT_URL', plugin_dir_url(__FILE__));
define('YII_CONNECT_FRAMEWORK', dirname(__FILE__) . '/../../../../yii/yii-1.1.13.e9e4a0/framework/yii.php');

// load YiiConnect
require_once(dirname(__FILE__) . '/YiiConnect.php');
YiiConnect::init();

//// auto-init
//function yii_connect_init()
//{
//}
//
//add_action('init', 'yii_connect_init');
//
//
