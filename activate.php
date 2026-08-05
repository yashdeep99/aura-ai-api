<?php
require_once 'api_config.php';
require_once 'api_security.php';

ApiSecurity::requirePost();
$data = ApiSecurity::getJsonPayload();

$key = $data['license_key'] ?? '';
$hwid = $data['hardware_id'] ?? '';
$os_version = $data['os_version'] ?? 'Unknown';

if (empty($key) || empty($hwid)) {
    ApiSecurity::jsonResponse('error', 'License key and Hardware ID are required.', 400);
}

$db = Database::getInstance()->getConnection();

// 1. Verify License exists and is active
$stmt = $db->prepare("SELECT * FROM licenses WHERE license_key = ? AND status = 'active'");
$stmt->execute([$key]);
$license = $stmt->fetch();

if (!$license) {
    ApiSecurity::jsonResponse('error', 'Invalid, revoked, or suspended license.', 401);
}

// 2. Check Expiry
if ($license['expires_at'] && strtotime($license['expires_at']) < time()) {
    $db->prepare("UPDATE licenses SET status = 'expired' WHERE id = ?")->execute([$license['id']]);
    ApiSecurity::jsonResponse('error', 'This license has expired.', 403);
}

// 3. Check Hardware Bindings
$stmt = $db->prepare("SELECT * FROM license_activations WHERE license_id = ? AND hardware_id = ?");
$stmt->execute([$license['id'], $hwid]);
$existing = $stmt->fetch();

if ($existing) {
    // Already bound to this device, update ping
    $db->prepare("UPDATE license_activations SET last_ping = NOW() WHERE id = ?")->execute([$existing['id']]);
    ApiSecurity::jsonResponse('success', 'License verified.', 200, ['plan' => $license['plan']]);
}

// 4. Check Limits for New Device
if ($license['activation_count'] >= $license['max_devices']) {
    ApiSecurity::jsonResponse('error', 'Maximum device limit reached for this license.', 403);
}

// 5. Activate New Device
$db->prepare("INSERT INTO license_activations (license_id, hardware_id, ip_address) VALUES (?, ?, ?)")
   ->execute([$license['id'], $hwid, $_SERVER['REMOTE_ADDR']]);
   
$db->prepare("UPDATE licenses SET activation_count = activation_count + 1, last_activated = NOW() WHERE id = ?")
   ->execute([$license['id']]);

ApiSecurity::jsonResponse('success', 'License activated successfully.', 200, ['plan' => $license['plan']]);
?>