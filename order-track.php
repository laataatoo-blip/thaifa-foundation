<?php
session_start();
include_once(__DIR__ . '/backend/classes/ShopManagement.class.php');
include_once(__DIR__ . '/backend/helpers/i18n.php');
thaifa_i18n_buffer_start();
include_once(__DIR__ . '/backend/helpers/cart_count.php');

$shop = new ShopManagement();
$cartCount = thaifaCartCount();

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function baht($v) { return number_format((float)$v, 2); }

$orderNo = trim((string)($_GET['order_no'] ?? $_POST['order_no'] ?? ''));
$phone = trim((string)($_POST['phone'] ?? ''));
$data = null;
$error = '';

if ($orderNo !== '') {
    $data = $shop->getOrderByNo($orderNo);
    if (!$data) {
        $error = 'ไม่พบคำสั่งซื้อ กรุณาตรวจสอบหมายเลขอีกครั้ง';
    } else {
        $realPhone = trim((string)($data['order']['customer_phone'] ?? ''));
        if ($phone !== '' && $realPhone !== '' && $phone !== $realPhone) {
            $data = null;
            $error = 'เบอร์โทรไม่ตรงกับคำสั่งซื้อ';
        }
    }
}

$steps = [
    ['pending', 'คำสั่งซื้อ'],
    ['confirmed', 'ยืนยัน'],
    ['packing', 'แพ็กสินค้า'],
    ['shipping', 'ขนส่ง'],
    ['out_for_delivery', 'กำลังนำส่ง'],
    ['delivered', 'สำเร็จ'],
];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(thaifa_lang(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ติดตามสินค้า</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body,*{font-family:'Prompt',sans-serif}</style>
</head>
<body class="bg-slate-50 text-slate-800">
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white shadow-sm">
        <div class="bg-[#d9e7ef66] border-b border-[#e2e8f0]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-10 text-sm">
                    <a href="mailto:thaifafoundation@gmail.com" class="flex items-center gap-2 text-[#303a56cc] hover:text-[#233882] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>thaifafoundation@gmail.com</span>
                    </a>
                    <div class="flex items-center gap-4">
                        <a href="cart.php" class="relative text-[#303a56cc] hover:text-[#233882] transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="absolute -top-2 -right-2 bg-[#d51d3c] text-white text-xs rounded-full w-4 h-4 flex items-center justify-center"><?= (int)$cartCount ?></span>
                        </a>
                        <div class="flex items-center gap-1">
                            <a href="<?= h(thaifa_lang_url('th')) ?>" class="text-xs px-2 py-0.5 rounded <?= thaifa_lang()==='th' ? 'bg-[#233882] text-white' : 'text-[#303a56cc] hover:text-[#233882]' ?>">TH</a>
                            <a href="<?= h(thaifa_lang_url('en')) ?>" class="text-xs px-2 py-0.5 rounded <?= thaifa_lang()==='en' ? 'bg-[#233882] text-white' : 'text-[#303a56cc] hover:text-[#233882]' ?>">EN</a>
                        </div>
                        <div class="flex items-center gap-2 pl-4 border-l border-[#e2e8f0]">
                            <a href="login.php" class="text-[#303a56cc] hover:text-[#233882] transition-colors"><?= h(thaifa_t('login')) ?></a>
                            <span class="text-[#303a5666]">/</span>
                            <a href="register.php" class="text-[#303a56cc] hover:text-[#233882] transition-colors"><?= h(thaifa_t('register')) ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white border-b border-[#e2e8f0]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                <a href="index.php"><img src="assets/images/Logo.png" alt="THAIFA" class="h-16 w-auto"></a>
                <div class="hidden lg:flex items-center gap-1 text-sm">
                    <a href="index.php" class="px-4 py-2 rounded-md hover:bg-sky-100"><?= h(thaifa_t('home')) ?></a>
                    <a href="about.php" class="px-4 py-2 rounded-md hover:bg-sky-100"><?= h(thaifa_t('about')) ?></a>
                    <a href="calendar.php" class="px-4 py-2 rounded-md hover:bg-sky-100"><?= h(thaifa_t('calendar')) ?></a>
                    <a href="shop.php" class="px-4 py-2 rounded-md hover:bg-sky-100"><?= h(thaifa_t('shop')) ?></a>
                    <a href="donate.php" class="px-4 py-2 rounded-md hover:bg-sky-100"><?= h(thaifa_t('donate')) ?></a>
                    <a href="volunteer.php" class="px-4 py-2 rounded-md hover:bg-sky-100"><?= h(thaifa_t('volunteer')) ?></a>
                    <a href="stories.php" class="px-4 py-2 rounded-md hover:bg-sky-100"><?= h(thaifa_t('stories')) ?></a>
                    <a href="contact.php" class="px-4 py-2 rounded-md hover:bg-sky-100"><?= h(thaifa_t('contact')) ?></a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 py-8 pt-[140px]">
        <h1 class="text-3xl font-semibold text-[#233882] mb-4">ติดตามสถานะสินค้า</h1>

        <?php if (!empty($_GET['created'])): ?>
            <div class="mb-4 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-green-700">สร้างคำสั่งซื้อเรียบร้อย ระบบได้บันทึกออเดอร์แล้ว</div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl border p-4 mb-6">
            <form method="post" class="grid md:grid-cols-3 gap-3">
                <div>
                    <label class="text-sm">หมายเลขคำสั่งซื้อ</label>
                    <input type="text" name="order_no" value="<?= h($orderNo) ?>" class="w-full border rounded px-3 py-2 mt-1" placeholder="เช่น TF260305ABC123" required>
                </div>
                <div>
                    <label class="text-sm">เบอร์โทร (ถ้ามี)</label>
                    <input type="text" name="phone" value="<?= h($phone) ?>" class="w-full border rounded px-3 py-2 mt-1" placeholder="เพื่อยืนยันเจ้าของคำสั่งซื้อ">
                </div>
                <div class="flex items-end">
                    <button class="w-full px-4 py-2 rounded bg-[#233882] text-white hover:bg-[#1b2f72]">ดูสถานะ</button>
                </div>
            </form>
        </div>

        <?php if ($error): ?>
            <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-red-700 mb-6"><?= h($error) ?></div>
        <?php endif; ?>

        <?php if ($data): ?>
            <?php $order = $data['order']; $currentStep = ShopManagement::progressStep($order['status']); ?>

            <div class="bg-white rounded-2xl border p-5 mb-6">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div>
                        <div class="text-sm text-slate-500">Order No</div>
                        <div class="text-xl font-semibold text-[#233882]"><?= h($order['order_no']) ?></div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">สถานะล่าสุด</div>
                        <span class="inline-flex px-3 py-1 rounded-full text-sm bg-blue-50 text-blue-700 border border-blue-200">
                            <?= h(ShopManagement::statusLabel($order['status'])) ?>
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
                    <?php foreach ($steps as $idx => $s): ?>
                        <?php $active = ($idx + 1) <= $currentStep && $order['status'] !== 'cancelled'; ?>
                        <div class="rounded-xl border p-3 text-center <?= $active ? 'bg-blue-50 border-blue-300 text-blue-700' : 'bg-slate-50 text-slate-400' ?>">
                            <div class="text-xs mb-1">ขั้นตอน <?= $idx + 1 ?></div>
                            <div class="text-sm font-medium"><?= h($s[1]) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($order['tracking_code'])): ?>
                    <div class="mt-4 text-sm"><strong>Tracking:</strong> <?= h($order['tracking_code']) ?></div>
                <?php endif; ?>
            </div>

            <div class="grid lg:grid-cols-5 gap-6">
                <div class="lg:col-span-2 bg-white rounded-2xl border p-4">
                    <h2 class="font-semibold text-[#233882] mb-3">สินค้าในคำสั่งซื้อ</h2>
                    <div class="space-y-2">
                        <?php foreach ($data['items'] as $it): ?>
                            <div class="border rounded-lg p-3">
                                <div class="font-medium"><?= h($it['product_name']) ?></div>
                                <div class="text-sm text-slate-500">จำนวน <?= (int)$it['qty'] ?> x <?= baht($it['product_price']) ?></div>
                                <div class="text-rose-600 font-semibold text-sm">รวม <?= baht($it['line_total']) ?> บาท</div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="border-t mt-3 pt-3 font-semibold">ยอดรวม <?= baht($order['total_amount']) ?> บาท</div>
                </div>

                <div class="lg:col-span-3 bg-white rounded-2xl border p-4">
                    <h2 class="font-semibold text-[#233882] mb-3">ไทม์ไลน์สถานะ</h2>
                    <div class="space-y-3">
                        <?php foreach ($data['logs'] as $log): ?>
                            <div class="border-l-4 border-blue-300 pl-3 py-1">
                                <div class="font-medium"><?= h($log['title']) ?></div>
                                <div class="text-xs text-slate-500"><?= h($log['created_at']) ?><?= $log['location'] ? ' • ' . h($log['location']) : '' ?></div>
                                <?php if (!empty($log['description'])): ?>
                                    <div class="text-sm text-slate-700 mt-1"><?= nl2br(h($log['description'])) ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($data['logs'])): ?>
                            <div class="text-slate-500">ยังไม่มีประวัติสถานะ</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>
<?php include __DIR__ . '/backend/helpers/floating_contact_widget.php'; ?>
</body>
</html>
