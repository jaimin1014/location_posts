<?php

class WP_LOCATION_POSTS {
    /**
     * Constructor.
     *
     * @since  1.0.0
     */
    public function __construct() {
        // Add action for adding the location field in posts
        add_action( 'add_meta_boxes', array( $this, 'location_posts_add_location_field' ) );

        // Add action to save the location field when a post is saved
        add_action( 'save_post', array( $this, 'location_posts_save_location' ) );

        // Register REST API endpoint to retrieve posts with location data
        add_action( 'rest_api_init', array( $this, 'register_location_posts_rest_endpoint' ) );

        // Shortcode to display the map
        add_shortcode( 'location_posts_map', array( $this, 'render_location_posts_map' ) );

        // Enqueue scripts and styles
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_leaflet_assets' ) );
    }

    /**
     * Enqueue Leaflet CSS and JS files
     *
     * @since 1.0.0
     */
    public function enqueue_leaflet_assets() {
    	global $post;

    	// Check if we are on a singular post/page and if the content contains the shortcode
    	if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'location_posts_map' ) ) {
	        // Leaflet CSS
	        wp_enqueue_style( 'leaflet-css', LOCATION_POSTS_URL .'/assets/css/leaflet.css' );

	        // Leaflet JS
	        wp_enqueue_script( 'leaflet-js', LOCATION_POSTS_URL .'/assets/js/leaflet.js', array(), null, true );

	        wp_enqueue_script( 'location-posts-script', LOCATION_POSTS_URL .'/assets/js/location-posts-map.js', array( 'leaflet-js' ), '1.0.0', true );

	        // Pass WordPress Base URL to JavaScript
		    wp_localize_script(
		        'location-posts-script', // Ensure the last loaded script is localized
		        'wpLocationPosts', 
		        array( 'baseUrl' => home_url() )
		    );
		}
    }

    /**
     * Add the Location field to the post editor.
     *
     * @since 1.0.0
     */
    public function location_posts_add_location_field() {
        add_meta_box(
            'location_field',  // ID of the meta box
            'Location',        // Title of the meta box
            array( $this, 'location_posts_location_field_callback' ), // Callback function to display the field
            'post',            // Show on posts only
            'side',            // Display on the right side
            'default'          // Default priority
        );
    }

    /**
     * Callback function to display the location field input.
     *
     * @param WP_Post $post The post object.
     * @since 1.0.0
     */
    public function location_posts_location_field_callback( $post ) {
        // Use nonce for verification
        wp_nonce_field( 'location_posts_save_location', 'location_posts_nonce' );

        // Get the current location value
        $location = get_post_meta( $post->ID, '_location', true );
        
        echo '<label for="location">Enter Location:</label>';
        echo '<input type="text" id="location" name="location" value="' . esc_attr( $location ) . '" class="widefat" />';
    }

    /**
     * Save the location field when the post is saved.
     *
     * @param int $post_id The post ID.
     * @since 1.0.0
     */
    public function location_posts_save_location( $post_id ) {
        // Check if nonce is set and valid
        if ( ! isset( $_POST['location_posts_nonce'] ) || ! wp_verify_nonce( $_POST['location_posts_nonce'], 'location_posts_save_location' ) ) {
            return $post_id;
        }

        // Check if it's an autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }

        // Check if the user has permission to edit the post
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }

        // If location is set, save the metadata
        if ( isset( $_POST['location'] ) ) {
            update_post_meta( $post_id, '_location', sanitize_text_field( $_POST['location'] ) );

            $cordinates = get_lat_long_from_address($_POST['location']);
            if ($cordinates) {
            	update_post_meta( $post_id, '_location_lat', sanitize_text_field( $cordinates['lat'] ));
            	update_post_meta( $post_id, '_location_long', sanitize_text_field( $cordinates['long'] ));
            }
        }
    }

    /**
     * Register a custom REST API endpoint to retrieve posts with location data.
     *
     * @since 1.0.0
     */
    public function register_location_posts_rest_endpoint() {
        register_rest_route( 'location/v1', '/posts', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_location_posts' ),
            'permission_callback' =>  '__return_true',
        ) );
    }

    /**
     * Callback function for the custom REST API endpoint to fetch posts with location.
     *
     * @since 1.0.0
     */
    public function get_location_posts() {
        // Query posts that have a location meta field
        $args = array(
            'post_type' => 'post',
            'meta_key' => '_location', // The custom field we are saving location in
            'posts_per_page' => -1, // Retrieve all posts
        );

        $query = new WP_Query( $args );

        $posts = array();

        while ( $query->have_posts() ) {
            $query->the_post();

            // Get the location meta field value
            $location = get_post_meta( get_the_ID(), '_location', true );
            $locationLat = get_post_meta( get_the_ID(), '_location_lat', true );
            $locationLong = get_post_meta( get_the_ID(), '_location_long', true );

            // Assuming location is stored as lat,long, otherwise you'd need to handle geocoding
            if ( $location ) {
                $post_data = array(
                    'title' => get_the_title(),
                    'url' => get_permalink(),
                    'location' => $location,
                    'location_lat' => $locationLat,
                    'location_long' => $locationLong,
                );
                $posts[] = $post_data;
            }
        }

        wp_reset_postdata();

        return $posts;
    }

    /**
     * Shortcode to render the location map.
     *
     * @since 1.0.0
     */
    public function render_location_posts_map() {
        ob_start();
        ?>
        <div id="map" style="height: 500px;"></div>
        <?php
        return ob_get_clean();
    }
}

// Initialize the class
$locationPostsObj = new WP_LOCATION_POSTS();
