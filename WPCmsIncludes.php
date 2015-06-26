<?php

//
// Requires
//

require_once "Singleton/WPCmsStatus.php";
require_once "Singleton/WPCmsUtils.php";

require_once "Classes/WPCmsField.php";

require_once "Classes/WPCmsDatePickerField.php";
require_once "Classes/WPCmsInputField.php";
require_once "Classes/WPCmsTextField.php";
require_once "Classes/WPCmsTextareaField.php";
require_once "Classes/WPCmsTinyMCEField.php";
require_once "Classes/WPCmsPasswordField.php";
require_once "Classes/WPCmsCheckboxField.php";
require_once "Classes/WPCmsSelectField.php";
require_once "Classes/WPCmsRadioField.php";
require_once "Classes/WPCmsListField.php";
require_once "Classes/WPCmsMultiListField.php";
require_once "Classes/WPCmsDynamicListField.php";

require_once "Classes/WPCmsGoogleFontsField.php";
require_once "Classes/WPCmsRelationField.php";
require_once "Classes/WPCmsModulesField.php";
require_once "Classes/WPCmsColorPicker.php";
require_once "Classes/WPCmsUploadField.php";
require_once "Classes/WPCmsImageField.php";
require_once "Classes/WPCmsImageProField.php";
require_once "Classes/WPCmsGoogleMapField.php";
require_once "Classes/WPCmsGalleryField.php";

require_once "Classes/WPCmsSeparatorField.php";

require_once "Classes/WPCmsPostType.php";
require_once "Classes/WPCmsSettingsPage.php";
require_once "Classes/WPCmsEditorPlugin.php";
require_once "Classes/WPCmsSEOReplacePostTypeWithTaxonomy.php";


// Hooks

function update_edit_form () {
  echo ' enctype="multipart/form-data"';
}
add_action('post_edit_form_tag', 'update_edit_form');