<?php
require_once 'api_config.php';
require_once 'api_security.php';

ApiSecurity::requirePost();
$data = ApiSecurity::getJsonPayload();

$key = $data['license_key'] ?? '';
$hwid = $data['hardware_id'] ?? '';

if (empty($key) || empty($hwid)) {
    ApiSecurity::jsonResponse('error', 'Missing credentials.', 400);
}

$db = Database::getInstance()->getConnection();

$stmt = $db->prepare("SELECT l.* FROM licenses l JOIN license_activations a ON l.id = a.license_id WHERE l.license_key = ? AND a.hardware_id = ? AND l.status = 'active'");
$stmt->execute([$key, $hwid]);
$valid = $stmt->fetch();

if ($valid) {
    // Update last ping
    $db->prepare("UPDATE license_activations SET last_ping = NOW() WHERE license_id = ? AND hardware_id = ?")->execute([$valid['id'], $hwid]);
    ApiSecurity::jsonResponse('success', 'Valid', 200, ['plan' => $valid['plan'], 'expires_at' => $valid['expires_at']]);
} else {
    ApiSecurity::jsonResponse('error', 'Invalid license or hardware mismatch.', 401);
}
?>