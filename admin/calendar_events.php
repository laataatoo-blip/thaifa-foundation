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

$calendar = new CalendarManagement();

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function dtLocal($v) {
    if (!$v) return '';
    $t = strtotime((string)$v);
    if (!$t) return '';
    return date('Y-m-d\TH:i', $t);
}

$errors = [];
$success = '';
$syncFlash = $_SESSION['calendar_sync_flash'] ?? null;
unset($_SESSION['calendar_sync_flash']);
$resolvedGoogleIcsUrl = GoogleCalendarSync::resolveConfiguredIcsUrl();
$isGoogleSyncReady = $resolvedGoogleIcsUrl !== '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'save_event') {
            $id = (int)($_POST['event_id'] ?? 0);
            $adminId = (int)($_SESSION['AdminLogin']['AdminID'] ?? 0) ?: null;
            $calendar->saveEvent($id, $_POST, $adminId);
            $success = 'บันทึกกิจกรรมเรียบร้อย';
        }

        if ($action === 'delete_event') {
            $id = (int)($_POST['event_id'] ?? 0);
            if ($id > 0) {
                $calendar->deleteEvent($id);
                $success = 'ลบกิจกรรมเรียบร้อย';
            }
        }

        if ($action === 'toggle_event') {
            $id = (int)($_POST['event_id'] ?? 0);
            $visible = (int)($_POST['is_visible'] ?? 0);
            $calendar->toggleVisible($id, $visible);
            $success = 'อัปเดตสถานะกิจกรรมเรียบร้อย';
        }

        if ($action === 'save_type') {
            $id = (int)($_POST['type_id'] ?? 0);
            $calendar->saveType($id, $_POST);
            $success = 'บันทึกประเภทกิจกรรมเรียบร้อย';
        }
    } catch (Throwable $e) {
        $errors[] = $e->getMessage();
    }
}

$monthFilter = (int)($_GET['month'] ?? 0);
$yearFilter = (int)($_GET['year'] ?? 0);
$events = $calendar->getEvents(false, $monthFilter, $yearFilter);
$types = $calendar->getTypes(false);
$upcoming = $calendar->getUpcomingEvents(12);
$calendarEventsJson = $calendar->eventsForAdminJson();
$eventCount = count($events);
$visibleCount = 0;
foreach ($events as $e) {
    if ((int)$e['is_visible'] === 1) $visibleCount++;
}
$typeCount = count($types);

$editId = (int)($_GET['edit_id'] ?? 0);
$editEvent = $editId > 0 ? $calendar->getEventById($editId) : null;
if (!$editEvent) {
    $editEvent = [
        'id' => 0,
        'type_id' => '',
        'title' => '',
        'summary' => '',
        'description' => '',
        'location' => '',
        'start_at' => date('Y-m-d H:i:s', strtotime('+1 day 09:00')),
        'end_at' => date('Y-m-d H:i:s', strtotime('+1 day 12:00')),
        'is_all_day' => 0,
        'is_visible' => 1,
    ];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include('./structure/head.php') ?>
    <title>จัดการปฏิทินกิจกรรม</title>
    <link rel="stylesheet" href="assets/plugins/fullcalendar/css/main.min.css" />
    <style>
        .calendar-filter { min-width: 120px; }
        .calendar-actions { min-width: 210px; }
        .admin-calendar-wrap {
            border: 1px solid #d9e7ef;
            border-radius: 14px;
            background: #fff;
            overflow: hidden;
        }
        #adminFullCalendar {
            padding: 10px;
            min-height: 680px;
        }
        .fc .fc-toolbar-title {
            font-size: 1.35rem;
            color: #303a56;
        }
        .fc .fc-button {
            background: #3357a3;
            border-color: #3357a3;
        }
        .fc .fc-button-primary:not(:disabled):active,
        .fc .fc-button-primary:not(:disabled).fc-button-active {
            background: #233882;
            border-color: #233882;
        }
        .event-hint {
            font-size: 12px;
            color: #64748b;
            margin-top: 8px;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include('./components/sidebar.php') ?>
    <?php include('./components/navbar.php') ?>

    <div class="page-wrapper">
        <div class="page-content-wrapper page-content-margin-padding">
            <div class="page-content page-content-margin-padding">

                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <div class="breadcrumb-title pe-3">Calendar</div>
                    <div class="ps-3">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0">
                                <li class="breadcrumb-item"><a href="index.php"><i class="bx bx-home-alt"></i></a></li>
                                <li class="breadcrumb-item active" aria-current="page">ปฏิทินกิจกรรม</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-md-3 g-3 mb-3">
                    <div class="col"><div class="thaifa-stat"><div class="label">กิจกรรมทั้งหมด</div><div class="value mt-2"><?= (int)$eventCount ?></div></div></div>
                    <div class="col"><div class="thaifa-stat"><div class="label">กิจกรรมที่แสดง</div><div class="value mt-2"><?= (int)$visibleCount ?></div></div></div>
                    <div class="col"><div class="thaifa-stat"><div class="label">ประเภทกิจกรรม</div><div class="value mt-2"><?= (int)$typeCount ?></div></div></div>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $e): ?>
                            <div><?= h($e) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?= h($success) ?></div>
                <?php endif; ?>

                <?php if (is_array($syncFlash) && !empty($syncFlash['message'])): ?>
                    <div class="alert alert-<?= ($syncFlash['type'] ?? '') === 'success' ? 'success' : 'warning' ?>">
                        <?= h($syncFlash['message']) ?>
                    </div>
                <?php endif; ?>

                <?php if (!$isGoogleSyncReady): ?>
                    <div class="alert alert-warning">
                        ยังไม่พบลิงก์ Google Calendar ที่พร้อมใช้งาน กรุณาตั้งค่า <code>GOOGLE_CALENDAR_ICS_URL</code> หรือ <code>GOOGLE_CALENDAR_PUBLIC_URL</code>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        พร้อมซิงก์ Google Calendar แล้ว
                    </div>
                <?php endif; ?>

                <div class="card thaifa-card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                            <div>
                                <h5 class="mb-1">ปฏิทินกิจกรรม (สไตล์ Google Calendar)</h5>
                                <div class="text-muted small">คลิกวันเพื่อสร้างกิจกรรมใหม่, ลากย้าย/ยืดเวลาเพื่ออัปเดตทันที, คลิกอีเวนต์เพื่อดึงข้อมูลมาแก้ไขในฟอร์ม</div>
                            </div>
                            <div class="d-flex flex-column align-items-end gap-2">
                                <form method="post" action="calendar_sync_google.php" class="m-0">
                                    <button type="submit" class="btn btn-primary btn-sm" <?= $isGoogleSyncReady ? '' : 'disabled' ?>>
                                        <i class='bx bx-sync me-1'></i> ซิงก์จาก Google Calendar
                                    </button>
                                </form>
                                <div class="text-muted small text-end" style="max-width: 420px;">
                                    แหล่งข้อมูล: <?= h($resolvedGoogleIcsUrl !== '' ? 'Google Calendar ที่กำหนดไว้' : 'ยังไม่กำหนด') ?>
                                </div>
                            </div>
                        </div>
                        <div class="admin-calendar-wrap">
                            <div id="adminFullCalendar"></div>
                        </div>
                        <div class="event-hint">Tip: การลากหรือยืดอีเวนต์จะอัปเดตวันเวลาในระบบทันที โดยข้อมูลอื่นยังอยู่เหมือนเดิม</div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-xl-5">
                        <div class="card thaifa-card">
                            <div class="card-body">
                                <h5 class="mb-3"><?= (int)$editEvent['id'] > 0 ? 'แก้ไขกิจกรรม' : 'เพิ่มกิจกรรมใหม่' ?></h5>
                                <form method="post" id="eventForm">
                                    <input type="hidden" name="action" value="save_event">
                                    <input type="hidden" name="event_id" id="event_id" value="<?= (int)$editEvent['id'] ?>">

                                    <div class="mb-3">
                                        <label class="form-label">ประเภทกิจกรรม</label>
                                        <select name="type_id" class="form-select" id="type_id">
                                            <option value="">ไม่ระบุ</option>
                                            <?php foreach ($types as $t): ?>
                                                <option value="<?= (int)$t['id'] ?>" <?= ((int)$editEvent['type_id'] === (int)$t['id']) ? 'selected' : '' ?>>
                                                    <?= h($t['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">ชื่อกิจกรรม</label>
                                        <input type="text" name="title" class="form-control" id="title" value="<?= h($editEvent['title']) ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">สรุปสั้น (แสดงในการ์ด)</label>
                                        <input type="text" name="summary" class="form-control" id="summary" value="<?= h($editEvent['summary']) ?>">
                                    </div>

                                    <div class="row g-2">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">เริ่มกิจกรรม</label>
                                            <input type="datetime-local" name="start_at" id="start_at" class="form-control" value="<?= h(dtLocal($editEvent['start_at'])) ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">สิ้นสุดกิจกรรม</label>
                                            <input type="datetime-local" name="end_at" id="end_at" class="form-control" value="<?= h(dtLocal($editEvent['end_at'])) ?>">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">สถานที่</label>
                                        <input type="text" name="location" class="form-control" id="location" value="<?= h($editEvent['location']) ?>" placeholder="เช่น ห้องประชุมใหญ่ หรือ โรงเรียน...">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">รายละเอียด</label>
                                        <textarea name="description" rows="4" id="description" class="form-control"><?= h($editEvent['description']) ?></textarea>
                                    </div>

                                    <div class="d-flex gap-3 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_all_day" name="is_all_day" value="1" <?= !empty($editEvent['is_all_day']) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="is_all_day">ทั้งวัน</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" <?= !empty($editEvent['is_visible']) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="is_visible">แสดงบนเว็บ</label>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">บันทึกกิจกรรม</button>
                                        <a href="calendar_events.php" class="btn btn-outline-secondary">เพิ่มใหม่</a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card thaifa-card mt-3">
                            <div class="card-body">
                                <h6 class="mb-3">เพิ่ม/แก้ประเภทกิจกรรม</h6>
                                <form method="post" class="row g-2">
                                    <input type="hidden" name="action" value="save_type">
                                    <div class="col-12">
                                        <input type="text" name="name" class="form-control" placeholder="ชื่อประเภท" required>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="slug" class="form-control" placeholder="slug เช่น scholarship">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="color" name="color_hex" class="form-control form-control-color w-100" value="#233882">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="sort_order" class="form-control" placeholder="Sort" value="0">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-center">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-outline-primary btn-sm">บันทึกประเภท</button>
                                    </div>
                                </form>
                                <hr>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($types as $t): ?>
                                        <span class="badge" style="background: <?= h($t['color_hex']) ?>; color: #fff;">
                                            <?= h($t['name']) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-7">
                        <div class="card thaifa-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">รายการกิจกรรมทั้งหมด</h5>
                                    <form class="admin-filter-bar" method="get">
                                        <select name="month" class="form-select form-select-sm calendar-filter">
                                            <option value="">ทุกเดือน</option>
                                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                                <option value="<?= $m ?>" <?= ($monthFilter === $m) ? 'selected' : '' ?>><?= $m ?></option>
                                            <?php endfor; ?>
                                        </select>
                                        <input type="number" name="year" class="form-control form-control-sm calendar-filter" value="<?= $yearFilter > 0 ? (int)$yearFilter : '' ?>" placeholder="ปี ค.ศ.">
                                        <button class="btn btn-sm btn-outline-secondary" type="submit">กรอง</button>
                                    </form>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>กิจกรรม</th>
                                                <th>วันเวลา</th>
                                                <th>ประเภท</th>
                                                <th>สถานะ</th>
                                                <th>จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (empty($events)): ?>
                                            <tr><td colspan="6" class="text-center text-muted">ยังไม่มีกิจกรรม</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($events as $e): ?>
                                                <tr>
                                                    <td><?= (int)$e['id'] ?></td>
                                                    <td>
                                                        <div class="fw-semibold"><?= h($e['title']) ?></div>
                                                        <small class="text-muted"><?= h($e['summary']) ?></small>
                                                        <?php if (!empty($e['location'])): ?>
                                                            <div><small class="text-muted"><i class='bx bx-map'></i> <?= h($e['location']) ?></small></div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div><?= h(date('d/m/Y H:i', strtotime($e['start_at']))) ?></div>
                                                        <?php if (!empty($e['end_at'])): ?>
                                                            <small class="text-muted">ถึง <?= h(date('d/m/Y H:i', strtotime($e['end_at']))) ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge" style="background: <?= h($e['color_hex'] ?: '#233882') ?>; color:#fff;">
                                                            <?= h($e['type_name'] ?: 'ไม่ระบุ') ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge <?= ((int)$e['is_visible'] === 1) ? 'bg-success' : 'bg-secondary' ?>">
                                                            <?= ((int)$e['is_visible'] === 1) ? 'แสดง' : 'ซ่อน' ?>
                                                        </span>
                                                    </td>
                                                    <td class="calendar-actions">
                                                        <div class="admin-inline-actions">
                                                            <a href="calendar_events.php?edit_id=<?= (int)$e['id'] ?>" class="btn btn-sm btn-outline-primary">แก้ไข</a>

                                                            <form method="post" class="d-inline">
                                                                <input type="hidden" name="action" value="toggle_event">
                                                                <input type="hidden" name="event_id" value="<?= (int)$e['id'] ?>">
                                                                <input type="hidden" name="is_visible" value="<?= ((int)$e['is_visible'] === 1) ? 0 : 1 ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-<?= ((int)$e['is_visible'] === 1) ? 'warning' : 'success' ?>">
                                                                    <?= ((int)$e['is_visible'] === 1) ? 'ซ่อน' : 'แสดง' ?>
                                                                </button>
                                                            </form>

                                                            <form method="post" class="d-inline" onsubmit="return confirm('ยืนยันการลบกิจกรรมนี้?')">
                                                                <input type="hidden" name="action" value="delete_event">
                                                                <input type="hidden" name="event_id" value="<?= (int)$e['id'] ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-danger">ลบ</button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card thaifa-card mt-3">
                            <div class="card-body">
                                <h6 class="mb-3">กิจกรรมที่กำลังจะมาถึง (ตรวจสอบหน้าเว็บจริง)</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle">
                                        <thead>
                                            <tr>
                                                <th>วันเวลา</th>
                                                <th>กิจกรรม</th>
                                                <th>ประเภท</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (empty($upcoming)): ?>
                                            <tr><td colspan="3" class="text-center text-muted">ยังไม่มีกิจกรรมถัดไป</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($upcoming as $u): ?>
                                                <tr>
                                                    <td><?= h(date('d/m/Y H:i', strtotime($u['start_at']))) ?></td>
                                                    <td><?= h($u['title']) ?></td>
                                                    <td><span class="badge" style="background: <?= h($u['color_hex'] ?: '#233882') ?>; color:#fff;"><?= h($u['type_name'] ?: 'ไม่ระบุ') ?></span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="overlay toggle-btn-mobile"></div>
<a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
<?php include('./structure/script.php') ?>
<script src="assets/plugins/fullcalendar/js/main.min.js"></script>
<script>
const adminEvents = <?= json_encode($calendarEventsJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

function toDateTimeLocal(dateObj) {
    if (!dateObj) return '';
    const y = dateObj.getFullYear();
    const m = String(dateObj.getMonth() + 1).padStart(2, '0');
    const d = String(dateObj.getDate()).padStart(2, '0');
    const h = String(dateObj.getHours()).padStart(2, '0');
    const i = String(dateObj.getMinutes()).padStart(2, '0');
    return `${y}-${m}-${d}T${h}:${i}`;
}

function setFormFromEvent(ev) {
    document.getElementById('event_id').value = ev.id || 0;
    document.getElementById('type_id').value = ev.type_id || '';
    document.getElementById('title').value = ev.title || '';
    document.getElementById('summary').value = ev.summary || '';
    document.getElementById('location').value = ev.location || '';
    document.getElementById('description').value = ev.description || '';

    const startAt = ev.start_at ? new Date(String(ev.start_at).replace(' ', 'T')) : null;
    const endAt = ev.end_at ? new Date(String(ev.end_at).replace(' ', 'T')) : null;
    document.getElementById('start_at').value = toDateTimeLocal(startAt);
    document.getElementById('end_at').value = toDateTimeLocal(endAt);
    document.getElementById('is_all_day').checked = Number(ev.is_all_day || 0) === 1;
    document.getElementById('is_visible').checked = Number(ev.is_visible || 0) === 1;
    document.getElementById('eventForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function resetForNewEvent(startDate) {
    document.getElementById('event_id').value = 0;
    document.getElementById('title').value = '';
    document.getElementById('summary').value = '';
    document.getElementById('location').value = '';
    document.getElementById('description').value = '';
    document.getElementById('type_id').value = '';
    document.getElementById('is_all_day').checked = false;
    document.getElementById('is_visible').checked = true;
    document.getElementById('start_at').value = toDateTimeLocal(startDate || new Date());
    document.getElementById('end_at').value = '';
    document.getElementById('eventForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function postMoveEvent(payload, revertFn) {
    const form = new URLSearchParams();
    form.append('action', 'move_event');
    form.append('event_id', payload.event_id);
    form.append('start_at', payload.start_at);
    form.append('end_at', payload.end_at || '');
    form.append('is_all_day', payload.is_all_day ? '1' : '0');

    fetch('calendar_event_ajax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: form.toString()
    }).then(r => r.json()).then(res => {
        if (!res || Number(res.ok) !== 1) {
            if (typeof revertFn === 'function') revertFn();
            alert(res && res.message ? res.message : 'อัปเดตกิจกรรมไม่สำเร็จ');
        }
    }).catch(() => {
        if (typeof revertFn === 'function') revertFn();
        alert('อัปเดตกิจกรรมไม่สำเร็จ');
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('adminFullCalendar');
    if (!el) return;

    const fcEvents = adminEvents.map(e => ({
        id: String(e.id),
        title: e.title || '',
        start: e.start_at ? String(e.start_at).replace(' ', 'T') : null,
        end: e.end_at ? String(e.end_at).replace(' ', 'T') : null,
        allDay: Number(e.is_all_day || 0) === 1,
        backgroundColor: e.color_hex || '#233882',
        borderColor: e.color_hex || '#233882',
        textColor: '#ffffff',
        extendedProps: e
    }));

    const calendar = new FullCalendar.Calendar(el, {
        initialView: 'dayGridMonth',
        height: 'auto',
        locale: 'th',
        selectable: true,
        editable: true,
        nowIndicator: true,
        dayMaxEvents: true,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: {
            today: 'วันนี้',
            month: 'เดือน',
            week: 'สัปดาห์',
            day: 'วัน',
            list: 'รายการ'
        },
        events: fcEvents,
        dateClick: function (info) {
            resetForNewEvent(info.date);
        },
        eventClick: function (info) {
            const ev = info.event.extendedProps || {};
            ev.id = info.event.id;
            ev.title = info.event.title;
            ev.start_at = toDateTimeLocal(info.event.start).replace('T', ' ');
            ev.end_at = info.event.end ? toDateTimeLocal(info.event.end).replace('T', ' ') : '';
            ev.is_all_day = info.event.allDay ? 1 : 0;
            setFormFromEvent(ev);
        },
        eventDrop: function (info) {
            postMoveEvent({
                event_id: info.event.id,
                start_at: toDateTimeLocal(info.event.start).replace('T', ' '),
                end_at: info.event.end ? toDateTimeLocal(info.event.end).replace('T', ' ') : '',
                is_all_day: info.event.allDay
            }, info.revert);
        },
        eventResize: function (info) {
            postMoveEvent({
                event_id: info.event.id,
                start_at: toDateTimeLocal(info.event.start).replace('T', ' '),
                end_at: info.event.end ? toDateTimeLocal(info.event.end).replace('T', ' ') : '',
                is_all_day: info.event.allDay
            }, info.revert);
        }
    });

    calendar.render();
});
</script>
</body>
</html>
