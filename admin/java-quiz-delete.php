<?php
// java-quiz-delete.php

session_start();
// Redirect if not logged in as admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login/login.php");
    exit();
}

require_once("../conn/conn.php");

// Check if ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['message'] = "No question ID provided";
    header("Location: ../admin/questions.php");
    exit();
}

$question_id = $_GET['id'];

// Start a transaction
mysqli_begin_transaction($conn);

try {
    // First, delete from the answer table (because of foreign key constraints)
    $query1 = "DELETE FROM answer WHERE question_id = ?";
    $stmt1 = $conn->prepare($query1);
    $stmt1->bind_param("s", $question_id);
    $result1 = $stmt1->execute();
    
    if (!$result1) {
        throw new Exception("Error deleting associated answer: " . $stmt1->error);
    }
    
    // Then, delete from the question table
    $query2 = "DELETE FROM question WHERE question_id = ?";
    $stmt2 = $conn->prepare($query2);
    $stmt2->bind_param("s", $question_id);
    $result2 = $stmt2->execute();
    
    if (!$result2) {
        throw new Exception("Error deleting question: " . $stmt2->error);
    }
    
    // If we got here, both operations were successful
    mysqli_commit($conn);
    $_SESSION['message'] = "Question and associated answer deleted successfully";
    
} catch (Exception $e) {
    // An error occurred, rollback the transaction
    mysqli_rollback($conn);
    $_SESSION['message'] = "Error: " . $e->getMessage();
}

// Redirect back to the questions page
header("Location: ../admin/questions.php");
exit();
?>