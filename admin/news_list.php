<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$MM_authorizedUsers = "Admin";
$MM_donotCheckaccess = "false";
include('./components/funcCheckSession.php');

include(__DIR__ . '/../backend/classes/DatabaseManagement.class.php');
include_once(__DIR__ . '/../backend/classes/NewsContentAnalyzer.class.php');
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
$syncFlash = $_SESSION['sync_flash'] ?? null;
unset($_SESSION['sync_flash']);
$isFacebookSyncReady = trim((string)(getenv('FB_PAGE_ID') ?: '')) !== ''
    && trim((string)(getenv('FB_PAGE_ACCESS_TOKEN') ?: '')) !== '';

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
        n.category,
        n.posted_date,
        n.source_published_at,
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
    ORDER BY COALESCE(n.source_published_at, CONCAT(n.posted_date, ' 00:00:00')) DESC, n.id DESC
");

$categoryOptions = [];
foreach ($newsRows as $r) {
    $cat = NewsContentAnalyzer::normalizeNewsCategory($r['category'] ?? '');
    $categoryOptions[$cat] = true;
}
$categoryOptions = array_keys($categoryOptions);
sort($categoryOptions, SORT_NATURAL | SORT_FLAG_CASE);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include('./structure/head.php') ?>

    <link href="assets/plugins/datatable/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css">
    <link href="assets/plugins/datatable/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css">

    <title>จัดการข่าว</title>

    <style>
        .news-table-wrap {
            width: 100%;
            overflow: hidden;
        }
        #newsTable {
            width: 100% !important;
            table-layout: fixed;
            margin-bottom: 0 !important;
        }
        #newsTable th,
        #newsTable td {
            vertical-align: top;
        }
        #newsTable td:not(.col-news) {
            white-space: nowrap;
        }
        #newsTable .col-id { width: 48px; text-align: center; }
        #newsTable .col-news { width: 45%; min-width: 420px; }
        #newsTable .col-category { width: 110px; text-align: center; }
        #newsTable .col-date { width: 120px; }
        #newsTable .col-status { width: 72px; text-align: center; }
        #newsTable .col-imgcount { width: 46px; text-align: center; }
        #newsTable .col-admin { width: 56px; text-align: center; }
        #newsTable .col-updated { width: 138px; }
        #newsTable .col-action { width: 158px; }

        .news-thumb {
            width: 72px;
            height: 48px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid rgba(0,0,0,.08);
            background: #f3f4f6;
        }
        .title-wrap {
            min-width: 0;
            width: 100%;
        }
        .news-headline-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(220px, 36%);
            gap: 10px;
            align-items: flex-start;
        }
        .news-title {
            line-height: 1.25;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .muted-snippet {
            font-size: 12px;
            color: #6b7280;
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .category-badge {
            display: inline-block;
            border-radius: 999px;
            padding: .2rem .55rem;
            background: #eaf4ff;
            color: #1d4f91;
            font-size: 12px;
            font-weight: 600;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        @media (max-width: 1200px) {
            .news-headline-row {
                grid-template-columns: 1fr;
                gap: 4px;
            }
        }
        .action-group {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }
        .action-group .btn {
            min-width: 46px;
            padding-left: .45rem;
            padding-right: .45rem;
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
                        <?php if ($isFacebookSyncReady): ?>
                            <a href="news_sync.php" class="btn btn-outline-primary btn-sm me-2">ซิงก์จาก Facebook</a>
                        <?php endif; ?>
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

                <?php if ($isFacebookSyncReady && is_array($syncFlash) && !empty($syncFlash['message'])): ?>
                    <div class="alert alert-<?= ($syncFlash['type'] ?? '') === 'success' ? 'success' : 'warning' ?>">
                        <?= h($syncFlash['message']) ?>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <h4 class="mb-0">รายการข่าวทั้งหมด</h4>
                        </div>
                        <hr/>

                        <div class="d-flex justify-content-end mb-2">
                            <div style="min-width:220px;">
                                <label for="categoryFilter" class="form-label mb-1">หมวดหมู่</label>
                                <select id="categoryFilter" class="form-select form-select-sm">
                                    <option value="">ทั้งหมด</option>
                                    <?php foreach ($categoryOptions as $cat): ?>
                                        <option value="<?= h($cat) ?>"><?= h($cat) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="news-table-wrap">
                            <table id="newsTable" class="table table-striped table-bordered table-sm">
                                <thead>
                                <tr>
                                    <th class="col-id">ID</th>
                                    <th class="col-news">ข่าว</th>
                                    <th class="col-category">หมวดหมู่</th>
                                    <th class="col-date">วันที่ลงข่าว</th>
                                    <th class="col-status">สถานะ</th>
                                    <th class="col-imgcount">รูป</th>
                                    <th class="col-admin">Admin</th>
                                    <th class="col-updated">อัปเดตล่าสุด</th>
                                    <th class="col-action">จัดการ</th>
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
                                        $category = NewsContentAnalyzer::normalizeNewsCategory($row['category'] ?? '');
                                        $imgCount = (int)($row['image_count'] ?? 0);
                                        $updated = $row['source_published_at'] ?? $row['updated_at'] ?? $row['created_at'] ?? '';
                                    ?>
                                    <tr>
                                        <td class="col-id"><?= $id ?></td>

                                        <td class="col-news">
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
                                                    <?php $snip = snippet(NewsContentAnalyzer::simplifyDetail($row['detail'] ?? ''), 120); ?>
                                                    <div class="news-headline-row">
                                                        <div class="fw-bold text-dark news-title"><?= h($row['title']) ?></div>
                                                        <?php if ($snip !== ''): ?>
                                                            <div class="muted-snippet"><?= h($snip) ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="col-category"><span class="category-badge"><?= h($category) ?></span></td>
                                        <td class="col-date"><?= h(thaiDateShort($row['posted_date'])) ?></td>
                                        <td class="col-status"><span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span></td>
                                        <td class="col-imgcount"><?= $imgCount ?></td>
                                        <td class="col-admin"><?= (int)$row['admin_id'] ?></td>
                                        <td class="col-updated"><?= h(date('d/m/Y H:i', strtotime((string)$updated))) ?></td>

                                        <td class="col-action">
                                            <div class="action-group">
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
        order: [[3, 'desc'], [0, 'desc']],
        autoWidth: false,
        columnDefs: [
            { orderable: false, targets: [1, 8] },
            { className: 'col-id', targets: 0 },
            { className: 'col-news', targets: 1 },
            { className: 'col-category', targets: 2 },
            { className: 'col-date', targets: 3 },
            { className: 'col-status', targets: 4 },
            { className: 'col-imgcount', targets: 5 },
            { className: 'col-admin', targets: 6 },
            { className: 'col-updated', targets: 7 },
            { className: 'col-action', targets: 8 }
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

    $('#categoryFilter').on('change', function () {
        const val = this.value;
        const escaped = $.fn.dataTable.util.escapeRegex(val);
        $('#newsTable').DataTable().column(2).search(val ? '^' + escaped + '$' : '', true, false).draw();
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
