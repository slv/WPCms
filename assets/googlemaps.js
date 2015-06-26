if (typeof _WPCmsGlobalInit == "undefined") _WPCmsGlobalInit = {};

_WPCmsGlobalInit.GoogleMaps = function ($, openModule) {

    $('.wpcms-google-map-field').each(function (k, cont) {
      $(cont).mousedown(function (e) {
        e.stopPropagation();
      });
      var fieldInput = $(cont).find('.wpcms-map-input').first();
      var mapOptions = {
        zoom: 9,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        center: new google.maps.LatLng(53.000,0.0005)
      }
      var map = new google.maps.Map($(cont).find('.wpcms-map-map')[0], mapOptions);
      var marker;

      var createMap = function() {
        var address = $(cont).find('.wpcms-map-address').val();
        var geocoder = new google.maps.Geocoder();
        geocoder.geocode( {address: address}, function (results,status) {
          if (status == google.maps.GeocoderStatus.OK) {
            console.log(results[0].geometry.location);
            map.setCenter(results[0].geometry.location);
            if (marker) {
              marker.setPosition(results[0].geometry.location);
            } else {
              marker = new google.maps.Marker({position: results[0].geometry.location, map: map, draggable: true});
            }
            fieldInput.val(marker.position.lat() + ',' + marker.position.lng());
          } else {

          }
        });
      }

      if (fieldInput.val() != '') {
        map.setCenter(new google.maps.LatLng(fieldInput.val().split(',')[0], fieldInput.val().split(',')[1]));
        marker = new google.maps.Marker({
          position: new google.maps.LatLng(fieldInput.val().split(',')[0], fieldInput.val().split(',')[1]),
          map: map,
          draggable: true
        });
        google.maps.event.addListener(marker, 'dragend', function () {
          fieldInput.val(marker.position.lat() + ',' + marker.position.lng());
        });
      }

      google.maps.event.addListener(map, 'click', function(event) {
        if (marker) {
          marker.setPosition(event.latLng);
        }
        else {
          marker = new google.maps.Marker({
            position: event.latLng,
            map: map,
            draggable: true
          });
          google.maps.event.addListener(marker, 'dragend', function () {
            fieldInput.val(marker.position.lat() + ',' + marker.position.lng());
          });
        }
        fieldInput.val(event.latLng.lat() + ',' + event.latLng.lng());
      });

      $(cont).find('.wpcms-map-submit').unbind('click').click(createMap);

    });
};

jQuery(document).ready(_WPCmsGlobalInit.GoogleMaps);
