<?php

Class WPCmsSettingsPage {

  function __construct($config) {

    if (!is_user_logged_in()) return;

    $this->title = isset($config['title']) ? $config['title'] : 'Untitled';
    $this->slug = isset($config['menu_slug']) ? $config['menu_slug'] : '';
    $this->fields = isset($config['fields']) ? $config['fields'] : array();
    $this->parentSlug = isset($config['parent_slug']) ? $config['parent_slug'] : null;
    $this->capabilityType = 'manage_options';

    $this->options_group = $this->hash($this->title);

    add_action('admin_init', array(&$this, 'on_action_admin_init'));
    add_action('admin_menu', array(&$this, 'on_action_admin_menu'));
    add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'), 10, 1);


    foreach ($this->fields as $field) {

      $field->addActionRegister($this->slug);
    }
  }

  public function getMenuSlug ()
  {
    return $this->normalize($this->slug);
  }

  public function hash ($str)
  {
    return md5($str);
  }

  public function normalize ($str)
  {
    return preg_replace(array("/(\s+)/", "/([^a-zA-Z0-9_]*)/", "/(_+)/"), array("_", "", "_"), $str);
  }

  public function on_action_admin_init() {

    foreach ($this->fields as $k => $field) {

      $field->registerSettingInOptionsGroup($this->hash($this->title));
    }
  }
  function admin_enqueue_scripts ($hook) {

    if ($hook == $this->slug) {

      wp_register_script('wpcms-custompost', WPCMS_STYLESHEET_URI . '/assets/custom.post.js', 'jquery');
      wp_enqueue_script('wpcms-custompost');
      wp_enqueue_style('wpcms-field', WPCMS_STYLESHEET_URI . '/assets/field.css');

      foreach ($this->fields as $k => $field) {

        $field->addActionAdminEnqueueScripts($hook);
      }
    }
  }

  public function on_action_admin_menu () {

    if ($this->parentSlug)
    {
      $this->slug = add_submenu_page($this->parentSlug,
        $this->title, $this->title, $this->capabilityType, $this->getMenuSlug(), array($this, 'settingsPage')
      );
    }
    else
    {
      $this->slug = add_menu_page(
        $this->title, $this->title, $this->capabilityType, $this->getMenuSlug(), array($this, 'settingsPage')
      );
    }
  }

  public function settingsPage () {
    echo '<form class="form-horizontal wpcms-settings-page" method="post" action="options.php" enctype="multipart/form-data">',
        '<h3>' . $this->title . '</h3>';

      settings_fields($this->options_group);
      do_settings_fields($this->options_group, 'options-general.php');

      foreach ($this->fields as $k => $field) {

        $field->renderSetting();
      }

      echo '<div class="form-group">',
        '<div class="wpcms-buttons"><input type="submit" class="button button-primary" value="' . __('Save Changes') . '" /></div>',
        '</div>',
      '</form>';
  }
}
