<?php
$conn = mysqli_connect("localhost", "root", "", "javaquest");

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}
?>
