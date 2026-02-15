<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตะกร้าสินค้า - THAIFA Foundation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Prompt', sans-serif; border-color: #e2e8f0; }
        body { background: #f8fafb; color: #303a56; }
        .checkbox-custom { appearance: none; width: 1.25rem; height: 1.25rem; border: 2px solid #e2e8f0; border-radius: 0.25rem; cursor: pointer; position: relative; }
        .checkbox-custom:checked { background: #233882; border-color: #233882; }
        .checkbox-custom:checked::after { content: '✓'; position: absolute; color: white; font-size: 0.875rem; top: 50%; left: 50%; transform: translate(-50%, -50%); }
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
                            <span id="cart-badge" class="absolute -top-2 -right-2 bg-accent text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">2</span>
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
                    <a href="index.php"><img src="https://via.placeholder.com/150x64/233882/FFFFFF?text=THAIFA" alt="THAIFA" class="h-16" /></a>
                    <div class="hidden lg:flex items-center gap-1">
                        <a href="index.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100 transition-colors">หน้าแรก</a>
                        <a href="about.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100 transition-colors">เกี่ยวกับเรา</a>
                        <a href="calendar.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100 transition-colors">ปฏิทิน</a>
                        <a href="shop.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100 transition-colors">ร้านค้า</a>
                        <a href="donate.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100 transition-colors">การบริจาค</a>
                        <a href="volunteer.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100 transition-colors">จิตอาสา</a>
                        <a href="stories.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100 transition-colors">เสียงจากใจ</a>
                        <a href="contact.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100 transition-colors">ติดต่อเรา</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Header (Sticky) -->
    <header class="bg-white border-b border-border sticky top-[120px] z-10 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl text-primary">ตะกร้าสินค้า</h1>
                <a href="shop.php" class="flex items-center gap-2 text-primary hover:text-primary/80 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    <span class="hidden sm:inline">เลือกซื้อสินค้าต่อ</span>
                </a>
            </div>
        </div>
    </header>

    <main class="pt-[160px] pb-32 lg:pb-8">
        <!-- Cart Content -->
        <section class="py-4 lg:py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-12 gap-4">
                    <!-- Items List - Left Side -->
                    <div class="lg:col-span-8 space-y-3">
                        <!-- Select All Header -->
                        <div class="bg-white rounded-lg shadow-sm p-4 border border-border">
                            <div class="flex items-center gap-4">
                                <input type="checkbox" id="select-all" class="checkbox-custom" onchange="toggleAllItems()" checked />
                                <span class="flex-1 text-foreground/80">สินค้า</span>
                                <div class="hidden sm:grid grid-cols-3 gap-4 text-foreground/60 text-sm text-center" style="width: 360px;">
                                    <span>ราคา</span>
                                    <span>จำนวน</span>
                                    <span>รวม</span>
                                </div>
                                <button onclick="deleteSelected()" class="text-foreground/40 hover:text-accent transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Cart Item 1 - หนังสือ VIP -->
                        <div class="bg-white rounded-lg shadow-sm border border-border hover:border-primary/30 transition-all" data-item-id="1">
                            <div class="p-4">
                                <div class="flex items-start gap-4">
                                    <!-- Checkbox -->
                                    <div class="pt-1">
                                        <input type="checkbox" class="item-checkbox checkbox-custom" checked onchange="updateSummary()" />
                                    </div>

                                    <!-- Product Image & Info -->
                                    <div class="flex items-start gap-4 flex-1">
                                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-lg overflow-hidden bg-secondary/10 flex-shrink-0 border border-border">
                                            <img src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=400" alt="หนังสือ VIP" class="w-full h-full object-cover" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-foreground mb-1 line-clamp-2">หนังสือ VIP - Visualization in Professional</h3>
                                            <p class="text-sm text-foreground/60 line-clamp-1 mb-2 sm:mb-0">หนังสือพัฒนาตนเองสำหรับมืออาชีพ</p>
                                            
                                            <!-- Mobile Layout -->
                                            <div class="sm:hidden mt-3 space-y-2">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-foreground/60 text-sm">ราคา</span>
                                                    <span class="text-foreground">฿350</span>
                                                </div>
                                                <div class="flex items-center justify-between">
                                                    <span class="text-foreground/60 text-sm">จำนวน</span>
                                                    <div class="flex items-center gap-2">
                                                        <button onclick="updateQuantity(this, -1)" class="h-7 w-7 border border-foreground/20 rounded flex items-center justify-center hover:bg-gray-50">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                                        </button>
                                                        <span class="min-w-[2rem] text-center quantity-value">1</span>
                                                        <button onclick="updateQuantity(this, 1)" class="h-7 w-7 border border-foreground/20 rounded flex items-center justify-center hover:bg-gray-50">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="flex items-center justify-between">
                                                    <span class="text-foreground/60 text-sm">ยอดรวม</span>
                                                    <span class="text-accent item-total">฿350</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Desktop Layout -->
                                    <div class="hidden sm:grid grid-cols-3 gap-4 items-center" style="width: 360px;">
                                        <div class="text-center">
                                            <span class="text-foreground item-price" data-price="350">฿350</span>
                                        </div>
                                        <div class="flex items-center justify-center gap-2">
                                            <button onclick="updateQuantity(this, -1)" class="h-8 w-8 border border-foreground/20 rounded flex items-center justify-center hover:bg-gray-50">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                            </button>
                                            <span class="min-w-[2.5rem] text-center quantity-value">1</span>
                                            <button onclick="updateQuantity(this, 1)" class="h-8 w-8 border border-foreground/20 rounded flex items-center justify-center hover:bg-gray-50">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            </button>
                                        </div>
                                        <div class="text-center">
                                            <span class="text-accent item-total">฿350</span>
                                        </div>
                                    </div>

                                    <!-- Delete Button -->
                                    <button onclick="deleteItem(this)" class="text-foreground/40 hover:text-accent transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Cart Item 2 - เสื้อยืด -->
                        <div class="bg-white rounded-lg shadow-sm border border-border hover:border-primary/30 transition-all" data-item-id="2">
                            <div class="p-4">
                                <div class="flex items-start gap-4">
                                    <div class="pt-1">
                                        <input type="checkbox" class="item-checkbox checkbox-custom" checked onchange="updateSummary()" />
                                    </div>
                                    <div class="flex items-start gap-4 flex-1">
                                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-lg overflow-hidden bg-secondary/10 flex-shrink-0 border border-border">
                                            <img src="https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400" alt="เสื้อยืด" class="w-full h-full object-cover" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-foreground mb-1 line-clamp-2">เสื้อยืด THAIFA Foundation</h3>
                                            <p class="text-sm text-foreground/60 line-clamp-1 mb-2 sm:mb-0">เสื้อยืดคุณภาพดี ไซส์ L</p>
                                            
                                            <div class="sm:hidden mt-3 space-y-2">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-foreground/60 text-sm">ราคา</span>
                                                    <span class="text-foreground">฿250</span>
                                                </div>
                                                <div class="flex items-center justify-between">
                                                    <span class="text-foreground/60 text-sm">จำนวน</span>
                                                    <div class="flex items-center gap-2">
                                                        <button onclick="updateQuantity(this, -1)" class="h-7 w-7 border border-foreground/20 rounded flex items-center justify-center hover:bg-gray-50">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                                        </button>
                                                        <span class="min-w-[2rem] text-center quantity-value">2</span>
                                                        <button onclick="updateQuantity(this, 1)" class="h-7 w-7 border border-foreground/20 rounded flex items-center justify-center hover:bg-gray-50">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="flex items-center justify-between">
                                                    <span class="text-foreground/60 text-sm">ยอดรวม</span>
                                                    <span class="text-accent item-total">฿500</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="hidden sm:grid grid-cols-3 gap-4 items-center" style="width: 360px;">
                                        <div class="text-center">
                                            <span class="text-foreground item-price" data-price="250">฿250</span>
                                        </div>
                                        <div class="flex items-center justify-center gap-2">
                                            <button onclick="updateQuantity(this, -1)" class="h-8 w-8 border border-foreground/20 rounded flex items-center justify-center hover:bg-gray-50">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                            </button>
                                            <span class="min-w-[2.5rem] text-center quantity-value">2</span>
                                            <button onclick="updateQuantity(this, 1)" class="h-8 w-8 border border-foreground/20 rounded flex items-center justify-center hover:bg-gray-50">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            </button>
                                        </div>
                                        <div class="text-center">
                                            <span class="text-accent item-total">฿500</span>
                                        </div>
                                    </div>
                                    <button onclick="deleteItem(this)" class="text-foreground/40 hover:text-accent transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary - Right Side (Desktop) -->
                    <div class="hidden lg:block lg:col-span-4">
                        <div class="bg-white rounded-lg shadow-sm p-6 sticky top-44 border border-border">
                            <h3 class="text-lg text-primary mb-4 pb-4 border-b border-border">สรุปคำสั่งซื้อ</h3>
                            
                            <div class="space-y-3 mb-4 pb-4 border-b border-border">
                                <div class="flex justify-between text-foreground/70">
                                    <span>สินค้าที่เลือก (<span id="selected-count">3</span> ชิ้น)</span>
                                </div>
                                <div class="flex justify-between text-foreground/70">
                                    <span>ราคาสินค้า</span>
                                    <span id="subtotal">฿850</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-foreground/70">ค่าจัดส่ง</span>
                                    <div class="text-right">
                                        <div class="text-accent text-sm">ฟรี</div>
                                        <div class="text-foreground/50 text-xs line-through">฿50</div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-between items-center mb-6">
                                <span class="text-foreground/70">ยอดรวมทั้งสิ้น</span>
                                <div class="text-right">
                                    <div class="text-2xl text-accent" id="total">฿850</div>
                                </div>
                            </div>

                            <button onclick="checkout()" class="w-full bg-accent hover:bg-accent/90 text-white h-12 rounded-lg transition-colors">
                                ชำระเงิน (<span id="checkout-count">2</span>)
                            </button>

                            <div class="mt-4 p-3 bg-secondary/10 rounded-lg">
                                <p class="text-sm text-primary text-center">จัดส่งฟรีทุกรายการ!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Mobile Sticky Summary -->
    <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t-2 border-border shadow-lg z-20">
        <div class="px-4 py-3">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <input type="checkbox" id="select-all-mobile" class="checkbox-custom" onchange="toggleAllItems()" checked />
                    <span class="text-sm text-foreground/80">
                        เลือกทั้งหมด (<span id="selected-mobile">2</span>/<span id="total-mobile">2</span>)
                    </span>
                </div>
                <div class="text-right">
                    <div class="text-xs text-foreground/60">ยอดรวม</div>
                    <div class="text-lg text-accent" id="total-mobile-amount">฿850</div>
                </div>
            </div>
            
            <button onclick="checkout()" class="w-full bg-accent hover:bg-accent/90 text-white h-12 rounded-lg transition-colors">
                ชำระเงิน (<span id="checkout-mobile">2</span> รายการ)
            </button>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-primary text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <div>
                    <div class="mb-6">
                        <img src="https://via.placeholder.com/150x64/FFFFFF/FFFFFF?text=THAIFA" alt="THAIFA Foundation" class="h-16 w-auto brightness-0 invert" />
                    </div>
                    <p class="text-white/80 text-sm leading-relaxed mb-4">
                        มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน มุ่งมั่นสร้างโอกาสและพัฒนาคุณภาพชีวิตของเด็กและเยาวชนไทย
                    </p>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-accent" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /></svg>
                        <span class="text-white/90 text-sm">ขอบคุณที่ร่วมเคียงฝันไปด้วยกัน</span>
                    </div>
                </div>
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
        function updateQuantity(btn, change) {
            const item = btn.closest('[data-item-id]');
            const qtyElements = item.querySelectorAll('.quantity-value');
            const currentQty = parseInt(qtyElements[0].textContent);
            const newQty = Math.max(1, currentQty + change);
            
            qtyElements.forEach(el => el.textContent = newQty);
            
            // Update item total
            const price = parseInt(item.querySelector('.item-price').dataset.price);
            const total = price * newQty;
            item.querySelectorAll('.item-total').forEach(el => {
                el.textContent = '฿' + total.toLocaleString();
            });
            
            updateSummary();
        }

        function deleteItem(btn) {
            const item = btn.closest('[data-item-id]');
            item.remove();
            updateSummary();
            updateCartBadge();
        }

        function deleteSelected() {
            const checkboxes = document.querySelectorAll('.item-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('กรุณาเลือกสินค้าที่ต้องการลบ');
                return;
            }
            checkboxes.forEach(cb => {
                cb.closest('[data-item-id]').remove();
            });
            updateSummary();
            updateCartBadge();
        }

        function toggleAllItems() {
            const selectAll = document.getElementById('select-all').checked;
            const selectAllMobile = document.getElementById('select-all-mobile');
            if (selectAllMobile) selectAllMobile.checked = selectAll;
            
            document.querySelectorAll('.item-checkbox').forEach(cb => {
                cb.checked = selectAll;
            });
            updateSummary();
        }

        function updateSummary() {
            const items = document.querySelectorAll('[data-item-id]');
            const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
            
            let total = 0;
            let selectedCount = 0;
            
            selectedCheckboxes.forEach(cb => {
                const item = cb.closest('[data-item-id]');
                const price = parseInt(item.querySelector('.item-price').dataset.price);
                const qty = parseInt(item.querySelector('.quantity-value').textContent);
                total += price * qty;
                selectedCount += qty;
            });
            
            // Update desktop summary
            document.getElementById('selected-count').textContent = selectedCount;
            document.getElementById('subtotal').textContent = '฿' + total.toLocaleString();
            document.getElementById('total').textContent = '฿' + total.toLocaleString();
            document.getElementById('checkout-count').textContent = selectedCheckboxes.length;
            
            // Update mobile summary
            document.getElementById('selected-mobile').textContent = selectedCheckboxes.length;
            document.getElementById('total-mobile').textContent = items.length;
            document.getElementById('total-mobile-amount').textContent = '฿' + total.toLocaleString();
            document.getElementById('checkout-mobile').textContent = selectedCheckboxes.length;
        }

        function updateCartBadge() {
            const items = document.querySelectorAll('[data-item-id]');
            document.getElementById('cart-badge').textContent = items.length;
        }

        function checkout() {
            const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
            if (selectedCheckboxes.length === 0) {
                alert('กรุณาเลือกสินค้าที่ต้องการชำระเงิน');
                return;
            }
            alert('กำลังไปยังหน้าชำระเงิน...');
            // window.location.href = 'checkout.php';
        }

        // Sync select-all checkboxes
        document.getElementById('select-all').addEventListener('change', function() {
            const mobile = document.getElementById('select-all-mobile');
            if (mobile) mobile.checked = this.checked;
        });
        
        const mobileSelectAll = document.getElementById('select-all-mobile');
        if (mobileSelectAll) {
            mobileSelectAll.addEventListener('change', function() {
                document.getElementById('select-all').checked = this.checked;
            });
        }

        // Initialize
        updateSummary();

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
                <a href="https://line.me/ti/p/~@thaifa" target="_blank" class="flex items-center gap-4 p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-all group">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform" style="background: #06C755;">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-primary">LINE</div>
                        <div class="text-sm text-foreground/60">@thaifa</div>
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
