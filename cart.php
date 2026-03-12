<?php
session_start();
include_once(__DIR__ . '/backend/classes/ShopManagement.class.php');
include_once(__DIR__ . '/backend/helpers/i18n.php');
thaifa_i18n_buffer_start();
include_once(__DIR__ . '/backend/helpers/member_auth.php');

$shop = new ShopManagement();
$auth = thaifaMemberAuth();
$member = $auth->currentMember();

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function baht($v) { return number_format((float)$v, 2); }

function parseSelectedIds($raw)
{
    $ids = [];
    if (is_array($raw)) {
        foreach ($raw as $id) {
            $id = (int)$id;
            if ($id > 0) {
                $ids[$id] = $id;
            }
        }
    } else {
        $text = trim((string)$raw);
        if ($text !== '') {
            foreach (explode(',', $text) as $id) {
                $id = (int)trim($id);
                if ($id > 0) {
                    $ids[$id] = $id;
                }
            }
        }
    }
    return array_values($ids);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_qty') {
        $shop->updateCartQty((int)($_POST['product_id'] ?? 0), (int)($_POST['qty'] ?? 1));
        header('Location: cart.php');
        exit;
    }

    if ($action === 'adjust_qty') {
        $pid = (int)($_POST['product_id'] ?? 0);
        $current = max(1, (int)($_POST['current_qty'] ?? 1));
        $mode = (string)($_POST['mode'] ?? 'inc');
        $next = $mode === 'dec' ? max(1, $current - 1) : min(99, $current + 1);
        $shop->updateCartQty($pid, $next);
        header('Location: cart.php');
        exit;
    }

    if ($action === 'remove') {
        $shop->updateCartQty((int)($_POST['product_id'] ?? 0), 0);
        header('Location: cart.php');
        exit;
    }

    if ($action === 'remove_selected') {
        $selected = parseSelectedIds($_POST['selected_ids'] ?? []);
        foreach ($selected as $pid) {
            $shop->updateCartQty((int)$pid, 0);
        }
        header('Location: cart.php');
        exit;
    }

    if ($action === 'checkout') {
        if (!$member) {
            header('Location: login.php?required=1&next=' . urlencode('cart.php'));
            exit;
        }
        $selected = parseSelectedIds($_POST['selected_ids'] ?? []);
        if (empty($selected)) {
            $error = 'กรุณาเลือกสินค้าอย่างน้อย 1 รายการ';
        } else {
            try {
                $fullName = trim((string)($member['first_name'] ?? '') . ' ' . (string)($member['last_name'] ?? ''));
                $created = $shop->createOrder([
                    'member_id' => (int)($member['id'] ?? 0),
                    'customer_name' => $_POST['customer_name'] ?? $fullName,
                    'customer_phone' => $_POST['customer_phone'] ?? ($member['phone'] ?? ''),
                    'customer_address' => $_POST['customer_address'] ?? ($member['address'] ?? ''),
                    'note' => $_POST['note'] ?? '',
                ], $selected);

                header('Location: order-track.php?order_no=' . urlencode($created['order_no']) . '&created=1');
                exit;
            } catch (Throwable $e) {
                $error = $e->getMessage();
            }
        }
    }
}

$items = $shop->getCartItems();
$subtotal = $shop->cartSubtotal();
$totalQty = 0;
foreach ($items as $it) {
    $totalQty += (int)$it['qty'];
}

$memberFullName = '';
$memberPhone = '';
$memberAddress = '';
if ($member) {
    $memberFullName = trim((string)($member['first_name'] ?? '') . ' ' . (string)($member['last_name'] ?? ''));
    $memberPhone = trim((string)($member['phone'] ?? ''));
    $memberAddress = trim((string)($member['address'] ?? ''));
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(thaifa_lang(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตะกร้าสินค้า - THAIFA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ci-primary: #233882;
            --ci-secondary: #d9e7ef;
            --ci-text: #303a56;
            --ci-accent: #d51d3c;
        }
        body, * { font-family: 'Prompt', sans-serif; }
        body { background: #f5f7fb; color: var(--ci-text); }
        .cart-card { border: 1px solid #dfe6f1; border-radius: 16px; background: #fff; }
        .qty-btn {
            width: 34px; height: 34px; border-radius: 999px;
            border: 1px solid #d7e2f1; background: #fff; color: #415379;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .qty-btn:hover { background: #f2f6ff; }
        .sticky-summary { position: sticky; top: 102px; }
        .checkout-modal {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .45);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 80;
            padding: 16px;
        }
        .checkout-modal.show { display: flex; }
        .checkout-sheet {
            width: min(560px, 100%);
            border-radius: 18px;
            background: #fff;
            border: 1px solid #dbe3f0;
            box-shadow: 0 20px 50px rgba(15, 23, 42, .2);
            max-height: calc(100vh - 32px);
            overflow: auto;
        }
        .quick-contact {
            position: fixed;
            right: 18px;
            bottom: 18px;
            z-index: 60;
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: flex-end;
        }
        .quick-contact-btn {
            width: 56px;
            height: 56px;
            border-radius: 999px;
            color: #fff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 22px rgba(35, 56, 130, .20);
            transition: width .22s ease, padding .22s ease, transform .15s ease;
            overflow: hidden;
            white-space: nowrap;
        }
        .quick-contact-btn:hover {
            width: 168px;
            padding: 0 16px 0 12px;
            justify-content: flex-start;
            transform: translateY(-1px);
        }
        .quick-contact-icon {
            width: 22px;
            min-width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .quick-contact-label {
            margin-left: 10px;
            opacity: 0;
            transform: translateX(6px);
            transition: opacity .2s ease, transform .2s ease;
            font-size: 20px;
            font-weight: 500;
            letter-spacing: .1px;
        }
        .quick-contact-btn:hover .quick-contact-label {
            opacity: 1;
            transform: translateX(0);
        }
        .quick-contact-toggle {
            border: 0;
            cursor: pointer;
            background: #233882;
            width: 108px;
            height: 50px;
            border-radius: 999px;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 8px 22px rgba(35, 56, 130, .25);
        }
        .quick-contact.is-collapsed .quick-contact-btn {
            display: none;
        }
    </style>
</head>
<body>

    <nav class="fixed top-0 left-0 right-0 z-50 bg-white shadow-sm">
        <div class="bg-[#d9e7ef66] border-b border-[#e2e8f0]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-10 text-sm">
                    <div class="hidden md:flex items-center gap-6 text-[#303a56cc]">
                        <a href="mailto:thaifafoundation@gmail.com" class="flex items-center gap-2 hover:text-[#233882] transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            <span>thaifafoundation@gmail.com</span>
                        </a>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="cart.php" class="relative text-[#303a56cc] hover:text-[#233882] transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            <span class="absolute -top-2 -right-2 bg-[#d51d3c] text-white text-xs rounded-full w-4 h-4 flex items-center justify-center"><?= (int)$totalQty ?></span>
                        </a>
                        <div class="flex items-center gap-1">
                            <a href="<?= h(thaifa_lang_url('th')) ?>" class="text-xs px-2 py-0.5 rounded <?= thaifa_lang()==='th' ? 'bg-[#233882] text-white' : 'text-[#303a56cc] hover:text-[#233882]' ?>">TH</a>
                            <a href="<?= h(thaifa_lang_url('en')) ?>" class="text-xs px-2 py-0.5 rounded <?= thaifa_lang()==='en' ? 'bg-[#233882] text-white' : 'text-[#303a56cc] hover:text-[#233882]' ?>">EN</a>
                        </div>
                        <div class="flex items-center gap-2 pl-4 border-l border-[#e2e8f0]">
                            <?php if ($member): ?>
                                <span class="text-[#303a56cc]">สวัสดี <?= h(trim(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? ''))) ?></span>
                                <span class="text-[#303a5666]">/</span>
                                <a href="logout.php?next=cart.php" class="text-[#303a56cc] hover:text-[#233882] transition-colors">ออกจากระบบ</a>
                            <?php else: ?>
                                <a href="login.php?next=cart.php" class="text-[#303a56cc] hover:text-[#233882] transition-colors"><?= h(thaifa_t('login')) ?></a>
                                <span class="text-[#303a5666]">/</span>
                                <a href="register.php" class="text-[#303a56cc] hover:text-[#233882] transition-colors"><?= h(thaifa_t('register')) ?></a>
                            <?php endif; ?>
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
                    <a href="shop.php" class="px-4 py-2 rounded-md bg-sky-100 text-[#315d9f]"><?= h(thaifa_t('shop')) ?></a>
                    <a href="donate.php" class="px-4 py-2 rounded-md hover:bg-sky-100"><?= h(thaifa_t('donate')) ?></a>
                    <a href="volunteer.php" class="px-4 py-2 rounded-md hover:bg-sky-100"><?= h(thaifa_t('volunteer')) ?></a>
                    <a href="stories.php" class="px-4 py-2 rounded-md hover:bg-sky-100"><?= h(thaifa_t('stories')) ?></a>
                    <a href="contact.php" class="px-4 py-2 rounded-md hover:bg-sky-100"><?= h(thaifa_t('contact')) ?></a>
                </div>
            </div>
        </div>
    </nav>

    <main class="pt-[124px] md:pt-[120px]">
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-4xl md:text-5xl text-[#233882] font-semibold">ตะกร้าสินค้า</h1>
                <a href="shop.php" class="text-[#233882] hover:underline">เลือกซื้อสินค้าเพิ่ม</a>
            </div>

            <?php if ($error): ?>
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-red-700"><?= h($error) ?></div>
            <?php endif; ?>

            <?php if (empty($items)): ?>
                <div class="cart-card p-16 text-center max-w-3xl mx-auto">
                    <svg class="w-20 h-20 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1 5h12M7 13L5.4 5M17 18a2 2 0 100 4 2 2 0 000-4zm-8 0a2 2 0 100 4 2 2 0 000-4"/></svg>
                    <h3 class="mt-4 text-3xl font-semibold text-[#233882]">ตะกร้าสินค้าว่างเปล่า</h3>
                    <p class="mt-2 text-slate-500">เริ่มช้อปปิ้งเพื่อสนับสนุนมูลนิธิกันเถอะ</p>
                    <a href="shop.php" class="inline-flex mt-5 px-5 py-2.5 rounded-full bg-[#233882] text-white">กลับไปร้านค้า</a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                    <div class="lg:col-span-8">
                        <div class="cart-card overflow-hidden">
                            <div class="grid grid-cols-12 gap-2 px-4 py-3 border-b bg-[#f8fbff] text-[#56627e] text-sm">
                                <div class="col-span-6 flex items-center gap-2">
                                    <input id="checkAllTop" type="checkbox" class="w-4 h-4 rounded border-slate-300 accent-[#233882]" checked>
                                    <label for="checkAllTop" class="cursor-pointer">สินค้า</label>
                                </div>
                                <div class="col-span-2 text-center">ราคา</div>
                                <div class="col-span-2 text-center">จำนวน</div>
                                <div class="col-span-2 text-right">รวม</div>
                            </div>

                            <?php foreach ($items as $it): ?>
                                <?php $pid = (int)$it['product_id']; ?>
                                <div class="grid grid-cols-12 gap-2 px-4 py-4 border-b last:border-b-0 items-center cart-row" data-product-id="<?= $pid ?>" data-line-total="<?= (float)$it['line_total'] ?>" data-qty="<?= (int)$it['qty'] ?>">
                                    <div class="col-span-12 md:col-span-6 flex items-center gap-3">
                                        <input type="checkbox" class="item-check w-4 h-4 rounded border-slate-300 accent-[#233882]" value="<?= $pid ?>" checked>
                                        <img src="<?= h($it['cover_image'] ?: 'https://via.placeholder.com/120x120?text=Product') ?>" class="w-20 h-20 rounded-xl object-cover border" alt="<?= h($it['name']) ?>">
                                        <div class="min-w-0">
                                            <div class="font-semibold line-clamp-2 text-[#1f2a44]"><?= h($it['name']) ?></div>
                                            <div class="text-xs text-slate-500 mt-1">คงเหลือ <?= (int)$it['stock_qty'] ?> ชิ้น</div>
                                        </div>
                                    </div>

                                    <div class="col-span-4 md:col-span-2 text-left md:text-center text-[#3d4a69]">฿<?= baht($it['price']) ?></div>

                                    <div class="col-span-4 md:col-span-2">
                                        <div class="flex items-center md:justify-center gap-1">
                                            <form method="post">
                                                <input type="hidden" name="action" value="adjust_qty">
                                                <input type="hidden" name="product_id" value="<?= $pid ?>">
                                                <input type="hidden" name="current_qty" value="<?= (int)$it['qty'] ?>">
                                                <input type="hidden" name="mode" value="dec">
                                                <button class="qty-btn" type="submit">-</button>
                                            </form>
                                            <span class="w-9 text-center text-sm text-[#303a56] font-medium"><?= (int)$it['qty'] ?></span>
                                            <form method="post">
                                                <input type="hidden" name="action" value="adjust_qty">
                                                <input type="hidden" name="product_id" value="<?= $pid ?>">
                                                <input type="hidden" name="current_qty" value="<?= (int)$it['qty'] ?>">
                                                <input type="hidden" name="mode" value="inc">
                                                <button class="qty-btn" type="submit">+</button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="col-span-4 md:col-span-2 text-right">
                                        <div class="text-[#d51d3c] font-semibold">฿<?= baht($it['line_total']) ?></div>
                                        <form method="post" class="mt-1">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="hidden" name="product_id" value="<?= $pid ?>">
                                            <button type="submit" class="text-xs text-slate-500 hover:text-red-500">ลบ</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="lg:col-span-4">
                        <div class="cart-card p-5 sticky-summary">
                            <h3 class="text-2xl font-semibold text-[#233882]">สรุปคำสั่งซื้อ</h3>
                            <div class="border-t mt-3 pt-3 space-y-2 text-[#56627e]">
                                <div class="flex justify-between"><span>สินค้าที่เลือก</span><span id="selectedQtyText">0 ชิ้น</span></div>
                                <div class="flex justify-between"><span>ราคาสินค้า</span><span id="selectedSubtotalText">฿0.00</span></div>
                                <div class="flex justify-between"><span>ค่าจัดส่ง</span><span class="text-green-600">ฟรี</span></div>
                            </div>

                            <div class="border-t mt-4 pt-4 flex justify-between items-end">
                                <span class="text-[#56627e]">ยอดรวมทั้งสิ้น</span>
                                <span id="selectedTotalText" class="text-4xl leading-none font-bold text-[#d51d3c]">฿0.00</span>
                            </div>

                            <form id="checkoutForm" method="post" class="mt-5 space-y-3">
                                <input type="hidden" name="action" value="checkout">
                                <input id="selectedIdsInput" type="hidden" name="selected_ids" value="">
                                <?php if ($member): ?>
                                    <button id="openCheckoutModal" type="button" class="w-full h-12 rounded-xl bg-[#d51d3c] text-white font-semibold">ชำระเงิน (<span id="selectedItemCountBtn">0</span>)</button>
                                <?php else: ?>
                                    <a href="login.php?required=1&next=cart.php" class="block text-center w-full h-12 rounded-xl bg-[#d51d3c] text-white font-semibold leading-[48px]">เข้าสู่ระบบก่อนชำระเงิน</a>
                                    <span id="selectedItemCountBtn" class="hidden">0</span>
                                <?php endif; ?>
                                <a href="shop.php" class="block text-center w-full h-11 rounded-xl bg-[#f1f5fb] text-[#233882] font-medium leading-[44px]">ซื้อสินค้าต่อ</a>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="fixed bottom-0 left-0 right-0 z-40 bg-white border-t shadow-[0_-8px_24px_rgba(0,0,0,0.06)]">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-wrap gap-3 items-center justify-between">
                        <div class="flex items-center gap-3 text-sm text-[#56627e]">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input id="checkAllBottom" type="checkbox" class="w-4 h-4 rounded border-slate-300 accent-[#233882]" checked>
                                <span>เลือกทั้งหมด</span>
                            </label>
                            <form id="removeSelectedForm" method="post" class="inline">
                                <input type="hidden" name="action" value="remove_selected">
                                <input id="removeSelectedIdsInput" type="hidden" name="selected_ids" value="">
                                <button type="submit" class="text-[#d51d3c] hover:underline">ลบรายการที่เลือก</button>
                            </form>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <div class="text-xs text-[#7a859d]">รวม (<span id="selectedQtyFooter">0</span> ชิ้น)</div>
                                <div id="selectedTotalFooter" class="text-2xl font-semibold text-[#d51d3c]">฿0.00</div>
                            </div>
                            <?php if ($member): ?>
                                <button id="checkoutTrigger" type="button" class="h-11 px-6 rounded-xl bg-[#d51d3c] text-white font-semibold">ชำระเงิน</button>
                            <?php else: ?>
                                <a href="login.php?required=1&next=cart.php" class="h-11 px-6 rounded-xl bg-[#d51d3c] text-white font-semibold leading-[44px]">เข้าสู่ระบบก่อนชำระเงิน</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <div id="checkoutModal" class="checkout-modal" aria-hidden="true">
        <div class="checkout-sheet">
            <div class="p-5 border-b border-[#e4ebf5] flex items-center justify-between">
                <h3 class="text-2xl font-semibold text-[#233882]">ข้อมูลจัดส่ง</h3>
                <button id="closeCheckoutModal" type="button" class="w-9 h-9 rounded-full border border-[#dbe3f0] text-[#60708f]">✕</button>
            </div>
            <div class="p-5 space-y-3">
                <p class="text-sm text-[#60708f]">กรอกข้อมูลให้ครบก่อนกดยืนยันคำสั่งซื้อ</p>
                <div>
                    <label class="text-sm text-[#56627e]">ชื่อผู้รับ</label>
                    <input id="modalCustomerName" value="<?= h($memberFullName) ?>" required class="mt-1 w-full border border-[#dbe3f0] rounded-xl px-3 py-2.5">
                </div>
                <div>
                    <label class="text-sm text-[#56627e]">เบอร์โทร</label>
                    <input id="modalCustomerPhone" value="<?= h($memberPhone) ?>" required class="mt-1 w-full border border-[#dbe3f0] rounded-xl px-3 py-2.5">
                </div>
                <div>
                    <label class="text-sm text-[#56627e]">ที่อยู่จัดส่ง</label>
                    <textarea id="modalCustomerAddress" required rows="3" class="mt-1 w-full border border-[#dbe3f0] rounded-xl px-3 py-2.5"><?= h($memberAddress) ?></textarea>
                </div>
                <div>
                    <label class="text-sm text-[#56627e]">หมายเหตุ</label>
                    <textarea id="modalNote" rows="2" class="mt-1 w-full border border-[#dbe3f0] rounded-xl px-3 py-2.5" placeholder="เช่น เวลาสะดวกรับสินค้า"></textarea>
                </div>

                <input type="hidden" name="customer_name" id="hiddenCustomerName" form="checkoutForm">
                <input type="hidden" name="customer_phone" id="hiddenCustomerPhone" form="checkoutForm">
                <input type="hidden" name="customer_address" id="hiddenCustomerAddress" form="checkoutForm">
                <input type="hidden" name="note" id="hiddenNote" form="checkoutForm">

                <div class="pt-2 grid grid-cols-2 gap-2">
                    <button id="confirmCheckoutBtn" type="button" class="h-11 rounded-xl bg-[#d51d3c] text-white font-semibold">ยืนยันสั่งซื้อ</button>
                    <button id="cancelCheckoutBtn" type="button" class="h-11 rounded-xl bg-[#f1f5fb] text-[#233882] font-semibold">ยกเลิก</button>
                </div>
            </div>
        </div>
    </div>

    <div id="quickContact" class="quick-contact">
        <a class="quick-contact-btn" style="background:#ff7a00;" href="mailto:thaifafoundation@gmail.com" title="อีเมล">
            <span class="quick-contact-icon">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 5h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z"/>
                    <path d="M22 7l-10 7L2 7"/>
                </svg>
            </span>
            <span class="quick-contact-label">Email</span>
        </a>
        <a class="quick-contact-btn" style="background:#2454e6;" href="https://www.facebook.com/share/1FdXqqJNXE/" target="_blank" rel="noopener" title="Facebook">
            <span class="quick-contact-icon">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                    <path d="M22 12a10 10 0 1 0-11.6 9.87v-6.99H7.9V12h2.5V9.8c0-2.48 1.48-3.85 3.74-3.85 1.08 0 2.2.19 2.2.19v2.42h-1.24c-1.23 0-1.61.76-1.61 1.54V12h2.73l-.44 2.88h-2.29v6.99A10 10 0 0 0 22 12Z"/>
                </svg>
            </span>
            <span class="quick-contact-label">Facebook</span>
        </a>
        <a class="quick-contact-btn" style="background:#06c755;" href="https://line.me/R/ti/p/@519lkcsb" target="_blank" rel="noopener" title="LINE">
            <span class="quick-contact-icon">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                    <path d="M20.4 10.1c0-4.3-3.8-7.8-8.4-7.8S3.6 5.8 3.6 10.1c0 3.8 3 7 7.1 7.6.3.1.7.2.8.5.1.2.1.6.1.9l-.1.8c0 .2-.2 1 .9.5 1.1-.5 5.8-3.4 7.9-5.8 1.4-1.5 2.1-2.9 2.1-4.5zM9 12.7H7.3a.5.5 0 0 1-.5-.5V8.3c0-.3.2-.5.5-.5s.5.2.5.5v3.4H9c.3 0 .5.2.5.5s-.2.5-.5.5zm2.1-.5a.5.5 0 1 1-1 0V8.3a.5.5 0 1 1 1 0v3.9zm4.5 0a.5.5 0 0 1-.9.3l-2.1-2.8v2.5a.5.5 0 1 1-1 0V8.3a.5.5 0 0 1 .9-.3l2.1 2.8V8.3a.5.5 0 1 1 1 0v3.9zm2.1-2.9c.3 0 .5.2.5.5s-.2.5-.5.5h-1.3v1h1.3c.3 0 .5.2.5.5s-.2.5-.5.5h-1.8a.5.5 0 0 1-.5-.5V8.3c0-.3.2-.5.5-.5h1.8c.3 0 .5.2.5.5s-.2.5-.5.5h-1.3v1h1.3z"/>
                </svg>
            </span>
            <span class="quick-contact-label">LINE</span>
        </a>
        <a class="quick-contact-btn" style="background:#ef0000;" href="https://www.youtube.com/@THAIFAFoundation" target="_blank" rel="noopener" title="YouTube">
            <span class="quick-contact-icon">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                    <path d="M23.5 7.1a3 3 0 0 0-2.1-2.1C19.6 4.5 12 4.5 12 4.5s-7.6 0-9.4.5A3 3 0 0 0 .5 7.1 31 31 0 0 0 0 12a31 31 0 0 0 .5 4.9 3 3 0 0 0 2.1 2.1c1.8.5 9.4.5 9.4.5s7.6 0 9.4-.5a3 3 0 0 0 2.1-2.1A31 31 0 0 0 24 12a31 31 0 0 0-.5-4.9ZM9.7 15.1V8.9l5.5 3.1-5.5 3.1z"/>
                </svg>
            </span>
            <span class="quick-contact-label">YouTube</span>
        </a>
        <a class="quick-contact-btn" style="background:#000;" href="https://www.tiktok.com/@thaifafoundation" target="_blank" rel="noopener" title="TikTok">
            <span class="quick-contact-icon">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                    <path d="M15.5 2h3.1c.2 1.8 1.2 3.4 2.8 4.2v3.2a7.6 7.6 0 0 1-2.8-.9v6.1c0 3.6-2.9 6.4-6.4 6.4S5.8 18.2 5.8 14.7s2.9-6.4 6.4-6.4c.3 0 .7 0 1 .1v3.2a3.3 3.3 0 0 0-1-.2c-1.8 0-3.2 1.4-3.2 3.2s1.4 3.2 3.2 3.2 3.2-1.4 3.2-3.2V2z"/>
                </svg>
            </span>
            <span class="quick-contact-label">TikTok</span>
        </a>
        <button id="quickContactToggle" class="quick-contact-toggle" type="button">
            <span style="font-size:20px;line-height:1;">×</span><span>ปิด</span>
        </button>
    </div>

    <script>
        (function () {
            const rows = Array.from(document.querySelectorAll('.cart-row'));
            if (!rows.length) return;

            const checkAllTop = document.getElementById('checkAllTop');
            const checkAllBottom = document.getElementById('checkAllBottom');
            const itemChecks = Array.from(document.querySelectorAll('.item-check'));

            const selectedQtyText = document.getElementById('selectedQtyText');
            const selectedSubtotalText = document.getElementById('selectedSubtotalText');
            const selectedTotalText = document.getElementById('selectedTotalText');
            const selectedItemCountBtn = document.getElementById('selectedItemCountBtn');
            const selectedQtyFooter = document.getElementById('selectedQtyFooter');
            const selectedTotalFooter = document.getElementById('selectedTotalFooter');

            const selectedIdsInput = document.getElementById('selectedIdsInput');
            const removeSelectedIdsInput = document.getElementById('removeSelectedIdsInput');
            const removeSelectedForm = document.getElementById('removeSelectedForm');
            const checkoutForm = document.getElementById('checkoutForm');
            const checkoutTrigger = document.getElementById('checkoutTrigger');
            const openCheckoutModalBtn = document.getElementById('openCheckoutModal');
            const checkoutModal = document.getElementById('checkoutModal');
            const closeCheckoutModalBtn = document.getElementById('closeCheckoutModal');
            const cancelCheckoutBtn = document.getElementById('cancelCheckoutBtn');
            const confirmCheckoutBtn = document.getElementById('confirmCheckoutBtn');

            const modalCustomerName = document.getElementById('modalCustomerName');
            const modalCustomerPhone = document.getElementById('modalCustomerPhone');
            const modalCustomerAddress = document.getElementById('modalCustomerAddress');
            const modalNote = document.getElementById('modalNote');

            const hiddenCustomerName = document.getElementById('hiddenCustomerName');
            const hiddenCustomerPhone = document.getElementById('hiddenCustomerPhone');
            const hiddenCustomerAddress = document.getElementById('hiddenCustomerAddress');
            const hiddenNote = document.getElementById('hiddenNote');

            function formatBaht(n) {
                return '฿' + Number(n).toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            function selectedIds() {
                return itemChecks.filter(c => c.checked).map(c => Number(c.value));
            }

            function updateSummary() {
                const checked = new Set(selectedIds());
                let qty = 0;
                let total = 0;

                rows.forEach(row => {
                    const pid = Number(row.dataset.productId || 0);
                    if (!checked.has(pid)) return;
                    qty += Number(row.dataset.qty || 0);
                    total += Number(row.dataset.lineTotal || 0);
                });

                const idsCsv = Array.from(checked).join(',');
                selectedIdsInput.value = idsCsv;
                removeSelectedIdsInput.value = idsCsv;

                selectedQtyText.textContent = qty + ' ชิ้น';
                selectedSubtotalText.textContent = formatBaht(total);
                selectedTotalText.textContent = formatBaht(total);
                selectedItemCountBtn.textContent = String(checked.size);
                selectedQtyFooter.textContent = String(qty);
                selectedTotalFooter.textContent = formatBaht(total);

                const allChecked = itemChecks.length > 0 && itemChecks.every(c => c.checked);
                checkAllTop.checked = allChecked;
                checkAllBottom.checked = allChecked;
            }

            function toggleAll(checked) {
                itemChecks.forEach(c => { c.checked = checked; });
                updateSummary();
            }

            checkAllTop.addEventListener('change', function () { toggleAll(this.checked); });
            checkAllBottom.addEventListener('change', function () { toggleAll(this.checked); });
            itemChecks.forEach(c => c.addEventListener('change', updateSummary));

            removeSelectedForm.addEventListener('submit', function (e) {
                if (!removeSelectedIdsInput.value) {
                    e.preventDefault();
                    alert('กรุณาเลือกสินค้าที่ต้องการลบ');
                }
            });

            if (checkoutTrigger) {
                checkoutTrigger.addEventListener('click', function () {
                    if (!selectedIdsInput.value) {
                        alert('กรุณาเลือกสินค้าอย่างน้อย 1 รายการ');
                        return;
                    }
                    checkoutModal.classList.add('show');
                    checkoutModal.setAttribute('aria-hidden', 'false');
                });
            }

            if (openCheckoutModalBtn) {
                openCheckoutModalBtn.addEventListener('click', function () {
                    if (!selectedIdsInput.value) {
                        alert('กรุณาเลือกสินค้าอย่างน้อย 1 รายการ');
                        return;
                    }
                    checkoutModal.classList.add('show');
                    checkoutModal.setAttribute('aria-hidden', 'false');
                });
            }

            function closeCheckoutModal() {
                checkoutModal.classList.remove('show');
                checkoutModal.setAttribute('aria-hidden', 'true');
            }

            closeCheckoutModalBtn.addEventListener('click', closeCheckoutModal);
            cancelCheckoutBtn.addEventListener('click', closeCheckoutModal);
            checkoutModal.addEventListener('click', function (e) {
                if (e.target === checkoutModal) closeCheckoutModal();
            });

            confirmCheckoutBtn.addEventListener('click', function () {
                if (!selectedIdsInput.value) {
                    alert('กรุณาเลือกสินค้าอย่างน้อย 1 รายการ');
                    return;
                }
                if (!modalCustomerName.value.trim() || !modalCustomerPhone.value.trim() || !modalCustomerAddress.value.trim()) {
                    alert('กรุณากรอกชื่อ เบอร์โทร และที่อยู่ให้ครบ');
                    return;
                }

                hiddenCustomerName.value = modalCustomerName.value.trim();
                hiddenCustomerPhone.value = modalCustomerPhone.value.trim();
                hiddenCustomerAddress.value = modalCustomerAddress.value.trim();
                hiddenNote.value = modalNote.value.trim();
                checkoutForm.requestSubmit();
            });

            checkoutForm.addEventListener('submit', function (e) {
                if (!selectedIdsInput.value) {
                    e.preventDefault();
                    alert('กรุณาเลือกสินค้าอย่างน้อย 1 รายการ');
                }
            });

            updateSummary();
        })();

        (function () {
            const wrap = document.getElementById('quickContact');
            const btn = document.getElementById('quickContactToggle');
            if (!wrap || !btn) return;
            btn.addEventListener('click', function () {
                const collapsed = wrap.classList.toggle('is-collapsed');
                btn.innerHTML = collapsed
                    ? '<span style="font-size:18px;line-height:1;">+</span><span>เปิด</span>'
                    : '<span style="font-size:20px;line-height:1;">×</span><span>ปิด</span>';
            });
        })();
    </script>
<?php include __DIR__ . '/backend/helpers/floating_contact_widget.php'; ?>
</body>
</html>
