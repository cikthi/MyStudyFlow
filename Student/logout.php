<?php
session_start();
session_unset();    // Buang semua data dalam session
session_destroy();  // Tamatkan session

// direct ke halaman login
header("Location: login.php"); 
exit();
?>
