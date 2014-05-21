<?php

//
// Requires
//

require_once "WPCms/Singleton/WPCmsStatus.php";

require_once "WPCms/Classes/WPCmsField.php";
require_once "WPCms/Classes/WPCmsMultilanguageField.php";

require_once "WPCms/Classes/WPCmsDatePicker.php";
require_once "WPCms/Classes/WPCmsInputField.php";
require_once "WPCms/Classes/WPCmsTextField.php";
require_once "WPCms/Classes/WPCmsTextareaField.php";
require_once "WPCms/Classes/WPCmsTinyMCEField.php";
require_once "WPCms/Classes/WPCmsPasswordField.php";
require_once "WPCms/Classes/WPCmsCheckboxField.php";
require_once "WPCms/Classes/WPCmsSelectField.php";
require_once "WPCms/Classes/WPCmsRadioField.php";

require_once "WPCms/Classes/WPCmsGoogleFontsField.php";
require_once "WPCms/Classes/WPCmsRelationField.php";
require_once "WPCms/Classes/WPCmsModulesField.php";
require_once "WPCms/Classes/WPCmsColorPicker.php";
require_once "WPCms/Classes/WPCmsUploadField.php";
require_once "WPCms/Classes/WPCmsImageField.php";
require_once "WPCms/Classes/WPCmsImageProField.php";
require_once "WPCms/Classes/WPCmsGoogleMapField.php";
require_once "WPCms/Classes/WPCmsGalleryField.php";

require_once "WPCms/Classes/WPCmsSeparatorField.php";

require_once "WPCms/Classes/WPCmsPostType.php";
require_once "WPCms/Classes/WPCmsSettingsPage.php";

require_once "WPCms/actions-filters.php";


function wpcms_override_styles () {
  wp_enqueue_style('wpcms-override-css', WPCMS_STYLESHEET_URI . '/wpcms-override.css');
};
add_action('admin_enqueue_scripts', 'wpcms_override_styles', 10, 1);

