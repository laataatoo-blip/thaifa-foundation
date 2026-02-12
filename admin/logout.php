<?php 
    session_start();
    $redirec = "login.php";
    session_destroy();
    header("Location: $redirec");
    exit;
?>