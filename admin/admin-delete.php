<?php
include("../conn/conn.php"); // Include the database connection
session_start(); // Start the session

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login/login.php'); // Redirect to login page if not authorized
    exit();
}

// Check if delete request is valid
if (isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type'];
    $id = $_GET['id'];
    
    // Create appropriate delete logic based on type
    try {
        // Begin transaction to ensure data consistency
        $conn->begin_transaction();
        
        if ($type === 'student') {
            // First, delete related records in PROGRESS table if it exists
            $check_progress_table = "SHOW TABLES LIKE 'PROGRESS'";
            $progress_table_check = mysqli_query($conn, $check_progress_table);
            
            if ($progress_table_check && mysqli_num_rows($progress_table_check) > 0) {
                $delete_progress_query = "DELETE FROM PROGRESS WHERE student_id = ?";
                $progress_stmt = $conn->prepare($delete_progress_query);
                if (!$progress_stmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                $progress_stmt->bind_param("s", $id);
                if (!$progress_stmt->execute()) {
                    throw new Exception("Failed to delete progress records: " . $progress_stmt->error);
                }
                $progress_stmt->close();
            }
            
            // Then delete the student record
            $delete_query = "DELETE FROM STUDENT WHERE student_id = ?";
            $stmt = $conn->prepare($delete_query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("s", $id);
            if (!$stmt->execute()) {
                throw new Exception("Failed to delete student: " . $stmt->error);
            }
            
            // Check if any rows were affected
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            if ($affected_rows === 0) {
                throw new Exception("Student not found.");
            }
            
            $conn->commit();
            $_SESSION['message'] = "Student deleted successfully!";
            $_SESSION['message_type'] = 'delete'; // Set message type for styling
            
        } elseif ($type === 'question') {
            // First, delete related records in PROGRESS table if it exists
            $check_progress_table = "SHOW TABLES LIKE 'PROGRESS'";
            $progress_table_check = mysqli_query($conn, $check_progress_table);
            
            if ($progress_table_check && mysqli_num_rows($progress_table_check) > 0) {
                $delete_progress_query = "DELETE FROM PROGRESS WHERE question_id = ?";
                $progress_stmt = $conn->prepare($delete_progress_query);
                if (!$progress_stmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                $progress_stmt->bind_param("s", $id);
                if (!$progress_stmt->execute()) {
                    throw new Exception("Failed to delete progress records: " . $progress_stmt->error);
                }
                $progress_stmt->close();
            }
            
            // Then delete the question record
            $delete_query = "DELETE FROM QUESTIONS WHERE question_id = ?";
            $stmt = $conn->prepare($delete_query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("s", $id);
            if (!$stmt->execute()) {
                throw new Exception("Failed to delete question: " . $stmt->error);
            }
            
            // Check if any rows were affected
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            if ($affected_rows === 0) {
                throw new Exception("Question not found.");
            }
            
            $conn->commit();
            $_SESSION['message'] = "Question deleted successfully!";
            $_SESSION['message_type'] = 'delete'; // Set message type for styling
            
        } else {
            throw new Exception("Invalid deletion type.");
        }
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['message'] = "Error: " . $e->getMessage();
        $_SESSION['message_type'] = 'error';
    }
    
    // Close connection before redirecting
    $conn->close();
    
    // Redirect back to admin dashboard
    header('Location: ../admin/admin.php');
    exit();
} else {
    // Close connection before redirecting
    $conn->close();
    
    $_SESSION['message'] = "Invalid request parameters.";
    $_SESSION['message_type'] = 'error';
    header('Location: ../admin/admin.php');
    exit();
}
?>