<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(120);
header('Content-Type: application/json');

function try_flask_server($filePath) {
    if (!function_exists('curl_init')) {
        return null;
    }
    $url = 'http://127.0.0.1:5000/ocr';
    $ch = curl_init($url);
    $cfile = new CURLFile($filePath);
    $post = ['file' => $cfile, 'engine' => 'tesseract']; // fast engine by default

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $post,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT_MS => 700,
        CURLOPT_TIMEOUT => 25,
    ]);

    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($resp !== false && $http === 200) {
        $decoded = json_decode($resp, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }
    }
    return null;
}

function process_single_file($targetPath) {
    // Try persistent server first
    $serverResult = try_flask_server($targetPath);
    if (is_array($serverResult)) {
        if (!isset($serverResult['file_type'])) {
            $serverResult['file_type'] = pathinfo($targetPath, PATHINFO_EXTENSION);
        }
        return $serverResult;
    }

    // Fallback to Python CLI
    putenv('OCR_ENGINE=fast'); // hint fast pipeline
    $python = '"C:\\Users\\PLPASIG\\AppData\\Local\\Programs\\Python\\Python313\\python.exe"';
    $script = __DIR__ . "\\ocr_engine.py";
    $command = "$python $script " . escapeshellarg($targetPath);

    $output = shell_exec("$command 2>&1");
    if (!$output || trim($output) === "") {
        return ['error' => 'No output from OCR engine.', 'cmd' => $command];
    }

    // Trim to JSON if extra logs exist
    $jsonStart = strpos($output, '{');
    $jsonEnd = strrpos($output, '}');
    if ($jsonStart !== false && $jsonEnd !== false) {
        $output = substr($output, $jsonStart, $jsonEnd - $jsonStart + 1);
    }

    $decoded = json_decode($output, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['error' => 'Invalid JSON returned from Python.', 'raw' => substr($output, 0, 500)];
    }

    if (!isset($decoded['file_type'])) {
        $decoded['file_type'] = pathinfo($targetPath, PATHINFO_EXTENSION);
    }
    return $decoded;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['documentFile'])) {
    $uploadDir = __DIR__ . '/uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $files = $_FILES['documentFile'];
    $isMultiple = is_array($files['name']);
    $count = $isMultiple ? count($files['name']) : 1;

    $results = [];

    for ($i = 0; $i < $count; $i++) {
        $origName = $isMultiple ? $files['name'][$i] : $files['name'];
        $tmpName  = $isMultiple ? $files['tmp_name'][$i] : $files['tmp_name'];
        $error    = $isMultiple ? $files['error'][$i] : $files['error'];
        $size     = $isMultiple ? $files['size'][$i] : $files['size'];

        if ($error !== UPLOAD_ERR_OK || !is_uploaded_file($tmpName)) {
            $results[] = [
                'original_name' => $origName,
                'error' => 'File upload failed.'
            ];
            continue;
        }

        // Ensure unique target path to avoid collisions
        $safeBase = basename($origName);
        $uniqueName = uniqid('up_', true) . '_' . $safeBase;
        $targetPath = $uploadDir . $uniqueName;

        if (!move_uploaded_file($tmpName, $targetPath)) {
            $results[] = [
                'original_name' => $origName,
                'error' => 'Failed to move uploaded file.'
            ];
            continue;
        }

        // Public URL for preview (Apache serves /ocr/uploads/*)
        $publicUrl = '/ocr/uploads/' . $uniqueName;

        // Process the file
        $res = process_single_file($targetPath);
        $res['original_name'] = $origName;
        $res['public_url'] = $publicUrl;
        $res['file_size'] = $size;

        // NOTE: keep files for preview (do not unlink)
        $results[] = $res;
    }

    echo json_encode(['results' => $results]);
    exit;
} else {
    echo json_encode(['error' => 'Invalid request.']);
    exit;
}
?>
