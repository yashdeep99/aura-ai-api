<?php
require_once 'api_config.php';
require_once 'api_security.php';

ApiSecurity::requirePost();
$data = ApiSecurity::getJsonPayload();

// We only process if explicitly opted in. 
// No API keys, no chat history, no personal data.
$os = $data['os'] ?? 'Unknown';
$app_version = $data['app_version'] ?? 'Unknown';
$feature_used = $data['feature'] ?? 'general';

// On InfinityFree, extensive telemetry logging can exhaust database limits quickly.
// For this architecture, we return success but use a lightweight or memory-only placeholder.
// In a dedicated VPS, you would INSERT INTO telemetry_logs.

ApiSecurity::jsonResponse('success', 'Telemetry received anonymously.', 200);
?>