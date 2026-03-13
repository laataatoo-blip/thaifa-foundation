<?php
if (defined('THAIFA_FLOATING_CONTACT_WIDGET_RENDERED')) {
    return;
}
define('THAIFA_FLOATING_CONTACT_WIDGET_RENDERED', true);

$lang = function_exists('thaifa_lang') ? thaifa_lang() : 'th';
$isEn = ($lang === 'en');
$txtContact = $isEn ? 'Contact' : 'ติดต่อเรา';
$txtSelect = $isEn ? 'Choose your preferred contact channel' : 'เลือกช่องทางที่คุณต้องการติดต่อ';
$txtClose = $isEn ? 'Close' : 'ปิด';
$txtOpen = $isEn ? 'Open' : 'เปิด';
$txtWorkHour = $isEn ? 'Business Hours: Mon-Fri 9:00-17:00' : 'เวลาทำการ: จันทร์-ศุกร์ 9:00-17:00 น.';
$txtFacebook = 'Facebook';
$txtLine = 'LINE';
$txtYoutube = 'YouTube';
$txtTiktok = 'TikTok';
$txtEmail = $isEn ? 'Email' : 'อีเมล';
?>
<style>
.thaifa-contact-float {
    position: fixed;
    right: 20px;
    bottom: 20px;
    z-index: 80;
    display: flex;
    flex-direction: column;
    gap: 14px;
    align-items: flex-end;
}
.thaifa-contact-list {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 10px;
}
.thaifa-contact-item {
    border: 0;
    text-decoration: none;
    color: #fff;
    width: 56px;
    height: 56px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 20px rgba(35,56,130,0.22);
    transition: width .22s ease, transform .15s ease, box-shadow .2s ease;
    overflow: hidden;
    white-space: nowrap;
    cursor: pointer;
}
.thaifa-contact-item:hover {
    width: 132px;
    transform: translateY(-1px);
}
.thaifa-contact-item .thaifa-contact-label {
    opacity: 0;
    max-width: 0;
    margin-left: 0;
    transition: opacity .2s ease, max-width .2s ease, margin-left .2s ease;
    font-size: 15px;
    font-weight: 500;
}
.thaifa-contact-item:hover .thaifa-contact-label {
    opacity: 1;
    max-width: 90px;
    margin-left: 8px;
}
.thaifa-contact-item svg {
    width: 24px;
    height: 24px;
    flex: 0 0 auto;
}
.thaifa-contact-item.email { background: #ff7a00; }
.thaifa-contact-item.facebook { background: #2563eb; }
.thaifa-contact-item.line { background: #06c755; }
.thaifa-contact-item.youtube { background: #ff0000; }
.thaifa-contact-item.tiktok { background: #000; }
.thaifa-contact-toggle {
    background: #233882;
    width: 86px;
    align-self: flex-end;
}
.thaifa-contact-toggle .thaifa-contact-label {
    opacity: 1;
    max-width: 60px;
    margin-left: 8px;
}
.thaifa-contact-float.is-collapsed .thaifa-contact-list {
    display: none;
}
.thaifa-contact-float.is-collapsed .thaifa-contact-toggle {
    width: 120px;
}
.thaifa-contact-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.55);
    z-index: 90;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 16px;
}
.thaifa-contact-overlay.show { display: flex; }
.thaifa-contact-modal {
    width: min(100%, 430px);
    background: #fff;
    border-radius: 22px;
    box-shadow: 0 20px 55px rgba(10,25,69,.35);
    padding: 24px;
}
.thaifa-contact-modal-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
}
.thaifa-contact-modal-head h3 {
    margin: 0;
    font-size: 34px;
    font-weight: 700;
    color: #233882;
}
.thaifa-contact-modal-close {
    border: 0;
    background: transparent;
    color: #94a3b8;
    width: 32px;
    height: 32px;
    cursor: pointer;
}
.thaifa-contact-modal-desc {
    margin: 0 0 14px;
    color: #64748b;
    font-size: 16px;
}
.thaifa-contact-modal-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.thaifa-contact-modal-link {
    display: grid;
    grid-template-columns: 46px 1fr 18px;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border-radius: 14px;
    background: #f8fafc;
    text-decoration: none;
    color: #233882;
    border: 1px solid #e2e8f0;
}
.thaifa-contact-modal-link:hover { background: #f1f5f9; }
.thaifa-contact-modal-link .name { font-size: 22px; font-weight: 600; line-height: 1.1; }
.thaifa-contact-modal-link .sub { font-size: 14px; color: #64748b; }
.thaifa-contact-modal-dot {
    width: 46px; height: 46px; border-radius: 50%; color: #fff;
    display: inline-flex; align-items: center; justify-content: center;
}
.thaifa-contact-modal-foot {
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px solid #e2e8f0;
    text-align: center;
    font-size: 12px;
    color: #94a3b8;
}
@media (max-width: 640px) {
    .thaifa-contact-float { right: 12px; bottom: 12px; }
    .thaifa-contact-list { gap: 8px; }
    .thaifa-contact-item { width: 52px; height: 52px; }
    .thaifa-contact-item:hover { width: 114px; }
    .thaifa-contact-modal-head h3 { font-size: 28px; }
    .thaifa-contact-modal-link .name { font-size: 18px; }
}
</style>

<div id="thaifaContactFloat" class="thaifa-contact-float">
        <div class="thaifa-contact-list">
            <a class="thaifa-contact-item email" href="mailto:thaifafoundation@gmail.com" title="<?= htmlspecialchars($txtEmail, ENT_QUOTES, 'UTF-8') ?>">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span class="thaifa-contact-label"><?= htmlspecialchars($txtEmail, ENT_QUOTES, 'UTF-8') ?></span>
            </a>
        <a class="thaifa-contact-item facebook" href="https://www.facebook.com/THAIFAFD/" target="_blank" rel="noopener" title="Facebook">
            <svg fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12S0 5.446 0 12.073c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            <span class="thaifa-contact-label"><?= htmlspecialchars($txtFacebook, ENT_QUOTES, 'UTF-8') ?></span>
        </a>
        <a class="thaifa-contact-item line" href="https://line.me/R/ti/p/@519lkcsb" target="_blank" rel="noopener" title="LINE">
            <svg fill="currentColor" viewBox="0 0 24 24"><path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/></svg>
            <span class="thaifa-contact-label"><?= htmlspecialchars($txtLine, ENT_QUOTES, 'UTF-8') ?></span>
        </a>
        <a class="thaifa-contact-item youtube" href="https://www.youtube.com/@THAIFAFoundation" target="_blank" rel="noopener" title="YouTube">
            <svg fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
            <span class="thaifa-contact-label"><?= htmlspecialchars($txtYoutube, ENT_QUOTES, 'UTF-8') ?></span>
        </a>
        <a class="thaifa-contact-item tiktok" href="https://www.tiktok.com/@thaifafoundation" target="_blank" rel="noopener" title="TikTok">
            <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
            <span class="thaifa-contact-label"><?= htmlspecialchars($txtTiktok, ENT_QUOTES, 'UTF-8') ?></span>
        </a>
    </div>
    <button type="button" class="thaifa-contact-item thaifa-contact-toggle" onclick="ThaifaContactWidget.togglePanel()">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        <span class="thaifa-contact-label" id="thaifaContactToggleText"><?= htmlspecialchars($txtClose, ENT_QUOTES, 'UTF-8') ?></span>
    </button>
</div>

<div id="thaifaContactOverlay" class="thaifa-contact-overlay" onclick="ThaifaContactWidget.closeModal(event)">
    <div class="thaifa-contact-modal" onclick="event.stopPropagation()">
        <div class="thaifa-contact-modal-head">
            <h3><?= htmlspecialchars($txtContact, ENT_QUOTES, 'UTF-8') ?></h3>
            <button type="button" class="thaifa-contact-modal-close" onclick="ThaifaContactWidget.closeModal()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <p class="thaifa-contact-modal-desc"><?= htmlspecialchars($txtSelect, ENT_QUOTES, 'UTF-8') ?></p>
        <div class="thaifa-contact-modal-list">
            <a href="mailto:thaifafoundation@gmail.com" class="thaifa-contact-modal-link"><span class="thaifa-contact-modal-dot" style="background:#ff7a00;"><svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></span><span><div class="name"><?= htmlspecialchars($txtEmail, ENT_QUOTES, 'UTF-8') ?></div><div class="sub">thaifafoundation@gmail.com</div></span><span>›</span></a>
            <a href="https://www.facebook.com/THAIFAFD/" target="_blank" rel="noopener" class="thaifa-contact-modal-link"><span class="thaifa-contact-modal-dot" style="background:#1877F2;"><svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12S0 5.446 0 12.073c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></span><span><div class="name">Facebook</div><div class="sub">THAIFA Foundation</div></span><span>›</span></a>
            <a href="https://line.me/R/ti/p/@519lkcsb" target="_blank" rel="noopener" class="thaifa-contact-modal-link"><span class="thaifa-contact-modal-dot" style="background:#06C755;"><svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/></svg></span><span><div class="name">LINE</div><div class="sub">@519lkcsb</div></span><span>›</span></a>
            <a href="https://www.youtube.com/@THAIFAFoundation" target="_blank" rel="noopener" class="thaifa-contact-modal-link"><span class="thaifa-contact-modal-dot" style="background:#FF0000;"><svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg></span><span><div class="name">YouTube</div><div class="sub">THAIFA Foundation</div></span><span>›</span></a>
            <a href="https://www.tiktok.com/@thaifafoundation" target="_blank" rel="noopener" class="thaifa-contact-modal-link"><span class="thaifa-contact-modal-dot" style="background:#000;"><svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg></span><span><div class="name">TikTok</div><div class="sub">@thaifafoundation</div></span><span>›</span></a>
        </div>
        <div class="thaifa-contact-modal-foot"><?= htmlspecialchars($txtWorkHour, ENT_QUOTES, 'UTF-8') ?></div>
    </div>
</div>

<script>
(function () {
    const root = document.getElementById('thaifaContactFloat');
    const overlay = document.getElementById('thaifaContactOverlay');
    const toggleText = document.getElementById('thaifaContactToggleText');
    if (!root || !overlay || !toggleText) return;

    const labels = {
        close: <?= json_encode($txtClose, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        open: <?= json_encode($txtOpen, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
    };

    function setCollapsed(collapsed) {
        root.classList.toggle('is-collapsed', collapsed);
        toggleText.textContent = collapsed ? labels.open : labels.close;
    }

    window.ThaifaContactWidget = {
        togglePanel: function () {
            setCollapsed(!root.classList.contains('is-collapsed'));
        },
        openModal: function () {
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        },
        closeModal: function (event) {
            if (event && event.target && event.target !== overlay) return;
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        }
    };

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            window.ThaifaContactWidget.closeModal();
        }
    });
})();
</script>
