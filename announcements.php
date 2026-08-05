<?php
require_once 'api_config.php';
require_once 'api_security.php';

ApiSecurity::requireGet();
$db = Database::getInstance()->getConnection();

$stmt = $db->query("SELECT title, content, created_at FROM announcements WHERE status = 'published' ORDER BY created_at DESC LIMIT 5");
$announcements = $stmt->fetchAll();

ApiSecurity::jsonResponse('success', 'Announcements retrieved.', 200, ['announcements' => $announcements]);
?>