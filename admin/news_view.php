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

function h($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

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

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    die('Bad Request: missing id');
}

$news = $DB->selectOne("SELECT * FROM news WHERE id = :id", ['id' => $id]);
if (!$news) {
    http_response_code(404);
    die('Not Found');
}

$images = $DB->selectAll("
    SELECT id, image_url, alt_text, sort_order
    FROM news_images
    WHERE news_id = :id
    ORDER BY sort_order ASC, id ASC
", ['id' => $id]);

$isVisible = (int)$news['is_visible'] === 1;
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include('./structure/head.php') ?>
    <title>ดูข่าว</title>
    <style>
        .gallery-img {
            width: 100%;
            max-height: 520px;
            object-fit: contain;
            background: #0b1220;
            border-radius: 12px;
        }
        .thumb-grid img {
            width: 100%;
            height: 90px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid rgba(0,0,0,.08);
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

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="mb-1"><?= h($news['title']) ?></h4>
                        <div class="text-muted small">
                            วันที่ลงข่าว: <span class="fw-bold"><?= h(thaiDateShort($news['posted_date'])) ?></span>
                            <span class="mx-2">|</span>
                            สถานะ: <span class="badge <?= $isVisible ? 'bg-success' : 'bg-secondary' ?>"><?= $isVisible ? 'แสดง' : 'ไม่แสดง' ?></span>
                            <span class="mx-2">|</span>
                            Admin: <?= (int)$news['admin_id'] ?>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="news_list.php" class="btn btn-outline-secondary btn-sm">กลับไปหน้ารายการ</a>
                        <a href="news_edit.php?id=<?= (int)$news['id'] ?>" class="btn btn-primary btn-sm">แก้ไขข่าว</a>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-3">รูปประกอบข่าว</h6>

                                <?php if (empty($images)): ?>
                                    <div class="text-muted">ข่าวนี้ยังไม่มีรูป</div>
                                <?php else: ?>
                                    <div id="newsCarousel" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-inner">
                                            <?php foreach ($images as $idx => $img): ?>
                                                <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                                                    <img src="<?= h($img['image_url']) ?>" class="gallery-img" alt="<?= h($img['alt_text'] ?? $news['title']) ?>">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <?php if (count($images) > 1): ?>
                                            <button class="carousel-control-prev" type="button" data-bs-target="#newsCarousel" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Previous</span>
                                            </button>
                                            <button class="carousel-control-next" type="button" data-bs-target="#newsCarousel" data-bs-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Next</span>
                                            </button>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (count($images) > 1): ?>
                                        <div class="row g-2 mt-3 thumb-grid">
                                            <?php foreach ($images as $idx => $img): ?>
                                                <div class="col-4 col-md-3">
                                                    <a href="javascript:void(0)" class="d-block" onclick="$('#newsCarousel').carousel(<?= (int)$idx ?>)">
                                                        <img src="<?= h($img['image_url']) ?>" alt="<?= h($img['alt_text'] ?? '') ?>">
                                                    </a>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-3">รายละเอียดข่าว</h6>
                                <div style="white-space: pre-wrap; line-height: 1.7;">
                                    <?= h($news['detail']) ?>
                                </div>
                                <hr>
                                <div class="text-muted small">
                                    created_at: <?= h($news['created_at'] ?? '-') ?><br>
                                    updated_at: <?= h($news['updated_at'] ?? '-') ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

</div>

<?php include('./structure/script.php') ?>
</body>
</html>
