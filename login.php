<?php
include_once(__DIR__ . '/backend/helpers/i18n.php');
thaifa_i18n_buffer_start();
include_once(__DIR__ . '/backend/helpers/cart_count.php');
include_once(__DIR__ . '/backend/helpers/member_auth.php');

$auth = thaifaMemberAuth();
$cartCount = thaifaCartCount();
$member = $auth->currentMember();

function h($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function safeNextPath($next)
{
    $next = trim((string)$next);
    if ($next === '') {
        return 'index.php';
    }
    if (preg_match('/^https?:\/\//i', $next)) {
        return 'index.php';
    }
    if (strpos($next, '//') !== false) {
        return 'index.php';
    }
    return ltrim($next, '/');
}

$next = safeNextPath($_GET['next'] ?? $_POST['next'] ?? 'index.php');
$error = '';
$notice = '';
$identity = '';

if ($member) {
    header('Location: ' . $next);
    exit;
}

if (isset($_GET['registered'])) {
    $notice = 'สมัครสมาชิกเรียบร้อยแล้ว กรุณาเข้าสู่ระบบ';
}
if (isset($_GET['reset'])) {
    $notice = 'ตั้งรหัสผ่านใหม่เรียบร้อยแล้ว กรุณาเข้าสู่ระบบ';
}
if (isset($_GET['required'])) {
    $notice = 'กรุณาเข้าสู่ระบบก่อนทำรายการสั่งซื้อ';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identity = trim((string)($_POST['identity'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    try {
        $auth->login($identity, $password);
        header('Location: ' . $next);
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(thaifa_lang(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - THAIFA Foundation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#233882',
                        accent: '#d51d3c',
                        secondary: '#d9e7ef',
                        foreground: '#303a56',
                        border: '#e2e8f0'
                    }
                }
            }
        }
    </script>
    <style>
        body, * { font-family: 'Prompt', sans-serif; }
    </style>
</head>
<body class="bg-[#f4f7fb] text-foreground">
<nav class="fixed top-0 left-0 right-0 z-50 bg-white shadow-sm">
    <div class="bg-secondary/30 border-b border-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-10 text-sm">
                <a href="mailto:thaifafoundation@gmail.com" class="flex items-center gap-2 text-foreground/80 hover:text-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>thaifafoundation@gmail.com</span>
                </a>

                <div class="flex items-center gap-4">
                    <a href="cart.php" class="relative text-foreground/80 hover:text-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span class="absolute -top-2 -right-2 bg-accent text-white text-xs rounded-full w-4 h-4 flex items-center justify-center"><?= (int)$cartCount ?></span>
                    </a>

                    <div class="flex items-center gap-1"><a href="<?= h(thaifa_lang_url('th')) ?>" class="text-xs px-2 py-0.5 rounded <?= thaifa_lang()==='th' ? 'bg-primary text-white' : 'text-foreground/70 hover:text-primary' ?>">TH</a><a href="<?= h(thaifa_lang_url('en')) ?>" class="text-xs px-2 py-0.5 rounded <?= thaifa_lang()==='en' ? 'bg-primary text-white' : 'text-foreground/70 hover:text-primary' ?>">EN</a></div><div class="flex items-center gap-2 pl-4 border-l border-border">
                        <a href="login.php" class="text-primary"><?= h(thaifa_t('login')) ?></a>
                        <span class="text-foreground/40">/</span>
                        <a href="register.php" class="text-foreground/80 hover:text-primary"><?= h(thaifa_t('register')) ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

            <div class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="index.php"><img src="assets/images/Logo.png" alt="THAIFA" class="h-16"></a>
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

<main class="pt-[120px] min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 border border-border/70">
        <h1 class="text-3xl text-primary font-semibold text-center mb-2"><?= h(thaifa_t('login')) ?></h1>
        <p class="text-center text-foreground/60 mb-6">สำหรับสมาชิกมูลนิธิ</p>

        <?php if ($notice !== ''): ?>
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"><?= h($notice) ?></div>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"><?= h($error) ?></div>
        <?php endif; ?>

        <form method="post" class="space-y-4">
            <input type="hidden" name="next" value="<?= h($next) ?>">
            <div>
                <label class="block mb-1 text-sm text-foreground/80">อีเมล หรือ เบอร์โทร</label>
                <input type="text" name="identity" value="<?= h($identity) ?>" required class="w-full px-4 py-3 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30">
            </div>
            <div>
                <label class="block mb-1 text-sm text-foreground/80">รหัสผ่าน</label>
                <input type="password" name="password" required class="w-full px-4 py-3 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30">
            </div>
            <button type="submit" class="w-full bg-primary text-white py-3 rounded-lg hover:bg-primary/90"><?= h(thaifa_t('login')) ?></button>
        </form>

        <div class="mt-4 text-sm text-center">
            <a href="forgot-password.php" class="text-primary hover:underline">ลืมรหัสผ่าน?</a>
        </div>

        <div class="mt-3 text-sm text-center text-foreground/60">
            ยังไม่มีบัญชี? <a href="register.php" class="text-primary hover:underline"><?= h(thaifa_t('register')) ?></a>
        </div>
    </div>
</main>
<?php include __DIR__ . '/backend/helpers/floating_contact_widget.php'; ?>
</body>
</html>
