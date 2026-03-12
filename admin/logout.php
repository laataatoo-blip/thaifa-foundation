<?php 
    session_start();
    include_once(__DIR__ . '/../backend/classes/AdminSecurityManagement.class.php');
    if (class_exists('AdminSecurityManagement')) {
        $securityAudit = new AdminSecurityManagement();
        $adminId = (int)($_SESSION['AdminLogin']['AdminID'] ?? 0);
        $username = trim((string)($_SESSION['AdminLogin']['Username'] ?? ''));
        $securityAudit->recordLogout($adminId, $username);
    }
    $redirec = "login.php";
    session_destroy();
    header("Location: $redirec");
    exit;
?>
