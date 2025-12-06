<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Simple authentication (in production, use proper authentication)
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$token = str_replace('Bearer ', '', $authHeader);
$validToken = 'WonderDev2024'; // Should be stored securely

if ($token !== $validToken) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Validate required fields
$required = ['title', 'description', 'type', 'version'];
foreach ($required as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Missing field: $field"]);
        exit;
    }
}

// Read existing updates
$updatesFile = 'updates.json';
if (file_exists($updatesFile)) {
    $updates = json_decode(file_get_contents($updatesFile), true);
} else {
    $updates = [];
}

// Add new update
$input['date'] = date('c');
$updates[] = $input;

// Save to file
if (file_put_contents($updatesFile, json_encode($updates, JSON_PRETTY_PRINT))) {
    echo json_encode(['success' => true, 'message' => 'Update published successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save update']);
}
?>
