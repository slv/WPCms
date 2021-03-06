<?php

Class WPCmsGoogleMapField Extends WPCmsField {

  public function addActionAdminEnqueueScripts ($hook)
  {
    wp_enqueue_script('gmap', 'http://maps.google.com/maps/api/js?sensor=false');
    wp_enqueue_script('wpcms-googlemaps', WPCMS_STYLESHEET_URI . '/assets/googlemaps.js');
  }

  public function renderInnerInput ($post, $data = array())
  {
    $mapId = 'gmap-' . $data['id'];
    echo '<div class="gmap">';
    echo '<input class="wpcms-map-input" type="hidden" name="', $data['name'], '" id="', $data['id'], '" value="', esc_attr($data['value']), '" size="30" />';
    echo '<div class="wpcms-map-map" style="width:400px;height:300px;border:8px solid #eeeeee;border-radius:8px;" id="' . $mapId . '"></div>';
    echo '</div>';
    echo '<input class="wpcms-map-address" id="' . $mapId . '-address" type="textbox" value="" />';
    echo '<input class="wpcms-map-submit" id="' . $mapId . '-submit" type="button" value="GO" />';
  }

}

