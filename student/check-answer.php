<?php
header("Content-Type: application/json");
include("../conn/conn.php");

// Get POST values safely
$question_id = isset($_POST['question_id']) ? $_POST['question_id'] : '';
$selected_answer = isset($_POST['selected_answer']) ? $_POST['selected_answer'] : '';

if (empty($question_id) || empty($selected_answer)) {
    echo json_encode(["correct" => false, "error" => "Missing question ID or answer"]);
    exit();
}

// Fetch the correct answer from the database
$query = "SELECT answer_text FROM answer WHERE question_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $question_id);
$stmt->execute();
$result = $stmt->get_result();

$isCorrect = false;

if ($row = $result->fetch_assoc()) {
    // Decode HTML entities & normalize
    $correct = strtolower(trim(preg_replace('/\s+/', '', html_entity_decode($row['answer_text']))));
    $selected = strtolower(trim(preg_replace('/\s+/', '', html_entity_decode($selected_answer))));

    // Debug log (optional, helpful for WAMP/XAMPP logs)
    error_log("Correct: " . $correct);
    error_log("Selected: " . $selected);

    $isCorrect = ($correct === $selected);
}

echo json_encode(["correct" => $isCorrect]);
?>
