<?php
/**
* Plugin Name: Build Private Store For Woocommerce
* Description: This plugin allows create OC Private Store For Woocommerce plugin.
* Version: 1.0
* Copyright: 2022
* Text Domain: build-private-store-for-woocommerce
* Domain Path: /languages 
*/


if (!defined('ABSPATH')) {
	exit('-1');
}
if (!defined('BPSFW_PLUGIN_DIR')) {
  define('BPSFW_PLUGIN_DIR',plugins_url('', __FILE__));
}
      
//Load all includes files
include_once('main/backend/bpsfw-backend.php');
include_once('main/frontend/bpsfw-frontend.php');
include_once('main/backend/bpsfw-common.php');
include_once('main/resource/bpsfw-load-js-css.php');
include_once('main/resource/bpsfw-language.php');
include_once('main/resource/bpsfw-load-my-plugin.php');
include_once('main/resource/bpsfw-installation-require.php');
