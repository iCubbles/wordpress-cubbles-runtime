<?php
/*
Plugin Name: Cubbles Runtime
Plugin URI: https://github.com/iCubbles/wordpress-cubbles-runtime
Description: Add capability to use Cubbles inside Wordpress HTML Editor
Version: 0.2
Author: Philipp Wagner
Autho URI: https://github.com/pwrinc
License: GPL
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class CubxRuntime {

  // useds to store options into wordpress database using it's option API
  private static $optionsKey = 'cubx_runtime_options';

  // contains all cubbles related attributes a cubble custom html tag can have
  private static $allowedAttrs = array(
    'cubx-dependency' => true
  );

  // default list of allowed custom html tags. Indeed each custom html tag represents a cubbles component name
  // Only cubbles component names that are included in allowedTags array can be used by users which don't have
  // permission 'unfiltered_html'
  private static $allowedTags = array(
    'pie-chart' => array(),
    'travel-planner' => array(),
    'co2-footprint' => array()
  );

  // default remote storeUrl to be used
  private static $remoteStoreUrl = 'https://cubbles.world/sandbox';
  // default rte to be used
  private static $rteWebpackage = 'cubx.core.rte@1.8.0';

  private static $scripts = array(
    'webcomponents-lite' => 'webcomponents/webcomponents-lite.js',
    'crc-loader' => 'crc-loader/js/main.js'
  );

  // get full script url by name
  private static function _getScripts($name) {
    $script = self::$remoteStoreUrl . '/' . self::$rteWebpackage . '/' . self::$scripts[$name];
    return $script;
  }

  private static function _getAllowedTagsFromDB() {
    // for now just return the static default allowedTags
    return self::$allowedTags;
  }

  private static function _writeAllowdTagsToDB() {

  }

  // merge all allowed cubble tags into global $allowedposttags array
  static function addAllowedCustomTags() {
    global $allowedposttags;
    $allowedTags = self::_getAllowedTagsFromDB();
    foreach ($allowedTags as &$tag) {
      $tag = self::$allowedAttrs;
    }
    unset($tag);
    $allowedposttags = array_merge($allowedposttags, $allowedTags);
  }

  static function filterTinyMceBeforeInit($options) {
    $opts = '*[*]';
    $options['valid_elements'] = $opts;
    $options['extended_valid_elements'] = $opts;
    return $options;
  }

  static function addRuntime() {
    wp_enqueue_script('webcomponents-lite', self::_getScripts('webcomponents-lite'), array(), null);
    wp_enqueue_script('crc-loader', self::_getScripts('crc-loader'), array(), null);
  }

  static function addCifScriptAttr($url) {
    $crcLoaderUrl = self::_getScripts('crc-loader');
    if ($url === $crcLoaderUrl) {
      return "$url' data-crcinit-loadcif='true";
    } else {
      return $url;
    }
  }

  static function init() {
    // this adds capability to use cubbles also for users with roles 'Author' and 'Contributor' (which do not have the permission 'unfiltered_html')
    // For more info see: https://codex.wordpress.org/Roles_and_Capabilities#unfiltered_html
    add_action('init', array('CubxRuntime', 'addAllowedCustomTags'));

    add_filter('tiny_mce_before_init', array('CubxRuntime', 'filterTinyMceBeforeInit'));
    add_action('wp_enqueue_scripts', array('CubxRuntime', 'addRuntime'));
    add_filter('clean_url', array('CubxRuntime', 'addCifScriptAttr'), 10, 1);
  }

  static function onActivate() {
    //check if there is already an option entry
    $options = get_option(self::$optionsKey);
    if (!is_null($optionsKey)) {
      // write default options into database
      $options = array();
      $options['allowedAttrs'] = self::$allowedAttrs;
      $options['allowedTags'] = self::$allowedTags;
      $options['remoteStoreUrl'] = self::$remoteStoreUrl;
      $options['rteWebpackage'] = self::$rteWebpackage;
    }
  }

  static function onDeactivate() {

  }

  static function onUninstall() {
    delete_option(self::$optionsKey);
  }

  static function registerActivationHook() {
    register_activation_hook(__FILE__, array('CubxRuntime', 'onActivate'));
  }

  static function registerDeactivationHook() {
    register_deactivation_hook(__FILE__, array('CubxRuntime', 'onDeactivate'));
  }

  static function registerUninstallHook() {
    register_uninstall_hook(__FILE__, array('CubxRuntime', 'onUninstall'));
  }
}

CubxRuntime::registerActivationHook();
CubxRuntime::registerDeactivationHook();
CubxRuntime::registerUninstallHook();
CubxRuntime::init();
?>
