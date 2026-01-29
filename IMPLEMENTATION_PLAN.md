# 🚀 แผนการอัพเดทไฟล์ HTML ทั้งหมด

## 📋 สถานะปัจจุบัน

### ✅ ไฟล์ที่มี Navigation สมบูรณ์
- index.html (แต่ยังไม่มี Contact Modal)
- about.html (แต่ยังไม่มี Contact Modal)
- calendar.html (แต่ยังไม่มี Contact Modal)
- shop.html (แต่ยังไม่มี Contact Modal)
- login.html (แต่ยังไม่มี Contact Modal)
- register.html (แต่ยังไม่มี Contact Modal)

### ⚠️ ไฟล์ที่มี Navigation แบบ minified (ต้องแก้)
- donate.html
- volunteer.html
- stories.html
- contact.html
- cart.html

---

## 🎯 งานที่ต้องทำ

### Phase 1: เพิ่ม Contact Modal (11 ไฟล์)
ทุกไฟล์ต้องมี Contact Modal Popup ที่มีช่องทางติดต่อครบ 5 ช่องทาง

**ต้องแก้ในส่วน:**
1. แทนที่ function `toggleFloatingContact()` ที่เป็น alert
2. เพิ่ม HTML ของ Contact Modal
3. เพิ่ม functions: `closeContactModal()` และ ESC key handler

**ไฟล์ที่ต้องแก้:**
1. ✅ index.html
2. ⏳ about.html  
3. ⏳ calendar.html
4. ⏳ shop.html
5. ⏳ donate.html
6. ⏳ volunteer.html
7. ⏳ stories.html
8. ⏳ contact.html
9. ⏳ cart.html
10. ⏳ login.html
11. ⏳ register.html

### Phase 2: Expand Navigation (5 ไฟล์)
แก้ไข Navigation ที่เป็นแบบ minified ให้เป็นแบบ expanded และอ่านง่าย

**ไฟล์ที่ต้องแก้:**
1. ⏳ donate.html
2. ⏳ volunteer.html
3. ⏳ stories.html
4. ⏳ contact.html
5. ⏳ cart.html

---

## 📝 โค้ดที่ต้องเพิ่มในทุกไฟล์

### 1. HTML: Contact Modal (วางก่อน </body>)

```html
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
            <!-- Email -->
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
            <!-- Facebook, LINE, YouTube, TikTok... (ดูใน _navigation-template.html) -->
        </div>
        <div class="mt-6 pt-6 border-t border-border">
            <p class="text-xs text-center text-foreground/50">เวลาทำการ: จันทร์-ศุกร์ 9:00-17:00 น.</p>
        </div>
    </div>
</div>
```

### 2. JavaScript: แทนที่ใน <script>

```javascript
// แทนที่ function เดิม
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
```

---

## 🔍 วิธีตรวจสอบว่าตรง Figma หรือไม่

### Navigation
- ✅ Top bar มีสีพื้นหลัง `bg-secondary/30`
- ✅ มี email และ cart icon
- ✅ มีปุ่ม login/register
- ✅ Main nav มีเมนู 8 เมนู
- ✅ Active state เป็นสี `text-[#315d9f] bg-sky-100`

### Contact Modal
- ✅ มี 5 ช่องทางติดต่อ
- ✅ แต่ละช่องทางมี icon สีตาม brand
- ✅ มี hover effects
- ✅ สามารถปิดได้ด้วย ESC หรือคลิกนอก modal

### Content
- ✅ ข้อความและรูปภาพตรงกับ Figma
- ✅ สีและ spacing ถูกต้อง
- ✅ Responsive ทำงานได้ดี

---

## 📊 Progress Tracking

**Phase 1:** เพิ่ม Contact Modal
- [ ] 0/11 ไฟล์เสร็จ

**Phase 2:** Expand Navigation  
- [ ] 0/5 ไฟล์เสร็จ

**Total Progress:** 0%

---

## 🎨 Design Tokens (อ้างอิง)

```css
--primary: #233882;
--accent: #e83b3b;
--secondary: #d9e7ef;
--foreground: #303a56;
--border: #e2e8f0;
--hover-blue: #315d9f;
```

---

## 📞 Social Media Links

1. **Email:** thaifafoundation@gmail.com
2. **Facebook:** https://www.facebook.com/share/1FdXqqJNXE/
3. **LINE:** https://line.me/ti/p/~@thaifa  
4. **YouTube:** https://www.youtube.com/@THAIFAFoundation
5. **TikTok:** https://www.tiktok.com/@thaifafoundation

---

**Last Updated:** 2024-11-21  
**Status:** 🚧 In Progress - Need to update all files

**โค้ด Modal สมบูรณ์อยู่ใน:**
- `_navigation-template.html`
- `contact-modal-component.html`