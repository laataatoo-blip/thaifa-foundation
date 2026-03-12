<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

header('Content-Type: application/json; charset=UTF-8');

$MM_authorizedUsers = "Admin";
$MM_donotCheckaccess = "false";
include('./components/funcCheckSession.php');
include_once(__DIR__ . '/../backend/classes/CalendarManagement.class.php');

function jsonOut($ok, $message = '', $data = [])
{
    echo json_encode([
        'ok' => $ok ? 1 : 0,
        'message' => (string)$message,
        'data' => $data,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonOut(false, 'Method not allowed');
}

$action = trim((string)($_POST['action'] ?? ''));
$calendar = new CalendarManagement();
$adminId = (int)($_SESSION['AdminLogin']['AdminID'] ?? 0) ?: null;

try {
    if ($action === 'move_event') {
        $id = (int)($_POST['event_id'] ?? 0);
        $startAt = trim((string)($_POST['start_at'] ?? ''));
        $endAt = trim((string)($_POST['end_at'] ?? ''));
        $isAllDay = !empty($_POST['is_all_day']) ? 1 : 0;
        if ($id <= 0 || $startAt === '') {
            jsonOut(false, 'ข้อมูลไม่ครบ');
        }
        $calendar->updateEventDateRange($id, $startAt, $endAt !== '' ? $endAt : null, $isAllDay, $adminId);
        jsonOut(true, 'อัปเดตเวลาในปฏิทินเรียบร้อย');
    }

    if ($action === 'quick_create') {
        $eventId = $calendar->saveEvent(0, [
            'type_id' => $_POST['type_id'] ?? '',
            'title' => $_POST['title'] ?? '',
            'summary' => $_POST['summary'] ?? '',
            'description' => $_POST['description'] ?? '',
            'location' => $_POST['location'] ?? '',
            'start_at' => $_POST['start_at'] ?? '',
            'end_at' => $_POST['end_at'] ?? '',
            'is_all_day' => !empty($_POST['is_all_day']) ? 1 : 0,
            'is_visible' => 1,
        ], $adminId);
        $event = $calendar->getEventById($eventId);
        jsonOut(true, 'สร้างกิจกรรมใหม่เรียบร้อย', ['event' => $event]);
    }

    jsonOut(false, 'ไม่รู้จัก action');
} catch (Throwable $e) {
    jsonOut(false, $e->getMessage());
}

