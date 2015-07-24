<?php

namespace WarehouseShipping;

class GeoLookup {

  /* Google Geocode API client code
   * Note: this function is roughly copied from the Internet.
   */
  public static function getLatLon($address){
    $ctx = stream_context_create( ['http'=> [ 'timeout' => 20 ] ] );
    $url = 'http://maps.google.com/maps/api/geocode/json?sensor=false&address=' . urlencode($address);
    $resp_json = file_get_contents($url, false, $ctx);
    $resp = json_decode($resp_json, true);

    if ( isset($resp['status']) && $resp['status'] == 'OK' ){
      $lati = $resp['results'][0]['geometry']['location']['lat'];
      $longi = $resp['results'][0]['geometry']['location']['lng'];
      $formatted_address = $resp['results'][0]['formatted_address'];
      if ( $lati && $longi && $formatted_address ){
        return [$lati, $longi, $formatted_address];
      }
    }

    throw new \Exception('GeoLookup failed for this address');
  }
}
