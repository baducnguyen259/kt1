<?php
require_once(__DIR__ . "/../vendor/autoload.php");
require_once(__DIR__ . "/../config/core.php");
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function authenticate($allowed_roles = []) {
    $headers = apache_request_headers();
    $authHeader = $headers['Authorization'] ?? '';
    
    if (!$authHeader) {
        http_response_code(401);
        echo json_encode(["message" => "Unauthorized"]);
        exit;
    }

    $token = str_replace('Bearer ', '', $authHeader);

    try {
        $decoded = JWT::decode($token, new Key(JWT_SECRET_KEY, 'HS256'));
        if (!empty($allowed_roles) && !in_array($decoded->role, $allowed_roles)) {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden"]);
            exit;
        }
        return $decoded;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["message" => "Invalid token"]);
        exit;
    }
}