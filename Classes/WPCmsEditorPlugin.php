<?php

Class WPCmsEditorPlugin {

  function __construct ($config) {
    $this->id = $config['id'];
    add_action('init', array($this, 'filters'));
    add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'), 10, 1);

  }

  function admin_enqueue_scripts ($hook) {
    wp_enqueue_style('wpcms-field', WPCMS_STYLESHEET_URI . '/assets/field.css');
  }

  function filters () {
    add_filter('mce_external_plugins', array($this, 'add_buttons'));
    add_filter('mce_buttons', array($this, 'register_buttons'));
  }

  function add_buttons ($plugins) {
    $plugins[$this->id] = get_template_directory_uri() . '/Plugins/' . $this->id . '/script.js';
    return $plugins;
  }

  function register_buttons ($buttons) {
    array_push($buttons, $this->id);
    return $buttons;
  }
}

