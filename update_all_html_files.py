#!/usr/bin/env python3
"""
สคริปต์อัพเดทไฟล์ HTML ทั้งหมดให้มี Contact Modal

วิธีใช้: python3 update_all_html_files.py
"""

import os
import re

# Contact Modal HTML
CONTACT_MODAL_HTML = '''    <!-- Contact Modal -->
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

'''

# JavaScript Functions
JS_FUNCTIONS = '''
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
        });'''

# ไฟล์ที่ต้องอัพเดท (ยกเว้น index.html และ about.html ที่ทำแล้ว)
FILES_TO_UPDATE = [
    'calendar.html',
    'shop.html',
    'donate.html',
    'volunteer.html',
    'stories.html',
    'contact.html',
    'cart.html',
    'login.html',
    'register.html'
]

def update_html_file(filename):
    """อัพเดทไฟล์ HTML ให้มี Contact Modal"""
    filepath = os.path.join('/static-html-exact', filename)
    
    if not os.path.exists(filepath):
        print(f"❌ ไม่พบไฟล์: {filename}")
        return False
    
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # ตรวจสอบว่ามี Contact Modal อยู่แล้วหรือไม่
    if 'id="contactModal"' in content:
        print(f"✅ {filename} - มี Contact Modal อยู่แล้ว")
        return True
    
    # หา </body> tag
    if '</body>' not in content:
        print(f"❌ {filename} - ไม่พบ </body> tag")
        return False
    
    # แทรก Contact Modal ก่อน </body>
    content = content.replace('</body>', CONTACT_MODAL_HTML + '\n</body>')
    
    # เพิ่ม JS functions ใน <script> ที่มีอยู่แล้ว หรือสร้างใหม่
    if '<script>' in content and '</script>' in content:
        # หา <script> tag สุดท้าย
        script_pattern = r'(<script>.*?)(</script>)'
        
        def add_functions(match):
            return match.group(1) + JS_FUNCTIONS + '\n    ' + match.group(2)
        
        content = re.sub(script_pattern, add_functions, content, flags=re.DOTALL | re.MULTILINE)
        content = content.replace('</script>', '\n    </script>')
    else:
        # ถ้าไม่มี <script> ให้สร้างใหม่
        script_block = f'\n    <script>{JS_FUNCTIONS}\n    </script>\n'
        content = content.replace('</body>', script_block + '\n</body>')
    
    # แก้ไข toggleFloatingContact() ที่มีอยู่แล้ว (ถ้ามี)
    content = re.sub(
        r'function toggleFloatingContact\(\) \{[^}]+\}',
        '''function toggleFloatingContact() {
            document.getElementById('contactModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }''',
        content
    )
    
    # บันทึกไฟล์
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print(f"✅ {filename} - อัพเดทสำเร็จ")
    return True

def main():
    print("🚀 เริ่มอัพเดทไฟล์ HTML ทั้งหมด...\n")
    
    success_count = 0
    for filename in FILES_TO_UPDATE:
        if update_html_file(filename):
            success_count += 1
    
    print(f"\n{'='*50}")
    print(f"✅ สำเร็จ: {success_count}/{len(FILES_TO_UPDATE)} ไฟล์")
    print(f"{'='*50}")
    
    if success_count == len(FILES_TO_UPDATE):
        print("\n🎉 อัพเดททุกไฟล์สำเร็จ!")
    else:
        print(f"\n⚠️  มีไฟล์ที่อัพเดทไม่สำเร็จ: {len(FILES_TO_UPDATE) - success_count} ไฟล์")

if __name__ == '__main__':
    main()
