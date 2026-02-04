<?php
/**
 * API Phân tích từ khóa tìm kiếm việc làm bằng Google Gemini AI
 * Version: 2.0
 * Cải tiến: Security, Error Handling, Performance, Caching
 */

// Headers
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Require config
require_once "../config/core.php";

/**
 * Send JSON response and exit
 */
function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

/**
 * Log errors (nếu cần debug)
 */
function logError($message) {
    $logFile = __DIR__ . '/../logs/analyze_errors.log';
    $timestamp = date('Y-m-d H:i:s');
    if (is_writable(dirname($logFile))) {
        error_log("[$timestamp] $message\n", 3, $logFile);
    }
}

/**
 * Sanitize input keyword
 */
function sanitizeKeyword($keyword) {
    // Remove excessive whitespace
    $keyword = preg_replace('/\s+/', ' ', $keyword);
    // Trim
    $keyword = trim($keyword);
    // Remove dangerous characters but keep Vietnamese
    $keyword = strip_tags($keyword);
    return $keyword;
}

/**
 * Simple cache system
 */
function getCachedResult($keyword) {
    $cacheDir = __DIR__ . '/../cache/analyze/';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0755, true);
    }
    
    $cacheFile = $cacheDir . md5($keyword) . '.json';
    
    // Check if cache exists and is less than 24 hours old
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 86400) {
        $cached = file_get_contents($cacheFile);
        return json_decode($cached, true);
    }
    
    return null;
}

function setCachedResult($keyword, $result) {
    $cacheDir = __DIR__ . '/../cache/analyze/';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0755, true);
    }
    
    $cacheFile = $cacheDir . md5($keyword) . '.json';
    @file_put_contents($cacheFile, json_encode($result));
}

// ============================================================================
// MAIN LOGIC
// ============================================================================

// 1. Check HTTP method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse([
        'success' => false,
        'error' => 'Method not allowed. Use POST.'
    ], 405);
}

// 2. Get and validate input
$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput);

// Check JSON parse error
if (json_last_error() !== JSON_ERROR_NONE) {
    sendResponse([
        'success' => false,
        'error' => 'Invalid JSON input'
    ], 400);
}

// Check if keyword exists
if (!isset($data->keyword) || empty(trim($data->keyword))) {
    sendResponse([
        'success' => true,
        'data' => [
            'field' => '',
            'experience' => '',
            'keywords' => []
        ],
        'cached' => false,
        'message' => 'No keyword provided'
    ]);
}

// 3. Sanitize input
$userKeyword = sanitizeKeyword($data->keyword);

// Validate keyword length
if (strlen($userKeyword) < 2) {
    sendResponse([
        'success' => true,
        'data' => [
            'field' => '',
            'experience' => '',
            'keywords' => []
        ],
        'message' => 'Keyword too short'
    ]);
}

if (strlen($userKeyword) > 200) {
    sendResponse([
        'success' => false,
        'error' => 'Keyword too long (max 200 characters)'
    ], 400);
}

// 4. Check cache first
$cachedResult = getCachedResult($userKeyword);
if ($cachedResult !== null) {
    sendResponse([
        'success' => true,
        'data' => $cachedResult,
        'cached' => true,
        'message' => 'Result from cache'
    ]);
}

// 5. Check if Gemini API key is configured
if (!defined('GEMINI_API_KEY') || empty(GEMINI_API_KEY)) {
    logError('GEMINI_API_KEY not configured');
    sendResponse([
        'success' => false,
        'error' => 'AI service not configured'
    ], 500);
}

// 6. Build Gemini API request
$apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . GEMINI_API_KEY;

$prompt = "Bạn là hệ thống phân tích từ khóa tìm kiếm việc làm tại Việt Nam.

Phân tích câu tìm kiếm sau và trích xuất thông tin có cấu trúc:

Từ khóa: \"" . $userKeyword . "\"

Yêu cầu:
- Hiểu ý định người tìm việc, kể cả khi từ khóa mơ hồ
- Xác định lĩnh vực nghề nghiệp (IT, Marketing, Kế toán, Nhân sự, Kinh doanh, Giáo dục, Y tế, Xây dựng, v.v.)
- Xác định cấp độ kinh nghiệm nếu có (Intern, Fresher, Junior, Mid-level, Senior, Manager)
- Trích xuất các từ khóa kỹ năng hoặc vị trí liên quan
- Nếu không xác định được thì để chuỗi rỗng \"\"
- KHÔNG bịa thông tin

Trả về CHÍNH XÁC định dạng JSON sau (KHÔNG có giải thích, KHÔNG có markdown):

{
  \"field\": \"\",
  \"experience\": \"\",
  \"keywords\": []
}

Ví dụ:
- Input: \"tuyển lập trình viên python\" → {\"field\": \"IT\", \"experience\": \"\", \"keywords\": [\"Python\", \"Lập trình viên\", \"Developer\"]}
- Input: \"junior marketing executive\" → {\"field\": \"Marketing\", \"experience\": \"Junior\", \"keywords\": [\"Marketing Executive\", \"Digital Marketing\"]}
- Input: \"kế toán có kinh nghiệm\" → {\"field\": \"Kế toán\", \"experience\": \"Mid-level\", \"keywords\": [\"Kế toán\", \"Accounting\"]}";

$requestBody = [
    "contents" => [
        [
            "parts" => [
                ["text" => $prompt]
            ]
        ]
    ],
    "generationConfig" => [
        "response_mime_type" => "application/json",
        "temperature" => 0.1, // Lower temperature for more consistent results
        "maxOutputTokens" => 500
    ],
    "safetySettings" => [
        [
            "category" => "HARM_CATEGORY_HARASSMENT",
            "threshold" => "BLOCK_MEDIUM_AND_ABOVE"
        ],
        [
            "category" => "HARM_CATEGORY_HATE_SPEECH",
            "threshold" => "BLOCK_MEDIUM_AND_ABOVE"
        ]
    ]
];

// 7. Make API request with proper error handling
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'User-Agent: JobSearchPlatform/1.0'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15); // 15 seconds timeout
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // 5 seconds connection timeout
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// 8. Handle cURL errors
if ($response === false) {
    logError("cURL Error: $curlError for keyword: $userKeyword");
    sendResponse([
        'success' => false,
        'error' => 'Unable to connect to AI service',
        'details' => $curlError
    ], 503);
}

// 9. Parse API response
$responseData = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    logError("Invalid JSON response from Gemini API: " . json_last_error_msg());
    sendResponse([
        'success' => false,
        'error' => 'Invalid response from AI service'
    ], 500);
}

// 10. Check for API errors
if ($httpCode !== 200) {
    $errorMsg = $responseData['error']['message'] ?? 'Unknown API error';
    logError("Gemini API Error (HTTP $httpCode): $errorMsg");
    sendResponse([
        'success' => false,
        'error' => 'AI service returned an error',
        'details' => $errorMsg
    ], 502);
}

// 11. Extract result
$text = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? null;

if ($text === null) {
    logError("No text content in Gemini response for keyword: $userKeyword");
    sendResponse([
        'success' => false,
        'error' => 'No result from AI service'
    ], 500);
}

// 12. Clean up response (remove markdown if any)
$text = preg_replace('/^```json\s*|\s*```$/m', '', trim($text));
$text = trim($text);

// 13. Parse result JSON
$result = json_decode($text, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    logError("Invalid JSON from AI analysis: $text");
    sendResponse([
        'success' => false,
        'error' => 'Invalid result format from AI'
    ], 500);
}

// 14. Validate result structure
$validatedResult = [
    'field' => isset($result['field']) ? trim($result['field']) : '',
    'experience' => isset($result['experience']) ? trim($result['experience']) : '',
    'keywords' => isset($result['keywords']) && is_array($result['keywords']) 
        ? array_values(array_filter(array_map('trim', $result['keywords']))) 
        : []
];

// 15. Cache the result
setCachedResult($userKeyword, $validatedResult);

// 16. Send success response
sendResponse([
    'success' => true,
    'data' => $validatedResult,
    'cached' => false,
    'message' => 'Analysis completed successfully'
]);