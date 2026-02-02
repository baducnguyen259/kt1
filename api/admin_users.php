<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../middleware/admin.php");

header("Content-Type: application/json");
$db = (new Database())->getConnection();
$method = $_SERVER["REQUEST_METHOD"];

adminOnly();

if ($method === "GET") {
    $stmt = $db->query("SELECT id, email, name, role, active, created_at FROM users ORDER BY created_at DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if ($method === "PUT") {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $db->prepare("UPDATE users SET active=? WHERE id=?");
    $stmt->execute([(int)$data['active'], (int)$data['id']]);
    echo json_encode(["message" => "Cập nhật trạng thái người dùng thành công"]);
    exit;
}

http_response_code(405);
echo json_encode(["message" => "Method not allowed"]);