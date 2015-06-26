<?php

Class WPCmsUtils
{
   private static $instance = null;

   private static $defaultLang = 'en';

   public static function getStatus () {
      if (self::$instance == null) {
         $c = __CLASS__;
         self::$instance = new $c;
      }

      return self::$instance;
   }

   public static function getAvailableLanguages () {

     if (function_exists('qtrans_getSortedLanguages'))
       return qtrans_getSortedLanguages();

     if (function_exists('icl_get_languages')) {
       $array = array();
       $languages = icl_get_languages('skip_missing=1');

       foreach($languages as $l){
         $array[] = $l['language_code'];
       }
       return $array;
     }

     return array(self::$defaultLang);
   }

   public static function getLanguage () {

     if (function_exists('qtrans_getLanguage'))
       return qtrans_getLanguage();

     if (function_exists('icl_get_languages')) {
       $array = array();
       $languages = icl_get_languages('skip_missing=1');
       foreach($languages as $l){
         if (!empty($l['active'])) return $l['language_code'];
       }
     }

     return self::$defaultLang;
   }

   public static function getLanguageURL ($url, $lang = false) {
     if (!$lang)
       $lang = self::getLanguage();

     if (function_exists('qtrans_convertURL'))
       return qtrans_convertURL($url, $lang);

     return $url;
   }

   public static function getLanguageSelfURL ($lang = false) {
     if (!$lang)
       $lang = self::getLanguage();

     if (function_exists('qTranslateSlug_getSelfUrl'))
       return qTranslateSlug_getSelfUrl($lang);

     if (function_exists('icl_get_languages')) {
       $array = array();
       $languages = icl_get_languages('skip_missing=1');
       foreach($languages as $l){
         if (!empty($l['language_code']) && $l['language_code'] == $lang) return $l['url'];
       }
     }

     return self::getLanguageURL(home_url(), $lang);
   }


   public static function getOption ($label, $default = '') {
     $value = get_option(WPCmsStatus::getStatus()->getData('pre') . $label, $default);

     if (!$value)
       return $default;

     return $value;
   }


   public static function getPostMeta ($label, $postID = false) {
     if (!$postID) $postID = get_the_ID();

     return get_post_meta($postID, WPCmsStatus::getStatus()->getData('pre') . $label, true);
   }

   public static function isRelatedTo ($label, $postID = false) {
     if (!$postID) $postID = get_the_ID();

     return get_post_meta($postID, WPCmsStatus::getStatus()->getData('pre') . $label . '__related_as', true);
   }

   public static function printModule ($id, $postID = false) {
     if (_m($id, $postID)) {
       foreach (_m($id, $postID) as $key => $m) {
         global $module;

         $module = array();
         $module['wpcms_module_unique_id'] = $key;
         foreach ($m as $k => $v) {
           $module[preg_replace("/^" . WPCmsStatus::getStatus()->getData('pre') . "/", "", $k)] = $v;
         }

         get_template_part('Modules/' . $module['widget_type'] . '/view');
       }
     }
   }

   public static function printModuleStyles ($id, $postID = false) {
     if (_m($id, $postID)) {
       foreach(_m($id, $postID) as $key => $m) {
         global $module;

         $module = array();
         $module['wpcms_module_unique_id'] = $key;
         foreach ($m as $k => $v) {
           $module[preg_replace("/^" . WPCmsStatus::getStatus()->getData('pre') . "/", "", $k)] = $v;
         }

         get_template_part('Modules/' . $module['widget_type'] . '/head');
       }
     }
   }
}