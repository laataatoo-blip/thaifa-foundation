<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$MM_authorizedUsers = "Admin";
$MM_donotCheckaccess = "false";
include('./components/funcCheckSession.php');

include_once(__DIR__ . '/../backend/classes/AdminSecurityManagement.class.php');

$security = new AdminSecurityManagement();

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function deviceLabelFromUa($ua)
{
    $ua = strtolower((string)$ua);
    if ($ua === '') return 'Unknown device';
    if (strpos($ua, 'iphone') !== false || strpos($ua, 'ipad') !== false || strpos($ua, 'ios') !== false) return 'Apple iOS';
    if (strpos($ua, 'android') !== false) return 'Android';
    if (strpos($ua, 'macintosh') !== false || strpos($ua, 'mac os') !== false) return 'Mac';
    if (strpos($ua, 'windows') !== false) return 'Windows PC';
    if (strpos($ua, 'linux') !== false) return 'Linux';
    return 'Other device';
}

$success = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'revoke_session') {
    $revokeSessionId = trim((string)($_POST['session_id'] ?? ''));
    try {
        if ($revokeSessionId === '') {
            throw new Exception('ไม่พบเซสชันที่ต้องการยกเลิก');
        }
        $security->revokeSessionById($revokeSessionId);
        $success = 'ยกเลิกเซสชันเรียบร้อยแล้ว';
    } catch (Throwable $e) {
        $errors[] = $e->getMessage();
    }
}

$summary = $security->getSecuritySummary();
$accounts = $security->getAdminAccounts();
$activeSessions = $security->getActiveSessions(10080, false); // 7 days
$logs = $security->getAccessLogs(300);
$currentSid = session_id();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include('./structure/head.php') ?>
    <title>Admin Access Audit</title>
    <style>
        .audit-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }
        .audit-session-card {
            border: 1px solid #dbe8f2;
            border-radius: 12px;
            padding: 10px 12px;
            background: #fff;
        }
        .audit-session-card.me {
            border-color: #3357a3;
            box-shadow: 0 8px 18px rgba(51, 87, 163, .15);
        }
        .audit-chip {
            border: 1px solid #dbe8f2;
            background: #f8fbff;
            border-radius: 999px;
            font-size: 11px;
            padding: .15rem .5rem;
            color: #334155;
            display: inline-block;
        }
        .audit-session-meta {
            font-size: 12px;
            color: #64748b;
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
                    <div class="breadcrumb-title pe-3">Security</div>
                    <div class="ps-3">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0">
                                <li class="breadcrumb-item"><a href="index.php"><i class="bx bx-home-alt"></i></a></li>
                                <li class="breadcrumb-item active" aria-current="page">Admin Access Audit</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger"><?php foreach ($errors as $e): ?><div><?= h($e) ?></div><?php endforeach; ?></div>
                <?php endif; ?>
                <?php if ($success !== ''): ?>
                    <div class="alert alert-success"><?= h($success) ?></div>
                <?php endif; ?>

                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-5 g-3 mb-3">
                    <div class="col"><div class="thaifa-stat"><div class="label">บัญชีแอดมินทั้งหมด</div><div class="value mt-2"><?= (int)$summary['admin_total'] ?></div></div></div>
                    <div class="col"><div class="thaifa-stat"><div class="label">บัญชีที่เปิดใช้งาน</div><div class="value mt-2"><?= (int)$summary['admin_active'] ?></div></div></div>
                    <div class="col"><div class="thaifa-stat"><div class="label">อุปกรณ์ที่ยังออนไลน์</div><div class="value mt-2"><?= (int)$summary['active_sessions'] ?></div></div></div>
                    <div class="col"><div class="thaifa-stat"><div class="label">Login สำเร็จ (24 ชม.)</div><div class="value mt-2"><?= (int)$summary['success_24h'] ?></div></div></div>
                    <div class="col"><div class="thaifa-stat"><div class="label">Login ไม่สำเร็จ (24 ชม.)</div><div class="value mt-2"><?= (int)$summary['failed_24h'] ?></div></div></div>
                </div>

                <div class="row g-3">
                    <div class="col-xl-4">
                        <div class="card thaifa-card h-100">
                            <div class="card-header py-3"><h5 class="mb-0 thaifa-section-title">บัญชีที่เข้าถึง Backend ได้</h5></div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle">
                                        <thead><tr><th>#</th><th>ชื่อ</th><th>Username</th><th>สถานะ</th></tr></thead>
                                        <tbody>
                                        <?php foreach ($accounts as $acc): ?>
                                            <tr>
                                                <td><?= (int)$acc['AdminID'] ?></td>
                                                <td><?= h($acc['Name']) ?></td>
                                                <td><?= h($acc['Username']) ?></td>
                                                <td>
                                                    <?php if (($acc['isActive'] ?? '') === 'Y'): ?>
                                                        <span class="badge bg-success">ใช้งาน</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">ปิดใช้งาน</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-8">
                        <div class="card thaifa-card h-100">
                            <div class="card-header py-3"><h5 class="mb-0 thaifa-section-title">อุปกรณ์/เซสชันที่ใช้งานอยู่</h5></div>
                            <div class="card-body">
                                <?php if (empty($activeSessions)): ?>
                                    <div class="text-muted">ยังไม่พบเซสชันที่ใช้งาน</div>
                                <?php else: ?>
                                    <div class="audit-grid">
                                        <?php foreach ($activeSessions as $s): ?>
                                            <?php $isMe = $currentSid !== '' && $currentSid === (string)$s['session_id']; ?>
                                            <div class="audit-session-card<?= $isMe ? ' me' : '' ?>">
                                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                                                    <div>
                                                        <span class="fw-semibold"><?= h($s['admin_name'] ?: $s['username']) ?></span>
                                                        <span class="audit-chip ms-1"><?= h(deviceLabelFromUa($s['user_agent'] ?? '')) ?></span>
                                                        <?php if ($isMe): ?><span class="badge bg-primary ms-1">เซสชันนี้</span><?php endif; ?>
                                                    </div>
                                                    <form method="post" class="m-0">
                                                        <input type="hidden" name="action" value="revoke_session">
                                                        <input type="hidden" name="session_id" value="<?= h($s['session_id']) ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" <?= $isMe ? 'disabled' : '' ?>>
                                                            ยกเลิกเซสชัน
                                                        </button>
                                                    </form>
                                                </div>
                                                <div class="audit-session-meta">
                                                    IP: <?= h($s['ip_address'] ?: '-') ?> |
                                                    ล่าสุด: <?= h($s['last_seen']) ?> |
                                                    URI: <?= h($s['last_uri'] ?: '-') ?>
                                                </div>
                                                <div class="audit-session-meta mt-1">
                                                    SID: <?= h(substr((string)$s['session_id'], 0, 24)) ?>... |
                                                    Fingerprint: <?= h(substr((string)$s['device_hash'], 0, 14)) ?>...
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card thaifa-card mt-3">
                    <div class="card-header py-3">
                        <h5 class="mb-0 thaifa-section-title">ประวัติการเข้าใช้งาน (ล่าสุด)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>เวลา</th>
                                        <th>แอดมิน</th>
                                        <th>Action</th>
                                        <th>IP</th>
                                        <th>อุปกรณ์</th>
                                        <th>หน้า</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (empty($logs)): ?>
                                    <tr><td colspan="6" class="text-center text-muted">ยังไม่มีข้อมูล</td></tr>
                                <?php else: ?>
                                    <?php foreach ($logs as $l): ?>
                                        <tr>
                                            <td><?= h($l['created_at']) ?></td>
                                            <td>
                                                <?= h($l['admin_name'] ?: '-') ?><br>
                                                <small class="text-muted"><?= h($l['admin_username'] ?: $l['username']) ?></small>
                                            </td>
                                            <td>
                                                <?php
                                                $a = (string)($l['action_key'] ?? '');
                                                $cls = 'secondary';
                                                if ($a === 'login_success') $cls = 'success';
                                                if ($a === 'login_failed') $cls = 'danger';
                                                if ($a === 'logout') $cls = 'warning';
                                                ?>
                                                <span class="badge bg-<?= h($cls) ?>"><?= h($a) ?></span>
                                            </td>
                                            <td><?= h($l['ip_address'] ?: '-') ?></td>
                                            <td><?= h(deviceLabelFromUa($l['user_agent'] ?? '')) ?></td>
                                            <td><small><?= h($l['request_uri'] ?: '-') ?></small></td>
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

<div class="overlay toggle-btn-mobile"></div>
<a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
<?php include('./structure/script.php') ?>
</body>
</html>

