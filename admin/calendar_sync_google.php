<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$MM_authorizedUsers = "Admin";
$MM_donotCheckaccess = "false";
include('./components/funcCheckSession.php');

include_once(__DIR__ . '/../backend/classes/CalendarManagement.class.php');
include_once(__DIR__ . '/../backend/classes/GoogleCalendarSync.class.php');

try {
    $calendar = new CalendarManagement();
    $sync = new GoogleCalendarSync($calendar);
    $adminId = (int)($_SESSION['AdminLogin']['AdminID'] ?? 0) ?: null;
    $result = $sync->syncFromEnv($adminId);

    $_SESSION['calendar_sync_flash'] = [
        'type' => 'success',
        'message' => sprintf(
            'ซิงก์ Google Calendar สำเร็จ: เพิ่ม %d | อัปเดต %d | ข้าม %d | ทั้งหมด %d',
            (int)$result['created'],
            (int)$result['updated'],
            (int)$result['skipped'],
            (int)$result['total']
        ),
    ];
} catch (Throwable $e) {
    $_SESSION['calendar_sync_flash'] = [
        'type' => 'danger',
        'message' => 'ซิงก์ Google Calendar ไม่สำเร็จ: ' . $e->getMessage(),
    ];
}

header('Location: calendar_events.php');
exit;

