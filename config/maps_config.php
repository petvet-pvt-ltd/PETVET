<?php
/**
 * Maps API Configuration
 * Google Maps API for distance calculations, routing, and geocoding
 * Free tier: $200 credit/month (~40,000 Distance Matrix requests)
 */

// Google Maps API Key
// Get your free API key from: https://console.cloud.google.com/google/maps-apis/
define('GOOGLE_MAPS_API_KEY', 'YOUR_GOOGLE_MAPS_API_KEY_HERE');

// Google Maps API Endpoints
define('GOOGLE_DISTANCE_MATRIX_URL', 'https://maps.googleapis.com/maps/api/distancematrix/json');
define('GOOGLE_GEOCODE_URL', 'https://maps.googleapis.com/maps/api/geocode/json');
define('GOOGLE_DIRECTIONS_URL', 'https://maps.googleapis.com/maps/api/directions/json');
define('GOOGLE_PLACES_URL', 'https://maps.googleapis.com/maps/api/place/findplacefromtext/json');

// Backup: OpenRouteService API (if Google fails)
define('OPENROUTE_API_KEY', 'eyJvcmciOiI1YjNjZTM1OTc4NTExMTAwMDFjZjYyNDgiLCJpZCI6ImY0ZjczOWY2MmUwYTRkMDBhZTUwYzM2YWE1MDk3NzFiIiwiaCI6Im11cm11cjY0In0=');
define('OPENROUTE_BASE_URL', 'https://api.openrouteservice.org');
define('OPENROUTE_DIRECTIONS_URL', 'https://api.openrouteservice.org/v2/directions/driving-car');
define('OPENROUTE_REVERSE_GEOCODE_URL', 'https://api.openrouteservice.org/geocode/reverse');

// Distance calculation settings
define('DISTANCE_UNIT', 'km'); // km or miles
define('MAX_DISTANCE_DISPLAY', 100); // Maximum distance to show in km

// Rate limits
define('GOOGLE_RATE_LIMIT_MONTHLY', 40000); // Free tier requests

?>
