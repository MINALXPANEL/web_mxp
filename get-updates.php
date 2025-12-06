<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// In production, use a database. For simplicity, we'll use a JSON file.
$updatesFile = 'updates.json';

// Read existing updates
if (file_exists($updatesFile)) {
    $updates = json_decode(file_get_contents($updatesFile), true);
} else {
    $updates = [];
}

// Get client version from query parameter
$clientVersion = $_GET['version'] ?? '0.0.0';

// Filter updates newer than client version
$newUpdates = array_filter($updates, function($update) use ($clientVersion) {
    return version_compare($update['version'], $clientVersion, '>');
});

// Sort by date (newest first)
usort($newUpdates, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

echo json_encode(array_values($newUpdates));
?>
