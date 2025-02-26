<?php
session_start();
// Destroy the session
session_destroy();
// Redirect the user to the main page or login page
header("Location: main.php");
exit();
?>