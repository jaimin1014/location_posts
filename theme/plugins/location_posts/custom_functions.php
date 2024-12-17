<?php

/**
 * Geocode an address and get latitude and longitude using Nominatim API.
 *
 * @param string $address The address to geocode.
 * @return array|false Returns an array with 'lat' and 'lon' keys or false on failure.
 */
function get_lat_long_from_address( $address ) {
    if ( empty( $address ) ) {
        return false; // Ensure address is provided
    }

    // Nominatim API URL for geocoding
    $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode( $address );

    // Set up arguments for the HTTP request
    $args = array(
        'timeout' => 10, // Set a reasonable timeout
        'headers' => array(
            'User-Agent' => 'LocationPosts/1.0 (jaimin@example.com)' // Required User-Agent
        ),
        'sslverify' => true, // Verify SSL certificate
    );

    // Perform the HTTP GET request
    $response = wp_remote_get( $url, $args );

    // Check for errors
    if ( is_wp_error( $response ) ) {
        return false; // Return false if the request failed
    }

    // Get and decode the body of the response
    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );

    // Validate the response data
    if ( is_array( $data ) && ! empty( $data[0]['lat'] ) && ! empty( $data[0]['lon'] ) ) {
        return array(
            'lat'  => floatval( $data[0]['lat'] ),
            'long' => floatval( $data[0]['lon'] ),
        );
    }

    // Return false if geocoding failed
    return false;
}
