<?php
header("Content-Type: application/json");
include("../conn/conn.php");

if (
    isset($_GET['quiz_id']) &&
    in_array($_GET['quiz_id'], ['QZ01','QZ02','QZ03'], true)
) {
    $quiz_id = $_GET['quiz_id'];
} else {
    echo json_encode(["error" => "quiz_id not provided or invalid"]);
    exit;
}

$sql = "
    SELECT question_id,
           question_text,
           `Option 1`,
           `Option 2`,
           `Option 3`,
           `Option 4`
    FROM question
    WHERE quiz_id = ?
    ORDER BY RAND()
    LIMIT 1
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $quiz_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode($row);
} else {
    echo json_encode(["error" => "No question found"]);
}
