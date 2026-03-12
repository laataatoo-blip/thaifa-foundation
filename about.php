<?php
include(__DIR__ . '/backend/classes/DatabaseManagement.class.php');
include_once(__DIR__ . '/backend/classes/TeamManagement.class.php');
include_once(__DIR__ . '/backend/helpers/track_visit.php');
include_once(__DIR__ . '/backend/helpers/cart_count.php');
include_once(__DIR__ . '/backend/helpers/i18n.php');
thaifa_i18n_buffer_start();
$cartCount = thaifaCartCount();
$DB = new DatabaseManagement();
$teamManagement = new TeamManagement();

function h($str)
{
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

function imageUrlForFrontend($path)
{
    $path = trim((string)$path);
    if ($path === '') {
        return 'assets/images/about aplic.jpg';
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

$aboutHeroImage = 'assets/images/about aplic.jpg';
try {
    $row = $DB->selectOne("
        SELECT ni.image_url
        FROM news n
        INNER JOIN news_images ni ON ni.news_id = n.id
        WHERE n.is_visible = 1
          AND ni.image_url IS NOT NULL
          AND ni.image_url <> ''
        ORDER BY COALESCE(n.source_published_at, CONCAT(n.posted_date, ' 00:00:00')) DESC, ni.sort_order ASC, ni.id ASC
        LIMIT 1
    ");
    if (!empty($row['image_url'])) {
        $aboutHeroImage = imageUrlForFrontend($row['image_url']);
    }
} catch (Throwable $e) {
    $aboutHeroImage = 'assets/images/about aplic.jpg';
}

$teamGroups = ['advisors' => [], 'executives' => [], 'committee' => []];
try {
    $teamGroups = $teamManagement->membersGrouped(true);
} catch (Throwable $e) {
    $teamGroups = ['advisors' => [], 'executives' => [], 'committee' => []];
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(thaifa_lang(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เกี่ยวกับเรา - THAIFA Foundation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mali:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
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
        
        h1, h2, h3, h4, label, button {
            font-weight: var(--font-weight-medium);
            line-height: 1.5;
        }
        
        p, input {
            font-weight: var(--font-weight-normal);
            line-height: 1.5;
        }
        
        html {
            font-size: var(--font-size);
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
    
    <!-- Navigation - Fixed Top, 2-tier -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white shadow-sm">
        <!-- Top Bar -->
        <div class="bg-secondary/30 border-b border-border">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-10 text-sm">
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

                    <div class="flex items-center gap-4">
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

                    <!-- Main Navigation Links -->
                    <div class="flex items-center gap-1 flex-wrap">
                        <a href="index.php"
                            class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100 transition-colors"><?= h(thaifa_t('home')) ?></a>
                        <a href="about.php"
                            class="text-[#315d9f] bg-sky-100 px-4 py-2 rounded-md hover:bg-sky-100 transition-colors"><?= h(thaifa_t('about')) ?></a>
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
                    <button class="hidden p-2" onclick="toggleMobileMenu()">
                        <div class="w-6 h-5 flex flex-col justify-between">
                            <span class="w-full h-0.5 bg-primary"></span>
                            <span class="w-full h-0.5 bg-primary"></span>
                            <span class="w-full h-0.5 bg-primary"></span>
                        </div>
                    </button>
                </div>

                <!-- Mobile Menu -->
                <div id="mobileMenu" class="hidden border-t border-border bg-white">
                    <a href="index.php" class="block px-4 py-3 border-b border-border"><?= h(thaifa_t('home')) ?></a>
                    <a href="about.php" class="block px-4 py-3 border-b border-border bg-sky-50 text-[#315d9f]"><?= h(thaifa_t('about')) ?></a>
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

<!-- Main Content -->
<main class="pt-[120px]">

        
        <!-- 1. About Section -->
        <section id="about" class="py-20 bg-gradient-to-b from-secondary/20 to-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="text-center mb-16">
                    <div class="inline-block mb-4 px-6 py-2 bg-accent/10 rounded-full">
                        <span class="text-accent">เกี่ยวกับเรา</span>
                    </div>
                    <h2 class="text-4xl md:text-5xl text-primary mb-4">
                        มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน
                    </h2>
                </div>

                <!-- Main Content - Two Columns -->
                <div class="grid lg:grid-cols-2 gap-12 items-center mb-16">
                    <!-- Left - Image -->
                    <div class="relative">
                        <div class="relative rounded-3xl overflow-hidden shadow-xl">
                            <img
                                src="<?= h($aboutHeroImage) ?>"
                                alt="THAIFA Foundation - มอบทุนการศึกษา"
                                onerror="this.onerror=null;this.src='assets/images/about aplic.jpg';"
                                class="w-full h-[500px] object-cover"
                            />
                            <!-- Figma: figma:asset/4e491dc46acd78e1aa2e8dda1d3918386daea8f0.png -->
                            <div class="absolute inset-0 bg-gradient-to-t from-primary/40 to-transparent"></div>
                        </div>
                    </div>

                    <!-- Right - Content -->
                    <div>
                        <!-- Motto -->
                        <div class="mb-6 pb-6 border-b-2 border-primary/20">
                            <p class="text-2xl text-primary/90 italic">
                                "จากสิ่งที่เราได้รับ คืนกลับสู่สังคม"
                            </p>
                        </div>

                        <h3 class="text-3xl text-primary mb-6">
                            มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน
                        </h3>
                        
                        <!-- First Paragraph -->
                        <p class="text-foreground/80 mb-4 leading-relaxed text-[15px]">
                            มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน เดิมชื่อมูลนิธิตัวแทนประกันชีวิตเพื่อการกุศล ก่อตั้งขึ้นเมื่อปี พ.ศ. 2544 
                            สืบเนื่องจากผลสำเร็จในการการจัดงานประชุม APLIC ครั้งที่6 (สภาที่ปรึกษาการเงินแห่งเอเซียแปซิฟิค) โดยซึ่งประเทศไทยเป็นเจ้าภาพจัดขึ้น ซึ่งการสัมมนานี้ยังคงถูกบันทึกให้เป็นการสัมมนาตัวแทนประกันชีวิตที่ยิ่งใหญ่ที่สุดครั้งหนึ่งของเอเชีย ด้วยจำนวนผู้เข้าร่วมสัมมนากว่า 11,000 คน
                        </p>

                        <!-- Additional Content - Expandable -->
                        <div id="additional-content" class="max-h-0 overflow-hidden opacity-0 transition-all duration-500">
                            <p class="text-foreground/80 mb-4 leading-relaxed text-[15px]">
                                คุณมนตรี แสงอุไรพร นายกสมาคมตัวแทนประกันชีวิต (ซึ่งเป็นสมาชิกในขณะนั้น) ในฐานะประธานจัดงานดังกล่าว มีดำริที่จะก่อตั้งองค์กรการกุศลที่เป็นของตัวแทนประกันชีวิตคนไทย 
                                เพื่อฝึกตัวแทนเป็นผู้ให้ แบ่งปัน และมอบสิ่งดี ๆ คืนสู่สังคม ด้วยวิสัยทัศน์ของคุณมนตรี ที่เห็นว่าการเข้าถึงการศึกษาของเยาวชนคือพื้นฐานสำคัญที่สุด
                                ในการพัฒนาเยาวชนไทยให้เจริญรุ่งหน้าเทียบกับอารยประเทศ
                            </p>
                            
                        </div>

                        <button onclick="toggleContent()" class="bg-primary text-white hover:bg-primary/90 rounded-full px-8 py-3 flex items-center gap-2 transition-all">
                            <span id="btn-text">เพิ่มเติม</span>
                            <svg id="chevron-down" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                            <svg id="chevron-up" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Objectives Section -->
        <section class="py-14 bg-secondary/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10">
                    <div class="inline-block mb-3 px-5 py-1.5 bg-primary/10 rounded-full">
                        <span class="text-primary text-sm md:text-base">พันธกิจเพื่อสังคม</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl text-primary mb-3">วัตถุประสงค์หลัก</h2>
                    <p class="text-foreground/70 max-w-3xl mx-auto text-base md:text-lg">
                        ขับเคลื่อนการช่วยเหลืออย่างเป็นระบบ โปร่งใส และเกิดผลลัพธ์จริงต่อผู้รับประโยชน์
                    </p>
                </div>

                <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-5 md:gap-6">
                    <div class="bg-white/85 border border-primary/10 rounded-2xl p-5 md:p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-primary/10 flex items-center justify-center">
                            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                        </div>
                        <h3 class="text-primary text-xl md:text-2xl leading-tight mb-2">สนับสนุนทุนการศึกษา</h3>
                        <p class="text-foreground/70 text-base md:text-lg leading-relaxed">แก่เด็กและเยาวชนผู้ขาดแคลนทั่วประเทศ</p>
                    </div>

                    <div class="bg-white/85 border border-primary/10 rounded-2xl p-5 md:p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-primary/10 flex items-center justify-center">
                            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a4 4 0 00-5.656 0L12 17.2l-1.772-1.772a4 4 0 00-5.656 5.656L12 28.456l7.428-7.372a4 4 0 000-5.656z" transform="scale(.75) translate(3 -3)"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12M6 12h12"/>
                            </svg>
                        </div>
                        <h3 class="text-primary text-xl md:text-2xl leading-tight mb-2">จัดหาอุปกรณ์ทางการแพทย์</h3>
                        <p class="text-foreground/70 text-base md:text-lg leading-relaxed">ให้โรงพยาบาลของรัฐเพื่อเพิ่มโอกาสการรักษา</p>
                    </div>

                    <div class="bg-white/85 border border-primary/10 rounded-2xl p-5 md:p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-primary/10 flex items-center justify-center">
                            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m-4-7h8"/>
                            </svg>
                        </div>
                        <h3 class="text-primary text-xl md:text-2xl leading-tight mb-2">สนับสนุนผู้ด้อยโอกาส</h3>
                        <p class="text-foreground/70 text-base md:text-lg leading-relaxed">ผ่านกิจกรรมเพื่อผู้พิการ ผู้ป่วย และชุมชนที่ต้องการการช่วยเหลือ</p>
                    </div>

                    <div class="bg-white/85 border border-accent/15 rounded-2xl p-5 md:p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-accent/10 flex items-center justify-center">
                            <svg class="w-8 h-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13l-3 3m3-3l3 3M5 3h14a2 2 0 012 2v3a2 2 0 01-.586 1.414L15 15v4l-6 2v-6L3.586 9.414A2 2 0 013 8V5a2 2 0 012-2z"/>
                            </svg>
                        </div>
                        <h3 class="text-primary text-xl md:text-2xl leading-tight mb-2">ส่งเสริมคุณธรรมและจิตอาสา</h3>
                        <p class="text-foreground/70 text-base md:text-lg leading-relaxed">ปลูกฝังค่านิยมการแบ่งปันและการช่วยเหลือในสังคมไทย</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. Partner Organizations Section -->
        <section class="py-20 bg-gradient-to-b from-white to-secondary/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="text-center mb-16">
                    <div class="inline-block mb-4 px-4 py-2 bg-primary/10 rounded-full">
                        <span class="text-primary">ความร่วมมือ</span>
                    </div>
                    <h2 class="text-primary mb-4 text-4xl md:text-5xl">หน่วยงานที่เกี่ยวข้อง</h2>
                    <p class="text-muted-foreground max-w-3xl mx-auto text-lg">
                        มูลนิธิฯ ทำงานร่วมกับหน่วยงานและองค์กรชั้นนำในอุตสาหกรรมประกันภัยไทย
                    </p>
                </div>

                <!-- Partners Grid -->
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Partner 1 -->
                    <a href="https://www.tlaa.org/" target="_blank" rel="noopener noreferrer" class="block h-full">
                        <div class="p-6 bg-white hover:shadow-xl transition-all duration-300 border border-border rounded-2xl group hover:border-primary/30 cursor-pointer hover:scale-105 h-full">
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden border-2 border-primary/10 group-hover:border-primary/30 transition-colors p-1">
                                    <img src="assets/images/Logo.png" alt="THAIFA Foundation" class="h-16 w-auto" />
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-primary mb-2 text-lg group-hover:underline text-[16px]">สมาคมประกันชีวิตไทย</h3>
                                    <p class="text-muted-foreground text-sm leading-relaxed">
                                        Thai Life Assurance Association (TLAA)
                                    </p>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Partner 2 -->
                    <a href="https://www.thaifa.or.th" target="_blank" rel="noopener noreferrer" class="block h-full">
                        <div class="p-6 bg-white hover:shadow-xl transition-all duration-300 border border-border rounded-2xl group hover:border-primary/30 cursor-pointer hover:scale-105 h-full">
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden border-2 border-primary/10 group-hover:border-primary/30 transition-colors p-1">
                                    <img src="https://via.placeholder.com/56x56/233882/FFFFFF?text=THAIFA" alt="THAIFA" class="w-full h-full object-contain scale-150" />
                                    <!-- Figma: figma:asset/f3ceeee0959e3228361aca4a4c0ed503e303e718.png -->
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-primary mb-2 text-lg group-hover:underline text-[16px]">สมาคมตัวแทนประกันชีวิตและที่ปรึกษาการเงิน</h3>
                                    <p class="text-muted-foreground text-sm leading-relaxed">
                                        The Life Insurance Agent & Financial Advisor Association (THAIFA)
                                    </p>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Partner 3 -->
                    <a href="https://www.gamathailand.org" target="_blank" rel="noopener noreferrer" class="block h-full">
                        <div class="p-6 bg-white hover:shadow-xl transition-all duration-300 border border-border rounded-2xl group hover:border-primary/30 cursor-pointer hover:scale-105 h-full">
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden border-2 border-primary/10 group-hover:border-primary/30 transition-colors p-1">
                                    <img src="https://via.placeholder.com/56x56/233882/FFFFFF?text=GAMA" alt="GAMA" class="w-full h-full object-contain scale-150" />
                                    <!-- Figma: figma:asset/79b906402e58e89e60edba0705075ba15b6a9e5a.png -->
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-primary mb-2 text-lg group-hover:underline text-[16px]">GAMA Thailand</h3>
                                    <p class="text-muted-foreground text-sm leading-relaxed">
                                        General Agents & Managers Association – Thailand
                                    </p>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Partner 4 -->
                    <a href="https://www.tgia.org" target="_blank" rel="noopener noreferrer" class="block h-full">
                        <div class="p-6 bg-white hover:shadow-xl transition-all duration-300 border border-border rounded-2xl group hover:border-primary/30 cursor-pointer hover:scale-105 h-full">
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden border-2 border-primary/10 group-hover:border-primary/30 transition-colors p-1">
                                    <img src="https://via.placeholder.com/56x56/233882/FFFFFF?text=TGIA" alt="TGIA" class="w-full h-full object-contain scale-150" />
                                    <!-- Figma: figma:asset/7d4921276da62dcd14f88dfade6837d8f7a4b6a4.png -->
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-primary mb-2 text-lg group-hover:underline text-[16px]">สมาคมประกันวินาศภัยไทย</h3>
                                    <p class="text-muted-foreground text-sm leading-relaxed">
                                        Thai General Insurance Association (TGA / TGIA)
                                    </p>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Partner 5 -->
                    <a href="https://tiins.com/" target="_blank" rel="noopener noreferrer" class="block h-full">
                        <div class="p-6 bg-white hover:shadow-xl transition-all duration-300 border border-border rounded-2xl group hover:border-primary/30 cursor-pointer hover:scale-105 h-full">
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden border-2 border-primary/10 group-hover:border-primary/30 transition-colors p-1">
                                    <img src="https://via.placeholder.com/56x56/233882/FFFFFF?text=TII" alt="TII" class="w-full h-full object-contain scale-150" />
                                    <!-- Figma: figma:asset/7274ec0cefdbd0f2d32c1f9c1ccc4b156823cb42.png -->
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-primary mb-2 text-lg group-hover:underline text-[16px]">สถาบันประกันภัยไทย</h3>
                                    <p class="text-muted-foreground text-sm leading-relaxed">
                                        Thailand Insurance Institute (TII)
                                    </p>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Partner 6 -->
                    <a href="https://www.oic.or.th" target="_blank" rel="noopener noreferrer" class="block h-full">
                        <div class="p-6 bg-white hover:shadow-xl transition-all duration-300 border border-border rounded-2xl group hover:border-primary/30 cursor-pointer hover:scale-105 h-full">
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden border-2 border-primary/10 group-hover:border-primary/30 transition-colors p-1">
                                    <img src="https://via.placeholder.com/56x56/233882/FFFFFF?text=OIC" alt="OIC" class="w-full h-full object-contain scale-150" />
                                    <!-- Figma: figma:asset/8d311f95c78d098d5a1d10ba9158c17cc6536736.png -->
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-primary mb-2 text-lg group-hover:underline text-[16px]">สำนักงานคณะกรรมการกำกับและส่งเสริมการประกอบธุรกิจประกันภัย</h3>
                                    <p class="text-muted-foreground text-sm leading-relaxed">
                                        Office of Insurance Commission (OIC)
                                    </p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Bottom Note -->
                <div class="mt-12 text-center">
                    <p class="text-muted-foreground text-sm max-w-2xl mx-auto">
                        การทำงานร่วมกันกับหน่วยงานเหล่านี้ช่วยให้มูลนิธิสามารถดำเนินภารกิจเพื่อสังคมได้อย่างมีประสิทธิภาพ
                        และโปร่งใสตามมาตรฐานสากล
                    </p>
                </div>
            </div>
        </section>

        <!-- 3. Team Section with Tabs -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="inline-block mb-4 px-6 py-2 bg-primary/10 rounded-full">
                        <span class="text-primary">ทีมงาน</span>
                    </div>
                    <h2 class="text-4xl md:text-5xl text-primary mb-4">
                        บุคลากรมูลนิธิ
                    </h2>
                    <p class="text-muted-foreground max-w-3xl mx-auto text-lg">
                        คณะกรรมการและทีมงานมูลนิธิที่ทุ่มเทเพื่อสังคม
                    </p>
                </div>
                
                <!-- Tab Navigation -->
                <div class="flex justify-center mb-12">
                    <div class="inline-flex bg-gray-100 rounded-full p-1.5 gap-1">
                        <button onclick="switchTab('advisors')" id="tab-advisors" class="px-8 py-3 rounded-full font-medium transition-all duration-300 bg-primary text-white">
                            ที่ปรึกษา
                        </button>
                        <button onclick="switchTab('executives')" id="tab-executives" class="px-8 py-3 rounded-full font-medium transition-all duration-300 text-gray-600 hover:text-primary">
                            กรรมการบริหาร
                        </button>
                        <button onclick="switchTab('committee')" id="tab-committee" class="px-8 py-3 rounded-full font-medium transition-all duration-300 text-gray-600 hover:text-primary">
                            คณะกรรมการ
                        </button>
                    </div>
                </div>

                <div id="team-tabs-panel" class="relative">
                    <!-- Tab Content: Advisors -->
                    <div id="content-advisors" class="tab-content">
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-y-12 gap-x-8">
                            <?php if (empty($teamGroups['advisors'])): ?>
                                <div class="col-span-full text-center text-muted-foreground">ยังไม่มีข้อมูลที่ปรึกษา</div>
                            <?php else: ?>
                                <?php foreach ($teamGroups['advisors'] as $member): ?>
                                    <div class="text-center">
                                        <div class="w-36 h-36 md:w-40 md:h-40 rounded-full mx-auto mb-4 p-[3px] bg-[#a9c2ea]">
                                            <div class="w-full h-full rounded-full overflow-hidden bg-[#3569b2]">
                                                <img src="<?= h($member['photo_url'] ?: 'https://via.placeholder.com/220x220?text=Advisor') ?>" alt="<?= h($member['member_name']) ?>" class="w-full h-full object-cover">
                                            </div>
                                        </div>
                                        <h3 class="text-[18px] md:text-[20px] leading-snug text-primary mb-1"><?= h($member['member_name']) ?></h3>
                                        <p class="text-[14px] md:text-[16px] text-muted-foreground leading-snug"><?= h($member['member_title']) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Tab Content: Executives -->
                    <div id="content-executives" class="tab-content hidden">
                        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <?php if (empty($teamGroups['executives'])): ?>
                                <div class="col-span-full text-center text-muted-foreground">ยังไม่มีข้อมูลกรรมการบริหาร</div>
                            <?php else: ?>
                                <?php foreach ($teamGroups['executives'] as $member): ?>
                                    <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all border-2 border-primary/10 hover:border-primary/30">
                                        <div class="w-20 h-20 bg-primary/10 rounded-full mx-auto mb-4 flex items-center justify-center overflow-hidden">
                                            <img src="<?= h($member['photo_url'] ?: 'https://via.placeholder.com/100x100?text=Exec') ?>" alt="<?= h($member['member_name']) ?>" class="w-full h-full object-cover">
                                        </div>
                                        <h3 class="text-lg text-primary text-center mb-1"><?= h($member['member_name']) ?></h3>
                                        <p class="text-center text-accent text-sm mb-2"><?= h($member['member_title']) ?></p>
                                        <p class="text-muted-foreground text-xs text-center leading-relaxed"><?= h($member['member_bio']) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Tab Content: Committee -->
                    <div id="content-committee" class="tab-content hidden">
                        <div class="grid md:grid-cols-3 lg:grid-cols-5 gap-6">
                            <?php if (empty($teamGroups['committee'])): ?>
                                <div class="col-span-full text-center text-muted-foreground">ยังไม่มีข้อมูลคณะกรรมการ</div>
                            <?php else: ?>
                                <?php foreach ($teamGroups['committee'] as $member): ?>
                                    <div class="bg-gradient-to-br from-white to-accent/5 p-6 rounded-xl shadow-md hover:shadow-lg transition-all text-center">
                                        <div class="w-16 h-16 bg-accent/10 rounded-full mx-auto mb-3 flex items-center justify-center overflow-hidden">
                                            <img src="<?= h($member['photo_url'] ?: 'https://via.placeholder.com/80x80?text=CM') ?>" alt="<?= h($member['member_name']) ?>" class="w-full h-full object-cover">
                                        </div>
                                        <h3 class="text-base text-primary mb-1"><?= h($member['member_name']) ?></h3>
                                        <p class="text-accent text-xs"><?= h($member['member_title']) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script>
            function setTeamPanelStableHeight() {
                const panel = document.getElementById('team-tabs-panel');
                if (!panel) return;
                const contents = Array.from(panel.querySelectorAll('.tab-content'));
                if (!contents.length) return;

                let maxHeight = 0;
                contents.forEach((content) => {
                    const wasHidden = content.classList.contains('hidden');
                    const prev = {
                        position: content.style.position,
                        visibility: content.style.visibility,
                        pointerEvents: content.style.pointerEvents,
                        left: content.style.left,
                        right: content.style.right,
                        top: content.style.top
                    };

                    if (wasHidden) content.classList.remove('hidden');
                    content.style.position = 'absolute';
                    content.style.visibility = 'hidden';
                    content.style.pointerEvents = 'none';
                    content.style.left = '0';
                    content.style.right = '0';
                    content.style.top = '0';

                    maxHeight = Math.max(maxHeight, content.scrollHeight);

                    content.style.position = prev.position;
                    content.style.visibility = prev.visibility;
                    content.style.pointerEvents = prev.pointerEvents;
                    content.style.left = prev.left;
                    content.style.right = prev.right;
                    content.style.top = prev.top;
                    if (wasHidden) content.classList.add('hidden');
                });

                panel.style.minHeight = maxHeight + 'px';
            }

            // Tab switching functionality
            function switchTab(tabName) {
                // Hide all tab contents
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Remove active state from all tabs
                document.querySelectorAll('[id^="tab-"]').forEach(tab => {
                    tab.classList.remove('bg-primary', 'text-white');
                    tab.classList.add('text-gray-600');
                });
                
                // Show selected tab content
                document.getElementById('content-' + tabName).classList.remove('hidden');
                
                // Add active state to selected tab
                const activeTab = document.getElementById('tab-' + tabName);
                activeTab.classList.add('bg-primary', 'text-white');
                activeTab.classList.remove('text-gray-600');
            }

            window.addEventListener('load', setTeamPanelStableHeight);
            window.addEventListener('resize', setTeamPanelStableHeight);
        </script>

    </main>

    <!-- Footer (compact) -->
    <footer class="bg-primary text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <!-- About -->
                <div>
                    <div class="mb-6">
                        <img src="assets/images/Logo.png" alt="THAIFA Foundation" class="h-16 w-auto bg-white rounded-md p-1" />
                    </div>
                    <p class="text-white/80 text-sm leading-relaxed mb-4">
                        มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน มุ่งมั่นสร้างโอกาสและพัฒนาคุณภาพชีวิตของเด็กและเยาวชนไทย
                    </p>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-accent" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /></svg>
                        <span class="text-white/90 text-sm">ขอบคุณที่ร่วมเคียงฝันไปด้วยกัน</span>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="mb-6 text-xl">เมนูหลัก</h3>
                    <ul class="space-y-3">
                        <li><a href="index.php" class="text-white/80 hover:text-accent transition-colors text-sm">หน้าแรก</a></li>
                        <li><a href="about.php" class="text-white/80 hover:text-accent transition-colors text-sm">เกี่ยวกับเรา</a></li>
                        <li><a href="donate.php" class="text-white/80 hover:text-accent transition-colors text-sm">การบริจาค</a></li>
                        <li><a href="volunteer.php" class="text-white/80 hover:text-accent transition-colors text-sm">จิตอาสา</a></li>
                        <li><a href="stories.php" class="text-white/80 hover:text-accent transition-colors text-sm">เสียงจากใจ</a></li>
                        <li><a href="contact.php" class="text-white/80 hover:text-accent transition-colors text-sm">ติดต่อเรา</a></li>
                    </ul>
                </div>

                <!-- Programs -->
                <div>
                    <h3 class="mb-6 text-xl">โครงการของเรา</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#" class="text-white/80 hover:text-accent transition-colors">ทุนการศึกษา</a></li>
                        <li><a href="#" class="text-white/80 hover:text-accent transition-colors">ช่วยเหลือเด็กกำพร้า</a></li>
                        <li><a href="#" class="text-white/80 hover:text-accent transition-colors">เครื่องมือแพทย์</a></li>
                        <li><a href="#" class="text-white/80 hover:text-accent transition-colors">กิจกรรมชุมชน</a></li>
                        <li><a href="#" class="text-white/80 hover:text-accent transition-colors">เสียงจากใจผู้รับ</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="mb-6 text-xl">ติดต่อเรา</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="text-white/80 text-sm">
                                อาคาร จูเวลเลอรี่ ห้อง 138/32 ชั้น 12<br />
                                เลขที่ 138 ถนนนเรศ แขวงสี่พระยา<br />
                                เขตบางรัก กรุงเทพฯ 10500
                            </span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
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
    </footer>

    <script>
        let isExpanded = false;

        function toggleMobileMenu() {
            const el = document.getElementById('mobileMenu');
            if (!el) return;
            el.classList.toggle('hidden');
        }
        
        function toggleContent() {
            const content = document.getElementById('additional-content');
            const btnText = document.getElementById('btn-text');
            const chevronDown = document.getElementById('chevron-down');
            const chevronUp = document.getElementById('chevron-up');
            
            isExpanded = !isExpanded;
            
            if (isExpanded) {
                content.style.maxHeight = '1500px';
                content.style.opacity = '1';
                btnText.textContent = 'ซ่อน';
                chevronDown.classList.add('hidden');
                chevronUp.classList.remove('hidden');
            } else {
                content.style.maxHeight = '0';
                content.style.opacity = '0';
                btnText.textContent = 'เพิ่มเติม';
                chevronDown.classList.remove('hidden');
                chevronUp.classList.add('hidden');
            }
        }

    </script>
    <?php include __DIR__ . '/backend/helpers/floating_contact_widget.php'; ?>
</body>
</html>
