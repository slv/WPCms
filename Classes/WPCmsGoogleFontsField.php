<?php

Class WPCmsGoogleFontsField Extends WPCmsField {

  function __construct ($config) {
    $this->id = WPCmsStatus::getStatus()->getData('pre') . $this->normalize($config['id']);
    $this->name = isset($config['name']) ? $config['name'] : '';
    $this->description = isset($config['description']) ? $config['description'] : '';
    $this->default = isset($config['default']) ? $config['default'] : '';
    $this->fontSize = isset($config['font_size']) ? $config['font_size'] : 16;

    return $this;
  }

  static function printGoogleFontsStyles ($fontsSelectors)
  {
    $elements = array();
    $families = array();

    foreach ($fontsSelectors as $selector => $font) {
      if (in_array($font, $elements)) continue;

      $elements[] = $font;

      $font_exploded = explode(':', $font);
      $ff = array_shift($font_exploded);

      $font_exploded = explode(':', $font);
      $fw = array_pop($font_exploded);

      if (isset($families[$ff])) $families[$ff] .= ',' . $fw;
      else $families[$ff] = $font;
    }


    echo "<link href='http://fonts.googleapis.com/css?family=" . str_replace(" ", "+", implode('|', $families)) . "' rel='stylesheet' type='text/css'>" . PHP_EOL .
      "<style type=\"text/css\">" . PHP_EOL;


    foreach ($fontsSelectors as $selector => $font) {

      $font_exploded = explode(':', $font);
      $ff = array_shift($font_exploded);

      $font_exploded = explode(':', $font);
      $fw = array_pop($font_exploded);

      echo $selector . "{ font-family: '" . $ff . "'; font-weight:" .
        preg_replace(array("/^(regular|italic)$/", "/^(\d*)(italic)$/"), array("400", "$1"), $fw) .
        "; font-style:" .
        (preg_replace(array("/^(\d*)(.*)/", "/regular/"), array("$2", "normal"), $fw) ?
        preg_replace(array("/^(\d*)(.*)/", "/regular/"), array("$2", "normal"), $fw) : 'normal') .
        "; }" . PHP_EOL;
    }

    echo "</style>";
  }

  public function addActionAdminEnqueueScripts ($hook)
  {
    wp_enqueue_script('wpcms-googlefonts-lib', '//ajax.googleapis.com/ajax/libs/webfont/1/webfont.js');
    wp_enqueue_script('wpcms-googlefonts', WPCMS_STYLESHEET_URI . '/WPCms/assets/google.fonts.js', array('jquery'));
  }

  public function renderInnerInput ($post, $data = array()) {

    // List from: https://developers.google.com/apis-explorer/#p/webfonts/v1/webfonts.webfonts.list?sort=trending&_h=1&
    // $fonts = json_decode(file_get_contents(..google api response..), true);
    // foreach ($fonts['items'] as $key => $value) { foreach ($value['variants'] as $variant) { echo $value['family'] . ':' . $variant . PHP_EOL; }}

    echo '<div class="field-wrapper">';
    echo '<select class="form-control" type="text" name="', $data['name'], '" id="', $data['id'], '">';

    if ($data['value'] == '' && !isset($this->options[$this->default]))
      echo '<option value="">', __('Select', WPCmsStatus::getStatus()->getData('textdomain')),'...</option>';

    $fonts = file_get_contents(WPCMS_STYLESHEET_DIR . '/WPCms/assets/google.fonts.list.20140215');
    $fonts = explode(PHP_EOL, $fonts);
    $families = array();

    foreach ($fonts as $font) {

      $font_exploded = explode(':', $font);
      $ff = array_shift($font_exploded);

      $font_exploded = explode(':', $font);
      $fw = array_pop($font_exploded);

      if (isset($families[$ff])) $families[$ff] .= ',' . $fw;
      else $families[$ff] = $font;
    }

    foreach ($fonts as $font) {

      $font_exploded = explode(':', $font);
      $ff = array_shift($font_exploded);

      $selected = ($font == $data['value'] ? ' selected="selected"' : '');
      echo '<option ', $selected,' value="', esc_attr($font), '" data-font="', urlencode($families[$ff]), '">', $font, '</option>';
    }

    echo '</select>';

    echo '<p class="form-control-static demo" data-fontsize="', $this->fontSize, '">', ($data['value'] != '' ? $data['value'] : 'Font: ' . __('Default', WPCmsStatus::getStatus()->getData('textdomain'))), '</p>';
    echo '</div>';
  }
}