<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Bangkok');

include(__DIR__ . '/backend/classes/DatabaseManagement.class.php');
include_once(__DIR__ . '/backend/helpers/track_visit.php');
include_once(__DIR__ . '/backend/helpers/cart_count.php');
include_once(__DIR__ . '/backend/helpers/i18n.php');
thaifa_i18n_buffer_start();
$cartCount = thaifaCartCount();
$isEn = thaifa_lang() === 'en';
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

function snippetText($text, $len = 140)
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

function translateNewsTextEn($text)
{
    $txt = (string)$text;
    if (thaifa_lang() !== 'en' || $txt === '') {
        return $txt;
    }
    $map = [
        'ขอขอบพระคุณ🙏 พี่สาวของคุณปาน ธนพร' => 'sincerely thanks Ms. Pan Thanaporn\'s sister',
        'ที่ถักร้อย สร้อยรักส่งมาให้มูลนิธิ' => 'for donating handcrafted love bracelets to the foundation',
        'เพื่อจัดหารายได้เข้ากองทุนการศึกษาเพื่อพัฒนาเด็กไทยต่อไป' => 'to raise scholarship funds for Thai children.',
        'เพื่อจัดหารายได้เข้ากองScholarshipเพื่อพัฒนาเด็กThailandต่อไป' => 'to raise scholarship funds for Thai children.',
        'มูลนิธิมาออกบูธที่งานของเครือชุมทอง AIAค่ะ' => 'The foundation joined the Chumthong AIA network event.',
        'ท่านใดสนใจBook หรือ หมอนผ้าห่ม เรียนเชิญที่บูธนะคะ' => 'If you are interested in books or blanket pillows, please visit our booth.',
        'TodayThe foundation joined the Chumthong AIA network event. ท่านใดสนใจBook Or  หมอนผ้าห่ม เรียนเชิญที่บูธนะคะ' => 'The foundation joined the Chumthong AIA network event. If you are interested in books or blanket pillows, please visit our booth.',
    ];
    $txt = strtr($txt, $map);
    // Fallback cleanup for mixed Thai/English titles from imported content.
    $txt = preg_replace('/[\x{0E00}-\x{0E7F}]+/u', ' ', $txt);
    $txt = preg_replace('/\s+/u', ' ', (string)$txt);
    return trim((string)$txt);
}

$latestNews = $DB->selectAll("
    SELECT
        n.id,
        n.title,
        n.detail,
        n.posted_date,
        n.source_published_at,
        (
            SELECT ni.image_url
            FROM news_images ni
            WHERE ni.news_id = n.id
            ORDER BY ni.sort_order ASC, ni.id ASC
            LIMIT 1
        ) AS cover_image
    FROM news n
    WHERE n.is_visible = 1
    ORDER BY COALESCE(n.source_published_at, CONCAT(n.posted_date, ' 00:00:00')) DESC, n.id DESC
");
$initialNewsVisible = 3;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(thaifa_lang(), ENT_QUOTES, 'UTF-8') ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>THAIFA Foundation - มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mali:wght@200;300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --font-size: 16px;
            --background: #ffffff;
            --foreground: #303a56;
            --card: #ffffff;
            --card-foreground: #303a56;
            --primary: #233882;
            --primary-foreground: #ffffff;
            --secondary: #d9e7ef;
            --secondary-foreground: #303a56;
            --muted: #f5f8fa;
            --muted-foreground: #64748b;
            --accent: #e83b3b;
            --accent-foreground: #ffffff;
            --border: #e2e8f0;
            --input-background: #f8fafc;
            --font-weight-medium: 500;
            --font-weight-normal: 400;
        }

        * {
            border-color: var(--border);
        }

        body {
            font-family: 'Prompt', sans-serif;
            background-color: var(--background);
            color: var(--foreground);
            font-size: var(--font-size);
        }

        h1,
        h2,
        h3,
        h4,
        label,
        button {
            font-weight: var(--font-weight-medium);
            line-height: 1.5;
        }

        p,
        input {
            font-weight: var(--font-weight-normal);
            line-height: 1.5;
        }

        html {
            font-size: var(--font-size);
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        background: '#ffffff',
                        foreground: '#303a56',
                        primary: '#233882',
                        'primary-foreground': '#ffffff',
                        secondary: '#d9e7ef',
                        'secondary-foreground': '#303a56',
                        muted: '#f5f8fa',
                        'muted-foreground': '#64748b',
                        accent: '#e83b3b',
                        'accent-foreground': '#ffffff',
                        border: '#e2e8f0',
                    }
                }
            }
        }
    </script>
</head>

<body>

    <!-- Navigation - Fixed Top, 2-tier - ตรง 100% -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white shadow-sm">
        <!-- Top Bar -->
        <div class="bg-secondary/30 border-b border-border">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-10 text-sm">
                    <!-- Contact Info -->
                    <div class="hidden md:flex items-center gap-6 text-foreground/80">
                        <a href="mailto:thaifafoundation@gmail.com"
                            class="flex items-center gap-2 hover:text-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>thaifafoundation@gmail.com</span>
                        </a>
                    </div>

                    <!-- Right Side -->
                    <div class="flex items-center gap-4">
                        <!-- Shopping Cart -->
                        <a href="cart.php" class="relative text-foreground/80 hover:text-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span
                                class="absolute -top-2 -right-2 bg-accent text-white text-xs rounded-full w-4 h-4 flex items-center justify-center"><?= (int)$cartCount ?></span>
                        </a>

                        <div class="flex items-center gap-1 rounded-full border border-border px-2 py-1 bg-white/70">
                            <a href="<?= h(thaifa_lang_url('th')) ?>" class="text-xs px-2 py-0.5 rounded <?= thaifa_lang()==='th' ? 'bg-primary text-white' : 'text-foreground/70 hover:text-primary' ?>">TH</a>
                            <a href="<?= h(thaifa_lang_url('en')) ?>" class="text-xs px-2 py-0.5 rounded <?= thaifa_lang()==='en' ? 'bg-primary text-white' : 'text-foreground/70 hover:text-primary' ?>">EN</a>
                        </div>

                        <!-- Auth Buttons -->
                        <div class="flex items-center gap-2 pl-4 border-l border-border">
                            <a href="login.php"
                                class="flex items-center gap-1 text-foreground/80 hover:text-primary transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span class="hidden sm:inline"><?= h(thaifa_t('login')) ?></span>
                            </a>
                            <span class="text-foreground/40">/</span>
                            <a href="register.php" class="text-foreground/80 hover:text-primary transition-colors">
                                <span class="hidden sm:inline"><?= h(thaifa_t('register')) ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Navigation -->
        <div class="bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-20">
      <a href="index.php" class="flex-shrink-0">
        <img src="assets/images/Logo.png" alt="THAIFA Logo" class="h-20 w-auto" />
      </a>

                    <!-- Desktop Navigation -->
                    <div class="hidden lg:flex items-center gap-1">
                        <a href="index.php"
                            class="text-[#315d9f] bg-sky-100 px-4 py-2 rounded-md hover:bg-sky-100 transition-colors"><?= h(thaifa_t('home')) ?></a>
                        <a href="about.php"
                            class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100 transition-colors"><?= h(thaifa_t('about')) ?></a>
                        <a href="calendar.php"
                            class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100 transition-colors"><?= h(thaifa_t('calendar')) ?></a>
                        <a href="shop.php"
                            class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100 transition-colors"><?= h(thaifa_t('shop')) ?></a>
                        <a href="donate.php"
                            class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100 transition-colors"><?= h(thaifa_t('donate')) ?></a>
                        <a href="volunteer.php"
                            class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100 transition-colors"><?= h(thaifa_t('volunteer')) ?></a>
                        <a href="stories.php"
                            class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100 transition-colors"><?= h(thaifa_t('stories')) ?></a>
                        <a href="contact.php"
                            class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100 transition-colors"><?= h(thaifa_t('contact')) ?></a>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button class="lg:hidden p-2" onclick="toggleMobileMenu()">
        <div class="w-6 h-5 flex flex-col justify-between">
          <span class="w-full h-0.5 bg-primary"></span>
          <span class="w-full h-0.5 bg-primary"></span>
          <span class="w-full h-0.5 bg-primary"></span>
        </div>
      </button>
    </div>

                    <!-- Mobile Menu -->
                    <div id="mobileMenu" class="lg:hidden hidden border-t border-border bg-white">
      <a href="index.php" class="block px-4 py-3 border-b border-border"><?= h(thaifa_t('home')) ?></a>
      <a href="about.php" class="block px-4 py-3 border-b border-border"><?= h(thaifa_t('about')) ?></a>
      <a href="calendar.php" class="block px-4 py-3 border-b border-border"><?= h(thaifa_t('calendar')) ?></a>
      <a href="shop.php" class="block px-4 py-3 border-b border-border"><?= h(thaifa_t('shop')) ?></a>
      <a href="donate.php" class="block px-4 py-3 border-b border-border"><?= h(thaifa_t('donate')) ?></a>
      <a href="volunteer.php" class="block px-4 py-3 border-b border-border"><?= h(thaifa_t('volunteer')) ?></a>
      <a href="stories.php" class="block px-4 py-3 border-b border-border"><?= h(thaifa_t('stories')) ?></a>
      <a href="contact.php" class="block px-4 py-3"><?= h(thaifa_t('contact')) ?></a>
    </div>
  </div>
</div>
</nav>

    <!-- Main Content with pt-[120px] -->
    <main class="pt-[120px]">

        <!-- Hero Section -->
        <section id="home" class="relative min-h-[600px] lg:min-h-[700px] flex items-center overflow-hidden">

            <!-- Background Image (เต็มจอเสมอ) -->
            <img
                src="assets/images/cover.png"
                alt="cover"
                class="absolute inset-0 z-0 w-full h-full object-cover" />
            <!-- Content -->
            <div class="relative z-20 w-full">
                <div class="max-w-7xl mx-auto px-6 sm:px-12 lg:px-20 py-20">
                    <div class="max-w-5xl">

                        <!-- Headline Image -->
                        <img
                            src="assets/images/หนึ่งความรักจากฉัน หมื่นพันความรักฝันของเธอ.png"
                            alt="หนึ่งความรักจากฉัน หมื่นพันความฝันของเธอ"
                            class="w-full max-w-[800px] h-auto object-contain" />
                    </div>

                    <!-- CTA -->
                    <div class="mt-8">
                        <a href="donate.php"
                            class="inline-flex items-center gap-2 bg-accent hover:bg-accent/90 text-white px-8 py-6 rounded-full text-lg shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            ร่วมบริจาค
                        </a>
                    </div>
                </div>
            </div>
            </div>

            <!-- Hands Giving Heart -->
            <div class="absolute bottom-0 right-0 z-20 w-[400px] sm:w-[480px] lg:w-[580px] xl:w-[680px]">
                <img src="assets/images/hands.png"
                    alt=""
                    class="w-full h-auto object-contain object-bottom object-right" />
            </div>
        </section>


        <!-- Quick Stats Section -->
        <section class="py-16 bg-gradient-to-b from-white to-secondary/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="text-center p-8 bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all">
                        <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="text-4xl text-primary mb-2">11,000+</div>
                        <p class="text-foreground/70">ผู้เข้าร่วมกิจกรรมทั่วประเทศ</p>
                    </div>
                    <div class="text-center p-8 bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all">
                        <div class="w-16 h-16 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                        <div class="text-4xl text-accent mb-2">20+ ปี</div>
                        <p class="text-foreground/70">ดำเนินงานอย่างต่อเนื่อง</p>
                    </div>
                    <div class="text-center p-8 bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all">
                        <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                        </div>
                        <div class="text-4xl text-primary mb-2">2 ล้าน+</div>
                        <p class="text-foreground/70">บาทต่อปี สนับสนุนสังคม</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Preview Section -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <div class="inline-block mb-4 px-6 py-2 bg-primary/10 rounded-full">
                            <span class="text-primary">เกี่ยวกับเรา</span>
                        </div>
                        <h2 class="text-4xl md:text-5xl text-primary mb-6">
                            มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน
                        </h2>
                        <p class="text-xl text-primary/90 italic mb-6 pb-4 border-b-2 border-primary/20">
                            "จากสิ่งที่เราได้รับ กลับคืนสู่สังคม"
                        </p>
                        <p class="text-foreground/80 mb-6 leading-relaxed">
                            ก่อตั้งขึ้นเมื่อปี พ.ศ. 2544 โดยกลุ่มตัวแทนประกันชีวิตในประเทศไทย
                            ภายใต้เจตนารมณ์ที่จะ "คืนกำไรสู่สังคม" หลังจากประสบความสำเร็จจากการจัดงาน
                            APLIC (สมาคมตัวแทนประกันชีวิตแห่งเอเชียแปซิฟิก) ที่มีผู้เข้าร่วมกว่า 11,000 คน
                        </p>
                        <p class="text-foreground/80 mb-8 leading-relaxed">
                            มูลนิธิทำหน้าที่เป็นศูนย์กลางของความร่วมมือระหว่างตัวแทนประกันชีวิตทั่วประเทศ
                            เพื่อช่วยเหลือเยาวชนขาดแคลน สนับสนุนทุนการศึกษา จัดหาอุปกรณ์ทางการแพทย์
                            และส่งเสริมกิจกรรมเพื่อสังคมอย่างต่อเนื่องมากว่า 20 ปี
                        </p>
                        <a href="about.php"
                            class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90 text-white rounded-full px-8 py-3 transition-all">
                            เกี่ยวกับเราเพิ่มเติม
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    </div>
                    <div class="relative">
                        <div class="relative rounded-3xl overflow-hidden shadow-2xl">
                            <img src="assets/images/APLIC 6 Conference.jpg"
                                alt="APLIC 6 Conference"
                                class="w-full h-[600px] object-cover" />
                            <!-- Figma: figma:asset/f6662b9721b933ec86972122ebf7d9511d690faf.png -->
                            <div class="absolute inset-0 bg-gradient-to-t from-primary/30 to-transparent"></div>
                        </div>
                        <!-- Decorative Element -->
                        <div class="absolute -bottom-6 -right-6 w-40 h-40 bg-accent/20 rounded-full blur-3xl -z-10">
                        </div>
                        <div class="absolute -top-6 -left-6 w-32 h-32 bg-primary/20 rounded-full blur-3xl -z-10"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- News Section -->
        <section id="news" class="py-20 bg-gradient-to-b from-secondary/10 to-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-12">
                    <div class="inline-block mb-4 px-6 py-2 bg-primary/10 rounded-full">
                        <span class="text-primary">ข่าวสารและกิจกรรม</span>
                    </div>
                    <h2 class="text-4xl md:text-5xl text-primary mb-2">
                        ข่าวสารล่าสุด
                    </h2>
                    <p class="text-foreground/70">
                        ติดตามกิจกรรมและข่าวสารของมูลนิธิ THAIFA
                    </p>
                </div>

                <!-- News Grid -->
                <div id="newsGrid" class="grid md:grid-cols-3 gap-6">
                    <?php if (!empty($latestNews)): ?>
                        <?php foreach ($latestNews as $idx => $item): ?>
                            <?php
                                $newsTitle = translateNewsTextEn((string)($item['title'] ?? ''));
                                $newsDetail = translateNewsTextEn((string)($item['detail'] ?? ''));
                            ?>
                            <a href="news-detail.php?id=<?= (int)$item['id'] ?>"
                               class="bg-card border border-border rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 group cursor-pointer block news-card <?= $idx >= $initialNewsVisible ? 'hidden' : '' ?>">
                                <div class="relative h-52 overflow-hidden">
                                    <img src="<?= h(newsImageUrl($item['cover_image'] ?? '')) ?>"
                                         alt="<?= h($newsTitle !== '' ? $newsTitle : ($isEn ? 'News' : 'ข่าวสาร')) ?>"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                                    <div class="absolute bottom-4 left-4 flex items-center gap-2 text-white text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span><?= h(thaiDate($item['posted_date'] ?? '')) ?></span>
                                    </div>
                                </div>
                                <div class="p-5">
                                    <h3 class="text-primary text-xl line-clamp-2 mb-3"><?= h($newsTitle) ?></h3>
                                    <p class="text-foreground/70 text-sm leading-relaxed line-clamp-3 mb-3">
                                        <?= h(snippetText($newsDetail, 120)) ?>
                                    </p>
                                    <span class="text-accent text-sm hover:underline">อ่านต่อ →</span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="md:col-span-3 rounded-2xl border border-border bg-white p-10 text-center text-foreground/70">
                            ยังไม่มีข่าวที่เผยแพร่ในขณะนี้
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (count($latestNews) > $initialNewsVisible): ?>
                <div class="mt-8 text-center">
                    <button id="showAllNewsBtn" type="button" onclick="toggleAllNews(true)"
                            class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90 text-white rounded-full px-6 py-3 transition-all">
                        ดูข่าวทั้งหมด
                    </button>
                    <button id="collapseNewsBtn" type="button" onclick="toggleAllNews(false)"
                            class="hidden inline-flex items-center gap-2 border border-primary text-primary hover:bg-primary/10 rounded-full px-6 py-3 transition-all">
                        ย่อข่าวทั้งหมด
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Footer (compact) -->
    <footer class="bg-primary text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                
                <!-- About -->
                <div>
                    <div class="mb-6">
                        <img src="assets/images/Logo.png" alt="THAIFA Logo"
                            class="h-16 w-auto bg-white rounded-md p-1" />
                    </div>
                    <p class="text-white/80 text-sm leading-relaxed mb-4">
                        มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน
                        มุ่งมั่นสร้างโอกาสและพัฒนาคุณภาพชีวิตของเด็กและเยาวชนไทย
                    </p>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-accent" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="text-white/90 text-sm">ขอบคุณที่ร่วมเคียงฝันไปด้วยกัน</span>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="mb-6 text-xl">เมนูหลัก</h3>
                    <ul class="space-y-3">
                        <li><a href="index.php"
                                class="text-white/80 hover:text-accent transition-colors text-sm">หน้าแรก</a></li>
                        <li><a href="about.php"
                                class="text-white/80 hover:text-accent transition-colors text-sm">เกี่ยวกับเรา</a></li>
                        <li><a href="donate.php"
                                class="text-white/80 hover:text-accent transition-colors text-sm">การบริจาค</a></li>
                        <li><a href="volunteer.php"
                                class="text-white/80 hover:text-accent transition-colors text-sm">จิตอาสา</a></li>
                        <li><a href="stories.php"
                                class="text-white/80 hover:text-accent transition-colors text-sm">เสียงจากใจ</a></li>
                        <li><a href="contact.php"
                                class="text-white/80 hover:text-accent transition-colors text-sm">ติดต่อเรา</a></li>
                    </ul>
                </div>

                <!-- Programs -->
                <div>
                    <h3 class="mb-6 text-xl">โครงการของเรา</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#" class="text-white/80 hover:text-accent transition-colors">ทุนการศึกษา</a></li>
                        <li><a href="#"
                                class="text-white/80 hover:text-accent transition-colors">ช่วยเหลือเด็กกำพร้า</a></li>
                        <li><a href="#" class="text-white/80 hover:text-accent transition-colors">เครื่องมือแพทย์</a>
                        </li>
                        <li><a href="#" class="text-white/80 hover:text-accent transition-colors">กิจกรรมชุมชน</a></li>
                        <li><a href="#" class="text-white/80 hover:text-accent transition-colors">เสียงจากใจผู้รับ</a>
                        </li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="mb-6 text-xl">ติดต่อเรา</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-accent" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-white/80 text-sm">
                                อาคาร จูเวลเลอรี่ ห้อง 138/32 ชั้น 12<br />
                                เลขที่ 138 ถนนนเรศ แขวงสี่พระยา<br />
                                เขตบางรัก กรุงเทพฯ 10500
                            </span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 text-accent" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="text-white/80 text-sm">thaifafoundation@gmail.com</span>
                        </li>
                    </ul>

                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="pt-8 border-t border-white/10">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-white/60 text-sm text-center md:text-left">
                        &copy; 2025 มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน (THAIFA Foundation). สงวนลิขสิทธิ์.
                    </p>
                    <div class="flex flex-wrap justify-center gap-6 text-sm">
                        <a href="#" class="text-white/60 hover:text-accent transition-colors">นโยบายความเป็นส่วนตัว</a>
                        <a href="#" class="text-white/60 hover:text-accent transition-colors">ข้อกำหนดการใช้งาน</a>
                        <a href="#" class="text-white/60 hover:text-accent transition-colors">รายงานประจำปี</a>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <p class="text-white/40 text-xs">
                        มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน | เลขประจำตัวผู้เสียภาษีอากร: 0993000440226
                    </p>
                </div>
            </div>
        </div>
    </footer>    </div>

    <script>
  function toggleMobileMenu() {
    const el = document.getElementById('mobileMenu');
    if (!el) return;
    el.classList.toggle('hidden');
  }
  function toggleAllNews(showAll) {
    const cards = document.querySelectorAll('#newsGrid .news-card');
    if (!cards.length) return;

    cards.forEach((card, index) => {
      if (showAll || index < <?= (int)$initialNewsVisible ?>) {
        card.classList.remove('hidden');
      } else {
        card.classList.add('hidden');
      }
    });

    const showBtn = document.getElementById('showAllNewsBtn');
    const collapseBtn = document.getElementById('collapseNewsBtn');
    if (showBtn && collapseBtn) {
      if (showAll) {
        showBtn.classList.add('hidden');
        collapseBtn.classList.remove('hidden');
      } else {
        collapseBtn.classList.add('hidden');
        showBtn.classList.remove('hidden');
        document.getElementById('news')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    }
  }</script>

<?php include __DIR__ . '/backend/helpers/floating_contact_widget.php'; ?>
</body>

</html>
