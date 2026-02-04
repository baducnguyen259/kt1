<?php
header("Content-Type: application/json; charset=UTF-8");
require_once "../config/db.php";
require_once "../middleware/auth.php";

$database = new Database();
$db = $database->getConnection();

$user = authenticate();
$employer_id = $user->data->id;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Lấy danh sách ứng viên cho một công việc cụ thể
    if (!isset($_GET['job_id'])) {
        http_response_code(400);
        echo json_encode(["message" => "Thiếu ID công việc."]);
        exit();
    }

    $job_id = $_GET['job_id'];

    // 1. Kiểm tra quyền: Nhà tuyển dụng có sở hữu công việc này không?
    $check_query = "SELECT title FROM jobs WHERE id = :id AND employer_id = :employer_id";
    $stmt = $db->prepare($check_query);
    $stmt->bindParam(":id", $job_id);
    $stmt->bindParam(":employer_id", $employer_id);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        http_response_code(403);
        echo json_encode(["message" => "Bạn không có quyền xem ứng viên của công việc này."]);
        exit();
    }
    
    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Lấy danh sách ứng viên
    // Bảng `applications` dùng `seeker_id` để liên kết với bảng `users`
    $query = "SELECT a.id, a.seeker_id, a.cv_text, a.status, a.created_at, 
                     u.name as candidate_name, u.email as candidate_email 
              FROM applications a 
              JOIN users u ON a.seeker_id = u.id 
              WHERE a.job_id = :job_id 
              ORDER BY a.created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":job_id", $job_id);
    $stmt->execute();
    $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "job_title" => $job['title'],
        "candidates" => $candidates
    ]);

} elseif ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
    // Cập nhật trạng thái hồ sơ (VD: Mời phỏng vấn, Từ chối)
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($_GET['id']) || !isset($data->status)) {
        http_response_code(400);
        echo json_encode(["message" => "Thiếu ID hồ sơ hoặc trạng thái mới."]);
        exit();
    }

    $app_id = $_GET['id'];
    $new_status = $data->status;

    // Kiểm tra quyền: Hồ sơ này có thuộc về công việc do NTD này đăng không?
    $check_query = "SELECT a.id 
                    FROM applications a 
                    JOIN jobs j ON a.job_id = j.id 
                    WHERE a.id = :app_id AND j.employer_id = :employer_id";
    
    $stmt = $db->prepare($check_query);
    $stmt->bindParam(":app_id", $app_id);
    $stmt->bindParam(":employer_id", $employer_id);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        http_response_code(403);
        echo json_encode(["message" => "Bạn không có quyền cập nhật hồ sơ này."]);
        exit();
    }

    $update_query = "UPDATE applications SET status = :status WHERE id = :id";
    $stmt = $db->prepare($update_query);
    $stmt->bindParam(":status", $new_status);
    $stmt->bindParam(":id", $app_id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Đã cập nhật trạng thái ứng viên."]);
    } else {
        http_response_code(503);
        echo json_encode(["message" => "Lỗi hệ thống, không thể cập nhật."]);
    }
}
?>