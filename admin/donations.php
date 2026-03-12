<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$MM_authorizedUsers = "Admin";
$MM_donotCheckaccess = "false";
include('./components/funcCheckSession.php');

include_once(__DIR__ . '/../backend/classes/DonationManagement.class.php');

$donation = new DonationManagement();
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'update_status') {
            $id = (int)($_POST['donation_id'] ?? 0);
            $status = trim((string)($_POST['status'] ?? 'pending'));
            $note = trim((string)($_POST['note'] ?? ''));
            $donation->updateDonationStatus($id, $status, $note);
            $success = 'อัปเดตสถานะการบริจาคเรียบร้อย';
        }
    } catch (Throwable $e) {
        $errors[] = $e->getMessage();
    }
}

$statusFilter = trim((string)($_GET['status'] ?? ''));
$rows = $donation->listDonations($statusFilter);
$stats = $donation->donationStats();
$statusMap = DonationManagement::statusMap();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include('./structure/head.php') ?>
    <title>จัดการบริจาคออนไลน์</title>
    <style>
        .donation-filter { min-width: 180px; }
        .donation-action-cell { min-width: 300px; }
        .donation-row-form {
            display: grid;
            grid-template-columns: 132px minmax(120px, 1fr) auto;
            gap: .35rem;
            align-items: center;
        }
        .donation-row-form .btn { white-space: nowrap; }
        @media (max-width: 1400px) {
            .donation-row-form { grid-template-columns: 132px minmax(120px, 1fr); }
            .donation-row-form .btn { grid-column: 1 / -1; }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include('./components/sidebar.php') ?>
    <?php include('./components/navbar.php') ?>

    <div class="page-wrapper"><div class="page-content-wrapper page-content-margin-padding"><div class="page-content page-content-margin-padding">

        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Donation</div>
            <div class="ps-3"><nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 p-0"><li class="breadcrumb-item"><a href="index.php"><i class="bx bx-home-alt"></i></a></li><li class="breadcrumb-item active">บริจาคออนไลน์</li></ol></nav></div>
        </div>
        <?php if (!empty($errors)): ?><div class="alert alert-danger"><?php foreach($errors as $e): ?><div><?= h($e) ?></div><?php endforeach; ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

        <div class="row row-cols-1 row-cols-md-4 g-3 mb-3">
            <div class="col"><div class="thaifa-stat"><div class="label">รายการบริจาคทั้งหมด</div><div class="value mt-2"><?= (int)$stats['total_count'] ?></div></div></div>
            <div class="col"><div class="thaifa-stat"><div class="label">ยอดบริจาครวม</div><div class="value mt-2"><?= number_format((float)$stats['total_amount'],0) ?></div></div></div>
            <div class="col"><div class="thaifa-stat"><div class="label">ชำระแล้ว/ยืนยันแล้ว</div><div class="value mt-2"><?= (int)$stats['paid_count'] ?></div></div></div>
            <div class="col"><div class="thaifa-stat"><div class="label">รอตรวจสอบ</div><div class="value mt-2"><?= (int)$stats['pending_count'] ?></div></div></div>
        </div>

        <div class="card thaifa-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">รายการบริจาค</h5>
                    <form method="get" class="admin-filter-bar">
                        <select name="status" class="form-select form-select-sm donation-filter">
                            <option value="">ทุกสถานะ</option>
                            <?php foreach($statusMap as $k=>$v): ?><option value="<?= h($k) ?>" <?= $statusFilter===$k?'selected':'' ?>><?= h($v) ?></option><?php endforeach; ?>
                        </select>
                        <button class="btn btn-sm btn-outline-secondary" type="submit">กรอง</button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead><tr><th>#</th><th>ผู้บริจาค</th><th>โครงการ</th><th>จำนวนเงิน</th><th>ประเภท</th><th>สถานะ</th><th>วันที่</th><th>จัดการ</th></tr></thead>
                        <tbody>
                        <?php if (empty($rows)): ?>
                            <tr><td colspan="8" class="text-center text-muted">ยังไม่มีข้อมูลบริจาค</td></tr>
                        <?php else: ?>
                            <?php foreach ($rows as $r): ?>
                                <tr>
                                    <td><?= (int)$r['id'] ?></td>
                                    <td>
                                        <div class="fw-semibold"><?= h($r['donor_name']) ?></div>
                                        <div class="small text-muted"><?= h($r['donor_phone']) ?> <?= h($r['donor_email']) ?></div>
                                    </td>
                                    <td><?= h($r['campaign_name'] ?: '-') ?></td>
                                    <td><?= number_format((float)$r['amount'],2) ?></td>
                                    <td><?= $r['donation_type']==='monthly' ? 'รายเดือน' : 'ครั้งเดียว' ?></td>
                                    <td><span class="badge bg-<?= in_array($r['status'],['paid','verified'],true)?'success':($r['status']==='pending'?'warning':'secondary') ?>"><?= h($statusMap[$r['status']] ?? $r['status']) ?></span></td>
                                    <td><?= h($r['created_at']) ?></td>
                                    <td class="donation-action-cell">
                                        <form method="post" class="donation-row-form">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="donation_id" value="<?= (int)$r['id'] ?>">
                                            <select name="status" class="form-select form-select-sm">
                                                <?php foreach($statusMap as $k=>$v): ?><option value="<?= h($k) ?>" <?= $r['status']===$k?'selected':'' ?>><?= h($v) ?></option><?php endforeach; ?>
                                            </select>
                                            <input type="text" name="note" class="form-control form-control-sm" placeholder="หมายเหตุ" value="<?= h($r['note']) ?>">
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
