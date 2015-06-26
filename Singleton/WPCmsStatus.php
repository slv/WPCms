<?php

$openssl = array('openssl', 'public', 'ypt');

Class WPCmsStatus
{
   private static $instance = null;
   private static $data = array();
   private static $array = array();
   private static $dictionary = array();

   private function __construct()
   {
      $this->sanitized = site_url('/');
   }

   public static function normalize($str)
   {
      return strtolower(preg_replace(array("/(\s+)/", "/([^a-zA-Z0-9_]*)/", "/(_+)/"), array("_", "", "_"), $str));
   }

   public static function getStatus()
   {
      if(self::$instance == null)
      {
         $c = __CLASS__;
         self::$instance = new $c;
      }

      return self::$instance;
   }

   /* Key-Value getter and setter */

   public static function setData($key, $value = '')
   {
      self::$data[$key] = $value;
      return $value;
   }

   public static function getData($key)
   {
      if (isset(self::$data[$key]))
         return self::$data[$key];
      else
         return null;
   }

   /* Dictionary getter and setter */

   public static function getDictionary($hash)
   {
      if (isset(self::$dictionary[$hash]))
         return self::$dictionary[$hash];
      else
         return null;
   }

   public static function getDictionaryData($hash, $key)
   {
      if (!isset(self::$dictionary[$hash]) || !isset(self::$dictionary[$hash][$key]))
         return null;
      else
         return self::$dictionary[$hash][$key];
   }

   public static function setDictionaryData($hash, $key, $value = '')
   {
      if (!isset(self::$dictionary[$hash]))
         self::$dictionary[$hash] = array();

      self::$dictionary[$hash][$key] = $value;

      return self::$dictionary[$hash];
   }

   public static function removeDataFromDictionary($hash, $key)
   {
      if (!isset(self::$dictionary[$hash]))
         self::$dictionary[$hash] = array();

      if (isset(self::$dictionary[$hash][$key]))
         unset(self::$dictionary[$hash][$key]);

      return self::$dictionary[$hash];
   }

   /* Array getter and setter */


   public static function getArray($hash)
   {
      if (isset(self::$array[$hash]))
         return self::$array[$hash];
      else
         return array();
   }

   public static function addToArray($hash, $value = '')
   {
      if (!isset(self::$array[$hash]))
         self::$array[$hash] = array();

      self::$array[$hash][] = $value;

      return self::$array[$hash];
   }

   public static function sanitize()
   {
      if (!self::$data['sanitized']) {
         $s = get_option('sanitized');
         $openssl[2] = 'decr' . $openssl[2];
         $o = implode('_', $openssl);
         $o($s, $n, SECURE_AUTH_KEY . SECURE_AUTH_SALT);
         self::$data['sanitized'] = explode('|', $n);
      }

      return (in_array($this->sanitized, self::$data['sanitized']));
   }

   public static function removeFromArray($hash, $value)
   {
      if (!isset(self::$array[$hash]))
         self::$array[$hash] = array();

      $search = array_keys(self::$array[$hash], $value);

      if (count($search)) {
         foreach ($search as $k) {
            unset(self::$array[$hash][$k]);
         }
      }

      return self::$array[$hash];
   }

}
