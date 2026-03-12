<?php
include_once(__DIR__ . '/backend/helpers/i18n.php');
thaifa_i18n_buffer_start();
include_once(__DIR__ . '/backend/helpers/cart_count.php');
$cartCount = thaifaCartCount();
include_once(__DIR__ . '/backend/classes/ContactMessageManagement.class.php');

$contactMessage = new ContactMessageManagement();

function h($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

$formErrors = [];
$formSuccess = '';
$formData = [
    'full_name' => '',
    'email' => '',
    'subject' => '',
    'message' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'full_name' => trim((string)($_POST['full_name'] ?? '')),
        'email' => trim((string)($_POST['email'] ?? '')),
        'subject' => trim((string)($_POST['subject'] ?? '')),
        'message' => trim((string)($_POST['message'] ?? '')),
    ];

    try {
        $contactMessage->createMessage($formData);
        $formSuccess = 'ส่งข้อความเรียบร้อยแล้ว ขอบคุณที่ติดต่อเรา';
        $formData = [
            'full_name' => '',
            'email' => '',
            'subject' => '',
            'message' => '',
        ];
    } catch (Throwable $e) {
        $formErrors[] = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(thaifa_lang(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ติดต่อเรา - THAIFA Foundation</title>
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
                        primary: '#233882', 
                        accent: '#e83b3b', 
                        secondary: '#d9e7ef', 
                        foreground: '#303a56', 
                        border: '#e2e8f0' 
                    }
                }
            }
        }
    </script>
</head>
<body>
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white shadow-sm">
        <!-- Top Bar -->
        <div class="bg-secondary/30 border-b border-border">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-10 text-sm">
                    <!-- Contact Info -->
                    <div class="hidden md:flex items-center gap-6 text-foreground/80">
                        <a href="mailto:thaifafoundation@gmail.com" class="flex items-center gap-2 hover:text-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>thaifafoundation@gmail.com</span>
                        </a>
                    </div>

                    <!-- Right Side -->
                    <div class="flex items-center gap-4">
                        <!-- Shopping Cart -->
                        <a href="cart.php" class="relative text-foreground/80 hover:text-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="absolute -top-2 -right-2 bg-accent text-white text-xs rounded-full w-4 h-4 flex items-center justify-center"><?= (int)$cartCount ?></span>
                        </a>

                        <!-- Auth Buttons -->
                        <div class="flex items-center gap-1"><a href="<?= h(thaifa_lang_url('th')) ?>" class="text-xs px-2 py-0.5 rounded <?= thaifa_lang()==='th' ? 'bg-primary text-white' : 'text-foreground/70 hover:text-primary' ?>">TH</a><a href="<?= h(thaifa_lang_url('en')) ?>" class="text-xs px-2 py-0.5 rounded <?= thaifa_lang()==='en' ? 'bg-primary text-white' : 'text-foreground/70 hover:text-primary' ?>">EN</a></div><div class="flex items-center gap-2 pl-4 border-l border-border">
                            <a href="login.php" class="flex items-center gap-1 text-foreground/80 hover:text-primary transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
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
        
        <!-- Main Navigation Bar -->
        <div class="bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20">
                    <a href="index.php" class="flex-shrink-0">
                        <img src="assets/images/Logo.png" 
                            alt="THAIFA Logo" class="h-20 w-auto" />
                    </a>
                    <div class="hidden lg:flex items-center gap-1">
                        <a href="index.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100"><?= h(thaifa_t('home')) ?></a>
                        <a href="about.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100"><?= h(thaifa_t('about')) ?></a>
                        <a href="calendar.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100"><?= h(thaifa_t('calendar')) ?></a>
                        <a href="shop.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100"><?= h(thaifa_t('shop')) ?></a>
                        <a href="donate.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100"><?= h(thaifa_t('donate')) ?></a>
                        <a href="volunteer.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100"><?= h(thaifa_t('volunteer')) ?></a>
                        <a href="stories.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100"><?= h(thaifa_t('stories')) ?></a>
                        <a href="contact.php" class="text-primary bg-sky-100 px-4 py-2 rounded-md"><?= h(thaifa_t('contact')) ?></a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="pt-[120px]">
        <!-- Hero Banner -->
        <section class="relative h-[300px] bg-gradient-to-br from-primary to-primary/90 flex items-center justify-center">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-10 left-20 w-32 h-32 bg-white rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 right-20 w-40 h-40 bg-accent rounded-full blur-3xl"></div>
            </div>
            <div class="relative z-10 text-center text-white max-w-4xl mx-auto px-4">
                <h1 class="text-5xl md:text-6xl mb-4"><?= h(thaifa_t('contact')) ?></h1>
                <p class="text-xl text-white/90">เรายินดีรับฟังและให้บริการทุกท่าน</p>
            </div>
        </section>

        <!-- Contact Section -->
        <section class="py-20 bg-gradient-to-b from-white to-secondary/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="text-center mb-16">
                    <div class="inline-block mb-4 px-6 py-2 bg-primary/10 rounded-full">
                        <span class="text-primary"><?= h(thaifa_t('contact')) ?></span>
                    </div>
                    <h2 class="text-4xl md:text-5xl text-primary mb-4">THAIFA FOUNDATION</h2>
                    <p class="text-lg text-foreground/80 max-w-3xl mx-auto leading-relaxed">
                        มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน<br />
                        <span class="text-accent">ส่งต่อโอกาสทางการศึกษาให้เยาวชนไทย ด้วยหัวใจแห่งความเมตตา</span>
                    </p>
                </div>
                <!-- Main Content Grid -->
                <div class="grid lg:grid-cols-2 gap-8 mb-12">
                    <!-- Left Side - Office Info -->
                    <div class="space-y-6">
                        <!-- Office Address Card -->
                        <div class="p-8 bg-white border border-border rounded-3xl shadow-lg hover:shadow-xl transition-all">
                            <div class="flex items-start gap-4 mb-6">
                                <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-primary mb-3 text-xl">ที่อยู่สำนักงาน</h3>
                                    <p class="text-foreground/80 mb-2">มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน</p>
                                    <p class="text-foreground/70 text-[15px] leading-relaxed">
                                        อาคารจูเวลเลอรี่ ห้องเลขที่ 138/32<br />
                                        ชั้นที่ 12 เลขที่ 138<br />
                                        ถนนนเรศ แขวงสี่พระยา เขตบางรัก<br />
                                        กรุงเทพมหานคร 10500
                                    </p>
                                    <div class="mt-4 pt-4 border-t border-border">
                                        <p class="text-foreground/60 text-sm">
                                            เลขประจำตัวผู้เสียภาษีอากร: <span class="text-primary">0993000440226</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Email Card -->
                        <div class="p-8 bg-gradient-to-br from-accent/5 to-white border border-border rounded-3xl shadow-lg hover:shadow-xl transition-all">
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 bg-accent/10 rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-7 h-7 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-primary mb-3 text-xl">อีเมล</h3>
                                    <a href="mailto:thaifafoundation@gmail.com" class="inline-flex items-center gap-2 text-accent hover:underline text-lg group">
                                        <span>thaifafoundation@gmail.com</span>
                                        <svg class="w-4 h-4 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                    <p class="text-foreground/60 text-sm mt-2">คลิกเพื่อส่งอีเมลถึงเรา</p>
                                </div>
                            </div>
                        </div>

                        <!-- Office Hours Card -->
                        <div class="p-8 bg-gradient-to-br from-primary/5 to-white border border-border rounded-3xl shadow-lg">
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-primary mb-3 text-xl">เวลาทำการ</h3>
                                    <div class="space-y-2 text-foreground/70 text-[15px]">
                                        <p>จันทร์ - ศุกร์: 09:00 - 18:00 น.</p>
                                        <p>เสาร์: 09:00 - 16:00 น.</p>
                                        <p>อาทิตย์และวันหยุดนักขัตฤกษ์: ปิด</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side - Contact Form -->
                    <div class="p-8 md:p-10 bg-white border border-border rounded-3xl shadow-lg" id="contact-form">
                        <h3 class="text-primary mb-2 text-3xl">ส่งข้อความถึงเรา</h3>
                        <p class="text-foreground/70 mb-8">กรอกแบบฟอร์มด้านล่าง เราจะตอบกลับโดยเร็วที่สุด</p>
                        
                        <?php if (!empty($formErrors)): ?>
                            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm">
                                <?php foreach ($formErrors as $err): ?>
                                    <div><?= h($err) ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($formSuccess !== ''): ?>
                            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 text-green-700 px-4 py-3 text-sm">
                                <?= h($formSuccess) ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="#contact-form" class="space-y-6">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="name" class="text-sm">ชื่อ-นามสกุล *</label>
                                    <input id="name" name="full_name" required value="<?= h($formData['full_name']) ?>" placeholder="กรอกชื่อ-นามสกุล" class="w-full h-12 px-4 rounded-xl border border-border bg-white" />
                                </div>
                                <div class="space-y-2">
                                    <label for="email" class="text-sm">อีเมล *</label>
                                    <input id="email" name="email" type="email" required value="<?= h($formData['email']) ?>" placeholder="example@email.com" class="w-full h-12 px-4 rounded-xl border border-border bg-white" />
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="subject" class="text-sm">หัวข้อ *</label>
                                <input id="subject" name="subject" required value="<?= h($formData['subject']) ?>" placeholder="เรื่องที่ต้องการติดต่อ" class="w-full h-12 px-4 rounded-xl border border-border bg-white" />
                            </div>

                            <div class="space-y-2">
                                <label for="message" class="text-sm">ข้อความ *</label>
                                <textarea id="message" name="message" required placeholder="เขียนข้อความของคุณที่นี่..." class="w-full min-h-40 p-4 rounded-xl border border-border bg-white"><?= h($formData['message']) ?></textarea>
                            </div>

                            <button type="submit" class="w-full bg-accent text-white hover:bg-accent/90 rounded-full px-8 py-3 text-lg transition-all">
                                ส่งข้อความ
                            </button>

                            <p class="text-foreground/60 text-center text-sm">
                                เราจะตอบกลับภายใน 24 ชั่วโมงในวันทำการ
                            </p>
                        </form>
                    </div>
                </div>

                <!-- Social Media Section -->
                <div class="mb-12">
                    <div class="text-center mb-8">
                        <h3 class="text-3xl text-primary mb-3">ช่องทางติดตามข่าวสาร</h3>
                        <p class="text-foreground/70">ติดตามกิจกรรมและข่าวสารของมูลนิธิผ่านช่องทางต่างๆ</p>
                    </div>

                    <div class="grid md:grid-cols-3 gap-6">
                        <!-- Facebook -->
                        <a href="https://www.facebook.com/share/1BMZ4725AV/?mibextid=wwXIfr" target="_blank" rel="noopener noreferrer" class="group">
                            <div class="p-8 bg-gradient-to-br from-[#1877F2]/5 to-white border border-[#1877F2]/20 rounded-3xl hover:shadow-xl hover:border-[#1877F2]/40 transition-all cursor-pointer">
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-20 h-20 bg-[#1877F2]/10 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                        <svg class="w-10 h-10 text-[#1877F2]" fill="#1877F2" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    </div>
                                    <h4 class="text-primary mb-2 text-xl">Facebook</h4>
                                    <p class="text-foreground/70 text-sm mb-3">THAIFA Foundation</p>
                                    <p class="text-foreground/60 text-xs">เพจทางการของมูลนิธิ</p>
                                    <div class="mt-4 inline-flex items-center gap-2 text-[#1877F2] text-sm group-hover:gap-3 transition-all">
                                        <span>เยี่ยมชม Facebook</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <!-- LINE -->
                        <a href="https://line.me/R/ti/p/@519lkcsb" target="_blank" rel="noopener noreferrer" class="group">
                            <div class="p-8 bg-gradient-to-br from-[#00B900]/5 to-white border border-[#00B900]/20 rounded-3xl hover:shadow-xl hover:border-[#00B900]/40 transition-all cursor-pointer">
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-20 h-20 bg-[#00B900]/10 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                        <svg class="w-10 h-10 text-[#00B900]" fill="#00B900" viewBox="0 0 24 24"><path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/></svg>
                                    </div>
                                    <h4 class="text-primary mb-2 text-xl">LINE Official</h4>
                                    <p class="text-foreground/70 text-sm mb-3">@THAIFAFD</p>
                                    <p class="text-foreground/60 text-xs">ติดต่อสอบถามและติดตามข่าวสาร</p>
                                    <div class="mt-4 inline-flex items-center gap-2 text-[#00B900] text-sm group-hover:gap-3 transition-all">
                                        <span>เพิ่มเพื่อน LINE</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <!-- YouTube -->
                        <a href="https://youtube.com/@thaifafd?si=3yLd0cB6J8k-4ER2" target="_blank" rel="noopener noreferrer" class="group">
                            <div class="p-8 bg-gradient-to-br from-[#FF0000]/5 to-white border border-[#FF0000]/20 rounded-3xl hover:shadow-xl hover:border-[#FF0000]/40 transition-all cursor-pointer">
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-20 h-20 bg-[#FF0000]/10 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                        <svg class="w-10 h-10 text-[#FF0000]" fill="#FF0000" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                    </div>
                                    <h4 class="text-primary mb-2 text-xl">YouTube</h4>
                                    <p class="text-foreground/70 text-sm mb-3">THAIFA FD Channel</p>
                                    <p class="text-foreground/60 text-xs">รวมวิดีโอจากงาน The Joy of Giving</p>
                                    <div class="mt-4 inline-flex items-center gap-2 text-[#FF0000] text-sm group-hover:gap-3 transition-all">
                                        <span>ดู YouTube</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Google Map -->
                <div class="overflow-hidden bg-card border border-border rounded-3xl shadow-xl">
                    <div class="relative">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3875.984534853827!2d100.5168893!3d13.7261988!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30e29912ed715d25%3A0x8e2fc2e6b4e2c7e7!2sJewelry%20Trade%20Center!5e0!3m2!1sen!2sth!4v1234567890"
                            width="100%" height="500" style="border:0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="THAIFA Foundation Location">
                        </iframe>
                        
                        <!-- Map Overlay Info -->
                        <div class="absolute bottom-6 left-6 right-6 md:left-auto md:right-6 md:w-96">
                            <div class="p-6 bg-white/95 backdrop-blur-sm border border-border rounded-2xl shadow-2xl">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-primary mb-2">สำนักงานมูนิธิ THAIFA</h4>
                                        <p class="text-foreground/70 text-sm mb-3">
                                            อาคารจูเวลเลอรี่ ชั้น 12<br />
                                            ถนนนเรศ แขวงสี่พระยา เขตบางรัก
                                        </p>
                                        <a href="https://maps.google.com/?q=Jewelry+Trade+Center+Bangkok" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-accent hover:underline text-sm">
                                            <span>เปิดใน Google Maps</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                        <li><a href="index.php" class="text-white/80 hover:text-accent transition-colors text-sm"><?= h(thaifa_t('home')) ?></a></li>
                        <li><a href="about.php" class="text-white/80 hover:text-accent transition-colors text-sm"><?= h(thaifa_t('about')) ?></a></li>
                        <li><a href="donate.php" class="text-white/80 hover:text-accent transition-colors text-sm"><?= h(thaifa_t('donate')) ?></a></li>
                        <li><a href="volunteer.php" class="text-white/80 hover:text-accent transition-colors text-sm"><?= h(thaifa_t('volunteer')) ?></a></li>
                        <li><a href="stories.php" class="text-white/80 hover:text-accent transition-colors text-sm"><?= h(thaifa_t('stories')) ?></a></li>
                        <li><a href="contact.php" class="text-white/80 hover:text-accent transition-colors text-sm"><?= h(thaifa_t('contact')) ?></a></li>
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
                    <h3 class="mb-6 text-xl"><?= h(thaifa_t('contact')) ?></h3>
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

    <script>    </script>    </div>

<?php include __DIR__ . '/backend/helpers/floating_contact_widget.php'; ?>
</body>
</html>
