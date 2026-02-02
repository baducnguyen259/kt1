<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once "../config/core.php";

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->keyword) || empty($data->keyword)) {
    echo json_encode(["field" => "", "experience" => "", "keywords" => []]);
    exit();
}

$userKeyword = $data->keyword;

// Đã thay thế bằng API Key của bạn (Google Gemini API)
$apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . GEMINI_API_KEY;

$prompt = "Bạn là hệ thống phân tích từ khóa tìm kiếm việc làm.

Hãy phân tích câu tìm kiếm sau và trích xuất thông tin có cấu trúc.

Câu tìm kiếm:
\"" . $userKeyword . "\"

Yêu cầu:
- Hiểu ý định người tìm việc, kể cả khi từ khóa mơ hồ
- Không bịa thông tin
- Nếu không xác định được thì để chuỗi rỗng \"\"

Chỉ trả về JSON đúng format sau (KHÔNG giải thích thêm):

{
  \"field\": \"\",
  \"experience\": \"\",
  \"keywords\": []
}

Trong đó:
- field: lĩnh vực (IT, Marketing, Kinh doanh, Kế toán, ...)
- experience: Intern | Fresher | Junior | Senior
- keywords: danh sách từ khóa kỹ năng / vị trí liên quan";

$requestBody = [
    "contents" => [
        [
            "parts" => [
                ["text" => $prompt]
            ]
        ]
    ],
    "generationConfig" => [
        "response_mime_type" => "application/json"
    ]
];

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

if ($response) {
    $responseData = json_decode($response, true);
    $text = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
    
    // Loại bỏ markdown code block (```json ... ```) nếu Gemini trả về
    $text = preg_replace('/^```json\s*|\s*```$/', '', trim($text));
    
    echo $text;
} else {
    echo json_encode(["error" => "Không thể kết nối đến AI service"]);
}
?>