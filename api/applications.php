<?php
header("Content-Type: application/json; charset=UTF-8");
require_once "../config/db.php";
require_once "../vendor/autoload.php";
require_once "../config/core.php";
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$database = new Database();
$db = $database->getConnection();

// Lấy token từ header
$headers = apache_request_headers();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
if (!$authHeader && isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
}
$token = str_replace('Bearer ', '', $authHeader);

if(!$token) {
    http_response_code(401);
    echo json_encode(["message" => "Bạn cần đăng nhập để thực hiện thao tác này."]);
    exit();
}

try {
    $key = JWT_SECRET_KEY;
    $decoded = JWT::decode($token, new Key($key, 'HS256'));
    $user_id = $decoded->data->id;
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["message" => "Phiên đăng nhập hết hạn hoặc không hợp lệ."]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ứng tuyển
    $data = json_decode(file_get_contents("php://input"));
    
    if(!empty($data->job_id) && !empty($data->cv_text)) {
        $query = "INSERT INTO applications SET job_id=:job_id, seeker_id=:seeker_id, cv_text=:cv_text, status='submitted'";
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(":job_id", $data->job_id);
        $stmt->bindParam(":seeker_id", $user_id);
        $stmt->bindParam(":cv_text", $data->cv_text);

        if($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "Ứng tuyển thành công."]);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Lỗi hệ thống, vui lòng thử lại sau."]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Thiếu thông tin ứng tuyển."]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Lấy danh sách đã ứng tuyển
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 6; // Hiển thị 6 hồ sơ mỗi trang
    $offset = ($page - 1) * $limit;

    // Query đếm tổng số hồ sơ
    $count_query = "SELECT COUNT(*) as total FROM applications WHERE seeker_id = :seeker_id";
    $count_stmt = $db->prepare($count_query);
    $count_stmt->bindParam(":seeker_id", $user_id);
    $count_stmt->execute();
    $total_rows = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_pages = ceil($total_rows / $limit);

    // Query lấy dữ liệu phân trang
    $query = "SELECT a.*, j.title, j.company FROM applications a LEFT JOIN jobs j ON a.job_id = j.id WHERE a.seeker_id = :seeker_id ORDER BY a.created_at DESC LIMIT :limit OFFSET :offset";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":seeker_id", $user_id);
    $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
    $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();
    $apps = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'applications' => $apps,
        'pagination' => [
            'page' => $page,
            'totalPages' => $total_pages,
            'totalApplications' => (int)$total_rows
        ]
    ]);
}
?>