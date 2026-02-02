<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once "../config/db.php";
require_once "../vendor/autoload.php";
require_once "../config/core.php";
use Firebase\JWT\JWT;

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Không thể kết nối đến cơ sở dữ liệu."]);
    exit();
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$data = json_decode(file_get_contents("php://input"));

if ($action == 'register' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!$data) {
        http_response_code(400);
        echo json_encode(["message" => "Dữ liệu gửi lên không hợp lệ hoặc rỗng."]);
        exit();
    }

    if(!empty($data->email) && !empty($data->password) && !empty($data->name)) {
        try {
            // Kiểm tra email tồn tại
            $checkQuery = "SELECT id FROM users WHERE email = :email LIMIT 1";
            $checkStmt = $db->prepare($checkQuery);
            $checkStmt->bindParam(":email", $data->email);
            $checkStmt->execute();
            if($checkStmt->rowCount() > 0){
                http_response_code(400);
                echo json_encode(["message" => "Email đã được sử dụng."]);
                exit();
            }

            $query = "INSERT INTO users SET name=:name, email=:email, password=:password, role=:role, active=1";
            $stmt = $db->prepare($query);
            
            $password_hash = password_hash($data->password, PASSWORD_BCRYPT);
            $role = !empty($data->role) ? $data->role : 'seeker';

            $stmt->bindParam(":name", $data->name);
            $stmt->bindParam(":email", $data->email);
            $stmt->bindParam(":password", $password_hash);
            $stmt->bindParam(":role", $role);

            if($stmt->execute()) {
                http_response_code(201);
                echo json_encode(["message" => "Đăng ký thành công."]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Không thể đăng ký người dùng."]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Lỗi hệ thống: " . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        $missing = [];
        if (empty($data->name)) $missing[] = "Họ tên";
        if (empty($data->email)) $missing[] = "Email";
        if (empty($data->password)) $missing[] = "Mật khẩu";
        echo json_encode(["message" => "Vui lòng nhập đầy đủ: " . implode(", ", $missing)]);
    }
} elseif ($action == 'login' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($data && !empty($data->email) && !empty($data->password)) {
        try {
            $query = "SELECT id, name, password, role FROM users WHERE email = :email LIMIT 0,1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":email", $data->email);
            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if(password_verify($data->password, $row['password'])) {
                    $key = JWT_SECRET_KEY;
                    $payload = [
                        "iss" => "webkiemthu",
                        "iat" => time(),
                        "data" => [
                            "id" => $row['id'],
                            "name" => $row['name'],
                            "role" => $row['role']
                        ]
                    ];
                    $jwt = JWT::encode($payload, $key, 'HS256');
                    echo json_encode(["token" => $jwt, "message" => "Đăng nhập thành công."]);
                } else {
                    http_response_code(401);
                    echo json_encode(["message" => "Mật khẩu không đúng."]);
                }
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Email không tồn tại."]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Lỗi hệ thống: " . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Vui lòng nhập email và mật khẩu."]);
    }
}
?>