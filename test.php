<?php
session_start();
$_SESSION['test'] = "Session ทำงานปกติ";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>