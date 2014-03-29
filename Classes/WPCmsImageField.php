<?php

Class WPCmsImageField Extends WPCmsField {

  public function addActionAdminEnqueueScripts ($hook)
  {
    if (function_exists('wp_enqueue_media')) { wp_enqueue_media(); }
    wp_enqueue_script('wpcms-image', WPCMS_STYLESHEET_URI . '/WPCms/assets/image.js', array('jquery-ui-core'));
    wp_enqueue_style('wpcms-image', WPCMS_STYLESHEET_URI . '/WPCms/assets/image.css');
  }

  public function renderInnerInput ($post, $data = array())
  {
    ?>

    <div class="image-wrapper"><?php if ($data['value'] != ''): $images = explode(',', $data['value']); foreach ($images as $image): ?>
      <div class="image-sort-item" id="image-sort-<?php echo $image; ?>">
        <?php echo wp_get_attachment_image($image, $size = 'thumbnail'); ?>
      </div>
    <?php endforeach; endif; ?></div>
    <input id="<?php echo $data['id']; ?>" class="image-input" type="hidden" name="<?php echo $data['name']; ?>" value="<?php echo esc_attr($data['value']); ?>" />
    <input type="button" value="<?php _e('Select Image', WPCmsStatus::getStatus()->getData('textdomain')); ?>" class="btn btn-default btn-xs image-button" />
    <input type="button" value="<?php _e('Remove Image', WPCmsStatus::getStatus()->getData('textdomain')); ?>" class="btn btn-danger btn-xs image-delete"<?php if ($data['value'] == '') echo ' style="display:none;"'; ?> />

    <?php
  }
}


