/*DEBUG for Question*/ 

<?php
// Setup file to add quiz records if they don't exist
require_once("../conn/conn.php");

// First, let's check if the table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'quiz'");
if ($tableCheck->num_rows == 0) {
    // Table doesn't exist, create it
    $createTable = "CREATE TABLE IF NOT EXISTS quiz (
        quiz_id VARCHAR(10) PRIMARY KEY,
        title VARCHAR(100) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        admin_id VARCHAR(10) NOT NULL
    )";
    
    if ($conn->query($createTable)) {
        echo "Quiz table created successfully.<br>";
    } else {
        echo "Error creating quiz table: " . $conn->error . "<br>";
    }
}

// Check if quiz data exists
$checkRecords = $conn->query("SELECT * FROM quiz WHERE quiz_id IN ('QZ01', 'QZ02', 'QZ03')");

// Check if administrator exists
$adminCheck = $conn->query("SELECT admin_id FROM administrator WHERE admin_id = 'A01'");
if ($adminCheck->num_rows == 0) {
    echo "<div style='color: red; font-weight: bold;'>Error: Administrator with ID 'A01' does not exist. Please make sure an administrator is set up first.</div>";
    exit;
}

$admin_id = 'A01'; // Default admin ID from the SQL dump

if ($checkRecords->num_rows < 3) {
    // Not all required records exist, insert or update them
    $quizData = [
        ['QZ01', 'Easy', 'Basic Java programming questions', $admin_id],
        ['QZ02', 'Normal', 'Intermediate Java programming questions', $admin_id],
        ['QZ03', 'Hard', 'Advanced Java programming questions', $admin_id]
    ];
    
    // Check each quiz_id
    foreach ($quizData as $quiz) {
        $quiz_id = $quiz[0];
        $title = $quiz[1];
        $description = $quiz[2];
        $admin = $quiz[3];
        
        // Check if this quiz_id exists
        $exists = $conn->query("SELECT 1 FROM quiz WHERE quiz_id = '$quiz_id'");
        
        if ($exists->num_rows > 0) {
            // Update existing record
            $updateQuery = "UPDATE quiz SET title = ?, description = ?, admin_id = ? WHERE quiz_id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("ssss", $title, $description, $admin, $quiz_id);
            
            if ($stmt->execute()) {
                echo "Updated quiz: $quiz_id - $title<br>";
            } else {
                echo "Error updating quiz $quiz_id: " . $stmt->error . "<br>";
            }
            $stmt->close();
        } else {
            // Insert new record
            $insertQuery = "INSERT INTO quiz (quiz_id, title, description, admin_id) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ssss", $quiz_id, $title, $description, $admin);
            
            if ($stmt->execute()) {
                echo "Added new quiz: $quiz_id - $title<br>";
            } else {
                echo "Error inserting quiz $quiz_id: " . $stmt->error . "<br>";
            }
            $stmt->close();
        }
    }
    
    echo "<div style='color: green; font-weight: bold;'>Quiz records updated successfully.</div>";
} else {
    echo "<div style='color: green;'>All necessary quiz records already exist.</div>";
}

// Display current quiz table data
echo "<h2>Current Quiz Table Data</h2>";
$result = $conn->query("SELECT * FROM quiz");
if ($result && $result->num_rows > 0) {
    echo "<table border='1'><tr><th>quiz_id</th><th>title</th><th>description</th><th>admin_id</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['quiz_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
        echo "<td>" . htmlspecialchars($row['admin_id']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No data in quiz table or error fetching data.</p>";
}

echo "<p>You can now use these quiz_id values in your forms.</p>";
echo "<a href='java-quiz-add.php'>Return to Add Question Form</a>";
?> 