<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Bangkok');

include(__DIR__ . '/backend/classes/DatabaseManagement.class.php');
include_once(__DIR__ . '/backend/helpers/i18n.php');
thaifa_i18n_buffer_start();
$DB = new DatabaseManagement();

function h($str)
{
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

function newsImageUrl($path)
{
    $path = trim((string)$path);
    if ($path === '') {
        return 'assets/images/Logo.png';
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    $clean = ltrim($path, '/');
    if (strpos($clean, 'admin/') === 0) {
        return $clean;
    }
    if (strpos($clean, 'uploads/') === 0) {
        return 'admin/' . $clean;
    }
    return $clean;
}

function thaiDate($dateStr)
{
    $ts = strtotime((string)$dateStr);
    if (!$ts) {
        return '-';
    }
    $thaiMonths = [1 => 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
    $d = (int)date('j', $ts);
    $m = (int)date('n', $ts);
    $y = (int)date('Y', $ts) + 543;
    return $d . ' ' . ($thaiMonths[$m] ?? '') . ' ' . $y;
}

function snippetText($text, $len = 110)
{
    $txt = trim(strip_tags((string)$text));
    if ($txt === '') {
        return '';
    }
    if (mb_strlen($txt, 'UTF-8') <= $len) {
        return $txt;
    }
    return mb_substr($txt, 0, $len, 'UTF-8') . '...';
}

function linkifyText($text)
{
    $parts = preg_split('~(https?://[^\s<]+)~u', (string)$text, -1, PREG_SPLIT_DELIM_CAPTURE);
    $html = '';
    foreach ($parts as $part) {
        if ($part === '') {
            continue;
        }
        if (preg_match('~^https?://~u', $part)) {
            $url = h($part);
            $html .= '<a href="' . $url . '" target="_blank" rel="noopener noreferrer" class="text-[#315d9f] underline break-all">' . $url . '</a>';
        } else {
            $html .= h($part);
        }
    }
    return $html;
}

function renderNewsDetailHtml($text)
{
    $text = trim((string)$text);
    if ($text === '') {
        return '';
    }

    $paragraphs = preg_split('/\R{2,}/u', $text);
    $html = [];
    foreach ($paragraphs as $p) {
        $p = trim((string)$p);
        if ($p === '') {
            continue;
        }
        $lineHtml = nl2br(linkifyText($p), false);
        $html[] = '<p>' . $lineHtml . '</p>';
    }
    return implode("\n", $html);
}

$newsId = (int)($_GET['id'] ?? 0);
if ($newsId <= 0) {
    $latest = $DB->selectOne(
        "SELECT id
         FROM news
         WHERE is_visible = 1
         ORDER BY COALESCE(source_published_at, CONCAT(posted_date, ' 00:00:00')) DESC, id DESC
         LIMIT 1"
    );
    if (!empty($latest['id'])) {
        header('Location: news-detail.php?id=' . (int)$latest['id']);
        exit;
    }
}

$news = null;
$images = [];
$relatedNews = [];

if ($newsId > 0) {
    $news = $DB->selectOne(
        "SELECT n.id, n.title, n.detail, n.posted_date, n.updated_at,
                (SELECT ni.image_url FROM news_images ni WHERE ni.news_id = n.id ORDER BY ni.sort_order ASC, ni.id ASC LIMIT 1) AS cover_image
         FROM news n
         WHERE n.id = :id AND n.is_visible = 1
         LIMIT 1",
        [':id' => $newsId]
    );

    if ($news) {
        $images = $DB->selectAll(
            "SELECT image_url, COALESCE(alt_text, '') AS alt_text
             FROM news_images
             WHERE news_id = :id
             ORDER BY sort_order ASC, id ASC",
            [':id' => $newsId]
        );

        $relatedNews = $DB->selectAll(
            "SELECT n.id, n.title, n.detail, n.posted_date,
                    (SELECT ni.image_url FROM news_images ni WHERE ni.news_id = n.id ORDER BY ni.sort_order ASC, ni.id ASC LIMIT 1) AS cover_image
             FROM news n
             WHERE n.is_visible = 1 AND n.id <> :id
             ORDER BY COALESCE(n.source_published_at, CONCAT(n.posted_date, ' 00:00:00')) DESC, n.id DESC
             LIMIT 3",
            [':id' => $newsId]
        );
    }
}

if (!$news) {
    http_response_code(404);
}

$galleryImages = [];
if (!empty($images)) {
    foreach ($images as $img) {
        $galleryImages[] = [
            'src' => newsImageUrl($img['image_url'] ?? ''),
            'title' => (string)($news['title'] ?? ''),
            'description' => (string)($img['alt_text'] ?? '')
        ];
    }
} elseif ($news && !empty($news['cover_image'])) {
    $galleryImages[] = [
        'src' => newsImageUrl($news['cover_image']),
        'title' => (string)$news['title'],
        'description' => ''
    ];
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(thaifa_lang(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $news ? h($news['title']) . ' - THAIFA Foundation' : 'ไม่พบข่าว - THAIFA Foundation' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Prompt', sans-serif; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .article-body p { margin-bottom: 1rem; }
        .article-body p:last-child { margin-bottom: 0; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
            <a href="index.php" class="flex items-center gap-3">
                <img src="assets/images/Logo.png" alt="THAIFA" class="h-12 w-auto">
            </a>
            <nav class="hidden md:flex items-center gap-6 text-sm">
                <a href="index.php" class="text-slate-600 hover:text-[#315d9f]"><?= h(thaifa_t('home')) ?></a>
                <a href="about.php" class="text-slate-600 hover:text-[#315d9f]"><?= h(thaifa_t('about')) ?></a>
                <a href="calendar.php" class="text-slate-600 hover:text-[#315d9f]"><?= h(thaifa_t('calendar')) ?></a>
                <a href="contact.php" class="text-slate-600 hover:text-[#315d9f]"><?= h(thaifa_t('contact')) ?></a>
                <span class="text-slate-300">|</span>
                <a href="<?= h(thaifa_lang_url('th')) ?>" class="text-xs px-2 py-0.5 rounded <?= thaifa_lang()==='th' ? 'bg-[#233882] text-white' : 'text-slate-600 hover:text-[#315d9f]' ?>">TH</a>
                <a href="<?= h(thaifa_lang_url('en')) ?>" class="text-xs px-2 py-0.5 rounded <?= thaifa_lang()==='en' ? 'bg-[#233882] text-white' : 'text-slate-600 hover:text-[#315d9f]' ?>">EN</a>
            </nav>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="text-sm text-slate-500 mb-5">
            <a href="index.php" class="hover:text-[#315d9f]"><?= h(thaifa_t('home')) ?></a>
            <span class="mx-2">/</span>
            <a href="index.php#news" class="hover:text-[#315d9f]">ข่าวสารล่าสุด</a>
            <?php if ($news): ?>
                <span class="mx-2">/</span>
                <span class="text-slate-700"><?= h($news['title']) ?></span>
            <?php endif; ?>
        </div>

        <?php if (!$news): ?>
            <div class="rounded-2xl border border-slate-200 bg-white p-10 text-center">
                <h1 class="text-2xl text-[#233882] mb-3">ไม่พบข่าวที่ต้องการ</h1>
                <p class="text-slate-600 mb-6">ข่าวอาจถูกปิดการแสดงผลหรือไม่มีอยู่ในระบบ</p>
                <a href="index.php#news" class="inline-flex items-center rounded-full bg-[#233882] text-white px-6 py-3">กลับไปหน้าข่าวสาร</a>
            </div>
        <?php else: ?>
            <article class="rounded-3xl border border-slate-200 bg-white overflow-hidden">
                <img src="<?= h(newsImageUrl($news['cover_image'] ?? '')) ?>" alt="<?= h($news['title']) ?>" class="w-full h-[260px] md:h-[420px] object-cover">

                <div class="p-6 md:p-10">
                    <a href="index.php#news" class="inline-flex items-center text-sm text-[#315d9f] hover:underline mb-4">← กลับไปหน้าข่าวสาร</a>
                    <h1 class="text-3xl md:text-4xl text-[#233882] leading-tight mb-3"><?= h($news['title']) ?></h1>
                    <div class="text-sm text-slate-500 mb-8">เผยแพร่เมื่อ <?= h(thaiDate($news['posted_date'] ?? '')) ?></div>

                    <div class="article-body max-w-none text-slate-700 leading-8">
                        <?= renderNewsDetailHtml($news['detail'] ?? '') ?>
                    </div>
                </div>
            </article>

            <?php if (!empty($galleryImages)): ?>
                <section class="mt-10">
                    <h2 class="text-2xl text-[#233882] mb-4">ภาพประกอบข่าว</h2>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($galleryImages as $idx => $img): ?>
                            <button type="button" onclick="openLightbox(<?= (int)$idx ?>)" class="group relative rounded-xl overflow-hidden border border-slate-200 bg-white text-left">
                                <img src="<?= h($img['src']) ?>" alt="<?= h($img['title']) ?>" class="w-full h-52 object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/75 to-transparent p-3 text-white text-sm">
                                    <?= h($img['description'] !== '' ? $img['description'] : $img['title']) ?>
                                </div>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if (!empty($relatedNews)): ?>
                <section class="mt-12">
                    <h2 class="text-2xl text-[#233882] mb-5">ข่าวที่เกี่ยวข้อง</h2>
                    <div class="grid md:grid-cols-3 gap-5">
                        <?php foreach ($relatedNews as $item): ?>
                            <a href="news-detail.php?id=<?= (int)$item['id'] ?>" class="rounded-2xl border border-slate-200 bg-white overflow-hidden hover:shadow-lg transition-shadow">
                                <img src="<?= h(newsImageUrl($item['cover_image'] ?? '')) ?>" alt="<?= h($item['title']) ?>" class="w-full h-44 object-cover">
                                <div class="p-4">
                                    <div class="text-xs text-slate-500 mb-2"><?= h(thaiDate($item['posted_date'] ?? '')) ?></div>
                                    <h3 class="text-[#233882] text-lg line-clamp-2 mb-2"><?= h($item['title']) ?></h3>
                                    <p class="text-sm text-slate-600"><?= h(snippetText($item['detail'] ?? '')) ?></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <div id="lightbox" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/90 p-6" onclick="if(event.target===this){closeLightbox();}">
        <button type="button" onclick="closeLightbox()" class="absolute top-4 right-6 text-white text-4xl leading-none">&times;</button>
        <button type="button" onclick="changeImage(-1)" class="absolute left-4 md:left-8 text-white text-4xl">&#10094;</button>
        <button type="button" onclick="changeImage(1)" class="absolute right-4 md:right-8 text-white text-4xl">&#10095;</button>
        <div class="max-w-5xl w-full" onclick="event.stopPropagation()">
            <img id="lightboxImage" src="" alt="" class="w-full max-h-[80vh] object-contain rounded-lg">
            <div class="text-center text-white mt-3">
                <div id="lightboxTitle" class="font-medium"></div>
                <div id="lightboxDescription" class="text-sm text-white/80"></div>
                <div id="lightboxCounter" class="text-xs text-white/70 mt-1"></div>
            </div>
        </div>
    </div>

    <script>
        const galleryImages = <?= json_encode($galleryImages, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        let currentImageIndex = 0;

        function openLightbox(index) {
            if (!galleryImages.length) return;
            currentImageIndex = index;
            const lightbox = document.getElementById('lightbox');
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
            document.body.style.overflow = 'hidden';
            renderLightbox();
        }

        function closeLightbox() {
            const lightbox = document.getElementById('lightbox');
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        function changeImage(direction) {
            if (!galleryImages.length) return;
            currentImageIndex += direction;
            if (currentImageIndex < 0) currentImageIndex = galleryImages.length - 1;
            if (currentImageIndex >= galleryImages.length) currentImageIndex = 0;
            renderLightbox();
        }

        function renderLightbox() {
            const current = galleryImages[currentImageIndex];
            document.getElementById('lightboxImage').src = current.src;
            document.getElementById('lightboxTitle').textContent = current.title || '';
            document.getElementById('lightboxDescription').textContent = current.description || '';
            document.getElementById('lightboxCounter').textContent = (currentImageIndex + 1) + ' / ' + galleryImages.length;
        }

        document.addEventListener('keydown', function (e) {
            const lightbox = document.getElementById('lightbox');
            if (lightbox.classList.contains('hidden')) return;
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') changeImage(-1);
            if (e.key === 'ArrowRight') changeImage(1);
        });
    </script>
<?php include __DIR__ . '/backend/helpers/floating_contact_widget.php'; ?>
</body>
</html>
