<?php
header("Content-Type: application/json; charset=UTF-8");
require_once "../config/db.php";
require_once "../middleware/auth.php";

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        // Lấy chi tiết công việc
        $query = "SELECT * FROM jobs WHERE id = :id LIMIT 0,1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $_GET['id']);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            echo json_encode($row);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Không tìm thấy công việc."]);
        }
    } elseif (isset($_GET['view']) && $_GET['view'] === 'employer') {
        // Lấy danh sách công việc của nhà tuyển dụng đã đăng
        $user = authenticate();
        $employer_id = $user->data->id;

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10; // Số tin mỗi trang cho trang quản lý
        $offset = ($page - 1) * $limit;

        // Đếm tổng số công việc của nhà tuyển dụng này
        $count_query = "SELECT COUNT(*) as total FROM jobs WHERE employer_id = :employer_id AND status != 'deleted'";
        $count_stmt = $db->prepare($count_query);
        $count_stmt->bindParam(":employer_id", $employer_id);
        $count_stmt->execute();
        $total_rows = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $total_pages = ceil($total_rows / $limit);

        // Lấy dữ liệu phân trang
        $query = "SELECT id, title, location, status, created_at FROM jobs WHERE employer_id = :employer_id AND status != 'deleted' ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":employer_id", $employer_id);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'jobs' => $jobs,
            'pagination' => [
                'page' => $page,
                'totalPages' => $total_pages,
                'totalJobs' => (int)$total_rows,
                'limit' => $limit,
                'hasNextPage' => $page < $total_pages,
                'hasPrevPage' => $page > 1
            ]
        ]);
    } else {
        // Tìm kiếm công việc với phân trang
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 6; // Số lượng công việc mỗi trang
        $offset = ($page - 1) * $limit;

        // --- Build query and params ---
        $where_clauses = ["status = 'open'"];
        $params = [];

        // Keyword search - tìm kiếm trong cả title và description
        if (!empty($_GET['keyword'])) {
            $where_clauses[] = "(title LIKE :keyword OR description LIKE :keyword)";
            $params[':keyword'] = '%' . $_GET['keyword'] . '%';
        }
        
        // Location search
        if (!empty($_GET['location'])) {
            $where_clauses[] = "location LIKE :location";
            $params[':location'] = '%' . $_GET['location'] . '%';
        }

        // Salary range search
        if (!empty($_GET['salary_range'])) {
            $ranges = explode(',', $_GET['salary_range']);
            $salary_clauses = [];
            
            foreach ($ranges as $range) {
                if ($range === 'under_10') {
                    // < 10 triệu (chỉ tính số)
                    $salary_clauses[] = "(salary REGEXP '^[0-9]+$' AND CAST(salary AS UNSIGNED) < 10000000)";
                } elseif ($range === '10_20') {
                    // 10 - 20 triệu
                    $salary_clauses[] = "(salary REGEXP '^[0-9]+$' AND CAST(salary AS UNSIGNED) >= 10000000 AND CAST(salary AS UNSIGNED) <= 20000000)";
                } elseif ($range === 'over_20') {
                    // > 20 triệu
                    $salary_clauses[] = "(salary REGEXP '^[0-9]+$' AND CAST(salary AS UNSIGNED) > 20000000)";
                } elseif ($range === 'agreement') {
                    // Thỏa thuận (không phải số hoặc null)
                    $salary_clauses[] = "(salary NOT REGEXP '^[0-9]+$' OR salary IS NULL OR salary = '')";
                }
            }
            
            if (!empty($salary_clauses)) {
                $where_clauses[] = "(" . implode(' OR ', $salary_clauses) . ")";
            }
        }

        // Helper function to build IN clauses for advanced filters
        function addInClause($param_name, $column_name, &$clauses, &$params) {
            if (!empty($_GET[$param_name])) {
                // Tách chuỗi thành mảng, loại bỏ các giá trị rỗng
                $values = array_filter(array_map('trim', explode(',', $_GET[$param_name])));
                if (empty($values)) return;

                $placeholders = [];
                foreach ($values as $i => $value) {
                    $key = ":{$column_name}_{$i}";
                    $placeholders[] = $key;
                    $params[$key] = $value;
                }
                $clauses[] = "{$column_name} IN (" . implode(',', $placeholders) . ")";
            }
        }

        // Áp dụng bộ lọc nâng cao
        addInClause('field', 'field', $where_clauses, $params);
        addInClause('experience', 'experience', $where_clauses, $params);
        addInClause('type', 'job_type', $where_clauses, $params);

        $where_sql = " WHERE " . implode(' AND ', $where_clauses);

        // Query đếm tổng số công việc
        $count_query = "SELECT COUNT(*) as total FROM jobs" . $where_sql;
        $count_stmt = $db->prepare($count_query);
        $count_stmt->execute($params);
        $total_rows = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $total_pages = ceil($total_rows / $limit);

        // Query lấy dữ liệu phân trang
        $query = "SELECT * FROM jobs" . $where_sql . " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $db->prepare($query);
        
        // Gán các tham số cho câu lệnh chính
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();

        $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Trả về kết quả với cấu trúc rõ ràng
        echo json_encode([
            'success' => true,
            'jobs' => $jobs,
            'pagination' => [
                'page' => $page,
                'totalPages' => $total_pages,
                'totalJobs' => (int)$total_rows,
                'limit' => $limit,
                'hasNextPage' => $page < $total_pages,
                'hasPrevPage' => $page > 1
            ]
        ]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Yêu cầu đăng nhập mới được đăng tin
    $user = authenticate();
    $employer_id = $user->data->id;

    $data = json_decode(file_get_contents("php://input"));

    if (
        $data &&
        !empty($data->title) && 
        !empty($data->company) && 
        !empty($data->location) && 
        !empty($data->salary)
    ) {
        $query = "INSERT INTO jobs SET title=:title, company=:company, location=:location, salary=:salary, description=:description, employer_id=:employer_id, status='open', created_at=NOW()";
        $stmt = $db->prepare($query);

        $stmt->bindParam(":title", $data->title);
        $stmt->bindParam(":company", $data->company);
        $stmt->bindParam(":location", $data->location);
        $stmt->bindParam(":salary", $data->salary);
        $stmt->bindParam(":description", $data->description);
        $stmt->bindParam(":employer_id", $employer_id);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "Đăng tin tuyển dụng thành công."]);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Không thể tạo tin tuyển dụng."]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Dữ liệu không đầy đủ."]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
    // Đóng/Mở tin tuyển dụng
    $user = authenticate();
    $employer_id = $user->data->id;

    $data = json_decode(file_get_contents("php://input"));
    
    if (!isset($_GET['id']) || !isset($data->status)) {
        http_response_code(400);
        echo json_encode(["message" => "Thiếu ID công việc hoặc trạng thái mới."]);
        exit();
    }

    $job_id = $_GET['id'];
    $new_status = $data->status;

    // Kiểm tra xem nhà tuyển dụng có sở hữu công việc này không
    $check_query = "SELECT id FROM jobs WHERE id = :id AND employer_id = :employer_id";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':id', $job_id);
    $check_stmt->bindParam(':employer_id', $employer_id);
    $check_stmt->execute();

    if ($check_stmt->rowCount() == 0) {
        http_response_code(403); // Forbidden
        echo json_encode(["message" => "Bạn không có quyền thay đổi công việc này."]);
        exit();
    }

    // Cập nhật trạng thái
    $update_query = "UPDATE jobs SET status = :status WHERE id = :id";
    $update_stmt = $db->prepare($update_query);
    $update_stmt->bindParam(':status', $new_status);
    $update_stmt->bindParam(':id', $job_id);

    if ($update_stmt->execute()) {
        http_response_code(200);
        echo json_encode(["message" => "Cập nhật trạng thái thành công."]);
    } else {
        http_response_code(503);
        echo json_encode(["message" => "Không thể cập nhật trạng thái."]);
    }

} elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Sửa tin tuyển dụng
    $user = authenticate();
    $employer_id = $user->data->id;

    $data = json_decode(file_get_contents("php://input"));
    $job_id = isset($_GET['id']) ? $_GET['id'] : null;

    if (!$job_id || !$data || empty($data->title) || empty($data->location)) {
        http_response_code(400);
        echo json_encode(["message" => "Dữ liệu không hợp lệ hoặc thiếu ID công việc."]);
        exit();
    }

    $query = "UPDATE jobs SET title=:title, company=:company, location=:location, salary=:salary, description=:description WHERE id=:id AND employer_id=:employer_id";
    $stmt = $db->prepare($query);

    $stmt->bindParam(":title", $data->title);
    $stmt->bindParam(":company", $data->company);
    $stmt->bindParam(":location", $data->location);
    $stmt->bindParam(":salary", $data->salary);
    $stmt->bindParam(":description", $data->description);
    $stmt->bindParam(":id", $job_id);
    $stmt->bindParam(":employer_id", $employer_id);

    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(["message" => "Cập nhật tin tuyển dụng thành công."]);
        } else {
            http_response_code(403); // Không tìm thấy hoặc không có quyền
            echo json_encode(["message" => "Không tìm thấy công việc hoặc bạn không có quyền sửa."]);
        }
    } else {
        http_response_code(503);
        echo json_encode(["message" => "Không thể cập nhật tin tuyển dụng."]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    // Xóa mềm tin tuyển dụng (chuyển status thành 'deleted')
    $user = authenticate();
    $employer_id = $user->data->id;

    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(["message" => "Thiếu ID công việc."]);
        exit();
    }

    $job_id = $_GET['id'];

    // Kiểm tra quyền sở hữu
    $check_query = "SELECT id FROM jobs WHERE id = :id AND employer_id = :employer_id";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':id', $job_id);
    $check_stmt->bindParam(':employer_id', $employer_id);
    $check_stmt->execute();

    if ($check_stmt->rowCount() == 0) {
        http_response_code(403);
        echo json_encode(["message" => "Bạn không có quyền xóa công việc này."]);
        exit();
    }

    $query = "UPDATE jobs SET status = 'deleted' WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $job_id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Đã xóa tin tuyển dụng thành công."]);
    } else {
        http_response_code(503);
        echo json_encode(["message" => "Không thể xóa tin tuyển dụng."]);
    }
}
?>