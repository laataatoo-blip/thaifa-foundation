<?php
include_once(__DIR__ . '/backend/helpers/i18n.php');
thaifa_i18n_buffer_start();
include_once(__DIR__ . '/backend/helpers/cart_count.php');
$cartCount = thaifaCartCount();
$isEn = thaifa_lang() === 'en';
include_once(__DIR__ . '/backend/classes/DonationManagement.class.php');

$donationManager = new DonationManagement();
$campaigns = $donationManager->getCampaigns(true);
$donationError = '';
$donationSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['action'] ?? '') === 'donate_submit')) {
    try {
        $donationId = $donationManager->createDonation($_POST);
        $donationSuccess = 'ส่งข้อมูลการบริจาคเรียบร้อย เลขที่รายการ #' . $donationId . ' ทีมงานจะตรวจสอบและติดต่อกลับ';
    } catch (Throwable $e) {
        $donationError = $e->getMessage();
    }
}

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(thaifa_lang(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การบริจาค - THAIFA Foundation</title>
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

        .amount-btn.active { border-color: #e83b3b; background: rgba(232, 59, 59, 0.1); color: #e83b3b; }
    </style>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: '#233882', accent: '#e83b3b', secondary: '#d9e7ef', foreground: '#303a56', border: '#e2e8f0' }}}
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
                        <a href="donate.php" class="text-[#315d9f] bg-sky-100 px-4 py-2 rounded-md"><?= h(thaifa_t('donate')) ?></a>
                        <a href="volunteer.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100"><?= h(thaifa_t('volunteer')) ?></a>
                        <a href="stories.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100"><?= h(thaifa_t('stories')) ?></a>
                        <a href="contact.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100"><?= h(thaifa_t('contact')) ?></a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="pt-[120px]">
        <!-- 1. Donation Section -->
        <section class="py-20 bg-gradient-to-br from-primary via-primary to-primary/90 relative overflow-hidden">
            <div class="absolute top-20 left-10 w-40 h-40 bg-accent/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-10 w-60 h-60 bg-white/5 rounded-full blur-3xl"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <!-- Left Side -->
                    <div class="text-white">
                        <div class="inline-block mb-4 px-6 py-2 bg-accent/20 backdrop-blur-sm rounded-full">
                            <span class="text-accent">ร่วมบริจาค</span>
                        </div>
                        <h2 class="mb-6 text-4xl md:text-5xl">
                            ร่วมส่งต่อความหวัง<br />สร้างอนาคตที่ดีกว่า
                        </h2>
                        <p class="mb-8 text-white/90 text-lg leading-relaxed">
                            การบริจาคของคุณไม่ว่าจะมากหรือน้อย จะช่วยเปลี่ยนชีวิตของเด็กและเยาวชนไทย
                            ให้มีโอกาสทางการศึกษาและอนาคตที่สดใส
                        </p>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all">
                                <div class="w-12 h-12 bg-accent/20 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-accent" fill="currentColor" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                                </div>
                                <div class="text-3xl mb-1 text-white">100%</div>
                                <div class="text-white/80 text-sm">โปร่งใส ตรวจสอบได้</div>
                            </div>

                            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all">
                                <div class="w-12 h-12 bg-accent/20 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div class="text-3xl mb-1 text-white">20+ ปี</div>
                                <div class="text-white/80 text-sm">เชื่อถือได้ ดำเนินงานต่อเนื่อง</div>
                            </div>
                        </div>

                        <div class="mt-8 p-6 bg-white/10 backdrop-blur-sm rounded-2xl border border-white/20">
                            <p class="text-white/90 text-sm mb-3">
                                *ยังไม่รองรับการลดหย่อนภาษี
                                พร้อมรับใบเสร็จรับเงินผ่านอีเมล
                            </p>
                            <p class="text-white text-center italic">
                                "ขอบคุณที่ร่วมแบ่งปันความฝันให้เยาวชนไทย"
                            </p>
                        </div>
                    </div>

                    <!-- Right Side - Donation Form -->
                    <div class="p-8 bg-white rounded-3xl shadow-2xl">
                        <?php if ($donationError): ?>
                            <div class="mb-4 p-3 rounded-xl bg-red-50 text-red-700 border border-red-200"><?= h($donationError) ?></div>
                        <?php endif; ?>
                        <?php if ($donationSuccess): ?>
                            <div class="mb-4 p-3 rounded-xl bg-green-50 text-green-700 border border-green-200"><?= h($donationSuccess) ?></div>
                        <?php endif; ?>

                        <form method="post">
                            <input type="hidden" name="action" value="donate_submit">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-14 h-14 bg-accent rounded-full flex items-center justify-center">
                                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-primary text-2xl">บริจาคออนไลน์</h3>
                                <p class="text-foreground/60 text-sm">ปลอดภัย รวดเร็ว สะดวก</p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-foreground mb-3">เลือกจำนวนเงิน (บาท)</label>
                            <div class="grid grid-cols-3 gap-3">
                                <button type="button" onclick="selectAmount(500)" id="btn-500" class="amount-btn py-3 px-4 rounded-xl border-2 border-accent bg-accent/10 text-accent transition-all">฿500</button>
                                <button type="button" onclick="selectAmount(1000)" id="btn-1000" class="amount-btn py-3 px-4 rounded-xl border-2 border-border text-foreground hover:border-accent/50 transition-all">฿1,000</button>
                                <button type="button" onclick="selectAmount(2500)" id="btn-2500" class="amount-btn py-3 px-4 rounded-xl border-2 border-border text-foreground hover:border-accent/50 transition-all">฿2,500</button>
                                <button type="button" onclick="selectAmount(5000)" id="btn-5000" class="amount-btn py-3 px-4 rounded-xl border-2 border-border text-foreground hover:border-accent/50 transition-all">฿5,000</button>
                                <button type="button" onclick="selectAmount(10000)" id="btn-10000" class="amount-btn py-3 px-4 rounded-xl border-2 border-border text-foreground hover:border-accent/50 transition-all">฿10,000</button>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-foreground mb-3">หรือระบุจำนวนเงิน</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-muted-foreground">฿</span>
                                <input id="custom-amount" name="amount" type="number" min="1" placeholder="ระบุจำนวนเงิน" oninput="handleCustomAmount()" class="w-full pl-10 pr-4 py-3 border-2 border-border rounded-xl focus:border-accent focus:outline-none bg-input-background transition-all" required />
                            </div>
                        </div>

                        <div class="mb-8">
                            <label class="block text-foreground mb-3">ประเภทการบริจาค</label>
                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" onclick="selectType('once')" id="type-once" class="py-3 px-4 rounded-xl border-2 border-accent bg-accent/10 text-accent transition-all">ครั้งเดียว</button>
                                <button type="button" onclick="selectType('monthly')" id="type-monthly" class="py-3 px-4 rounded-xl border-2 border-border hover:border-accent/50 transition-all">รายเดือน</button>
                            </div>
                            <input type="hidden" id="donation_type" name="donation_type" value="once">
                        </div>

                        <div class="grid md:grid-cols-2 gap-3 mb-4">
                            <input name="donor_name" class="w-full px-4 py-3 border-2 border-border rounded-xl focus:border-accent focus:outline-none bg-input-background transition-all" placeholder="ชื่อผู้บริจาค" required>
                            <input name="donor_phone" class="w-full px-4 py-3 border-2 border-border rounded-xl focus:border-accent focus:outline-none bg-input-background transition-all" placeholder="เบอร์โทรศัพท์">
                            <input name="donor_email" type="email" class="w-full px-4 py-3 border-2 border-border rounded-xl focus:border-accent focus:outline-none bg-input-background transition-all md:col-span-2" placeholder="อีเมล">
                            <select name="campaign_id" class="w-full px-4 py-3 border-2 border-border rounded-xl focus:border-accent focus:outline-none bg-input-background transition-all">
                                <option value="">เลือกวัตถุประสงค์การบริจาค</option>
                                <?php foreach ($campaigns as $cp): ?>
                                    <option value="<?= (int)$cp['id'] ?>"><?= h($cp['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select name="payment_method" class="w-full px-4 py-3 border-2 border-border rounded-xl focus:border-accent focus:outline-none bg-input-background transition-all">
                                <option value="transfer"><?= $isEn ? 'Bank Transfer' : 'โอนผ่านบัญชีธนาคาร' ?></option>
                                <option value="qr"><?= $isEn ? 'Scan QR' : 'สแกน QR' ?></option>
                                <option value="credit_card"><?= $isEn ? 'Credit / Debit Card' : 'บัตรเครดิต/เดบิต' ?></option>
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-accent text-white hover:bg-accent/90 rounded-full h-12 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                            ดำเนินการบริจาค
                        </button>

                        <p class="text-muted-foreground text-center mt-6 text-sm">
                            ข้อมูลการชำระเงินของคุณได้รับการปกป้องด้วยระบบความปลอดภัยขั้นสูง
                        </p>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. Why Donate Section -->
        <section class="py-20 bg-gradient-to-b from-white to-secondary/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="inline-block mb-4 px-6 py-2 bg-primary/10 rounded-full">
                        <span class="text-primary">ทำไมต้องบริจาค</span>
                    </div>
                    <h2 class="text-4xl md:text-5xl text-primary mb-4">ความมั่นใจในการบริจาค</h2>
                    <p class="text-lg text-foreground/70 max-w-3xl mx-auto">
                        การบริจาคของคุณจะถูกนำไปใช้อย่างโปร่งใสและมีประสิทธิภาพสูงสุด
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="flex flex-col items-center text-center p-8 bg-gradient-to-br from-primary/5 to-primary/10 rounded-3xl hover:from-primary/10 hover:to-primary/15 transition-all">
                        <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-6 shadow-lg">
                            <svg class="w-10 h-10 text-primary" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd"/></svg>
                        </div>
                        <h3 class="text-primary mb-2 text-2xl">ปลอดภัย</h3>
                    </div>

                    <div class="flex flex-col items-center text-center p-8 bg-gradient-to-br from-accent/5 to-accent/10 rounded-3xl hover:from-accent/10 hover:to-accent/15 transition-all">
                        <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-6 shadow-lg">
                            <svg class="w-10 h-10 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-primary mb-2 text-2xl">โปร่งใส</h3>
                    </div>

                    <div class="flex flex-col items-center text-center p-8 bg-gradient-to-br from-primary/5 to-primary/10 rounded-3xl hover:from-primary/10 hover:to-primary/15 transition-all">
                        <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-6 shadow-lg">
                            <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h3 class="text-primary mb-2 text-2xl">มีประสิทธิภาพ</h3>
                    </div>
                </div>

                <!-- 3. Donation Categories -->
                <div class="mt-20">
                    <div class="text-center mb-12">
                        <div class="inline-block mb-4 px-6 py-2 bg-accent/10 rounded-full">
                            <span class="text-accent">ประเภทการบริจาค</span>
                        </div>
                        <h2 class="text-4xl md:text-5xl text-primary mb-4"><?= $isEn ? 'Where Your Donation Goes' : 'เราบริจาคอะไรบ้าง' ?></h2>
                        <p class="text-lg text-foreground/70 max-w-3xl mx-auto">
                            การบริจาคของคุณจะถูกนำไปใช้ในการช่วยเหลือสังคมในหลายรูปแบบ
                        </p>
                    </div>

                    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="p-6 bg-white border border-border rounded-2xl hover:shadow-xl transition-all group">
                            <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-primary/20 transition-all">
                                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                            </div>
                            <h3 class="text-primary mb-2 text-xl">ทุนการศึกษา</h3>
                            <p class="text-foreground/70 text-sm">สนับสนุนทุนการศึกษาให้เด็กและเยาวชนที่ขาดแคลนทุนทรัพย์</p>
                        </div>

                        <div class="p-6 bg-white border border-border rounded-2xl hover:shadow-xl transition-all group">
                            <div class="w-16 h-16 bg-accent/10 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-accent/20 transition-all">
                                <svg class="w-8 h-8 text-accent" fill="currentColor" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                            </div>
                            <h3 class="text-primary mb-2 text-xl">ช่วยเหลือเด็กกำพร้า</h3>
                            <p class="text-foreground/70 text-sm">มอบความอบอุ่นและโอกาสให้เด็กกำพร้าและด้อยโอกาส</p>
                        </div>

                        <div class="p-6 bg-white border border-border rounded-2xl hover:shadow-xl transition-all group">
                            <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-primary/20 transition-all">
                                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.871 4A17.926 17.926 0 003 12c0 2.874.673 5.59 1.871 8m14.13 0a17.926 17.926 0 001.87-8c0-2.874-.673-5.59-1.87-8M9 9h1.246a1 1 0 01.961.725l1.586 5.55a1 1 0 00.961.725H15m1-7h-.08a2 2 0 00-1.519.698L9.6 15.302A2 2 0 018.08 16H8"/></svg>
                            </div>
                            <h3 class="text-primary mb-2 text-xl">เครื่องมือแพทย์</h3>
                            <p class="text-foreground/70 text-sm">จัดหาอุปกรณ์ทางการแพทย์ให้โรงพยาบาลของรัฐ</p>
                        </div>

                        <div class="p-6 bg-white border border-border rounded-2xl hover:shadow-xl transition-all group">
                            <div class="w-16 h-16 bg-accent/10 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-accent/20 transition-all">
                                <svg class="w-8 h-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <h3 class="text-primary mb-2 text-xl">กิจกรรมชุมชน</h3>
                            <p class="text-foreground/70 text-sm">สนับสนุนกิจกรรมเพื่อสังคมและพัฒนาชุมชน</p>
                        </div>
                    </div>

                    <!-- 2567 Summary -->
                    <div class="mt-16 bg-gradient-to-br from-primary to-primary/90 rounded-3xl p-8 md:p-12 text-white">
                        <div class="text-center mb-8">
                            <h3 class="text-3xl mb-4">สรุปรายการมอบทุนประจำปี 2567</h3>
                            <p class="text-white/90 text-lg">
                                มูลนิธิ THAIFA มอบทุนการศึกษาและสนับสนุนโรงเรียนทั่วประเทศ
                            </p>
                        </div>
                        <div class="grid md:grid-cols-3 gap-8 mb-8">
                            <div class="text-center">
                                <div class="text-4xl mb-2">2,223,250</div>
                                <p class="text-white/90">บาท มูลค่ารวม</p>
                            </div>
                            <div class="text-center">
                                <div class="text-4xl mb-2">233</div>
                                <p class="text-white/90">ทุนการศึกษา</p>
                            </div>
                            <div class="text-center">
                                <div class="text-4xl mb-2">153</div>
                                <p class="text-white/90">ชุดเครื่องเขียน</p>
                            </div>
                        </div>
                    </div>

                    <!-- Schools Supported -->
                    <div class="mt-12">
                        <div class="text-center mb-8">
                            <div class="inline-block mb-4 px-6 py-2 bg-primary/10 rounded-full">
                                <span class="text-primary">โรงเรียนที่ได้รับการสนับสนุน</span>
                            </div>
                            <h3 class="text-3xl text-primary mb-4">
                                ตัวอย่างโรงเรียนที่ได้รับทุนปี 2567
                            </h3>
                        </div>
                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="p-4 bg-white border border-border rounded-xl hover:shadow-lg transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                                    </div>
                                    <p class="text-foreground/80 text-sm">โรงเรียนเฉลียงพิทยาคม จ.นครราชสีมา</p>
                                </div>
                            </div>
                            <div class="p-4 bg-white border border-border rounded-xl hover:shadow-lg transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                                    </div>
                                    <p class="text-foreground/80 text-sm">โรงเรียนภูเก็ตวิทยาลัย จ.ภูเก็ต</p>
                                </div>
                            </div>
                            <div class="p-4 bg-white border border-border rounded-xl hover:shadow-lg transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                                    </div>
                                    <p class="text-foreground/80 text-sm">โรงเรียนวัดศรีคงคาโคก จ.ชลบุรี</p>
                                </div>
                            </div>
                            <div class="p-4 bg-white border border-border rounded-xl hover:shadow-lg transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                                    </div>
                                    <p class="text-foreground/80 text-sm">โรงเรียนมัธยม จ.สุพรรณบุรี</p>
                                </div>
                            </div>
                            <div class="p-4 bg-white border border-border rounded-xl hover:shadow-lg transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                                    </div>
                                    <p class="text-foreground/80 text-sm">โรงเรียนบ้านดอนกระต่ายทอง จ.เชียงใหม่</p>
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

    <script>
        let selectedAmount = 500;
        let donationType = 'once';
        document.addEventListener('DOMContentLoaded', function () {
            const amountInput = document.getElementById('custom-amount');
            if (amountInput && !amountInput.value) amountInput.value = selectedAmount;
        });

        function selectAmount(amount) {
            selectedAmount = amount;
            const customInput = document.getElementById('custom-amount');
            customInput.value = amount;
            customInput.className = 'w-full pl-10 pr-4 py-3 border-2 border-border rounded-xl focus:border-accent focus:outline-none bg-input-background transition-all';
            
            document.querySelectorAll('.amount-btn').forEach(btn => {
                btn.className = 'amount-btn py-3 px-4 rounded-xl border-2 border-border text-foreground hover:border-accent/50 transition-all';
            });
            document.getElementById('btn-' + amount).className = 'amount-btn py-3 px-4 rounded-xl border-2 border-accent bg-accent/10 text-accent transition-all';
        }

        function selectType(type) {
            donationType = type;
            const hiddenType = document.getElementById('donation_type');
            if (hiddenType) hiddenType.value = type;
            if (type === 'once') {
                document.getElementById('type-once').className = 'py-3 px-4 rounded-xl border-2 border-accent bg-accent/10 text-accent transition-all';
                document.getElementById('type-monthly').className = 'py-3 px-4 rounded-xl border-2 border-border hover:border-accent/50 transition-all';
            } else {
                document.getElementById('type-monthly').className = 'py-3 px-4 rounded-xl border-2 border-accent bg-accent/10 text-accent transition-all';
                document.getElementById('type-once').className = 'py-3 px-4 rounded-xl border-2 border-border hover:border-accent/50 transition-all';
            }
        }

        function handleCustomAmount() {
            const customInput = document.getElementById('custom-amount');
            const custom = customInput.value;
            
            if (custom) {
                // Remove active class from all preset buttons
                document.querySelectorAll('.amount-btn').forEach(btn => {
                    btn.className = 'amount-btn py-3 px-4 rounded-xl border-2 border-border text-foreground hover:border-accent/50 transition-all';
                });
                // Highlight the custom input
                customInput.className = 'w-full pl-10 pr-4 py-3 border-2 border-accent rounded-xl focus:border-accent focus:outline-none bg-input-background transition-all';
            } else {
                // Reset custom input border
                customInput.className = 'w-full pl-10 pr-4 py-3 border-2 border-border rounded-xl focus:border-accent focus:outline-none bg-input-background transition-all';
            }
        }    </script>    </div>

<?php include __DIR__ . '/backend/helpers/floating_contact_widget.php'; ?>
</body>
</html>
