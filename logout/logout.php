<?php
session_start(); // Start the session
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session
header("Location: ../homepage/main-homepage.html"); // Redirect to the main homepage
exit();
?>
