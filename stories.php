<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เสียงจากใจ - THAIFA Foundation</title>
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

        .tab-active { background: #233882; color: white; box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1); }
        .tab-inactive { color: rgba(48, 58, 86, 0.7); }
        .tab-inactive:hover { color: #233882; }
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
                            <span class="absolute -top-2 -right-2 bg-accent text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">0</span>
                        </a>

                        <!-- Auth Buttons -->
                        <div class="flex items-center gap-2 pl-4 border-l border-border">
                            <a href="login.php" class="flex items-center gap-1 text-foreground/80 hover:text-primary transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="hidden sm:inline">เข้าสู่ระบบ</span>
                            </a>
                            <span class="text-foreground/40">/</span>
                            <a href="register.php" class="text-foreground/80 hover:text-primary transition-colors">
                                <span class="hidden sm:inline">สมัครสมาชิก</span>
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
                        <a href="index.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100">หน้าแรก</a>
                        <a href="about.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100">เกี่ยวกับเรา</a>
                        <a href="calendar.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100">ปฏิทิน</a>
                        <a href="shop.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100">ร้านค้า</a>
                        <a href="donate.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100">การบริจาค</a>
                        <a href="volunteer.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100">จิตอาสา</a>
                        <a href="stories.php" class="text-[#315d9f] bg-sky-100 px-4 py-2 rounded-md">เสียงจากใจ</a>
                        <a href="contact.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100">ติดต่อเรา</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="pt-[120px]">
        <!-- Hero Banner -->
        <section class="relative h-[400px] bg-gradient-to-br from-primary via-primary to-primary/90 flex items-center justify-center overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-20 left-10 w-40 h-40 bg-white rounded-full blur-3xl"></div>
                <div class="absolute bottom-20 right-20 w-60 h-60 bg-accent rounded-full blur-3xl"></div>
            </div>
            <div class="relative z-10 text-center text-white max-w-4xl mx-auto px-4">
                <h1 class="text-5xl md:text-6xl mb-6">เสียงจากใจ</h1>
                <p class="text-xl text-white/90 leading-relaxed">
                    ฟังเรื่องราวและความประทับใจจากผู้รับทุนและจิตอาสา<br />
                    ที่ร่วมสร้างความเปลี่ยนแปลงที่ดีให้กับสังคม
                </p>
            </div>
        </section>

        <!-- Tab Section -->
        <section class="py-20 bg-gradient-to-b from-white to-secondary/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Tabs -->
                <div class="flex justify-center mb-12">
                    <div class="inline-flex flex-wrap justify-center bg-secondary/30 rounded-full p-1.5 gap-1 max-w-4xl">
                        <button onclick="showTab('volunteers')" id="tab-volunteers" class="px-4 sm:px-6 py-3 rounded-full transition-all duration-300 tab-active whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                <span class="text-sm sm:text-base">ความสุขของผู้ให้</span>
                            </div>
                        </button>
                        <button onclick="showTab('beneficiaries')" id="tab-beneficiaries" class="px-4 sm:px-6 py-3 rounded-full transition-all duration-300 tab-inactive whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                <span class="text-sm sm:text-base">ความในใจของผู้รับ</span>
                            </div>
                        </button>
                        <button onclick="showTab('applicants')" id="tab-applicants" class="px-4 sm:px-6 py-3 rounded-full transition-all duration-300 tab-inactive whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span class="text-sm sm:text-base">เสียงจากผู้สมัครจิตอาสา</span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Volunteers Content -->
                <div id="content-volunteers" class="space-y-8">
                    <div class="text-center mb-12">
                        <h2 class="text-4xl md:text-5xl text-primary mb-4">เสียงจากจิตอาสา</h2>
                        <p class="text-lg text-foreground/70 max-w-3xl mx-auto">
                            การเป็นจิตอาสาไม่ได้แค่ให้ แต่ยังได้รับกลับมามากมาย<br />
                            ทั้งความสุข ความภูมิใจ และความหมายของชีวิต
                        </p>
                    </div>

                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div class="p-6 bg-white border border-border rounded-2xl hover:shadow-xl transition-all">
                            <div class="flex items-start gap-4 mb-4">
                                <div class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-accent/20">
                                    <img src="https://via.placeholder.com/64x64/e83b3b/FFFFFF?text=V" alt="จิตอาสา" class="w-full h-full object-cover" />
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-primary mb-1">คุณสมชาย ใจดี</h3>
                                    <p class="text-foreground/60 text-sm">จิตอาสาสอนหนังสือ 2 ปี</p>
                                    <p class="text-foreground/60 text-sm">กรุงเทพฯ</p>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="w-10 h-10 bg-accent/10 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5 text-accent" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                                </div>
                                <p class="text-foreground/80 leading-relaxed italic text-sm">
                                    "การได้ร่วมเป็นจิตอาสาสอนหนังสือให้น้องๆ ทำให้ผมได้เห็นรอยยิ้มและความหวังในดวงตาของพวกเขา เป็นประสบการณ์ที่คุ้มค่ามากกว่าที่คาดคิด ทุกครั้งที่เห็นน้องๆ พัฒนาขึ้น รู้สึกภูมิใจและมีความสุขมากครับ"
                                </p>
                            </div>
                            <div class="pt-4 border-t border-border">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-accent flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                    <p class="text-foreground/70 text-sm">ช่วยสอนน้องๆ กว่า 50 คน ในหลากหลายวิชา</p>
                                </div>
                            </div>
                        </div>

                        <!-- Add 5 more volunteer stories similarly -->
                        <div class="p-6 bg-white border border-border rounded-2xl hover:shadow-xl transition-all">
                            <div class="flex items-start gap-4 mb-4">
                                <div class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-accent/20 bg-secondary"></div>
                                <div class="flex-1">
                                    <h3 class="text-primary mb-1">คุณนภา สวยงาม</h3>
                                    <p class="text-foreground/60 text-sm">จิตอาสาจัดกิจกรรม 1 ปี</p>
                                    <p class="text-foreground/60 text-sm">เชียงใหม่</p>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="w-10 h-10 bg-accent/10 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5 text-accent" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                                </div>
                                <p class="text-foreground/80 leading-relaxed italic text-sm">
                                    "ได้เป็นส่วนหนึ่งของการสร้างความสุขให้เด็กๆ ผ่านกิจกรรมต่างๆ ทำให้รู้สึกว่าเราก็มีส่วนช่วยสร้างสังคมที่ดีขึ้น การได้เห็นรอยยิ้มของเด็กๆ คือรางวัลที่ดีที่สุด"
                                </p>
                            </div>
                            <div class="pt-4 border-t border-border">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-accent flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                    <p class="text-foreground/70 text-sm">จัดกิจกรรมพัฒนาทักษะให้เด็กๆ มากกว่า 15 ครั้ง</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Beneficiaries Content (hidden by default) -->
                <div id="content-beneficiaries" class="space-y-8 hidden">
                    <div class="text-center mb-12">
                        <h2 class="text-4xl md:text-5xl text-primary mb-4">เสียงจากผู้รับทุน</h2>
                        <p class="text-lg text-foreground/70 max-w-3xl mx-auto">
                            ทุนการศึกษาไม่ได้เป็นแค่ตัวเงิน แต่เป็นความหวัง โอกาส และกำลังใจ<br />
                            ที่จะเปลี่ยนชีวิตของเด็กและครอบครัวไปตลอดกาล
                        </p>
                    </div>

                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div class="p-6 bg-white border border-border rounded-2xl hover:shadow-xl transition-all">
                            <div class="flex items-start gap-4 mb-4">
                                <div class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-primary/20">
                                    <img src="https://via.placeholder.com/64x64/233882/FFFFFF?text=K" alt="กิตตินันท์" class="w-full h-full object-cover" />
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-primary mb-1">นาย กิตตินันท์ กุลพิมาย</h3>
                                    <p class="text-foreground/60 text-sm">นักศึกษาปี 1 มหาวิทยาลัยขอนแก่น</p>
                                    <p class="text-foreground/60 text-sm">คณะศึกษาศาสตร์ สาขาวิทยาศาสตร์ศึกษา</p>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                                </div>
                                <p class="text-foreground/80 leading-relaxed italic text-sm">
                                    "ทุนการศึกษา 7,000 บาท นี้ไม่ได้แค่ช่วยเรื่องค่าใช้จ่าย แต่ยังเป็นแรงจูงใจที่ยิ่งใหญ่ ทำให้ผมรู้ว่ายังมีคนเชื่อในเด็กด้อยโอกาสเหมือนผม วันนี้ผมได้เข้ามหาลัยชั้นนำในอีสานตามความฝัน และในอนาคตผมจะส่งมอบโอกาสนี้ให้เด็กๆ ในฐานะคุณครูที่ดี สอนให้เด็กเป็นคนดีในสังคมตลอดไป"
                                </p>
                            </div>
                            <div class="pt-4 border-t border-border">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-accent flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    <p class="text-foreground/70 text-sm">เข้าศึกษาต่อ มข. คณะศึกษาศาสตร์ เพื่อความฝันเป็นครู</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 bg-white border border-border rounded-2xl hover:shadow-xl transition-all">
                            <div class="flex items-start gap-4 mb-4">
                                <div class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-primary/20">
                                    <img src="https://via.placeholder.com/64x64/233882/FFFFFF?text=N" alt="นิติธร" class="w-full h-full object-cover" />
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-primary mb-1">นาย นิติธร โฉงาม</h3>
                                    <p class="text-foreground/60 text-sm">นักเรียน</p>
                                    <p class="text-foreground/60 text-sm">ผู้รับทุนการศึกษา</p>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                                </div>
                                <p class="text-foreground/80 leading-relaxed italic text-sm">
                                    "ขอบคุณท่านผู้ใหญ่ใจดีทุกท่านที่มอบทุนการศึกษาให้ผม ผมจะตั้งใจเรียนให้จบและพัฒนาตัวเองให้ดีขึ้นไปเรื่อยๆ ผมขออำนาจสิ่งศักดิ์สิทธิ์ในสากลโลกจงดลบันดาลให้ทุกท่านพบแต่สิ่งดีๆ เข้ามาในชีวิต และเจริญในหน้าที่การงานยิ่งๆ ขึ้นไป"
                                </p>
                            </div>
                            <div class="pt-4 border-t border-border">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-accent flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    <p class="text-foreground/70 text-sm">มุ่งมั่นตั้งใจเรียนและพัฒนาตนเองอย่างต่อเนื่อง</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 bg-white border border-border rounded-2xl hover:shadow-xl transition-all">
                            <div class="flex items-start gap-4 mb-4">
                                <div class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-primary/20">
                                    <img src="https://via.placeholder.com/64x64/233882/FFFFFF?text=A" alt="อนุชิต" class="w-full h-full object-cover" />
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-primary mb-1">นาย อนุชิต พันธุ์ปรุ</h3>
                                    <p class="text-foreground/60 text-sm">นักศึกษามหาวิทยาลัยราชภัฏนครราชสีมา</p>
                                    <p class="text-foreground/60 text-sm">คณะครุศาสตร์ สาขาสังคมศึกษา</p>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                                </div>
                                <p class="text-foreground/80 leading-relaxed italic text-sm">
                                    "ทุนการศึกษา 7,000 บาท/ปี เป็นปัจจัยสำคัญที่ช่วยให้ผมสานฝันได้เต็มที่ ตั้งใจทำประโยชน์ต่อสังคม จนได้รับรางวัลผู้มีความประพฤติดีจากพุทธสมาคมฯ และรางวัลโครงงานวิทยาศาสตร์ระดับประเทศ จบการศึกษาด้วยเกรดเฉลี่ย 3.84 พวกผมสัญญาว่าจะเป็นคนดีของสังคมและเป็นคนที่สังคมต้องการ"
                                </p>
                            </div>
                            <div class="pt-4 border-t border-border">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-accent flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    <p class="text-foreground/70 text-sm">จบ ม.6 เกรดเฉลี่ย 3.84 และได้รับรางวัลระดับประเทศ</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Applicants Content (hidden by default) -->
                <div id="content-applicants" class="space-y-8 hidden">
                    <div class="text-center mb-12">
                        <h2 class="text-4xl md:text-5xl text-primary mb-4">เสียงจากผู้สมัครจิตอาสา</h2>
                        <p class="text-lg text-foreground/70 max-w-3xl mx-auto">
                            ฟังเสียงจากใจของผู้ที่ต้องการร่วมเป็นส่วนหนึ่งในการสร้างสังคมที่ดีขึ้น<br />
                            ความตั้งใจและแรงบันดาลใจในการเป็นจิตอาสา
                        </p>
                    </div>

                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Applicant 1 -->
                        <div class="p-6 bg-white border border-border rounded-2xl hover:shadow-xl transition-all">
                            <div class="flex items-start gap-4 mb-4">
                                <div class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-green-500/20 bg-green-100 flex items-center justify-center">
                                    <span class="text-2xl text-green-600">👤</span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-primary mb-1">คุณธนากร รักชาติ</h3>
                                    <p class="text-foreground/60 text-sm">วิศวกร อายุ 28 ปี</p>
                                    <p class="text-foreground/60 text-sm">กรุงเทพฯ</p>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="w-10 h-10 bg-green-500/10 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                                </div>
                                <p class="text-foreground/80 leading-relaxed italic text-sm">
                                    "ผมเคยเป็นเด็กที่ได้รับทุนมาตอนเด็ก และรู้สึกว่าถึงเวลาแล้วที่จะคืนกลับสู่สังคม อยากใช้ความรู้ด้านเทคโนโลยีของผมช่วยสอนน้องๆ ให้มีทักษะที่จำเป็นในยุคดิจิทัล เพื่อให้พวกเขามีโอกาสที่ดีในอนาคต"
                                </p>
                            </div>
                            <div class="pt-4 border-t border-border">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-foreground/70 text-sm">สนใจสอนเทคโนโลยีและคอมพิวเตอร์</p>
                                </div>
                            </div>
                        </div>

                        <!-- Applicant 2 -->
                        <div class="p-6 bg-white border border-border rounded-2xl hover:shadow-xl transition-all">
                            <div class="flex items-start gap-4 mb-4">
                                <div class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-green-500/20 bg-green-100 flex items-center justify-center">
                                    <span class="text-2xl text-green-600">👤</span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-primary mb-1">คุณสุดารัตน์ ใจบุญ</h3>
                                    <p class="text-foreground/60 text-sm">ครู อายุ 35 ปี</p>
                                    <p class="text-foreground/60 text-sm">เชียงใหม่</p>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="w-10 h-10 bg-green-500/10 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                                </div>
                                <p class="text-foreground/80 leading-relaxed italic text-sm">
                                    "นอกเหนือจากการสอนในโรงเรียน ฉันยังอยากทำอะไรให้มากกว่านั้น การเป็นจิตอาสากับมูลนิธิจะทำให้ฉันได้เข้าถึงเด็กๆ ที่ต้องการความช่วยเหลือจริงๆ และได้ใช้ประสบการณ์การสอนของฉันให้เกิดประโยชน์สูงสุด"
                                </p>
                            </div>
                            <div class="pt-4 border-t border-border">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-foreground/70 text-sm">สนใจติวหนังสือและให้คำปรึกษา</p>
                                </div>
                            </div>
                        </div>

                        <!-- Applicant 3 -->
                        <div class="p-6 bg-white border border-border rounded-2xl hover:shadow-xl transition-all">
                            <div class="flex items-start gap-4 mb-4">
                                <div class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-green-500/20 bg-green-100 flex items-center justify-center">
                                    <span class="text-2xl text-green-600">👤</span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-primary mb-1">คุณอรรถพล มีสุข</h3>
                                    <p class="text-foreground/60 text-sm">นักศึกษา อายุ 22 ปี</p>
                                    <p class="text-foreground/60 text-sm">กรุงเทพฯ</p>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="w-10 h-10 bg-green-500/10 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                                </div>
                                <p class="text-foreground/80 leading-relaxed italic text-sm">
                                    "แม้ว่าจะยังเป็นนักศึกษา แต่ผมรู้สึกว่าเรามีเวลาและพลังที่จะช่วยเหลือผู้อื่น การได้เป็นจิตอาสาจะทำให้ผมได้เรียนรู้ ได้พัฒนาตัวเอง และที่สำคัญคือได้มีส่วนร่วมในการสร้างสังคมที่ดีขึ้น"
                                </p>
                            </div>
                            <div class="pt-4 border-t border-border">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-foreground/70 text-sm">สนใจช่วยงานจัดกิจกรรมและลงพื้นที่</p>
                                </div>
                            </div>
                        </div>

                        <!-- Applicant 4 -->
                        <div class="p-6 bg-white border border-border rounded-2xl hover:shadow-xl transition-all">
                            <div class="flex items-start gap-4 mb-4">
                                <div class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-green-500/20 bg-green-100 flex items-center justify-center">
                                    <span class="text-2xl text-green-600">👤</span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-primary mb-1">คุณพิมพ์ชนก สว่างใจ</h3>
                                    <p class="text-foreground/60 text-sm">นักการตลาด อายุ 30 ปี</p>
                                    <p class="text-foreground/60 text-sm">กรุงเทพฯ</p>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="w-10 h-10 bg-green-500/10 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                                </div>
                                <p class="text-foreground/80 leading-relaxed italic text-sm">
                                    "อยากใช้ทักษะด้านการสื่อสารและการตลาดของฉันช่วยประชาสัมพันธ์กิจกรรมของมูลนิธิ ให้คนรู้จักและเข้าใจในงานที่ดีที่ทำอยู่ เพื่อดึงดูดผู้สนับสนุนและจิตอาสาคนใหม่ๆ เข้ามาร่วมงาน"
                                </p>
                            </div>
                            <div class="pt-4 border-t border-border">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-foreground/70 text-sm">สนใจช่วยงานประชาสัมพันธ์และสื่อสาร</p>
                                </div>
                            </div>
                        </div>

                        <!-- Applicant 5 -->
                        <div class="p-6 bg-white border border-border rounded-2xl hover:shadow-xl transition-all">
                            <div class="flex items-start gap-4 mb-4">
                                <div class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-green-500/20 bg-green-100 flex items-center justify-center">
                                    <span class="text-2xl text-green-600">👤</span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-primary mb-1">คุณวราภรณ์ ดีงาม</h3>
                                    <p class="text-foreground/60 text-sm">พยาบาล อายุ 40 ปี</p>
                                    <p class="text-foreground/60 text-sm">นนทบุรี</p>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="w-10 h-10 bg-green-500/10 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                                </div>
                                <p class="text-foreground/80 leading-relaxed italic text-sm">
                                    "ในฐานะพยาบาล ฉันเห็นความสำคัญของการดูแลสุขภาพและการศึกษาควบคู่กัน อยากอาสาให้ความรู้ด้านสุขภาพพื้นฐานแก่เด็กๆ และครอบครัว เพื่อให้พวกเขามีสุขภาพที่ดีไปพร้อมกับการเรียน"
                                </p>
                            </div>
                            <div class="pt-4 border-t border-border">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-foreground/70 text-sm">สนใจให้ความรู้ด้านสุขภาพและการดูแลตัวเอง</p>
                                </div>
                            </div>
                        </div>

                        <!-- Applicant 6 -->
                        <div class="p-6 bg-white border border-border rounded-2xl hover:shadow-xl transition-all">
                            <div class="flex items-start gap-4 mb-4">
                                <div class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-green-500/20 bg-green-100 flex items-center justify-center">
                                    <span class="text-2xl text-green-600">👤</span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-primary mb-1">คุณณัฐพงศ์ เจริญสุข</h3>
                                    <p class="text-foreground/60 text-sm">ช่างภาพ อายุ 26 ปี</p>
                                    <p class="text-foreground/60 text-sm">กรุงเทพฯ</p>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="w-10 h-10 bg-green-500/10 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                                </div>
                                <p class="text-foreground/80 leading-relaxed italic text-sm">
                                    "อยากใช้ทักษะการถ่ายภาพของผมช่วยบันทึกเรื่องราวดีๆ ของมูลนิธิ ทั้งกิจกรรมมอบทุน รอยยิ้มของเด็กๆ และความตั้งใจของจิตอาสา เพื่อสร้างแรงบันดาลใจให้คนอื่นๆ เข้ามาร่วมทำความดีด้วยกัน"
                                </p>
                            </div>
                            <div class="pt-4 border-t border-border">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-foreground/70 text-sm">สนใจถ่ายภาพและบันทึกกิจกรรม</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Call to Action -->
                <div class="mt-16 text-center">
                    <div class="max-w-4xl mx-auto p-8 md:p-12 bg-gradient-to-r from-primary/5 to-accent/5 border border-primary/20 rounded-3xl">
                        <h3 class="text-primary mb-4 text-3xl">ร่วมเป็นส่วนหนึ่งของเรื่องราวดีๆ</h3>
                        <p class="text-foreground/70 mb-8 text-lg">
                            คุณก็สามารถสร้างความเปลี่ยนแปลงและเป็นส่วนหนึ่งของเรื่องราวที่สร้างแรงบันดาลใจ
                        </p>
                        <div class="flex flex-wrap justify-center gap-4">
                            <a href="donate.html">
                                <button class="px-8 py-4 bg-primary text-white rounded-full hover:bg-primary/90 transition-all shadow-lg hover:shadow-xl">
                                    บริจาคเพื่อช่วยเหลือ
                                </button>
                            </a>
                            <a href="volunteer.html">
                                <button class="px-8 py-4 bg-accent text-white rounded-full hover:bg-accent/90 transition-all shadow-lg hover:shadow-xl">
                                    สมัครเป็นจิตอาสา
                                </button>
                            </a>
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
                        <img src="assets/images/Blue logo.png" alt="THAIFA Foundation" class="h-16 w-auto brightness-0 invert" />
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
                        <li><a href="index.html" class="text-white/80 hover:text-accent transition-colors text-sm">หน้าแรก</a></li>
                        <li><a href="about.html" class="text-white/80 hover:text-accent transition-colors text-sm">เกี่ยวกับเรา</a></li>
                        <li><a href="donate.html" class="text-white/80 hover:text-accent transition-colors text-sm">การบริจาค</a></li>
                        <li><a href="volunteer.html" class="text-white/80 hover:text-accent transition-colors text-sm">จิตอาสา</a></li>
                        <li><a href="stories.html" class="text-white/80 hover:text-accent transition-colors text-sm">เสียงจากใจ</a></li>
                        <li><a href="contact.html" class="text-white/80 hover:text-accent transition-colors text-sm">ติดต่อเรา</a></li>
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
        function showTab(tab) {
            const beneficiariesTab = document.getElementById('tab-beneficiaries');
            const volunteersTab = document.getElementById('tab-volunteers');
            const applicantsTab = document.getElementById('tab-applicants');
            const beneficiariesContent = document.getElementById('content-beneficiaries');
            const volunteersContent = document.getElementById('content-volunteers');
            const applicantsContent = document.getElementById('content-applicants');

            if (tab === 'beneficiaries') {
                beneficiariesTab.classList.add('tab-active');
                beneficiariesTab.classList.remove('tab-inactive');
                volunteersTab.classList.remove('tab-active');
                volunteersTab.classList.add('tab-inactive');
                applicantsTab.classList.remove('tab-active');
                applicantsTab.classList.add('tab-inactive');
                beneficiariesContent.classList.remove('hidden');
                volunteersContent.classList.add('hidden');
                applicantsContent.classList.add('hidden');
            } else if (tab === 'volunteers') {
                volunteersTab.classList.add('tab-active');
                volunteersTab.classList.remove('tab-inactive');
                beneficiariesTab.classList.remove('tab-active');
                beneficiariesTab.classList.add('tab-inactive');
                applicantsTab.classList.remove('tab-active');
                applicantsTab.classList.add('tab-inactive');
                volunteersContent.classList.remove('hidden');
                beneficiariesContent.classList.add('hidden');
                applicantsContent.classList.add('hidden');
            } else {
                applicantsTab.classList.add('tab-active');
                applicantsTab.classList.remove('tab-inactive');
                beneficiariesTab.classList.remove('tab-active');
                beneficiariesTab.classList.add('tab-inactive');
                volunteersTab.classList.remove('tab-active');
                volunteersTab.classList.add('tab-inactive');
                volunteersContent.classList.add('hidden');
                beneficiariesContent.classList.add('hidden');
                applicantsContent.classList.remove('hidden');
            }
        }

        function toggleFloatingContact() {
            document.getElementById('contactModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeContactModal() {
            document.getElementById('contactModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeContactModal();
        });
    </script>

    <!-- Floating Contact Button -->
    <div class="fixed bottom-6 right-6 z-50">
        <button onclick="toggleFloatingContact()" class="bg-primary hover:bg-primary/90 text-white rounded-full px-5 py-4 shadow-lg transition-all hover:scale-105 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <span class="text-sm">ติดต่อเรา</span>
        </button>
    </div>

    <!-- Contact Modal -->
    <div id="contactModal" class="hidden fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4" onclick="if(event.target.id === 'contactModal') closeContactModal()">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 transform transition-all" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl text-primary">ติดต่อเรา</h3>
                <button onclick="closeContactModal()" class="text-foreground/40 hover:text-foreground/80 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <p class="text-foreground/70 mb-6">เลือกช่องทางที่คุณต้องการติดต่อ</p>
            <div class="space-y-3">
                <a href="mailto:thaifafoundation@gmail.com" class="flex items-center gap-4 p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-all group">
                    <div class="w-12 h-12 bg-accent rounded-full flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-primary">อีเมล</div>
                        <div class="text-sm text-foreground/60">thaifafoundation@gmail.com</div>
                    </div>
                    <svg class="w-5 h-5 text-foreground/40 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="https://www.facebook.com/share/1FdXqqJNXE/" target="_blank" class="flex items-center gap-4 p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-all group">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform" style="background: #1877F2;">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-primary">Facebook</div>
                        <div class="text-sm text-foreground/60">THAIFA Foundation</div>
                    </div>
                    <svg class="w-5 h-5 text-foreground/40 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="https://line.me/R/ti/p/@519lkcsb" target="_blank" class="flex items-center gap-4 p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-all group">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform" style="background: #06C755;">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-primary">LINE</div>
                        <div class="text-sm text-foreground/60">@519lkcsb</div>
                    </div>
                    <svg class="w-5 h-5 text-foreground/40 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="https://www.youtube.com/@THAIFAFoundation" target="_blank" class="flex items-center gap-4 p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-all group">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform" style="background: #FF0000;">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-primary">YouTube</div>
                        <div class="text-sm text-foreground/60">THAIFA Foundation</div>
                    </div>
                    <svg class="w-5 h-5 text-foreground/40 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="https://www.tiktok.com/@thaifafoundation" target="_blank" class="flex items-center gap-4 p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-all group">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform" style="background: #000000;">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.10-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-primary">TikTok</div>
                        <div class="text-sm text-foreground/60">@thaifafoundation</div>
                    </div>
                    <svg class="w-5 h-5 text-foreground/40 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <div class="mt-6 pt-6 border-t border-border">
                <p class="text-xs text-center text-foreground/50">เวลาทำการ: จันทร์-ศุกร์ 9:00-17:00 น.</p>
            </div>
        </div>
    </div>

</body>
</html>