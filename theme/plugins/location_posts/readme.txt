Location Wise Posts Plugin

Description: 
The Location Wise Posts Plugin allows you to add location metadata to your WordPress posts, display markers on a map, and retrieve posts with location information via a custom REST API endpoint. It also provides an easy-to-use shortcode to embed an interactive map on your website.

Features

- Add location metadata (latitude, longitude) to posts.
- Display posts with their associated locations on an interactive map.
- Custom REST API to fetch posts with location data.
- Enqueue Leaflet.js assets for map rendering.
- Supports automatic geocoding of addresses to latitude and longitude.


Installation

1. Download and Install Plugin
	- Download the Location Posts Plugin ZIP file.
	- Upload the ZIP file through your WordPress Admin Dashboard under Plugins > Add New > Upload Plugin.
	- Activate the plugin.
2. Activate the Plugin
	- Upon activation, the plugin will:
	- Register necessary rewrite rules.
	- Add custom post metadata fields for location.

3. Use the Plugin
	Once activated, the plugin will:
	- Add a "Location" field in the post editor where you can enter the location address.
	- Automatically geocode the address into latitude and longitude and store it as post metadata.
	- Allow you to use the [location_posts_map] shortcode to display a map with markers representing posts.
	- Place the shortcode on any page or post: [location_posts_map]