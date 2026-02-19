<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$MM_authorizedUsers = "1,2,3,4";
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

/**
 * Query: list news with:
 * - image_count
 * - cover_image (first image by sort_order)
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

/**
 * ถ้าคุณมีตารางแอดมิน เช่น admins(id,name) และอยากโชว์ชื่อแทน admin_id:
 * ให้แก้ query เป็น JOIN แล้ว SELECT admins.name AS admin_name
 */
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include('./structure/head.php') ?>

    <!-- DataTables CSS (ตามเทมเพลตที่คุณใช้) -->
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
                        <a href="news_create.php" class="btn btn-primary btn-sm">
                            เพิ่มข่าว
                        </a>
                    </div>
                </div>

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
                                    <th style="width:140px;">จัดการ</th>
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

                                        <td>
                                            <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                                        </td>

                                        <td><?= $imgCount ?></td>

                                        <td><?= (int)$row['admin_id'] ?></td>

                                        <td><?= h($updated) ?></td>

                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="news_view.php?id=<?= $id ?>" class="btn btn-outline-primary btn-sm">
                                                    ดู
                                                </a>
                                                <a href="news_edit.php?id=<?= $id ?>" class="btn btn-outline-secondary btn-sm">
                                                    แก้ไข
                                                </a>
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

<!-- DataTables JS (ตามเทมเพลต) -->
<script src="assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatable/js/dataTables.bootstrap4.min.js"></script>

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
    });
</script>
</body>
</html>
