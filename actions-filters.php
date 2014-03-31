<?php

// Multilanguage

if (!defined('WPCMS_DEFAULT_LANG'))
  define ('WPCMS_DEFAULT_LANG', 'en');


function wpcms_get_available_languages () {
  if (function_exists('qtrans_getSortedLanguages'))
    return qtrans_getSortedLanguages();

  return array(WPCMS_DEFAULT_LANG);
};

function wpcms_get_language () {
  if (function_exists('qtrans_getLanguage'))
    return qtrans_getLanguage();

  return WPCMS_DEFAULT_LANG;
};

function wpcms_get_language_url ($url, $lang) {
  if (!$lang)
    $lang = wpcms_get_language();

  if (function_exists('qtrans_convertURL'))
    return qtrans_convertURL($url, $lang);

  return $url;
};



// Options Retriever

function _o($label, $default = '') {
  $value = get_option(WPCmsStatus::getStatus()->getData('pre') . $label, $default);

  if (!$value)
    return $default;

  return $value;
}

function _l($value, $lang = false) {
  if (!$lang && function_exists('wpcms_get_language'))
    $lang = wpcms_get_language();

  if (!is_array($value) || !isset($value[$lang]))
    return $value;

  return $value[$lang];
}

function _m($label, $postID = false) {
  if (!$postID) $postID = get_the_ID();

  return get_post_meta($postID, WPCmsStatus::getStatus()->getData('pre') . $label, true);
}

function _is_related_to($label, $postID = false) {
  if (!$postID) $postID = get_the_ID();

  return get_post_meta($postID, WPCmsStatus::getStatus()->getData('pre') . $label . '__related_as', true);
}

function _module($id, $postID = false) {
  if (_m($id, $postID)) {
    foreach(_m($id, $postID) as $key => $m) {
      global $module;

      $module = array();
      foreach ($m as $k => $v) {
        $module[preg_replace("/^" . WPCmsStatus::getStatus()->getData('pre') . "/", "", $k)] = $v;
      }

      get_template_part('Modules/' . $module['widget_type'] . '/view');
    }
  }
}


// To enable $_FILES variable

function update_edit_form () {
  echo ' enctype="multipart/form-data"';
}

add_action('post_edit_form_tag', 'update_edit_form');
