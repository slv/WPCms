<?php

Class WPCmsEditorPlugin {

  function __construct ($config) {
    $this->plugins = array();

    foreach ($config['plugins'] as $plugin) {
      if (!empty($plugin['id']))
        $this->plugins[$plugin['id']] = $plugin;
    }

    add_action('init', array($this, 'filters'));
    add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'), 10, 1);
    add_action('add_meta_boxes', array($this, 'add_meta_boxes'));

    foreach ($this->plugins as $id => $plugin) {
      if (get_template_directory_uri() . '/Plugins/' . $id . '/render.php')
        add_shortcode($id, array($this, 'shortcode'));
    }
  }

  function admin_enqueue_scripts ($hook) {
    foreach ($this->plugins as $id => $plugin) {
      foreach ($plugin['fields'] as $field) {
        if (!empty($field['scripts']))
          foreach ($field['scripts'] as $script) {
            wp_enqueue_script($script);
          }

        if (!empty($field['styles']))
          foreach ($field['styles'] as $style) {
            wp_enqueue_style($style);
          }

        wp_enqueue_script('wpcms-editor-plugin-' . $field['type'], WPCMS_STYLESHEET_URI . '/assets/editor.plugin.' . $field['type']. '.js');
      }
    }
  }

  function add_meta_boxes () {
    add_meta_box(
      null,
      'Available shortcodes',
      array($this, 'render_meta_box'),
      null,
      'normal', // context
      'high', // priority
      array('id' => 'shortcodes')
    );
  }

  function render_meta_box () {
    foreach ($this->plugins as $id => $plugin) {
      ?>
        <div>[<?php echo $id; ?><?php if (!empty($plugin['fields'])) foreach ($plugin['fields'] as $field) {
          echo ' ' . $field['name'] . '="<i>' . $field['type'] . '</i>"';
        } ?>] ... some content ... [/<?php echo $id; ?>]</div>
        <div class="wpcms-editor-plugin-item" data-shortcode="<?php echo $id; ?>" id="wpcms-editor-plugin-<?php echo $id; ?>" style="display:none;"><?php
          if (!empty($plugin['fields']))
            foreach ($plugin['fields'] as $field) {
              $atts = array();
              foreach ($field as $key => $value) {
                $attr = $value;

                if (is_array($value)) $attr = json_encode($value);

                $atts[] = 'data-wpcms-editor-plugin-' . $key . '="' . esc_attr($attr) . '"';
              }
              echo '<div class="wpcms-editor-plugin-input" ' . implode(' ', $atts) . '>editor wpcms-editor-plugin-input-' . $field['type'] . '</div>';
            }
        ?></div>
      <?php
    }
  }

  function filters () {
    add_filter('mce_external_plugins', array($this, 'add_buttons'));
    add_filter('mce_buttons', array($this, 'register_buttons'));
  }

  function shortcode ($atts, $content, $tag) {
    $render = require get_template_directory_uri() . '/Plugins/' . $tag . '/render.php';
    $render($atts, $content, $tag);
  }

  function add_buttons ($plugins) {
    foreach ($this->plugins as $id => $plugin) {
      $plugins['WPCmsEditor'] = WPCMS_STYLESHEET_URI . '/assets/editor.plugin.js';
    }
    return $plugins;
  }

  function register_buttons ($buttons) {
    foreach ($this->plugins as $id => $plugin) {
      if (!in_array($id, $buttons))
        array_push($buttons, $id);
    }
    return $buttons;
  }
}

