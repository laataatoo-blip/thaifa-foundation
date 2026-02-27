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
function h($str)
{
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

function parseThaiDateToYmd($text)
{
    $text = trim((string)$text);
    if ($text === '') return null;

    $months = [
        'ม.ค.' => 1, 'มกราคม' => 1,
        'ก.พ.' => 2, 'กุมภาพันธ์' => 2,
        'มี.ค.' => 3, 'มีนาคม' => 3,
        'เม.ย.' => 4, 'เมษายน' => 4,
        'พ.ค.' => 5, 'พฤษภาคม' => 5,
        'มิ.ย.' => 6, 'มิถุนายน' => 6,
        'ก.ค.' => 7, 'กรกฎาคม' => 7,
        'ส.ค.' => 8, 'สิงหาคม' => 8,
        'ก.ย.' => 9, 'กันยายน' => 9,
        'ต.ค.' => 10, 'ตุลาคม' => 10,
        'พ.ย.' => 11, 'พฤศจิกายน' => 11,
        'ธ.ค.' => 12, 'ธันวาคม' => 12,
    ];

    if (preg_match('/(\d{1,2})\s+([^\s]+)\s+(\d{4})/u', $text, $m)) {
        $d = (int)$m[1];
        $mTxt = trim($m[2]);
        $y = (int)$m[3];
        if (isset($months[$mTxt])) {
            $mm = (int)$months[$mTxt];
            if ($y > 2400) $y -= 543;
            if (checkdate($mm, $d, $y)) {
                return sprintf('%04d-%02d-%02d', $y, $mm, $d);
            }
        }
    }

    return null;
}

function normalizeNewsTitle($title, $detail = '')
{
    return NewsContentAnalyzer::normalizeNewsTitle($title, $detail);
}

function normalizeNewsCategory($category)
{
    return NewsContentAnalyzer::normalizeNewsCategory($category);
}

function fetchHtmlByUrl($url)
{
    $ctx = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)\r\n",
            'timeout' => 25
        ]
    ]);
    $html = @file_get_contents($url, false, $ctx);
    return is_string($html) ? $html : '';
}

function normalizeImageUrl($url)
{
    $url = trim((string)$url);
    if ($url === '') return '';

    $url = html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $url = str_replace(['\\/', '\\u002F', '\\u002f'], '/', $url);
    $url = str_replace(['\\u0025', '\\u0025'], '%', $url);
    $url = str_replace('&amp;', '&', $url);
    $url = preg_replace('/\\\\u00([0-9a-fA-F]{2})/', '%$1', $url);
    $url = stripcslashes($url);

    if (strpos($url, 'http://') !== 0 && strpos($url, 'https://') !== 0) {
        return '';
    }

    return $url;
}

function extractFacebookImageUrls($html)
{
    $html = (string)$html;
    if (trim($html) === '') return [];

    $patterns = [
        '/property="og:image"\s+content="([^"]+)"/i',
        '/property="og:image:url"\s+content="([^"]+)"/i',
        '/"image"\s*:\s*\{"uri":"([^"]+)"/i',
        '/"image"\s*:\s*"([^"]*scontent[^"]+)"/i',
        '/"src"\s*:\s*"([^"]*scontent[^"]+)"/i',
        '/(https:\\\/\\\/scontent[^"\'\s<]+)/i',
        '/(https:\/\/scontent[^"\'\s<]+)/i'
    ];

    $images = [];
    $seen = [];
    foreach ($patterns as $pattern) {
        if (!preg_match_all($pattern, $html, $m)) continue;
        foreach (($m[1] ?? []) as $raw) {
            $url = normalizeImageUrl($raw);
            if ($url === '' || isset($seen[$url])) continue;
            $seen[$url] = true;
            $images[] = $url;
        }
    }

    return array_slice($images, 0, 20);
}

function fetchFacebookPreview($url)
{
    $url = trim((string)$url);
    if ($url === '' || !preg_match('#^https?://#i', $url)) {
        throw new Exception('ลิงก์ไม่ถูกต้อง');
    }

    $html = fetchHtmlByUrl($url);
    if ($html === '') {
        throw new Exception('ไม่สามารถดึงข้อมูลจากลิงก์นี้ได้');
    }

    $pick = function ($prop) use ($html) {
        if (preg_match('/property="' . preg_quote($prop, '/') . '"\s+content="([^"]*)"/i', $html, $m)) {
            return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        return '';
    };

    $title = trim((string)$pick('og:title'));
    $desc = trim((string)$pick('og:description'));
    $canonicalUrl = trim((string)$pick('og:url'));
    $images = extractFacebookImageUrls($html);

    $mbasicUrl = preg_replace('#^https?://(www\.)?facebook\.com/#i', 'https://mbasic.facebook.com/', $url);
    if (is_string($mbasicUrl) && $mbasicUrl !== '' && $mbasicUrl !== $url) {
        $mbasicHtml = fetchHtmlByUrl($mbasicUrl);
        if ($mbasicHtml !== '') {
            $extra = extractFacebookImageUrls($mbasicHtml);
            if (!empty($extra)) {
                $images = array_values(array_unique(array_merge($images, $extra)));
                $images = array_slice($images, 0, 20);
            }
        }
    }

    $line = $desc !== '' ? trim((string)preg_split('/\R/u', $desc)[0]) : '';
    $ymd = parseThaiDateToYmd($line);

    return [
        'title' => $title,
        'description' => $desc,
        'source_url' => $canonicalUrl !== '' ? $canonicalUrl : $url,
        'posted_date' => $ymd ?: date('Y-m-d'),
        'images' => $images
    ];
}

/**
 * Try best to escape for SQL string
 */
function dbEscape($DB, $value)
{
    $value = (string)$value;

    if (method_exists($DB, 'escape')) {
        return $DB->escape($value);
    }
    if (property_exists($DB, 'conn') && is_object($DB->conn) && method_exists($DB->conn, 'real_escape_string')) {
        return $DB->conn->real_escape_string($value);
    }
    if (method_exists($DB, 'real_escape_string')) {
        return $DB->real_escape_string($value);
    }

    return addslashes($value);
}

/**
 * Execute query
 */
function dbExec($DB, $sql)
{
    if (method_exists($DB, 'query')) return $DB->query($sql);
    if (method_exists($DB, 'execute')) return $DB->execute($sql);
    if (method_exists($DB, 'selectAll')) return $DB->selectAll($sql);
    throw new Exception('ไม่พบ method สำหรับ execute SQL ใน DatabaseManagement');
}

$errors = [];
$success = '';
$showSuccessModal = false;

$news_id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if ($news_id <= 0) {
    die('ไม่พบรหัสข่าว');
}

// โหลดข่าว
$news = $DB->selectOne("SELECT * FROM news WHERE id = :id LIMIT 1", [':id' => $news_id]);
if (!$news) {
    die('ไม่พบข้อมูลข่าว');
}

// ค่าตั้งต้นจาก DB
$title = (string)($news['title'] ?? '');
$detail = (string)($news['detail'] ?? '');
$posted_date = (string)($news['posted_date'] ?? date('Y-m-d'));
$is_visible = (int)($news['is_visible'] ?? 1);
$category = normalizeNewsCategory($news['category'] ?? 'ทั่วไป');
$fb_source_url = trim((string)($news['source_post_url'] ?? ''));
$external_images = [];

// โหลดรูปเดิม
$images = $DB->selectAll("
    SELECT id, news_id, image_url, alt_text, sort_order, created_at
    FROM news_images
    WHERE news_id = :news_id
    ORDER BY sort_order ASC, id ASC
", [':news_id' => $news_id]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = trim((string)($_POST['action'] ?? 'update_news'));
    $title = trim((string)($_POST['title'] ?? ''));
    $detail = trim((string)($_POST['detail'] ?? ''));
    $posted_date = trim((string)($_POST['posted_date'] ?? date('Y-m-d')));
    $is_visible = isset($_POST['is_visible']) ? 1 : 0;
    $category = normalizeNewsCategory($_POST['category'] ?? $category);
    $fb_source_url = trim((string)($_POST['fb_source_url'] ?? ''));
    $external_images = array_values(array_filter($_POST['external_image_url'] ?? [], function ($v) {
        return trim((string)$v) !== '';
    }));

    if ($action === 'fetch_fb_preview') {
        try {
            $preview = fetchFacebookPreview($fb_source_url);
            $title = normalizeNewsTitle($preview['title'] !== '' ? $preview['title'] : $title, $preview['description']);
            $detail = NewsContentAnalyzer::simplifyDetail($preview['description']);
            if ($preview['source_url'] !== '') {
                $detail .= ($detail !== '' ? "\n\n" : '') . "ที่มาโพสต์ Facebook:\n" . $preview['source_url'];
            }
            $posted_date = $preview['posted_date'] ?: $posted_date;
            $external_images = $preview['images'];
            $success = 'ดึงข้อมูลล่าสุดจาก Facebook แล้ว กรุณาตรวจสอบและกดบันทึกการแก้ไข';
        } catch (Throwable $e) {
            $errors[] = 'ดึงข้อมูลจาก Facebook ไม่สำเร็จ: ' . $e->getMessage();
        }
    } else {

    $title = normalizeNewsTitle($title, $detail);
    $detail = NewsContentAnalyzer::simplifyDetail($detail);
    $category = NewsContentAnalyzer::detectCategory($title, $detail, $fb_source_url, $category);
    if ($title === '') $errors[] = 'กรุณากรอกหัวข้อข่าว';
    if ($detail === '') $errors[] = 'กรุณากรอกรายละเอียด';
    if ($posted_date === '') $errors[] = 'กรุณาเลือกวันที่ลงข่าว';

    if (empty($errors)) {
        try {
            // 1) update ข่าวหลัก
            $titleEsc = dbEscape($DB, $title);
            $detailEsc = dbEscape($DB, $detail);
            $dateEsc = dbEscape($DB, $posted_date);
            $categoryEsc = dbEscape($DB, $category);
            $sourceSql = ($fb_source_url === '') ? "NULL" : ("'" . dbEscape($DB, $fb_source_url) . "'");

            $sqlNews = "
                UPDATE news
                SET title = '{$titleEsc}',
                    detail = '{$detailEsc}',
                    posted_date = '{$dateEsc}',
                    category = '{$categoryEsc}',
                    source_post_url = {$sourceSql},
                    is_visible = {$is_visible}
                WHERE id = {$news_id}
                LIMIT 1
            ";
            dbExec($DB, $sqlNews);

            // 2) update / delete รูปเดิม
            $existingAlt = $_POST['existing_alt'] ?? [];
            $existingSort = $_POST['existing_sort'] ?? [];
            $deleteImage = $_POST['delete_image'] ?? [];
            $sortStart = 1;

            if (!empty($images)) {
                foreach ($images as $img) {
                    $imgId = (int)$img['id'];

                    // ลบรูป
                    if (isset($deleteImage[$imgId]) && (string)$deleteImage[$imgId] === '1') {
                        $oldPath = (string)($img['image_url'] ?? '');
                        if ($oldPath !== '') {
                            $absOld = __DIR__ . '/' . ltrim($oldPath, '/');
                            if (is_file($absOld)) {
                                @unlink($absOld);
                            }
                        }
                        dbExec($DB, "DELETE FROM news_images WHERE id = {$imgId} AND news_id = {$news_id} LIMIT 1");
                        continue;
                    }

                    // แก้ alt + sort
                    $alt = trim((string)($existingAlt[$imgId] ?? ''));
                    $sort = (int)($existingSort[$imgId] ?? 0);
                    $altSql = ($alt === '') ? "NULL" : ("'" . dbEscape($DB, $alt) . "'");
                    $sortSql = max(0, $sort);
                    if ($sortSql >= $sortStart) $sortStart = $sortSql + 1;

                    dbExec($DB, "
                        UPDATE news_images
                        SET alt_text = {$altSql}, sort_order = {$sortSql}
                        WHERE id = {$imgId} AND news_id = {$news_id}
                        LIMIT 1
                    ");
                }
            }

            // 3) เพิ่มรูปใหม่ (ถ้ามี)
            if (!empty($_FILES['images']['name'][0])) {
                $baseDir = __DIR__ . '/uploads/news/' . date('Y/m');
                if (!is_dir($baseDir) && !mkdir($baseDir, 0775, true)) {
                    throw new Exception('ไม่สามารถสร้างโฟลเดอร์อัปโหลดรูปได้');
                }

                $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                $count = count($_FILES['images']['name']);

                for ($i = 0; $i < $count; $i++) {
                    if (!isset($_FILES['images']['error'][$i]) || $_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
                        continue;
                    }

                    $origName = $_FILES['images']['name'][$i] ?? '';
                    $tmpPath = $_FILES['images']['tmp_name'][$i] ?? '';
                    if ($origName === '' || $tmpPath === '') continue;

                    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowed, true)) continue;

                    $newName = uniqid('news_', true) . '.' . $ext;
                    $target = $baseDir . '/' . $newName;

                    if (!move_uploaded_file($tmpPath, $target)) {
                        continue;
                    }

                    $relUrl = 'uploads/news/' . date('Y/m') . '/' . $newName;
                    $alt_text = trim($_POST['image_alt'][$i] ?? '');
                    $sort_order = (int)($_POST['image_sort'][$i] ?? ($i + 1));

                    $relEsc = dbEscape($DB, $relUrl);
                    $altSql = ($alt_text === '') ? "NULL" : ("'" . dbEscape($DB, $alt_text) . "'");
                    $sortSql = max(0, $sort_order);
                    if ($sortSql >= $sortStart) $sortStart = $sortSql + 1;

                    dbExec($DB, "
                        INSERT INTO news_images (news_id, image_url, alt_text, sort_order)
                        VALUES ({$news_id}, '{$relEsc}', {$altSql}, {$sortSql})
                    ");
                }
            }

            // 4) เพิ่มรูปจากลิงก์ Facebook ที่ดึงมา
            if (!empty($external_images)) {
                foreach ($external_images as $i => $extUrl) {
                    $extUrl = trim((string)$extUrl);
                    if ($extUrl === '' || !preg_match('#^https?://#i', $extUrl)) continue;
                    $relEsc = dbEscape($DB, $extUrl);
                    $altSql = "'ภาพประกอบข่าวจาก Facebook'";
                    $sortSql = $sortStart + $i;
                    dbExec($DB, "
                        INSERT INTO news_images (news_id, image_url, alt_text, sort_order)
                        VALUES ({$news_id}, '{$relEsc}', {$altSql}, {$sortSql})
                    ");
                }
            }

            $success = 'บันทึกการแก้ไขเรียบร้อยแล้ว';
            $showSuccessModal = true;

            // reload ข้อมูลหลังบันทึก
            $news = $DB->selectOne("SELECT * FROM news WHERE id = :id LIMIT 1", [':id' => $news_id]);
            $images = $DB->selectAll("
                SELECT id, news_id, image_url, alt_text, sort_order, created_at
                FROM news_images
                WHERE news_id = :news_id
                ORDER BY sort_order ASC, id ASC
            ", [':news_id' => $news_id]);

            $title = (string)($news['title'] ?? '');
            $detail = (string)($news['detail'] ?? '');
            $posted_date = (string)($news['posted_date'] ?? date('Y-m-d'));
            $is_visible = (int)($news['is_visible'] ?? 1);
            $category = normalizeNewsCategory($news['category'] ?? $category);
            $fb_source_url = trim((string)($news['source_post_url'] ?? $fb_source_url));
            $external_images = [];
        } catch (Throwable $e) {
            $errors[] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
        }
    }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include('./structure/head.php') ?>
    <title>แก้ไขข่าว</title>
    <style>
        .image-row {
            border: 1px dashed #d1d5db;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 10px;
            background: #fafafa;
        }
        .old-thumb {
            width: 92px;
            height: 62px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid rgba(0,0,0,.1);
            background: #f3f4f6;
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
                                <li class="breadcrumb-item"><a href="news_list.php">รายการข่าว</a></li>
                                <li class="breadcrumb-item active" aria-current="page">แก้ไขข่าว #<?= (int)$news_id ?></li>
                            </ol>
                        </nav>
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
                            <h4 class="mb-0">ฟอร์มแก้ไขข่าว</h4>
                        </div>
                        <hr />

                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= (int)$news_id ?>">
                            <?php if (!empty($external_images)): ?>
                                <?php foreach ($external_images as $imgUrl): ?>
                                    <input type="hidden" name="external_image_url[]" value="<?= h($imgUrl) ?>">
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <div class="row g-2 align-items-end mb-3">
                                <div class="col-md-9">
                                    <label class="form-label">ลิงก์โพสต์ Facebook (ดึงข้อมูลล่าสุด)</label>
                                    <input type="url" name="fb_source_url" class="form-control" placeholder="https://www.facebook.com/share/p/..." value="<?= h($fb_source_url) ?>">
                                </div>
                                <div class="col-md-3 d-grid">
                                    <button type="submit" name="action" value="fetch_fb_preview" class="btn btn-outline-primary">ดึงข้อมูลจาก Facebook</button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">หัวข้อข่าว <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" value="<?= h($title) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">รายละเอียด <span class="text-danger">*</span></label>
                                <textarea name="detail" class="form-control" rows="8" required><?= h($detail) ?></textarea>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">วันที่ลงข่าว <span class="text-danger">*</span></label>
                                    <input type="date" name="posted_date" class="form-control" value="<?= h($posted_date) ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">หมวดหมู่ข่าว</label>
                                    <input type="text" name="category" class="form-control" value="<?= h($category) ?>" list="newsCategoryList" placeholder="เช่น กิจกรรม">
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_visible" id="is_visible" <?= $is_visible ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_visible">แสดงข่าว</label>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <?php if (!empty($external_images)): ?>
                                <div class="mb-3">
                                    <label class="form-label">รูปจากลิงก์ Facebook ที่จะเพิ่ม (<?= count($external_images) ?> รูป)</label>
                                    <div class="row g-2">
                                        <?php foreach ($external_images as $imgUrl): ?>
                                            <div class="col-md-3 col-6">
                                                <a href="<?= h($imgUrl) ?>" target="_blank" class="d-block border rounded overflow-hidden">
                                                    <img src="<?= h($imgUrl) ?>" alt="fb-image" class="img-fluid" style="height:120px;width:100%;object-fit:cover;">
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="text-muted small mt-1">รูปชุดนี้จะถูกเพิ่มเมื่อกด "บันทึกการแก้ไข"</div>
                                </div>
                            <?php endif; ?>

                            <hr>
                            <h5 class="mb-3">รูปเดิม</h5>

                            <?php if (empty($images)): ?>
                                <div class="text-muted mb-3">ยังไม่มีรูปในข่าวนี้</div>
                            <?php else: ?>
                                <?php foreach ($images as $img): ?>
                                    <?php $imgId = (int)$img['id']; ?>
                                    <div class="image-row row g-2 align-items-end">
                                        <div class="col-md-2">
                                            <img src="<?= h($img['image_url']) ?>" class="old-thumb" alt="<?= h($img['alt_text'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label">Alt text</label>
                                            <input type="text" name="existing_alt[<?= $imgId ?>]" class="form-control" value="<?= h($img['alt_text'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Sort</label>
                                            <input type="number" name="existing_sort[<?= $imgId ?>]" class="form-control" value="<?= (int)($img['sort_order'] ?? 0) ?>" min="0">
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check mt-4">
                                                <input class="form-check-input" type="checkbox" value="1" name="delete_image[<?= $imgId ?>]" id="delete_<?= $imgId ?>">
                                                <label class="form-check-label text-danger" for="delete_<?= $imgId ?>">
                                                    ลบรูปนี้
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">เพิ่มรูปใหม่</h5>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="addImageBtn">+ เพิ่มแถวรูป</button>
                            </div>

                            <div id="imageRows">
                                <div class="image-row row g-2">
                                    <div class="col-md-5">
                                        <label class="form-label">ไฟล์รูป</label>
                                        <input type="file" name="images[]" class="form-control" accept=".jpg,.jpeg,.png,.webp,.gif">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Alt text</label>
                                        <input type="text" name="image_alt[]" class="form-control" placeholder="คำอธิบายรูป">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Sort</label>
                                        <input type="number" name="image_sort[]" class="form-control" value="1" min="0">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 d-flex gap-2">
                                <button type="submit" name="action" value="update_news" class="btn btn-primary">บันทึกการแก้ไข</button>
                                <a href="news_list.php" class="btn btn-light">ยกเลิก</a>
                            </div>
                        </form>
                        <datalist id="newsCategoryList">
                            <option value="ทั่วไป">
                            <option value="กิจกรรม">
                            <option value="ประชาสัมพันธ์">
                            <option value="อบรม/สัมมนา">
                            <option value="โครงการ">
                            <option value="Facebook">
                        </datalist>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="overlay toggle-btn-mobile"></div>
<a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>

<?php if ($showSuccessModal): ?>
<div class="modal fade" id="saveSuccessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">บันทึกสำเร็จ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?= h($success) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include('./structure/script.php') ?>

<script>
(function() {
    const addBtn = document.getElementById('addImageBtn');
    const box = document.getElementById('imageRows');
    if (!addBtn || !box) return;

    addBtn.addEventListener('click', function() {
        const idx = box.querySelectorAll('.image-row').length + 1;
        const row = document.createElement('div');
        row.className = 'image-row row g-2';
        row.innerHTML = `
            <div class="col-md-5">
                <label class="form-label">ไฟล์รูป</label>
                <input type="file" name="images[]" class="form-control" accept=".jpg,.jpeg,.png,.webp,.gif">
            </div>
            <div class="col-md-5">
                <label class="form-label">Alt text</label>
                <input type="text" name="image_alt[]" class="form-control" placeholder="คำอธิบายรูป">
            </div>
            <div class="col-md-2">
                <label class="form-label">Sort</label>
                <input type="number" name="image_sort[]" class="form-control" value="${idx}" min="0">
            </div>
        `;
        box.appendChild(row);
    });
})();

<?php if ($showSuccessModal): ?>
(function() {
    var el = document.getElementById('saveSuccessModal');
    if (!el || typeof bootstrap === 'undefined') return;

    var modal = new bootstrap.Modal(el, {
        backdrop: 'static',
        keyboard: false
    });

    el.addEventListener('hidden.bs.modal', function() {
        window.location.href = 'news_list.php';
    });

    modal.show();
})();
<?php endif; ?>
</script>
</body>
</html>
