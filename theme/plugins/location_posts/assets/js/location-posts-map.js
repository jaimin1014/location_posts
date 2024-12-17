 document.addEventListener("DOMContentLoaded", function () {
    var map = L.map('map').setView([0, 0], 2); // Default view

    // Add tile layer (OpenStreetMap tiles)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Create an empty bounds object to track all marker positions
    var bounds = L.latLngBounds();

    // Fetch posts with locations from the custom REST API endpoint
    fetch(`${wpLocationPosts.baseUrl}/wp-json/location/v1/posts`)
        .then(response => response.json())
        .then(posts => {
            posts.forEach(post => {
                var lat = parseFloat(post.location_lat);
                var long = parseFloat(post.location_long);

                if (lat && long) {
                    // Add marker to the map
                    var marker = L.marker([lat, long])
                        .addTo(map)
                        .bindPopup(`<a href="${post.url}">${post.title}</a>`);

                    // Extend bounds to include this marker
                    bounds.extend([lat, long]);
                }
            });
            // Automatically center and zoom the map to fit all markers
            if (bounds.length) {
                map.fitBounds(bounds);
            }
        });
});