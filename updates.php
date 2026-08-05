<?php
require_once 'api_config.php';
require_once 'api_security.php';

ApiSecurity::requireGet();
$db = Database::getInstance()->getConnection();

$settingsData = $db->query("SELECT setting_key, setting_value FROM software_settings WHERE setting_key IN ('software_version', 'download_url_win', 'release_notes', 'mandatory_update')")->fetchAll();
$settings = [];
foreach($settingsData as $s) { $settings[$s['setting_key']] = $s['setting_value']; }

ApiSecurity::jsonResponse('success', 'Update info retrieved.', 200, [
    'latest_version' => $settings['software_version'] ?? '1.0.0',
    'download_url' => SITE_URL . '/' . ($settings['download_url_win'] ?? ''),
    'release_notes' => $settings['release_notes'] ?? 'General performance improvements and bug fixes.',
    'is_mandatory' => ($settings['mandatory_update'] ?? '0') === '1'
]);
?>