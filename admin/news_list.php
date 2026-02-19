<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$MM_authorizedUsers = "Admin";
$MM_donotCheckaccess = "false";
include('./components/funcCheckSession.php');

include('./backend/classes/DatabaseManagement.class.php');
$DB = new DatabaseManagement();

/**
 * Escape helper
 */
function h($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

/**
 * Thai date short: 2025-11-01 -> 1 พ.ย. 2568
 */
function thaiDateShort($dateStr) {
    if (empty($dateStr)) return '-';
    $ts = strtotime($dateStr);
    if ($ts === false) return h($dateStr);

    $thaiMonths = [1 => 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
    $d = (int)date('j', $ts);
    $m = (int)date('n', $ts);
    $y = (int)date('Y', $ts) + 543;

    return $d . ' ' . ($thaiMonths[$m] ?? '') . ' ' . $y;
}

/**
 * Build snippet from detail (safe)
 */
function snippet($text, $len = 120) {
    $t = trim(strip_tags((string)$text));
    if ($t === '') return '';
    if (mb_strlen($t, 'UTF-8') <= $len) return $t;
    return mb_substr($t, 0, $len, 'UTF-8') . '...';
}

$errors = [];
$success = '';

// ===== Delete action =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_news') {
    $deleteId = (int)($_POST['delete_news_id'] ?? 0);

    if ($deleteId <= 0) {
        $errors[] = 'ไม่พบรหัสข่าวที่ต้องการลบ';
    } else {
        try {
            $imgs = $DB->selectAll(
                "SELECT image_url FROM news_images WHERE news_id = :news_id",
                [':news_id' => $deleteId]
            );

            if (!empty($imgs)) {
                foreach ($imgs as $img) {
                    $rel = trim((string)($img['image_url'] ?? ''));
                    if ($rel !== '') {
                        $abs = __DIR__ . '/' . ltrim($rel, '/');
                        if (is_file($abs)) {
                            @unlink($abs);
                        }
                    }
                }
            }

            if (method_exists($DB, 'query')) {
                $DB->query("DELETE FROM news_images WHERE news_id = {$deleteId}");
                $DB->query("DELETE FROM news WHERE id = {$deleteId} LIMIT 1");
            } else {
                $DB->execute("DELETE FROM news_images WHERE news_id = {$deleteId}");
                $DB->execute("DELETE FROM news WHERE id = {$deleteId} LIMIT 1");
            }

            $success = 'ลบข่าวเรียบร้อยแล้ว';
        } catch (Throwable $e) {
            $errors[] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
        }
    }
}

/**
 * Query: list news with image_count + cover_image
 */
$newsRows = $DB->selectAll("
    SELECT
        n.id,
        n.title,
        n.detail,
        n.posted_date,
        n.admin_id,
        n.is_visible,
        n.created_at,
        n.updated_at,
        (SELECT ni.image_url
           FROM news_images ni
          WHERE ni.news_id = n.id
          ORDER BY ni.sort_order ASC, ni.id ASC
          LIMIT 1
        ) AS cover_image,
        (SELECT COUNT(*)
           FROM news_images ni2
          WHERE ni2.news_id = n.id
        ) AS image_count
    FROM news n
    ORDER BY n.posted_date DESC, n.id DESC
");
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include('./structure/head.php') ?>

    <link href="assets/plugins/datatable/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css">
    <link href="assets/plugins/datatable/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css">

    <title>จัดการข่าว</title>

    <style>
        .news-thumb {
            width: 72px;
            height: 48px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid rgba(0,0,0,.08);
            background: #f3f4f6;
        }
        .title-wrap {
            min-width: 260px;
        }
        .muted-snippet {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
            line-height: 1.35;
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
                    <div class="breadcrumb-title pe-3">News</div>
                    <div class="ps-3">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0">
                                <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                                <li class="breadcrumb-item active" aria-current="page">รายการข่าว</li>
                            </ol>
                        </nav>
                    </div>

                    <div class="ms-auto">
                        <a href="news_create.php" class="btn btn-primary btn-sm">เพิ่มข่าว</a>
                    </div>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $er): ?>
                                <li><?= h($er) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <h4 class="mb-0">รายการข่าวทั้งหมด</h4>
                        </div>
                        <hr/>

                        <div class="table-responsive">
                            <table id="newsTable" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th style="width:70px;">ID</th>
                                    <th>ข่าว</th>
                                    <th style="width:140px;">วันที่ลงข่าว</th>
                                    <th style="width:110px;">สถานะ</th>
                                    <th style="width:90px;">รูป</th>
                                    <th style="width:90px;">Admin</th>
                                    <th style="width:170px;">อัปเดตล่าสุด</th>
                                    <th style="width:230px;">จัดการ</th>
                                </tr>
                                </thead>

                                <tbody>
                                <?php foreach ($newsRows as $row): ?>
                                    <?php
                                        $id = (int)$row['id'];
                                        $isVisible = (int)$row['is_visible'] === 1;
                                        $badgeClass = $isVisible ? 'bg-success' : 'bg-secondary';
                                        $badgeText  = $isVisible ? 'แสดง' : 'ไม่แสดง';

                                        $cover = $row['cover_image'] ?? '';
                                        $imgCount = (int)($row['image_count'] ?? 0);
                                        $updated = $row['updated_at'] ?? $row['created_at'] ?? '';
                                    ?>
                                    <tr>
                                        <td><?= $id ?></td>

                                        <td>
                                            <div class="d-flex align-items-start gap-3">
                                                <div>
                                                    <?php if (!empty($cover)): ?>
                                                        <img class="news-thumb" src="<?= h($cover) ?>" alt="<?= h($row['title']) ?>">
                                                    <?php else: ?>
                                                        <div class="news-thumb d-flex align-items-center justify-content-center text-muted" style="font-size:12px;">
                                                            ไม่มีรูป
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="title-wrap">
                                                    <div class="fw-bold text-dark"><?= h($row['title']) ?></div>
                                                    <?php $snip = snippet($row['detail'], 140); ?>
                                                    <?php if ($snip !== ''): ?>
                                                        <div class="muted-snippet"><?= h($snip) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>

                                        <td><?= h(thaiDateShort($row['posted_date'])) ?></td>
                                        <td><span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span></td>
                                        <td><?= $imgCount ?></td>
                                        <td><?= (int)$row['admin_id'] ?></td>
                                        <td><?= h($updated) ?></td>

                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="news_view.php?id=<?= $id ?>" class="btn btn-outline-primary btn-sm">ดู</a>
                                                <a href="news_edit.php?id=<?= $id ?>" class="btn btn-outline-secondary btn-sm">แก้ไข</a>

                                                <form method="post" class="d-inline js-delete-form">
                                                    <input type="hidden" name="action" value="delete_news">
                                                    <input type="hidden" name="delete_news_id" value="<?= $id ?>">
                                                    <input type="hidden" name="news_title" value="<?= h($row['title']) ?>">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">ลบ</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
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

<script src="assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatable/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    $('#newsTable').DataTable({
        pageLength: 10,
        order: [[2, 'desc'], [0, 'desc']],
        columnDefs: [
            { orderable: false, targets: [1, 7] }
        ],
        language: {
            search: "ค้นหา:",
            lengthMenu: "แสดง _MENU_ รายการ",
            info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
            infoEmpty: "ไม่มีข้อมูล",
            zeroRecords: "ไม่พบข้อมูลที่ค้นหา",
            paginate: {
                first: "หน้าแรก",
                last: "หน้าสุดท้าย",
                next: "ถัดไป",
                previous: "ก่อนหน้า"
            }
        }
    });

    $(document).on('submit', '.js-delete-form', function (e) {
        e.preventDefault();
        const form = this;
        const title = form.querySelector('input[name="news_title"]')?.value || '';

        Swal.fire({
            title: 'ยืนยันการลบข่าว',
            html: `คุณต้องการลบข่าวนี้ใช่หรือไม่?<br><b>${title}</b>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ลบเลย',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    <?php if ($success !== ''): ?>
    Swal.fire({
        icon: 'success',
        title: 'สำเร็จ',
        text: <?= json_encode($success) ?>,
        confirmButtonText: 'ตกลง',
        timer: 1800
    });
    <?php endif; ?>
});
</script>
</body>
</html>
