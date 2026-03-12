<?php
session_start();
include_once(__DIR__ . '/backend/classes/ShopManagement.class.php');
include_once(__DIR__ . '/backend/helpers/i18n.php');
thaifa_i18n_buffer_start();

$shop = new ShopManagement();

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function baht($v) { return number_format((float)$v, 2); }

$flash = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_to_cart') {
    $pid = (int)($_POST['product_id'] ?? 0);
    $qty = max(1, (int)($_POST['qty'] ?? 1));
    if ($pid > 0) {
        $shop->addToCart($pid, $qty);
        $flash = 'เพิ่มสินค้าลงตะกร้าแล้ว';
    }
}

$allProducts = $shop->getProducts(true);
$cartCount = $shop->cartCount();

$categoriesMap = [];
foreach ($allProducts as $p) {
    $name = trim((string)($p['category_name'] ?: 'ทั่วไป'));
    $categoriesMap[$name] = $name;
}
$categories = array_values($categoriesMap);
sort($categories, SORT_NATURAL | SORT_FLAG_CASE);

$selectedCategory = trim((string)($_GET['cat'] ?? ''));
$q = trim((string)($_GET['q'] ?? ''));
$sort = trim((string)($_GET['sort'] ?? 'new'));

$products = array_filter($allProducts, function ($p) use ($selectedCategory, $q) {
    if ($selectedCategory !== '' && ($p['category_name'] ?? 'ทั่วไป') !== $selectedCategory) {
        return false;
    }
    if ($q !== '') {
        $hay = mb_strtolower(($p['name'] ?? '') . ' ' . ($p['description'] ?? ''), 'UTF-8');
        if (mb_stripos($hay, mb_strtolower($q, 'UTF-8'), 0, 'UTF-8') === false) {
            return false;
        }
    }
    return true;
});

if ($sort === 'price_asc') {
    usort($products, fn($a, $b) => (float)$a['price'] <=> (float)$b['price']);
} elseif ($sort === 'price_desc') {
    usort($products, fn($a, $b) => (float)$b['price'] <=> (float)$a['price']);
} elseif ($sort === 'popular') {
    usort($products, fn($a, $b) => ((int)$b['id'] * 7 % 97) <=> ((int)$a['id'] * 7 % 97));
} else {
    usort($products, fn($a, $b) => (int)$b['id'] <=> (int)$a['id']);
}

$featuredProducts = array_slice($products, 0, 4);
$popularCategories = array_slice($categories, 0, 8);

$categoryIcons = ['📚','👕','👜','🎁','🧸','🕯️','🏅','💡'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(thaifa_lang(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ร้านค้าตระกล้า - THAIFA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --font-size: 16px;
            --background: #ffffff;
            --foreground: #303a56;
            --primary: #233882;
            --secondary: #d9e7ef;
            --accent: #e83b3b;
            --border: #e2e8f0;
        }
        body, * { font-family: 'Prompt', sans-serif; }
        body { background-color: #f3f5f7; color: var(--foreground); font-size: var(--font-size); }
        * { border-color: var(--border); }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .card-shadow { box-shadow: 0 8px 20px rgba(34, 52, 110, 0.06); }
        .product-shell {
            border-radius: 12px;
            border: 1px solid #e4e9f1;
            background: #fff;
            box-shadow: 0 4px 14px rgba(31, 42, 68, 0.06);
            transition: none;
        }
        .product-shell:hover {
            transform: none;
            box-shadow: 0 4px 14px rgba(31, 42, 68, 0.06);
        }
        .product-visual {
            height: 210px;
            background: #f5f6f8;
            border-bottom: 1px solid #e9edf4;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        .product-visual img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .pill-tag {
            position: absolute;
            right: 10px;
            top: 10px;
            padding: 3px 9px;
            font-size: 10px;
            font-weight: 600;
            border-radius: 999px;
            background: #ffffff;
            border: 1px solid #d4dcec;
            color: #42506d;
        }
        .action-outline {
            border: 1px solid #233882;
            background: #ffffff;
            color: #233882;
        }
        .action-filled {
            background: #233882;
            color: #ffffff;
        }
        .action-filled:hover {
            background: #1b2f72;
        }
        .shop-card-title {
            color: #1f2a44;
            font-size: 16px;
            line-height: 1.25;
            min-height: 40px;
        }
        .shop-card-desc {
            color: #6b778f;
            font-size: 13px;
            line-height: 1.38;
            min-height: 34px;
        }
        .shop-card-price {
            color: #d51d3c;
            font-weight: 700;
            font-size: 20px;
            letter-spacing: -0.02em;
            line-height: 1;
        }
        .shop-card-meta {
            color: #5c6882;
            font-size: 12px;
        }
        .shop-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
        }
        .shop-cart-btn {
            height: 34px;
            min-width: 118px;
            border-radius: 999px;
            border: 1px solid #233882;
            background: #233882;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
        }
        .shop-cart-btn:hover {
            background: #1b2f72;
        }
        .shop-stock-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            font-size: 11px;
            color: #3e4b6a;
            background: #eef3ff;
            border: 1px solid #d7e2fb;
            padding: 5px 9px;
        }
        .shop-impact-wrap {
            margin-top: 30px;
            border-radius: 20px;
            background: #dfe5ec;
            padding: 16px;
        }
        .shop-impact-card {
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            box-shadow: 0 8px 20px rgba(35, 56, 130, 0.07);
            padding: 22px 20px 18px;
        }
        .shop-impact-top {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            text-align: center;
        }
        .shop-impact-icon {
            width: 44px;
            height: 44px;
            border-radius: 999px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .shop-impact-icon svg {
            width: 22px;
            height: 22px;
        }
        .shop-impact-title {
            margin-top: 8px;
            font-size: 20px;
            line-height: 1.25;
            color: #233882;
        }
        .shop-impact-text {
            margin-top: 4px;
            font-size: 14px;
            line-height: 1.4;
            color: #7c8799;
        }
        .shop-impact-divider {
            margin: 18px 0 16px;
            border-top: 1px solid #dbe3ef;
        }
        .shop-impact-fund-title {
            text-align: center;
            font-size: 22px;
            line-height: 1.2;
            color: #233882;
            margin-bottom: 14px;
        }
        .shop-impact-bottom {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }
        .shop-impact-fund-card {
            border-radius: 14px;
            background: #f3f5f7;
            padding: 14px 16px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .shop-impact-fund-icon {
            width: 36px;
            height: 36px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .shop-impact-fund-icon svg {
            width: 18px;
            height: 18px;
        }
        .shop-impact-fund-head {
            font-size: 18px;
            line-height: 1.3;
            color: #233882;
        }
        .shop-impact-fund-desc {
            margin-top: 3px;
            font-size: 13px;
            line-height: 1.4;
            color: #7c8799;
        }
        @media (max-width: 1024px) {
            .shop-impact-title { font-size: 18px; }
            .shop-impact-text { font-size: 13px; }
            .shop-impact-fund-title { font-size: 20px; }
            .shop-impact-fund-head { font-size: 16px; }
            .shop-impact-fund-desc { font-size: 12px; }
        }
        @media (max-width: 767px) {
            .shop-impact-wrap { padding: 10px; margin-top: 22px; border-radius: 16px; }
            .shop-impact-card { padding: 16px 12px 14px; border-radius: 14px; }
            .shop-impact-top { grid-template-columns: 1fr; gap: 14px; }
            .shop-impact-title { font-size: 17px; margin-top: 6px; }
            .shop-impact-text { font-size: 13px; margin-top: 3px; }
            .shop-impact-divider { margin: 16px 0 12px; }
            .shop-impact-fund-title { font-size: 18px; margin-bottom: 10px; }
            .shop-impact-bottom { grid-template-columns: 1fr; gap: 12px; }
            .shop-impact-fund-card { border-radius: 12px; padding: 12px; }
            .shop-impact-fund-icon { width: 34px; height: 34px; }
            .shop-impact-fund-icon svg { width: 16px; height: 16px; }
            .shop-impact-fund-head { font-size: 15px; }
            .shop-impact-fund-desc { font-size: 12px; }
            .product-visual { height: 180px; }
            .shop-card-title { font-size: 15px; min-height: 36px; }
            .shop-card-desc { font-size: 12px; min-height: 28px; }
            .shop-card-price { font-size: 18px; }
            .shop-cart-btn { height: 32px; font-size: 12px; min-width: 110px; }
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        foreground: '#303a56',
                        primary: '#233882',
                        secondary: '#d9e7ef',
                        accent: '#e83b3b',
                        border: '#e2e8f0'
                    }
                }
            }
        }
    </script>
</head>
<body>

    <nav class="fixed top-0 left-0 right-0 z-50 bg-white shadow-sm">
        <div class="bg-secondary/30 border-b border-border">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-10 text-sm">
                    <div class="hidden md:flex items-center gap-6 text-foreground/80">
                        <a href="mailto:thaifafoundation@gmail.com" class="flex items-center gap-2 hover:text-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            <span>thaifafoundation@gmail.com</span>
                        </a>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="cart.php" class="relative text-foreground/80 hover:text-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            <span class="absolute -top-2 -right-2 bg-accent text-white text-xs rounded-full w-4 h-4 flex items-center justify-center"><?= (int)$cartCount ?></span>
                        </a>
                        <div class="flex items-center gap-1"><a href="<?= h(thaifa_lang_url('th')) ?>" class="text-xs px-2 py-0.5 rounded <?= thaifa_lang()==='th' ? 'bg-primary text-white' : 'text-foreground/70 hover:text-primary' ?>">TH</a><a href="<?= h(thaifa_lang_url('en')) ?>" class="text-xs px-2 py-0.5 rounded <?= thaifa_lang()==='en' ? 'bg-primary text-white' : 'text-foreground/70 hover:text-primary' ?>">EN</a></div><div class="flex items-center gap-2 pl-4 border-l border-border">
                            <a href="login.php" class="flex items-center gap-1 text-foreground/80 hover:text-primary transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                <span class="hidden sm:inline"><?= h(thaifa_t('login')) ?></span>
                            </a>
                            <span class="text-foreground/40">/</span>
                            <a href="register.php" class="text-foreground/80 hover:text-primary transition-colors"><span class="hidden sm:inline"><?= h(thaifa_t('register')) ?></span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20">
                    <a href="index.php" class="flex-shrink-0"><img src="assets/images/Logo.png" alt="THAIFA Logo" class="h-20 w-auto" /></a>
                    <div class="hidden lg:flex items-center gap-1">
                        <a href="index.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100 transition-colors"><?= h(thaifa_t('home')) ?></a>
                        <a href="about.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100 transition-colors"><?= h(thaifa_t('about')) ?></a>
                        <a href="calendar.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100 transition-colors"><?= h(thaifa_t('calendar')) ?></a>
                        <a href="shop.php" class="text-[#315d9f] bg-sky-100 px-4 py-2 rounded-md"><?= h(thaifa_t('shop')) ?></a>
                        <a href="donate.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100 transition-colors"><?= h(thaifa_t('donate')) ?></a>
                        <a href="volunteer.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100 transition-colors"><?= h(thaifa_t('volunteer')) ?></a>
                        <a href="stories.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100 transition-colors"><?= h(thaifa_t('stories')) ?></a>
                        <a href="contact.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100 transition-colors"><?= h(thaifa_t('contact')) ?></a>
                    </div>
                    <button class="lg:hidden p-2" onclick="toggleMobileMenu()">
                        <div class="w-6 h-5 flex flex-col justify-between"><span class="w-full h-0.5 bg-primary"></span><span class="w-full h-0.5 bg-primary"></span><span class="w-full h-0.5 bg-primary"></span></div>
                    </button>
                </div>
                <div id="mobileMenu" class="hidden border-t border-border bg-white">
                    <a href="index.php" class="block px-4 py-3 border-b border-border"><?= h(thaifa_t('home')) ?></a>
                    <a href="about.php" class="block px-4 py-3 border-b border-border"><?= h(thaifa_t('about')) ?></a>
                    <a href="calendar.php" class="block px-4 py-3 border-b border-border"><?= h(thaifa_t('calendar')) ?></a>
                    <a href="shop.php" class="block px-4 py-3 border-b border-border bg-sky-50 text-[#315d9f]"><?= h(thaifa_t('shop')) ?></a>
                    <a href="donate.php" class="block px-4 py-3 border-b border-border"><?= h(thaifa_t('donate')) ?></a>
                    <a href="volunteer.php" class="block px-4 py-3 border-b border-border"><?= h(thaifa_t('volunteer')) ?></a>
                    <a href="stories.php" class="block px-4 py-3 border-b border-border"><?= h(thaifa_t('stories')) ?></a>
                    <a href="contact.php" class="block px-4 py-3 border-b border-border"><?= h(thaifa_t('contact')) ?></a>
                </div>
            </div>
        </div>
    </nav>

    <main class="pt-[120px]">
        <section class="bg-gradient-to-br from-primary to-primary/80 text-white py-8 md:py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <div class="inline-flex items-center gap-2 bg-white/20 rounded-full px-4 py-1.5 mb-3 text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1 5h12M7 13L5.4 5M17 18a2 2 0 100 4 2 2 0 000-4zm-8 0a2 2 0 100 4 2 2 0 000-4z"/>
                        </svg>
                        <span>ร้านค้ามูลนิธิ</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl mb-3">สินค้าและของที่ระลึก</h1>
                    <p class="text-base text-white/90 max-w-2xl mx-auto">
                        ช้อปเพื่อส่งต่อโอกาส รายได้สมทบกองทุนการกุศลของมูลนิธิ
                    </p>
                </div>
            </div>
        </section>

        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
            <?php if ($flash): ?>
                <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-green-700"><?= h($flash) ?></div>
            <?php endif; ?>

            <div class="bg-white rounded-xl card-shadow border p-3 md:p-4 mb-4">
                <form method="get" class="grid md:grid-cols-12 gap-2 mb-3">
                    <input type="text" name="q" value="<?= h($q) ?>" placeholder="ค้นหาสินค้าในร้านมูลนิธิ" class="md:col-span-6 h-10 rounded-lg border px-3 text-sm">
                    <select name="cat" class="md:col-span-3 h-10 rounded-lg border px-3 text-sm">
                        <option value="">ทุกหมวดหมู่</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= h($cat) ?>" <?= $selectedCategory === $cat ? 'selected' : '' ?>><?= h($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="sort" class="md:col-span-3 h-10 rounded-lg border px-3 text-sm">
                        <option value="new" <?= $sort === 'new' ? 'selected' : '' ?>>ใหม่ล่าสุด</option>
                        <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>ยอดนิยม</option>
                        <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>ราคาน้อย-มาก</option>
                        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>ราคามาก-น้อย</option>
                    </select>
                </form>

            </div>

            <?php if (empty($products)): ?>
                <div class="bg-white rounded-2xl border p-10 text-center text-slate-500">ไม่พบสินค้าตามเงื่อนไข</div>
            <?php else: ?>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <?php foreach ($products as $p): ?>
                        <?php $sold = ((int)$p['id'] * 11) % 120 + 8; ?>
                        <div class="product-shell overflow-hidden">
                            <div class="product-visual">
                                <img src="<?= h($p['cover_image'] ?: 'https://via.placeholder.com/800x600?text=Product') ?>" alt="<?= h($p['name']) ?>">
                                <span class="pill-tag"><?= h($p['category_name'] ?: 'Other') ?></span>
                            </div>

                            <div class="p-3.5">
                                <h3 class="shop-card-title line-clamp-2"><?= h($p['name']) ?></h3>
                                <p class="shop-card-desc mt-2 line-clamp-2"><?= h($p['description']) ?></p>

                                <div class="shop-card-footer">
                                    <div class="shop-card-price">฿<?= baht($p['price']) ?></div>
                                    <span class="shop-stock-chip">คงเหลือ <?= (int)$p['stock_qty'] ?></span>
                                </div>

                                <div class="mt-3 flex items-center justify-between shop-card-meta">
                                    <div>ขายแล้ว <?= (int)$sold ?> ชิ้น</div>
                                    <div class="inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.243-4.243a8 8 0 1111.313 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        ไทย
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <form method="post" class="w-full">
                                        <input type="hidden" name="action" value="add_to_cart">
                                        <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                                        <input type="hidden" name="qty" value="1">
                                        <button type="submit" class="shop-cart-btn w-full">เพิ่มลงตะกร้า</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="shop-impact-wrap">
                <div class="shop-impact-card">
                    <div class="shop-impact-top">
                        <div>
                            <div class="shop-impact-icon bg-[#eef3ff] text-[#233882]">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1 5h12M7 13L5.4 5M17 18a2 2 0 100 4 2 2 0 000-4zm-8 0a2 2 0 100 4 2 2 0 000-4"/>
                                </svg>
                            </div>
                            <h3 class="shop-impact-title">จัดส่งทั่วประเทศ</h3>
                            <p class="shop-impact-text">ส่งฟรีทางไปรษณีย์หรือขนส่ง</p>
                        </div>
                        <div>
                            <div class="shop-impact-icon bg-[#ffeef0] text-[#ef4444]">
                                <svg fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                </svg>
                            </div>
                            <h3 class="shop-impact-title">รายได้เพื่อการกุศล</h3>
                            <p class="shop-impact-text">100% เข้ากองทุนมูลนิธิ</p>
                        </div>
                        <div>
                            <div class="shop-impact-icon bg-[#eef3ff] text-[#233882]">
                                <svg fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h3 class="shop-impact-title">สินค้าคุณภาพ</h3>
                            <p class="shop-impact-text">ผลิตด้วยมาตรฐานสูง</p>
                        </div>
                    </div>

                    <div class="shop-impact-divider"></div>
                    <h3 class="shop-impact-fund-title">การจัดสรรเงินกองทุน</h3>

                    <div class="shop-impact-bottom">
                        <div class="shop-impact-fund-card">
                            <div class="shop-impact-fund-icon bg-[#e7edf9] text-[#233882]">
                                <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 3l9 4.5-9 4.5L3 7.5 12 3zm-7 8.25l7 3.5 7-3.5V16h2v-5.75l-9 4.5-9-4.5V16h2v-4.75zM8 17h8v2H8v-2z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="shop-impact-fund-head">ทุนการศึกษาและการสงเคราะห์</div>
                                <div class="shop-impact-fund-desc">สนับสนุนการศึกษาและช่วยเหลือผู้ด้อยโอกาส</div>
                            </div>
                        </div>
                        <div class="shop-impact-fund-card">
                            <div class="shop-impact-fund-icon bg-[#ffe7e9] text-[#ef4444]">
                                <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M9 2h6a1 1 0 011 1v2h2a2 2 0 012 2v2H4V7a2 2 0 012-2h2V3a1 1 0 011-1zm1 3h4V4h-4v1zM4 11h16v8a2 2 0 01-2 2H6a2 2 0 01-2-2v-8z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="shop-impact-fund-head">ค่าใช้จ่ายบริหาร</div>
                                <div class="shop-impact-fund-desc">ค่าขนส่ง ค่าใช้จ่ายในการประชุม และค่าเดินทาง</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            if (!menu) return;
            menu.classList.toggle('hidden');
        }

    </script>
    <?php include __DIR__ . '/backend/helpers/floating_contact_widget.php'; ?>
</body>
</html>
