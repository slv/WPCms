<?php
/*
Plugin Name: WPCms
Version:     1.0
Author:      Michele Salvini
*/


define ('WPCMS_STYLESHEET_URI', plugins_url('', __FILE__));
define ('WPCMS_STYLESHEET_DIR', plugin_dir_path( __FILE__ ));


require_once "WPCmsIncludes.php";


// prefix to all postmeta and options, (not required)
WPCmsStatus::getStatus()->setData('pre', 'wpcms_');
