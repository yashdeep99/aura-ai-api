<?php
require_once 'api_config.php';
require_once 'api_security.php';

ApiSecurity::requireGet();

ApiSecurity::jsonResponse('success', 'Aura AI API is online and operational.', 200, [
    'version' => SITE_VERSION,
    'timestamp' => time()
]);
?>