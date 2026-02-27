<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$MM_authorizedUsers = "Admin";
$MM_donotCheckaccess = "false";
include('./components/funcCheckSession.php');

include(__DIR__ . '/../backend/classes/DatabaseManagement.class.php');
include(__DIR__ . '/../backend/classes/FacebookNewsSync.class.php');

try {
    $DB = new DatabaseManagement();
    $sync = new FacebookNewsSync($DB);
    $result = $sync->sync(15);
    $_SESSION['sync_flash'] = [
        'type' => 'success',
        'message' => 'ซิงก์ข่าวจาก Facebook สำเร็จ: เพิ่ม ' . (int)$result['created'] . ' | อัปเดต ' . (int)$result['updated'] . ' | ข้าม ' . (int)$result['skipped']
    ];
} catch (Throwable $e) {
    $_SESSION['sync_flash'] = [
        'type' => 'error',
        'message' => 'ซิงก์ไม่สำเร็จ: ' . $e->getMessage()
    ];
}

header('Location: news_list.php');
exit;
