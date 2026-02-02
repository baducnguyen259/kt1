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
    } else {
        // Tìm kiếm công việc
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 6; // Số lượng công việc mỗi trang
        $offset = ($page - 1) * $limit;

        // --- Build query and params ---
        $where_clauses = ["status = 'open'"];
        $params = [];

        // Keyword and Location
        if (!empty($_GET['keyword'])) {
            $where_clauses[] = "title LIKE :keyword";
            $params[':keyword'] = '%' . $_GET['keyword'] . '%';
        }
        if (!empty($_GET['location'])) {
            $where_clauses[] = "location LIKE :location";
            $params[':location'] = '%' . $_GET['location'] . '%';
        }

        // Helper function to build IN clauses for advanced filters
        function addInClause($param_name, $column_name, &$clauses, &$params) {
            if (!empty($_GET[$param_name])) {
                // Tách chuỗi thành mảng, loại bỏ các giá trị rỗng
                $values = array_filter(explode(',', $_GET[$param_name]), 'trim');
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

        // Áp dụng bộ lọc nâng cao (giả sử tên cột là field, experience, job_type)
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
        echo json_encode([
            'jobs' => $jobs,
            'pagination' => ['page' => $page, 'totalPages' => $total_pages, 'totalJobs' => (int)$total_rows]
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
}
?>