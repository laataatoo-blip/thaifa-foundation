<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$MM_authorizedUsers = "Admin";
$MM_donotCheckaccess = "false";
include('./components/funcCheckSession.php');

include_once(__DIR__ . '/../backend/classes/ContactMessageManagement.class.php');

$contact = new ContactMessageManagement();

function h($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = trim((string)($_POST['action'] ?? ''));
    try {
        if ($action === 'update_status') {
            $id = (int)($_POST['message_id'] ?? 0);
            $status = trim((string)($_POST['status'] ?? 'new'));
            $adminNote = trim((string)($_POST['admin_note'] ?? ''));
            $contact->updateStatus($id, $status, $adminNote);
            $success = 'อัปเดตสถานะข้อความเรียบร้อย';
        }
    } catch (Throwable $e) {
        $errors[] = $e->getMessage();
    }
}

$statusFilter = trim((string)($_GET['status'] ?? ''));
$messages = $contact->listMessages($statusFilter);
$stats = $contact->stats();
$statusMap = ContactMessageManagement::statusMap();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include('./structure/head.php') ?>
    <title>ข้อความจากหน้า Contact</title>
    <style>
        .contact-filter {
            min-width: 180px;
        }
        .contact-action-cell {
            min-width: 360px;
        }
        .contact-row-form {
            display: grid;
            grid-template-columns: 140px minmax(140px, 1fr) auto;
            gap: .35rem;
            align-items: center;
        }
        .contact-row-form .btn {
            white-space: nowrap;
        }
        @media (max-width: 1400px) {
            .contact-row-form {
                grid-template-columns: 140px minmax(140px, 1fr);
            }
            .contact-row-form .btn {
                grid-column: 1 / -1;
            }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include('./components/sidebar.php') ?>
    <?php include('./components/navbar.php') ?>

    <div class="page-wrapper"><div class="page-content-wrapper page-content-margin-padding"><div class="page-content page-content-margin-padding">

        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Contact</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="index.php"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active">ข้อความติดต่อ</li>
                    </ol>
                </nav>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><?php foreach ($errors as $e): ?><div><?= h($e) ?></div><?php endforeach; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= h($success) ?></div>
        <?php endif; ?>

        <div class="row row-cols-1 row-cols-md-4 g-3 mb-3">
            <div class="col"><div class="thaifa-stat"><div class="label">ข้อความทั้งหมด</div><div class="value mt-2"><?= (int)$stats['total'] ?></div></div></div>
            <div class="col"><div class="thaifa-stat"><div class="label">ข้อความใหม่</div><div class="value mt-2"><?= (int)$stats['new'] ?></div></div></div>
            <div class="col"><div class="thaifa-stat"><div class="label">กำลังดำเนินการ</div><div class="value mt-2"><?= (int)$stats['in_progress'] ?></div></div></div>
            <div class="col"><div class="thaifa-stat"><div class="label">ตอบแล้ว</div><div class="value mt-2"><?= (int)$stats['replied'] ?></div></div></div>
        </div>

        <div class="card thaifa-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">รายการข้อความจากหน้า Contact</h5>
                    <form method="get" class="admin-filter-bar">
                        <select name="status" class="form-select form-select-sm contact-filter">
                            <option value="">ทุกสถานะ</option>
                            <?php foreach ($statusMap as $k => $v): ?>
                                <option value="<?= h($k) ?>" <?= $statusFilter === $k ? 'selected' : '' ?>><?= h($v) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-sm btn-outline-secondary" type="submit">กรอง</button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ผู้ติดต่อ</th>
                                <th>หัวข้อ</th>
                                <th>ข้อความ</th>
                                <th>สถานะ</th>
                                <th>วันที่</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($messages)): ?>
                            <tr><td colspan="7" class="text-center text-muted">ยังไม่มีข้อความ</td></tr>
                        <?php else: ?>
                            <?php foreach ($messages as $m): ?>
                                <tr>
                                    <td><?= (int)$m['id'] ?></td>
                                    <td>
                                        <div class="fw-semibold"><?= h($m['full_name']) ?></div>
                                        <div class="small text-muted"><?= h($m['email']) ?></div>
                                    </td>
                                    <td><?= h($m['subject']) ?></td>
                                    <td>
                                        <div style="max-width: 420px; white-space: normal;">
                                            <?= nl2br(h((string)$m['message'])) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php $status = (string)($m['status'] ?? 'new'); ?>
                                        <span class="badge bg-<?= $status === 'new' ? 'danger' : ($status === 'replied' ? 'success' : ($status === 'in_progress' ? 'primary' : 'secondary')) ?>">
                                            <?= h($statusMap[$status] ?? $status) ?>
                                        </span>
                                    </td>
                                    <td><?= h((string)$m['created_at']) ?></td>
                                    <td class="contact-action-cell">
                                        <form method="post" class="contact-row-form">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="message_id" value="<?= (int)$m['id'] ?>">
                                            <select name="status" class="form-select form-select-sm">
                                                <?php foreach ($statusMap as $k => $v): ?>
                                                    <option value="<?= h($k) ?>" <?= $status === $k ? 'selected' : '' ?>><?= h($v) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <input type="text" name="admin_note" class="form-control form-control-sm" placeholder="หมายเหตุแอดมิน" value="<?= h((string)($m['admin_note'] ?? '')) ?>">
                                            <button class="btn btn-sm btn-primary" type="submit">บันทึก</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div></div></div>
</div>
<div class="overlay toggle-btn-mobile"></div>
<a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
<?php include('./structure/script.php') ?>
</body>
</html>
