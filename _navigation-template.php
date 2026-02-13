<!-- ========================================== 
     วาง Navigation ด้านบนก่อน main content
     วาง Contact Modal + Floating Button ด้านล่างก่อน </body>
     ========================================== -->

<!-- Contact Modal -->
<div
  id="contactModal"
  class="hidden fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4"
  onclick="if(event.target.id === 'contactModal') closeContactModal()"
>
  <div
    class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 transform transition-all"
    onclick="event.stopPropagation()"
  >
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-2xl text-primary">ติดต่อเรา</h3>
      <button
        type="button"
        onclick="closeContactModal()"
        class="text-foreground/40 hover:text-foreground/80 transition-colors"
        aria-label="Close"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <p class="text-foreground/70 mb-6">เลือกช่องทางที่คุณต้องการติดต่อ</p>

    <!-- Contact Options -->
    <div class="space-y-3">
      <!-- Email -->
      <a
        href="mailto:thaifafoundation@gmail.com"
        class="flex items-center gap-4 p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-all group"
      >
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

      <!-- Facebook -->
      <a
        href="https://www.facebook.com/share/1FdXqqJNXE/"
        target="_blank"
        rel="noopener"
        class="flex items-center gap-4 p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-all group"
      >
        <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform" style="background:#1877F2;">
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

      <!-- LINE -->
      <a
        href="https://line.me/ti/p/~@thaifa"
        target="_blank"
        rel="noopener"
        class="flex items-center gap-4 p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-all group"
      >
        <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform" style="background:#06C755;">
          <span class="text-white font-bold">LINE</span>
        </div>
        <div class="flex-1">
          <div class="text-primary">LINE</div>
          <div class="text-sm text-foreground/60">@thaifa</div>
        </div>
        <svg class="w-5 h-5 text-foreground/40 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
      </a>

      <!-- YouTube -->
      <a
        href="https://www.youtube.com/@THAIFAFoundation"
        target="_blank"
        rel="noopener"
        class="flex items-center gap-4 p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-all group"
      >
        <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform" style="background:#FF0000;">
          <span class="text-white font-bold">YT</span>
        </div>
        <div class="flex-1">
          <div class="text-primary">YouTube</div>
          <div class="text-sm text-foreground/60">THAIFA Foundation</div>
        </div>
        <svg class="w-5 h-5 text-foreground/40 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
      </a>

      <!-- TikTok -->
      <a
        href="https://www.tiktok.com/@thaifafoundation"
        target="_blank"
        rel="noopener"
        class="flex items-center gap-4 p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-all group"
      >
        <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform" style="background:#000;">
          <span class="text-white font-bold">TT</span>
        </div>
        <div class="flex-1">
          <div class="text-primary">TikTok</div>
          <div class="text-sm text-foreground/60">@thaifafoundation</div>
        </div>
        <svg class="w-5 h-5 text-foreground/40 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7-7 7"/>
        </svg>
      </a>
    </div>

    <!-- Footer Note -->
    <div class="mt-6 pt-6 border-t border-border">
      <p class="text-xs text-center text-foreground/50">
        เวลาทำการ: จันทร์-ศุกร์ 9:00-17:00 น.
      </p>
    </div>
  </div>
</div>

<!-- ========================================== 
     JAVASCRIPT FUNCTIONS
     (ควรอยู่ก่อน </body> ของหน้า)
     ========================================== -->
<script>
  function toggleMobileMenu() {
    alert('Mobile menu - Static HTML');
  }

  // Close modal on ESC key
</script>
