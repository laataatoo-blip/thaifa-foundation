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
        if ($action === 'save_product') {
            $id = (int)($_POST['product_id'] ?? 0);
            $name = trim((string)($_POST['name'] ?? ''));
            if ($name === '') {
                throw new Exception('กรุณากรอกชื่อสินค้า');
            }

            $shop->upsertProduct($id, [
                'category_id' => $_POST['category_id'] ?? null,
                'name' => $name,
                'slug' => $_POST['slug'] ?? '',
                'description' => $_POST['description'] ?? '',
                'price' => $_POST['price'] ?? 0,
                'stock_qty' => $_POST['stock_qty'] ?? 0,
                'cover_image' => $_POST['cover_image'] ?? '',
                'is_active' => $_POST['is_active'] ?? 0,
            ]);

            $success = 'บันทึกข้อมูลสินค้าเรียบร้อย';
        }

        if ($action === 'toggle_active') {
            $id = (int)($_POST['product_id'] ?? 0);
            $active = (int)($_POST['is_active'] ?? 0);
            $shop->getDb()->query(
                "UPDATE shop_products SET is_active = :is_active WHERE id = :id",
                [':is_active' => $active, ':id' => $id]
            );
            $success = 'อัปเดตสถานะสินค้าเรียบร้อย';
        }

        if ($action === 'adjust_stock') {
            $id = (int)($_POST['product_id'] ?? 0);
            $mode = trim((string)($_POST['stock_mode'] ?? 'add'));
            $qty = (int)($_POST['stock_qty_change'] ?? 0);
            $note = trim((string)($_POST['stock_note'] ?? ''));
            $adminId = (int)($_SESSION['AdminLogin']['AdminID'] ?? 0) ?: null;

            $shop->adjustProductStock($id, $mode, $qty, $adminId, $note);
            $success = 'ปรับยอดคงเหลือสินค้าเรียบร้อย';
        }
    } catch (Throwable $e) {
        $errors[] = $e->getMessage();
    }
}

$categories = $shop->getCategories(false);
$products = $shop->getProducts(false);
$stockMovements = $shop->getStockMovements(0, 25);
$productCount = count($products);
$activeCount = 0;
$lowStockCount = 0;
foreach ($products as $p) {
    if ((int)$p['is_active'] === 1) $activeCount++;
    if ((int)$p['stock_qty'] <= 10) $lowStockCount++;
}

$editId = (int)($_GET['edit_id'] ?? 0);
$editProduct = $editId > 0 ? $shop->getProductById($editId) : null;

if (!$editProduct) {
    $editProduct = [
        'id' => 0,
        'category_id' => '',
        'name' => '',
        'slug' => '',
        'description' => '',
        'price' => '0.00',
        'stock_qty' => 0,
        'cover_image' => '',
        'is_active' => 1,
    ];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include('./structure/head.php') ?>
    <title>จัดการสินค้า</title>
    <style>
        .shop-products-page {
            max-width: 1320px;
            margin: 0 auto;
        }
        .shop-products-page .thaifa-card {
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(48, 58, 86, 0.08);
        }
        .shop-products-page .thaifa-card .card-body {
            padding: .9rem 1rem;
        }
        .shop-products-page .form-label {
            margin-bottom: .3rem;
            font-size: 13px;
            font-weight: 600;
            color: #435071;
        }
        .shop-products-page .form-control,
        .shop-products-page .form-select {
            min-height: 38px;
            padding-top: .35rem;
            padding-bottom: .35rem;
            font-size: 14px;
            border-radius: 10px;
        }
        .shop-products-page textarea.form-control {
            min-height: 96px;
        }
        .shop-products-page .btn {
            border-radius: 10px;
            font-size: 13px;
            padding: .35rem .7rem;
        }
        .shop-products-page .btn-sm {
            padding: .28rem .58rem;
            font-size: 12px;
        }
        .shop-products-page .btn-close-sale {
            color: #8b1e2b;
            border-color: #8b1e2b;
            background: #fff;
        }
        .shop-products-page .btn-close-sale:hover,
        .shop-products-page .btn-close-sale:focus {
            color: #fff;
            background: #8b1e2b;
            border-color: #8b1e2b;
        }
        .shop-products-page .thaifa-stat {
            min-height: 98px;
            padding: 12px 14px;
            border-radius: 12px;
        }
        .shop-products-page .thaifa-stat .label {
            font-size: 12px;
        }
        .shop-products-page .thaifa-stat .value {
            font-size: 28px;
        }
        .product-table {
            width: 100%;
            table-layout: fixed;
        }
        .product-table thead th {
            white-space: normal;
        }
        .product-thumb {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            flex-shrink: 0;
        }
        .product-name-wrap {
            max-width: 230px;
        }
        .product-name-wrap .title {
            line-height: 1.25;
            font-size: 14px;
        }
        .product-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .28rem;
            margin-bottom: .35rem;
        }
        .stock-adjust-row {
            display: flex;
            flex-wrap: wrap;
            gap: .28rem;
            align-items: center;
        }
        .stock-adjust-row > .form-select {
            flex: 0 0 82px;
            min-width: 82px;
        }
        .stock-adjust-row > input[type="number"] {
            flex: 0 0 62px;
            min-width: 62px;
        }
        .stock-adjust-row > input[type="text"] {
            flex: 1 1 120px;
            min-width: 88px;
        }
        .stock-adjust-row .btn {
            width: 100%;
            white-space: nowrap;
        }
        .stock-label {
            font-size: 10px;
            color: #6b7280;
            margin-top: 2px;
        }
        .product-table td,
        .product-table th {
            padding: .45rem .45rem;
            font-size: 13px;
            vertical-align: top;
        }
        .product-table th:nth-child(1),
        .product-table td:nth-child(1) { width: 44px; }
        .product-table th:nth-child(2),
        .product-table td:nth-child(2) { width: 28%; }
        .product-table th:nth-child(3),
        .product-table td:nth-child(3) { width: 9%; }
        .product-table th:nth-child(4),
        .product-table td:nth-child(4) { width: 7%; }
        .product-table th:nth-child(5),
        .product-table td:nth-child(5) { width: 8%; }
        .product-table th:nth-child(6),
        .product-table td:nth-child(6) { width: 38%; }
        .product-table .badge {
            font-size: 11px;
            padding: .25rem .42rem;
            border-radius: 999px;
        }
        @media (min-width: 1200px) {
            .shop-products-page .table-responsive {
                max-height: 640px;
                overflow-y: auto;
            }
            .shop-products-page .table-responsive table thead th {
                position: sticky;
                top: 0;
                z-index: 2;
                background: #f8fbff;
            }
        }
        @media (max-width: 1400px) {
            .product-table th:nth-child(2),
            .product-table td:nth-child(2) { width: 24%; }
            .product-table th:nth-child(6),
            .product-table td:nth-child(6) { width: 42%; }
        }
        @media (max-width: 1200px) {
            .product-table {
                table-layout: auto;
            }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include('./components/sidebar.php') ?>
    <?php include('./components/navbar.php') ?>

    <div class="page-wrapper">
        <div class="page-content-wrapper page-content-margin-padding">
            <div class="page-content page-content-margin-padding shop-products-page">

                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <div class="breadcrumb-title pe-3">Shop</div>
                    <div class="ps-3">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0">
                                <li class="breadcrumb-item"><a href="index.php"><i class="bx bx-home-alt"></i></a></li>
                                <li class="breadcrumb-item active" aria-current="page">สินค้า</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-md-3 g-3 mb-3">
                    <div class="col"><div class="thaifa-stat"><div class="label">สินค้าทั้งหมด</div><div class="value mt-2"><?= (int)$productCount ?></div></div></div>
                    <div class="col"><div class="thaifa-stat"><div class="label">สินค้าเปิดขาย</div><div class="value mt-2"><?= (int)$activeCount ?></div></div></div>
                    <div class="col"><div class="thaifa-stat"><div class="label">สินค้าใกล้หมด (<=10)</div><div class="value mt-2"><?= (int)$lowStockCount ?></div></div></div>
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
                    <div class="col-xl-4">
                        <div class="card thaifa-card">
                            <div class="card-body">
                                <h5 class="mb-3"><?= (int)$editProduct['id'] > 0 ? 'แก้ไขสินค้า' : 'เพิ่มสินค้าใหม่' ?></h5>
                                <form method="post">
                                    <input type="hidden" name="action" value="save_product">
                                    <input type="hidden" name="product_id" value="<?= (int)$editProduct['id'] ?>">

                                    <div class="mb-3">
                                        <label class="form-label">หมวดหมู่</label>
                                        <select name="category_id" class="form-select">
                                            <option value="">ไม่ระบุ</option>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?= (int)$cat['id'] ?>" <?= ((int)$editProduct['category_id'] === (int)$cat['id']) ? 'selected' : '' ?>>
                                                    <?= h($cat['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">ชื่อสินค้า</label>
                                        <input type="text" class="form-control" name="name" value="<?= h($editProduct['name']) ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Slug</label>
                                        <input type="text" class="form-control" name="slug" value="<?= h($editProduct['slug']) ?>" placeholder="thaifa-shirt">
                                    </div>

                                    <div class="row g-2">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">ราคา</label>
                                            <input type="number" step="0.01" min="0" class="form-control" name="price" value="<?= h($editProduct['price']) ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">สต็อก</label>
                                            <input type="number" min="0" class="form-control" name="stock_qty" value="<?= h($editProduct['stock_qty']) ?>" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">รูปปก (URL)</label>
                                        <input type="url" class="form-control" name="cover_image" value="<?= h($editProduct['cover_image']) ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">รายละเอียด</label>
                                        <textarea class="form-control" rows="4" name="description"><?= h($editProduct['description']) ?></textarea>
                                    </div>

                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?= !empty($editProduct['is_active']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_active">เปิดใช้งานสินค้า</label>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">บันทึกสินค้า</button>
                                        <a href="shop_products.php" class="btn btn-outline-secondary">เพิ่มใหม่</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-8">
                        <div class="card thaifa-card">
                            <div class="card-body">
                                <h5 class="mb-3">รายการสินค้า</h5>
                                <div class="table-responsive">
                                    <table class="table align-middle table-hover product-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>สินค้า</th>
                                                <th>ราคา</th>
                                                <th>สต็อก</th>
                                                <th>สถานะ</th>
                                                <th>จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($products)): ?>
                                                <tr><td colspan="6" class="text-center text-muted">ยังไม่มีสินค้า</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($products as $p): ?>
                                                <tr>
                                                    <td><?= (int)$p['id'] ?></td>
                                                    <td>
                                                            <div class="d-flex gap-2 align-items-start">
                                                                <img src="<?= h($p['cover_image'] ?: 'https://via.placeholder.com/64x64?text=No+Image') ?>" class="product-thumb" alt="<?= h($p['name']) ?>">
                                                                <div class="product-name-wrap">
                                                                    <div class="fw-semibold title"><?= h($p['name']) ?></div>
                                                                    <small class="text-muted d-block"><?= h($p['category_name'] ?: 'ไม่ระบุหมวดหมู่') ?></small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><?= number_format((float)$p['price'], 2) ?></td>
                                                        <td><?= (int)$p['stock_qty'] ?></td>
                                                        <td>
                                                            <span class="badge <?= ((int)$p['is_active'] === 1) ? 'bg-success' : 'bg-secondary' ?>">
                                                                <?= ((int)$p['is_active'] === 1) ? 'แสดง' : 'ปิด' ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="product-actions">
                                                                <a href="shop_products.php?edit_id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-outline-primary">แก้ไข</a>
                                                                <form method="post" class="d-inline">
                                                                    <input type="hidden" name="action" value="toggle_active">
                                                                    <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                                                                    <input type="hidden" name="is_active" value="<?= ((int)$p['is_active'] === 1) ? 0 : 1 ?>">
                                                                    <button type="submit" class="btn btn-sm <?= ((int)$p['is_active'] === 1) ? 'btn-close-sale' : 'btn-outline-success' ?>">
                                                                        <?= ((int)$p['is_active'] === 1) ? 'ปิดขาย' : 'เปิดขาย' ?>
                                                                    </button>
                                                                </form>
                                                            </div>

                                                            <form method="post">
                                                                <input type="hidden" name="action" value="adjust_stock">
                                                                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                                                                <div class="stock-adjust-row">
                                                                    <select name="stock_mode" class="form-select form-select-sm">
                                                                        <option value="add">เพิ่ม</option>
                                                                        <option value="subtract">ลด</option>
                                                                        <option value="set">ตั้งค่า</option>
                                                                    </select>
                                                                    <input type="number" name="stock_qty_change" class="form-control form-control-sm" min="0" value="1" required>
                                                                    <input type="text" name="stock_note" class="form-control form-control-sm" placeholder="หมายเหตุ">
                                                                    <button type="submit" class="btn btn-sm btn-dark">อัปเดตสต็อก</button>
                                                                </div>
                                                                <div class="stock-label">ปรับสต็อกแบบยืดหยุ่น: เพิ่ม / ลด / ตั้งค่าใหม่</div>
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
                    </div>
                </div>

                <div class="card thaifa-card mt-3">
                    <div class="card-body">
                        <h5 class="mb-3">ประวัติปรับยอดคงเหลือล่าสุด</h5>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead>
                                <tr>
                                    <th>เวลา</th>
                                    <th>สินค้า</th>
                                    <th>ประเภท</th>
                                    <th>ก่อน</th>
                                    <th>เปลี่ยน</th>
                                    <th>หลัง</th>
                                    <th>หมายเหตุ</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (empty($stockMovements)): ?>
                                    <tr><td colspan="7" class="text-center text-muted">ยังไม่มีประวัติ</td></tr>
                                <?php else: ?>
                                    <?php foreach ($stockMovements as $m): ?>
                                        <tr>
                                            <td><?= h($m['created_at']) ?></td>
                                            <td><?= h($m['product_name']) ?></td>
                                            <td><?= h($m['movement_type']) ?></td>
                                            <td><?= (int)$m['qty_before'] ?></td>
                                            <td class="<?= ((int)$m['qty_change'] >= 0) ? 'text-success' : 'text-danger' ?>">
                                                <?= ((int)$m['qty_change'] >= 0 ? '+' : '') . (int)$m['qty_change'] ?>
                                            </td>
                                            <td><strong><?= (int)$m['qty_after'] ?></strong></td>
                                            <td><?= h($m['note']) ?></td>
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
