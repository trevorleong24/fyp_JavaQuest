<?php
// Temporary file to check quiz table
require_once("../conn/conn.php");

echo "<h2>Quiz Table Structure</h2>";
$result = $conn->query("DESCRIBE quiz");
if ($result) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $key => $value) {
            echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error fetching quiz table structure: " . $conn->error;
}

echo "<h2>Quiz Table Data</h2>";
$result = $conn->query("SELECT * FROM quiz");
if ($result) {
    if ($result->num_rows > 0) {
        echo "<table border='1'><tr>";
        $row = $result->fetch_assoc();
        foreach ($row as $key => $value) {
            echo "<th>" . htmlspecialchars($key) . "</th>";
        }
        echo "</tr>";
        
        // Reset result pointer to beginning
        $result->data_seek(0);
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No data in quiz table";
    }
} else {
    echo "Error fetching quiz data: " . $conn->error;
}
?> 