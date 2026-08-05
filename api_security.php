<?php
/**
 * Aura AI - API Security Middleware
 * Protects endpoints from abuse and invalid payloads.
 */
class ApiSecurity {
    public static function requirePost() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::jsonResponse('error', 'Method not allowed. POST required.', 405);
        }
    }

    public static function requireGet() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            self::jsonResponse('error', 'Method not allowed. GET required.', 405);
        }
    }

    public static function getJsonPayload() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            self::jsonResponse('error', 'Invalid JSON payload.', 400);
        }
        return $data;
    }

    public static function jsonResponse($status, $message, $code = 200, $data = []) {
        http_response_code($code);
        echo json_encode(array_merge([
            'status' => $status,
            'message' => $message
        ], $data));
        exit;
    }
}
?>