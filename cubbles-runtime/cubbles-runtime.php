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

// include admin.php which contains admin menu stuff
require_once(plugin_dir_path(__FILE__) . '/CubxAdminMenu.php');

class CubxRuntime {

  // this is the placeholder used to replace dash custom elements tag name during transformation of html
  // see transformCustomTags()
  private static $placeholderDash = '0000';

  // attribute used for making an html tag a client runtime container
  private static $cubxCoreCrcAttr = 'cubx-core-crc';

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
  private static $rteWebpackage = 'cubx.core.rte@1.9.0-SNAPSHOT';

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
    $options = get_option(self::$optionsKey);
    return $options['allowedTags'];
  }

  private static function _writeAllowdTagsToDB($allowedTags) {
    $options = get_option(self::$optionsKey);
    $options['allowedTags'] = $allowedTags;
    update_option(self::$optionsKey, $options);
  }

  static function returnAllowedCustomTags($allowedTags, $context) {
    $cubxAllowedTags = array_keys(self::_getAllowedTagsFromDB());
    foreach ($cubxAllowedTags as $tag) {
      $tagNameEscaped =  str_ireplace('-', self::$placeholderDash, $tag);
      $allowedTags[$tagNameEscaped] = self::$allowedAttrs;
    }

    return $allowedTags;
  }

  static function transformCustomTags($html) {
    $customTags = array_keys(self::_getAllowedTagsFromDB());

    // iterate over all allowed cubbles custom tags and replace them so they won't get stripped out by kses filter
    foreach ($customTags as $tag) {
      // transform each tag name replacing dash with defined self::$placeholderDash
      // e.g. <my-element ...> will be replaced with <my000element ...>
      // with that we can pevent kses from stripping them out
      $pattern = '<' . $tag;
      $replace = '<' . str_ireplace('-', self::$placeholderDash, $tag);
      $html = str_ireplace($pattern, $replace, $html);

      // replace all closing tags analog to opening tags above
      $pattern = '</' . $tag;
      $replace = '</' . str_ireplace('-', self::$placeholderDash, $tag);
      $html = str_ireplace($pattern, $replace, $html);
    }

    return $html;
  }

  static function retransformCustomTags($html) {
    $customTags = array_keys(self::_getAllowedTagsFromDB());

    // iterate over all allowed tags and reverse transformation to get the dash again in tag names
    foreach ($customTags as $tag) {
      // e.g. <my0000element ...> will be replaced with <my-element" ...>
      $pattern = '<' . str_ireplace('-', self::$placeholderDash, $tag);
      $replace = '<' . $tag;
      $html = str_ireplace($pattern, $replace, $html);

      // replace all closing tags analog to opening tags above
      $pattern = '</' . str_ireplace('-', self::$placeholderDash, $tag);
      $replace = '</' . $tag;
      $html = str_ireplace($pattern, $replace, $html);
    }

    return $html;
  }

  static function filterTinyMceBeforeInit($options) {
    $allowedTags = array_keys(self::_getAllowedTagsFromDB());
    $allowedAttrs = array_keys(self::$allowedAttrs);

    $extendedValidElements = '@[' . implode('|', $allowedAttrs) . ']';
    $extendedValidElements = $extendedValidElements . ',' . implode(',', $allowedTags);
    // $extendedValidElements = $extendedValidElements . ',div[align|class|dir|lang|style|xml::lang|' . self::$cubxCoreCrcAttr.']';

    $options['extended_valid_elements'] = $extendedValidElements;

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

  static function wrapContent($content) {
     return '<div ' . self::$cubxCoreCrcAttr . '>' . $content . '</div>';
  }

  static function addAdminMenu() {
    $values = array(
      'remoteStoreUrl' => self::$remoteStoreUrl,
      'rteWebpackage' => self::$rteWebpackage,
      'allowedTags' => array_keys(self::_getAllowedTagsFromDB())
    );
    // init new instance of CubixAdminMenu class
    new CubxAdminMenu($values);
  }

  static function init() {
    // this adds capability to use cubbles also for users with roles 'Author' and 'Contributor' (which do not have the permission 'unfiltered_html')
    // For more info see: https://codex.wordpress.org/Roles_and_Capabilities#unfiltered_html
    add_filter('wp_kses_allowed_html', array('CubxRuntime', 'returnAllowedCustomTags'), 10, 2);
    add_filter('tiny_mce_before_init', array('CubxRuntime', 'filterTinyMceBeforeInit'));
    // use this filter to replace all custom tags with dashes before kses filter is applied
    add_filter('content_save_pre', array('CubxRuntime', 'transformCustomTags'), 9);
    // use this to retransform filtered html before saving
    add_filter('content_save_pre', array('CubxRuntime', 'retransformCustomTags'), 11);

    // adding the needed cubbles platform scripts
    add_action('wp_enqueue_scripts', array('CubxRuntime', 'addRuntime'));
    // add cif init attribute to crc loader script tag
    add_filter('clean_url', array('CubxRuntime', 'addCifScriptAttr'), 10, 1);
    // make the content get wrapped by a client runtime container  (<div cubx-core-crc>[the content]</div>)
    add_filter('the_content', array('CubxRuntime', 'wrapContent'));

    // add admin menu
    add_action('admin_menu', array('CubxRuntime', 'addAdminMenu'));
  }

  static function onActivate() {
    //check if there is already an option entry
    $options = get_option(self::$optionsKey);
    if (!is_null($options)) {
      // write default options into database
      $options = array();
      $options['allowedAttrs'] = self::$allowedAttrs;
      $options['allowedTags'] = self::$allowedTags;
      $options['remoteStoreUrl'] = self::$remoteStoreUrl;
      $options['rteWebpackage'] = self::$rteWebpackage;
      add_option(self::$optionsKey, $options);
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

// add_action('activated_plugin','save_error');
// function save_error(){
//     update_option('cubx_plugin_error',  ob_get_contents());
// }

CubxRuntime::registerActivationHook();
CubxRuntime::registerDeactivationHook();
CubxRuntime::registerUninstallHook();
CubxRuntime::init();
?>
