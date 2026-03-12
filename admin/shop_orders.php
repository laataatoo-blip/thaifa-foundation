<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$MM_authorizedUsers = "Admin";
$MM_donotCheckaccess = "false";
include('./components/funcCheckSession.php');

include_once(__DIR__ . '/../backend/classes/ShopManagement.class.php');

$shop = new ShopManagement();

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'update_status') {
            $orderId = (int)($_POST['order_id'] ?? 0);
            $status = trim((string)($_POST['status'] ?? 'pending'));
            $description = trim((string)($_POST['description'] ?? ''));
            $location = trim((string)($_POST['location'] ?? ''));
            $adminId = (int)($_SESSION['AdminLogin']['AdminID'] ?? 0) ?: null;

            $shop->updateOrderStatus($orderId, $status, $description, $location, $adminId);
            $success = 'อัปเดตสถานะคำสั่งซื้อเรียบร้อย';
        }

        if ($action === 'update_tracking') {
            $orderId = (int)($_POST['order_id'] ?? 0);
            $trackingCode = trim((string)($_POST['tracking_code'] ?? ''));

            $shop->getDb()->query(
                "UPDATE shop_orders SET tracking_code = :tracking_code WHERE id = :id",
                [':tracking_code' => $trackingCode, ':id' => $orderId]
            );
            $success = 'บันทึกรหัสติดตามพัสดุเรียบร้อย';
        }
    } catch (Throwable $e) {
        $errors[] = $e->getMessage();
    }
}

$orders = $shop->listOrders();
$selectedId = (int)($_GET['id'] ?? 0);
$selectedOrderNo = trim((string)($_GET['order_no'] ?? ''));
$selected = $selectedId > 0 ? $shop->getOrderById($selectedId) : null;
if (!$selected && $selectedOrderNo !== '') {
    $selected = $shop->getOrderByNo($selectedOrderNo);
}
if (!$selected && !empty($orders)) {
    $selected = $shop->getOrderById((int)$orders[0]['id']);
}
$statusMap = ShopManagement::statusMap();
$orderCount = count($orders);
$pendingCount = 0;
$shippingCount = 0;
foreach ($orders as $o) {
    if (($o['status'] ?? '') === 'pending') $pendingCount++;
    if (in_array(($o['status'] ?? ''), ['shipping', 'out_for_delivery'], true)) $shippingCount++;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include('./structure/head.php') ?>
    <title>จัดการคำสั่งซื้อ</title>
    <style>
        .timeline-item { position: relative; padding-left: 24px; margin-bottom: 1rem; }
        .timeline-item:before { content: ''; position: absolute; left: 6px; top: 6px; width: 10px; height: 10px; border-radius: 50%; background: #0d6efd; }
        .timeline-item:after { content: ''; position: absolute; left: 10px; top: 18px; width: 2px; height: calc(100% + 6px); background: #dbe2ef; }
        .timeline-item:last-child:after { display: none; }
        .order-list-wrap {
            max-height: 720px;
            overflow: auto;
        }
        .order-list-table thead th {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 1;
        }
        .order-list-table td, .order-list-table th {
            white-space: nowrap;
        }
        .order-list-table td.customer-cell {
            white-space: normal;
            min-width: 170px;
        }
        .order-row-link {
            cursor: pointer;
        }
        .order-row-link.is-selected {
            background: #eef4ff;
        }
        .order-row-link:hover {
            background: #f7faff;
        }
        .order-detail-btn {
            min-width: 110px;
            white-space: nowrap;
        }
        .order-info-grid .row > div {
            margin-bottom: .35rem;
        }
        .order-info-value {
            background: #f8fbff;
            border: 1px solid #d9e7ef;
            border-radius: 10px;
            padding: .45rem .65rem;
            min-height: 42px;
            display: flex;
            align-items: flex-start;
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
                    <div class="breadcrumb-title pe-3">Shop</div>
                    <div class="ps-3">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0">
                                <li class="breadcrumb-item"><a href="index.php"><i class="bx bx-home-alt"></i></a></li>
                                <li class="breadcrumb-item active" aria-current="page">คำสั่งซื้อ</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-md-3 g-3 mb-3">
                    <div class="col"><div class="thaifa-stat"><div class="label">คำสั่งซื้อทั้งหมด</div><div class="value mt-2"><?= (int)$orderCount ?></div></div></div>
                    <div class="col"><div class="thaifa-stat"><div class="label">รอตรวจสอบ</div><div class="value mt-2"><?= (int)$pendingCount ?></div></div></div>
                    <div class="col"><div class="thaifa-stat"><div class="label">กำลังจัดส่ง</div><div class="value mt-2"><?= (int)$shippingCount ?></div></div></div>
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

                <div class="row g-3">
                    <div class="col-xl-5">
                        <div class="card thaifa-card">
                            <div class="card-body">
                                <h5 class="mb-3">รายการคำสั่งซื้อ</h5>
                                <div class="table-responsive order-list-wrap">
                                    <table class="table table-hover align-middle order-list-table">
                                        <thead>
                                            <tr>
                                                <th>Order No</th>
                                                <th>ลูกค้า</th>
                                                <th>ยอดรวม</th>
                                                <th>สถานะ</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (empty($orders)): ?>
                                            <tr><td colspan="5" class="text-center text-muted">ยังไม่มีคำสั่งซื้อ</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($orders as $o): ?>
                                                <?php
                                                $isSelectedRow = isset($selected['order']['id']) && (int)$selected['order']['id'] === (int)$o['id'];
                                                $detailUrl = 'shop_orders.php?id=' . (int)$o['id'] . '#order-detail';
                                                ?>
                                                <tr class="order-row-link<?= $isSelectedRow ? ' is-selected' : '' ?>" data-order-url="<?= h($detailUrl) ?>">
                                                    <td class="fw-semibold"><?= h($o['order_no']) ?></td>
                                                    <td class="customer-cell">
                                                        <div><?= h($o['customer_name']) ?></div>
                                                        <small class="text-muted"><?= h($o['customer_phone']) ?></small>
                                                    </td>
                                                    <td><?= number_format((float)$o['total_amount'], 2) ?></td>
                                                    <td>
                                                        <span class="badge bg-<?= h(ShopManagement::statusClass($o['status'])) ?>">
                                                            <?= h(ShopManagement::statusLabel($o['status'])) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-sm btn-outline-primary order-detail-btn" href="<?= h($detailUrl) ?>" onclick="event.stopPropagation();">
                                                            ดูรายละเอียด
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-7">
                        <?php if (!$selected): ?>
                            <div class="card"><div class="card-body text-muted">เลือกคำสั่งซื้อเพื่อดูรายละเอียด</div></div>
                        <?php else: ?>
                            <?php $order = $selected['order']; ?>
                            <div class="card thaifa-card mb-3" id="order-detail">
                                <div class="card-body">
                                    <h5 class="mb-3">รายละเอียดคำสั่งซื้อ <?= h($order['order_no']) ?></h5>
                                    <div class="order-info-grid">
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <div class="small text-muted mb-1">ชื่อลูกค้า</div>
                                                <div class="order-info-value fw-semibold"><?= h($order['customer_name']) ?></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="small text-muted mb-1">เบอร์โทร</div>
                                                <div class="order-info-value fw-semibold"><?= h($order['customer_phone']) ?></div>
                                            </div>
                                            <div class="col-12">
                                                <div class="small text-muted mb-1">ที่อยู่</div>
                                                <div class="order-info-value"><?= nl2br(h($order['customer_address'])) ?></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="small text-muted mb-1">สถานะ</div>
                                                <div class="order-info-value fw-semibold"><?= h(ShopManagement::statusLabel($order['status'])) ?></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="small text-muted mb-1">ยอดรวม</div>
                                                <div class="order-info-value fw-semibold"><?= number_format((float)$order['total_amount'], 2) ?> บาท</div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <h6>สินค้าในออเดอร์</h6>
                                    <ul class="mb-0">
                                        <?php foreach ($selected['items'] as $it): ?>
                                            <li><?= h($it['product_name']) ?> x <?= (int)$it['qty'] ?> = <?= number_format((float)$it['line_total'], 2) ?> บาท</li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>

                            <div class="card thaifa-card mb-3">
                                <div class="card-body">
                                    <h6 class="mb-3">อัปเดตสถานะ (สไตล์ Shopee tracking)</h6>
                                    <form method="post" class="row g-2">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                                        <div class="col-md-6">
                                            <label class="form-label">สถานะใหม่</label>
                                            <select name="status" class="form-select" required>
                                                <?php foreach ($statusMap as $code => $label): ?>
                                                    <option value="<?= h($code) ?>" <?= ($order['status'] === $code) ? 'selected' : '' ?>><?= h($label) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">สถานที่ / จุดพัสดุ</label>
                                            <input type="text" name="location" class="form-control" placeholder="เช่น ศูนย์คัดแยก กรุงเทพฯ">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">หมายเหตุ</label>
                                            <textarea name="description" rows="2" class="form-control" placeholder="ข้อความที่ผู้ใช้จะเห็นใน timeline"></textarea>
                                        </div>
                                        <div class="col-12"><button class="btn btn-primary" type="submit">บันทึกสถานะ</button></div>
                                    </form>

                                    <hr>
                                    <form method="post" class="row g-2 align-items-end">
                                        <input type="hidden" name="action" value="update_tracking">
                                        <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                                        <div class="col-md-8">
                                            <label class="form-label">Tracking Code</label>
                                            <input type="text" name="tracking_code" class="form-control" value="<?= h($order['tracking_code']) ?>" placeholder="TH1234567890">
                                        </div>
                                        <div class="col-md-4"><button class="btn btn-outline-primary w-100" type="submit">บันทึกรหัสพัสดุ</button></div>
                                    </form>
                                </div>
                            </div>

                            <div class="card thaifa-card">
                                <div class="card-body">
                                    <h6 class="mb-3">ไทม์ไลน์สถานะ</h6>
                                    <?php if (empty($selected['logs'])): ?>
                                        <p class="text-muted mb-0">ยังไม่มีประวัติสถานะ</p>
                                    <?php else: ?>
                                        <?php foreach ($selected['logs'] as $log): ?>
                                            <div class="timeline-item">
                                                <div class="fw-semibold"><?= h($log['title']) ?></div>
                                                <div class="small text-muted"><?= h($log['created_at']) ?><?= $log['location'] ? ' • ' . h($log['location']) : '' ?></div>
                                                <?php if (!empty($log['description'])): ?>
                                                    <div class="small mt-1"><?= nl2br(h($log['description'])) ?></div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="overlay toggle-btn-mobile"></div>
<a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
<?php include('./structure/script.php') ?>
<script>
document.querySelectorAll('.order-row-link').forEach(function (row) {
    row.addEventListener('click', function () {
        var url = row.getAttribute('data-order-url');
        if (url) {
            window.location.href = url;
        }
    });
});
</script>
</body>
</html>
