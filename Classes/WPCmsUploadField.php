<?php

Class WPCmsUploadField Extends WPCmsField {

  public function addActionAdminEnqueueScripts ($hook)
  {
    wp_enqueue_style('thickbox');
    wp_enqueue_script('wpcms-upload', WPCMS_STYLESHEET_URI . '/assets/upload.js', array('jquery', 'media-upload', 'thickbox'));
  }

  public function renderInnerInput ($post, $data = array())
  { ?>

    <div class="file-wrapper"><?php if (!empty($data['value'])): ?><a href="<?php echo $data['value']; ?>"><?php echo $data['value']; ?></a><?php endif; ?></div>
    <input id="<?php echo $data['id']; ?>" class="upload-file-input" type="hidden" name="<?php echo $data['name']; ?>" value="<?php echo esc_attr($data['value']); ?>" />
    <input type="button" value="<?php _e('Insert File', 'wpcms'); ?>" class="button button-primary upload-file-button" />
    <input type="button" value="<?php _e('Delete File', 'wpcms'); ?>" class="button upload-file-delete"<?php if (!empty($data['value'])) echo ' style="display:none;"'; ?> />

<?php
  }
}


