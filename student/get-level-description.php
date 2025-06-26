<?php
header("Content-Type: application/json");
include("../conn/conn.php");  // make sure this connects to your javaquest DB

// expected ?level=easy|normal|hard
$level = isset($_GET['level'])
       ? ucfirst(strtolower($_GET['level']))  // Easy, Normal, Hard
       : '';

if (!$level) {
    echo json_encode(['error' => 'No level specified']);
    exit;
}

$stmt = $conn->prepare("
    SELECT quiz_id, description
      FROM quiz
     WHERE title = ?
     LIMIT 1
");
$stmt->bind_param("s", $level);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
    echo json_encode([
        'quiz_id'     => $row['quiz_id'],
        'description' => $row['description']
    ]);
} else {
    echo json_encode(['error' => 'Level not found']);
}

$stmt->close();
$conn->close();
