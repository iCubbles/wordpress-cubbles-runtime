<?php
/*
Plugin Name: Cubbles Runtime
Plugin URI: https://github.com/iCubbles/wordpress-cubbles-runtime
Description: Add capability to use Cubbles inside Wordpress HTML Editor
Version: 0.1
Author: Philipp Wagner
Autho URI: https://github.com/pwrinc
License: GPL
*/


add_action('init', 'cubbles_allow_custom_tags');

function cubbles_allow_custom_tags() {
  global $allowedposttags;

  $allowedposttags['travel-planner'] = array(
    'cubx-dependency' => true
  );

}


add_filter('tiny_mce_before_init', 'cubbles_filter_tiny_mce_before_init');

function cubbles_filter_tiny_mce_before_init( $options ) {
  $opts = '*[*]';
  $options['valid_elements'] = $opts;
  $options['extended_valid_elements'] = $opts;
  return $options;
}

add_action('wp_enqueue_scripts', 'cubbles_add_runtime');

function cubbles_add_runtime() {
  wp_enqueue_script('webcomponents-lite', 'https://cubbles.world/sandbox/cubx.core.rte@1.8.0-SNAPSHOT/webcomponents/webcomponents-lite.js', array(), null);
  wp_enqueue_script('cubbles-rte', 'https://cubbles.world/sandbox/cubx.core.rte@1.8.0-SNAPSHOT/crc-loader/js/main.js', array(), null);
}

add_filter('clean_url', 'cubbles_add_cif_script_attr', 10, 1);

function cubbles_add_cif_script_attr( $url ) {
  if ($url === 'https://cubbles.world/sandbox/cubx.core.rte@1.8.0-SNAPSHOT/crc-loader/js/main.js') {
    return "$url' data-crcinit-loadcif='true";
  } else {
      return $url;
  }
}

?>
