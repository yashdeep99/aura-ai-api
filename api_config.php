<?php
/**
 * Aura AI - API Configuration
 * Sets JSON headers and loads the core database connection.
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Aura-Hardware-ID');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../database/database.php';

// Turn off display errors in API responses to prevent JSON breakage
ini_set('display_errors', 0);
error_reporting(E_ALL);
?>