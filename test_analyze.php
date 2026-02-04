<?php
/**
 * Test script for analyze.php API
 * Usage: php test_analyze.php
 * Or access via browser: http://localhost/webkiemthu/test_analyze.php
 */

// Configuration
$API_URL = 'http://localhost/webkiemthu/api/analyze.php';

// Test cases
$testCases = [
    [
        'name' => 'Test 1: IT keyword',
        'keyword' => 'tuyển lập trình viên python',
        'expected' => ['field' => 'IT']
    ],
    [
        'name' => 'Test 2: Marketing keyword',
        'keyword' => 'junior marketing executive',
        'expected' => ['field' => 'Marketing', 'experience' => 'Junior']
    ],
    [
        'name' => 'Test 3: Vietnamese keyword',
        'keyword' => 'kế toán có kinh nghiệm',
        'expected' => ['field' => 'Kế toán']
    ],
    [
        'name' => 'Test 4: Empty keyword',
        'keyword' => '',
        'expected' => ['field' => '', 'keywords' => []]
    ],
    [
        'name' => 'Test 5: Short keyword',
        'keyword' => 'it',
        'expected' => ['field' => 'IT']
    ],
    [
        'name' => 'Test 6: Mixed language',
        'keyword' => 'senior php developer ho chi minh',
        'expected' => ['field' => 'IT', 'experience' => 'Senior']
    ]
];

// Function to call API
function callAPI($url, $keyword) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['keyword' => $keyword]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'response' => $response,
        'httpCode' => $httpCode,
        'error' => $error
    ];
}

// Function to print result
function printResult($testName, $result, $expected) {
    echo "\n" . str_repeat('=', 80) . "\n";
    echo "🧪 $testName\n";
    echo str_repeat('-', 80) . "\n";
    
    if (!empty($result['error'])) {
        echo "❌ cURL Error: {$result['error']}\n";
        return false;
    }
    
    echo "HTTP Code: {$result['httpCode']}\n";
    
    $data = json_decode($result['response'], true);
    
    if ($data === null) {
        echo "❌ Invalid JSON response\n";
        echo "Raw response: {$result['response']}\n";
        return false;
    }
    
    echo "\n📊 Response:\n";
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    
    // Check expected values
    $passed = true;
    if (isset($data['success']) && $data['success'] && isset($data['data'])) {
        foreach ($expected as $key => $value) {
            if (isset($data['data'][$key])) {
                if ($data['data'][$key] === $value || 
                    (is_array($value) && $data['data'][$key] === $value)) {
                    echo "✅ Expected '$key' matches\n";
                } else {
                    echo "⚠️  Expected '$key': " . json_encode($value) . 
                         ", Got: " . json_encode($data['data'][$key]) . "\n";
                    $passed = false;
                }
            }
        }
    }
    
    if (isset($data['cached'])) {
        echo "\n💾 Cache status: " . ($data['cached'] ? 'HIT' : 'MISS') . "\n";
    }
    
    return $passed;
}

// HTML Mode
if (php_sapi_name() !== 'cli') {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Test Analyze API</title>
        <style>
            body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
            .test { background: #2d2d2d; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #007acc; }
            .pass { border-left-color: #4caf50; }
            .fail { border-left-color: #f44336; }
            .response { background: #1e1e1e; padding: 10px; margin: 10px 0; border-radius: 3px; overflow-x: auto; }
            h2 { color: #569cd6; }
            .success { color: #4caf50; }
            .error { color: #f44336; }
            .info { color: #ffc107; }
        </style>
    </head>
    <body>
        <h1>🧪 Analyze API Test Suite</h1>
        <p><strong>API URL:</strong> ' . htmlspecialchars($API_URL) . '</p>
        <hr>';
}

// Run tests
echo "\n🚀 Starting API Tests...\n";
echo "API URL: $API_URL\n";

$totalTests = count($testCases);
$passedTests = 0;
$startTime = microtime(true);

foreach ($testCases as $index => $test) {
    $result = callAPI($API_URL, $test['keyword']);
    $passed = printResult($test['name'], $result, $test['expected']);
    
    if ($passed) {
        $passedTests++;
    }
    
    // HTML mode formatting
    if (php_sapi_name() !== 'cli') {
        echo '<div class="test ' . ($passed ? 'pass' : 'fail') . '">';
        echo '<h2>' . htmlspecialchars($test['name']) . '</h2>';
        echo '<p><strong>Keyword:</strong> ' . htmlspecialchars($test['keyword']) . '</p>';
        
        if (!empty($result['error'])) {
            echo '<p class="error">❌ Error: ' . htmlspecialchars($result['error']) . '</p>';
        } else {
            $data = json_decode($result['response'], true);
            echo '<div class="response">';
            echo '<pre>' . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
            echo '</div>';
            
            if ($passed) {
                echo '<p class="success">✅ Test passed</p>';
            } else {
                echo '<p class="error">❌ Test failed</p>';
            }
        }
        echo '</div>';
    }
    
    // Wait a bit between tests to avoid rate limiting
    if ($index < $totalTests - 1) {
        sleep(1);
    }
}

$endTime = microtime(true);
$duration = round($endTime - $startTime, 2);

// Summary
echo "\n" . str_repeat('=', 80) . "\n";
echo "📊 TEST SUMMARY\n";
echo str_repeat('-', 80) . "\n";
echo "Total Tests: $totalTests\n";
echo "Passed: $passedTests\n";
echo "Failed: " . ($totalTests - $passedTests) . "\n";
echo "Duration: {$duration}s\n";
echo "Success Rate: " . round(($passedTests / $totalTests) * 100, 1) . "%\n";

if ($passedTests === $totalTests) {
    echo "\n✅ All tests passed!\n";
} else {
    echo "\n⚠️  Some tests failed. Please check the results above.\n";
}

// HTML mode footer
if (php_sapi_name() !== 'cli') {
    echo '<hr>';
    echo '<div style="background: #2d2d2d; padding: 15px; border-radius: 5px;">';
    echo '<h2>📊 Summary</h2>';
    echo '<p><strong>Total Tests:</strong> ' . $totalTests . '</p>';
    echo '<p><strong>Passed:</strong> <span class="success">' . $passedTests . '</span></p>';
    echo '<p><strong>Failed:</strong> <span class="error">' . ($totalTests - $passedTests) . '</span></p>';
    echo '<p><strong>Duration:</strong> ' . $duration . 's</p>';
    echo '<p><strong>Success Rate:</strong> ' . round(($passedTests / $totalTests) * 100, 1) . '%</p>';
    echo '</div>';
    echo '</body></html>';
}