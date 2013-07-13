<?php
/**
 * Zo2 Framework (http://zo2framework.org)
 *
 * @link         http://github.com/aploss/zo2
 * @package      Zo2
 * @author       Hiepvu
 * @copyright    Copyright ( c ) 2008 - 2013 APL Solutions
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
//no direct accees
defined('_JEXEC') or die ('resticted aceess');

if (!function_exists('googlemaps')) {
    /**
     * @param $atts
     * @param string $content
     * @return string
     */
    function googlemaps($atts, $content = "")
    {
        extract(shortcode_atts(array(
            'lat' => -34.397,
            'lng' => 150.644,
            'zoom' => 11,
            'w' => 100,
            'h' => 400
        ), $atts));

        if ( ! is_array( $atts ) ) {
            return '<!-- Googlemaps shortcode passed invalid attributes -->';
        }

        $w = ($w == '100%') ? $w : $w . 'px';

        Zo2Framework::addJsScript('https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false');

        $script = '

            var map;

            function initialize() {
              var myLatlng = new google.maps.LatLng(' . $lat . ', ' . $lng . ');
              var mapOptions = {
                zoom: ' . $zoom . ',
                center: myLatlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
              };
              map = new google.maps.Map(document.getElementById(\'map-canvas\'), mapOptions);
              var marker = new google.maps.Marker({
                            position: myLatlng,
                            title: "' . $content . '"
                           });
              marker.setMap(map);
            }

            google.maps.event.addDomListener(window, \'load\', initialize);

        ';
        Zo2Framework::addScriptDeclaration($script);

        return '<div id="map-canvas" style="width: ' . $w . '; height: ' . $h . 'px;"></div>';
    }

    add_shortcode('googlemaps', 'googlemaps');
}
