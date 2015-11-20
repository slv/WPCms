<?php

Class WPCmsGalleryField Extends WPCmsField {

  public function addActionAdminEnqueueScripts ($hook)
  {
    if (function_exists('wp_enqueue_media')) { wp_enqueue_media(); }
    wp_enqueue_script('wpcms-gallery', WPCMS_STYLESHEET_URI . '/assets/gallery.js', array('jquery-ui-core'));
    wp_enqueue_style('wpcms-gallery', WPCMS_STYLESHEET_URI . '/assets/gallery.css');
  }

  public function renderSettingInput () {

    echo '<div class="col-sm-9">';
    $this->renderInnerInput(null, array(
      'id' => $this->id,
      'name' => $this->id,
      'value' => $this->settingValue()
    ));
    echo '</div>';
  }

  public function renderInnerInput ($post, $data = array())
  {
    ?>

    <div class="gallery-sortable">
      <?php if (!empty($data['value'])): $images = explode(',', $data['value']); foreach ($images as $image): ?>
      <div class="gallery-sort-item" id="gallery-sort-<?php echo $image; ?>">
        <?php echo wp_get_attachment_image($image, $size = 'thumbnail'); ?>
      </div>
      <?php endforeach; endif; ?>
    </div>
    <input id="<?php echo $data['id']; ?>" class="gallery-input" type="hidden" name="<?php echo $data['name']; ?>" value="<?php echo esc_attr($data['value']); ?>" />
    <input type="button" value="<?php _e('Edit Gallery', 'wpcms'); ?>" class="button gallery-button" />
    <input type="button" value="<?php _e('Delete Gallery', 'wpcms'); ?>" class="button gallery-delete"<?php if (!empty($data['value'])) echo ' style="display:none;"'; ?> />

    <?php
  }
}


