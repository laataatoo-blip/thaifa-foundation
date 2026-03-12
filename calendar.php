<?php
include_once(__DIR__ . '/backend/classes/CalendarManagement.class.php');
include_once(__DIR__ . '/backend/helpers/i18n.php');
thaifa_i18n_buffer_start();
include_once(__DIR__ . '/backend/helpers/cart_count.php');
$cartCount = thaifaCartCount();
$calendar = new CalendarManagement();
$isEn = thaifa_lang() === 'en';

$calendarTypes = $calendar->getTypes(true);
$calendarEvents = $calendar->eventsForJson();
$upcomingEvents = $calendar->getUpcomingEvents(10);

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function calendarTypeLabel($name)
{
    if (thaifa_lang() !== 'en') {
        return (string)$name;
    }
    $map = [
        'กิจกรรมทั่วไป' => 'General Event',
        'ประชุม' => 'Meeting',
        'ระดมทุน' => 'Fundraising',
        'กิจกรรมชุมชน' => 'Community Activity',
        'มอบทุนการศึกษา' => 'Scholarship Grant',
    ];
    $n = trim((string)$name);
    return $map[$n] ?? $n;
}
function calendarTranslateEnText($text)
{
    $s = (string)$text;
    if (thaifa_lang() !== 'en' || $s === '') {
        return $s;
    }
    $map = [
        'สำนักงานใหญ่ THAIFA Foundation' => 'THAIFA Foundation Headquarters',
        'Scholarship Activitiesการศึกษา' => 'Scholarship activities',
        'การศึกษา' => 'education',
        'ประชุม' => 'Meeting',
        'ระดมทุน' => 'Fundraising',
        'กิจกรรมชุมชน' => 'Community Activity',
        'มอบทุนการศึกษา' => 'Scholarship Grant',
    ];
    return strtr($s, $map);
}
if ($isEn) {
    foreach ($calendarTypes as &$type) {
        $type['name'] = calendarTypeLabel($type['name'] ?? '');
    }
    unset($type);
    foreach ($upcomingEvents as &$event) {
        $event['type_name'] = calendarTypeLabel($event['type_name'] ?? '');
        $event['title'] = calendarTranslateEnText($event['title'] ?? '');
        $event['summary'] = calendarTranslateEnText($event['summary'] ?? '');
        $event['description'] = calendarTranslateEnText($event['description'] ?? '');
        $event['location'] = calendarTranslateEnText($event['location'] ?? '');
    }
    unset($event);
    foreach ($calendarEvents as &$event) {
        $event['type_name'] = calendarTypeLabel($event['type_name'] ?? '');
        $event['title'] = calendarTranslateEnText($event['title'] ?? '');
        $event['summary'] = calendarTranslateEnText($event['summary'] ?? '');
        $event['description'] = calendarTranslateEnText($event['description'] ?? '');
        $event['location'] = calendarTranslateEnText($event['location'] ?? '');
    }
    unset($event);
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(thaifa_lang(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ปฏิทินกิจกรรม - THAIFA Foundation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Prompt', sans-serif; }
        .view-btn.active {
            background: #233882;
            color: #fff;
            box-shadow: 0 8px 18px rgba(35, 56, 130, 0.25);
        }
        .calendar-today {
            background: #233882;
            color: #fff;
            box-shadow: 0 8px 18px rgba(35, 56, 130, 0.28);
        }
        .event-pill {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#233882',
                        secondary: '#edf3f8',
                        surface: '#f5f7fb',
                        border: '#dbe3ef',
                        foreground: '#303a56'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-surface text-foreground">

<nav class="fixed top-0 left-0 right-0 z-50 bg-white shadow-sm">
    <div class="bg-secondary/30 border-b border-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-10 text-sm">
                <a href="mailto:thaifafoundation@gmail.com" class="hidden md:flex items-center gap-2 text-foreground/80 hover:text-primary transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>thaifafoundation@gmail.com</span>
                </a>
                <div class="flex items-center gap-4">
                    <a href="cart.php" class="relative text-foreground/80 hover:text-primary transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="absolute -top-2 -right-2 bg-accent text-white text-xs rounded-full w-4 h-4 flex items-center justify-center"><?= (int)$cartCount ?></span>
                    </a>
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

    <div class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="index.php" class="flex-shrink-0">
                    <img src="assets/images/Logo.png" alt="THAIFA Logo" class="h-20 w-auto"/>
                </a>
                <div class="hidden lg:flex items-center gap-1">
                    <a href="index.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100"><?= h(thaifa_t('home')) ?></a>
                    <a href="about.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100"><?= h(thaifa_t('about')) ?></a>
                    <a href="calendar.php" class="text-[#315d9f] bg-sky-100 px-4 py-2 rounded-md"><?= h(thaifa_t('calendar')) ?></a>
                    <a href="shop.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100"><?= h(thaifa_t('shop')) ?></a>
                    <a href="donate.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100"><?= h(thaifa_t('donate')) ?></a>
                    <a href="volunteer.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100"><?= h(thaifa_t('volunteer')) ?></a>
                    <a href="stories.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100"><?= h(thaifa_t('stories')) ?></a>
                    <a href="contact.php" class="text-foreground px-4 py-2 rounded-md hover:text-[#315d9f] hover:bg-sky-100"><?= h(thaifa_t('contact')) ?></a>
                </div>
            </div>
        </div>
    </div>
</nav>

<main class="pt-[120px] pb-12">
    <section class="bg-gradient-to-br from-primary to-[#4b61a6] text-white py-10 md:py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 bg-white/20 rounded-full px-5 py-2 mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span><?= h(thaifa_t('calendar_badge')) ?></span>
            </div>
            <h1 class="text-4xl md:text-6xl text-white mb-3"><?= h(thaifa_t('calendar_title')) ?></h1>
            <p class="text-lg md:text-2xl text-white/90 max-w-4xl mx-auto leading-relaxed">
                <?= h(thaifa_t('calendar_subtitle')) ?>
            </p>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white border border-border rounded-2xl p-3 md:p-4 flex flex-col lg:flex-row gap-3 lg:items-center lg:justify-between shadow-sm">
            <div class="flex items-center gap-2 md:gap-3">
                <button id="btnToday" class="px-4 py-2 rounded-xl border border-border bg-surface text-sm hover:bg-secondary"><?= $isEn ? 'Today' : 'วันนี้' ?></button>
                <button id="btnPrev" class="w-9 h-9 rounded-xl border border-border bg-white hover:bg-secondary flex items-center justify-center" aria-label="<?= $isEn ? 'Previous' : 'ก่อนหน้า' ?>">‹</button>
                <button id="btnNext" class="w-9 h-9 rounded-xl border border-border bg-white hover:bg-secondary flex items-center justify-center" aria-label="<?= $isEn ? 'Next' : 'ถัดไป' ?>">›</button>
                <div id="currentLabel" class="ml-1 text-2xl text-primary"></div>
            </div>
            <div class="inline-flex bg-surface border border-border rounded-2xl p-1.5 gap-1">
                <button class="view-btn active px-3 py-1.5 rounded-xl text-sm" data-view="month"><?= $isEn ? 'Month' : 'เดือน' ?></button>
                <button class="view-btn px-3 py-1.5 rounded-xl text-sm" data-view="week"><?= $isEn ? 'Week' : 'สัปดาห์' ?></button>
                <button class="view-btn px-3 py-1.5 rounded-xl text-sm" data-view="day"><?= $isEn ? 'Day' : 'วัน' ?></button>
                <button class="view-btn px-3 py-1.5 rounded-xl text-sm" data-view="list"><?= $isEn ? 'Agenda' : 'กำหนดการ' ?></button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mt-4">
            <div class="lg:col-span-8">
                <div class="bg-white border border-border rounded-2xl overflow-hidden shadow-sm">
                    <div id="calendarView" class="min-h-[640px]"></div>
                </div>
            </div>

            <aside class="lg:col-span-4 space-y-4">
                <div class="bg-white border border-border rounded-2xl p-5 shadow-sm">
                    <h3 class="text-lg text-primary mb-3"><?= $isEn ? 'Event Categories' : 'ประเภทกิจกรรม' ?></h3>
                    <ul class="space-y-2">
                        <?php foreach ($calendarTypes as $type): ?>
                            <li class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-full" style="background: <?= h($type['color_hex']) ?>;"></span>
                                <span class="text-foreground/90"><?= h(calendarTypeLabel($type['name'])) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="bg-white border border-border rounded-2xl p-5 shadow-sm">
                    <h3 class="text-lg text-primary mb-3"><?= $isEn ? 'Upcoming Events' : 'กิจกรรมที่กำลังจะมาถึง' ?></h3>
                    <div id="upcomingList" class="space-y-3">
                        <?php if (empty($upcomingEvents)): ?>
                            <div class="text-sm text-foreground/60"><?= $isEn ? 'No upcoming events yet' : 'ยังไม่มีกิจกรรมที่กำลังจะมาถึง' ?></div>
                        <?php else: ?>
                            <?php foreach ($upcomingEvents as $u): ?>
                                <div class="rounded-xl border border-border p-3">
                                    <div class="text-sm text-foreground/60 mb-1"><?= h(date('d/m/Y H:i', strtotime($u['start_at']))) ?></div>
                                    <div class="text-primary font-medium leading-snug"><?= h($u['title']) ?></div>
                                    <div class="text-xs mt-2 inline-flex items-center gap-2 px-2 py-1 rounded-full" style="background: <?= h($u['color_hex'] ?: '#233882') ?>20; color: <?= h($u['color_hex'] ?: '#233882') ?>;">
                                        <span class="w-2 h-2 rounded-full" style="background: <?= h($u['color_hex'] ?: '#233882') ?>;"></span>
                                        <?= h(calendarTypeLabel($u['type_name'] ?: ($isEn ? 'General Event' : 'กิจกรรมทั่วไป'))) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </aside>
        </div>
    </section>
</main>

<footer class="bg-primary text-white pt-14 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
            <div>
                <img src="assets/images/Logo.png" alt="THAIFA Foundation" class="h-14 w-auto mb-4 bg-white rounded-md p-1" />
                <p class="text-white/80 text-sm">มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน มุ่งมั่นสร้างโอกาสและพัฒนาคุณภาพชีวิตของเด็กและเยาวชนไทย</p>
            </div>
            <div>
                <h3 class="mb-4 text-xl">เมนูหลัก</h3>
                <ul class="space-y-2 text-sm text-white/80">
                    <li><a href="index.php" class="hover:text-white"><?= h(thaifa_t('home')) ?></a></li>
                    <li><a href="about.php" class="hover:text-white"><?= h(thaifa_t('about')) ?></a></li>
                    <li><a href="shop.php" class="hover:text-white"><?= h(thaifa_t('shop')) ?></a></li>
                    <li><a href="donate.php" class="hover:text-white"><?= h(thaifa_t('donate')) ?></a></li>
                </ul>
            </div>
            <div>
                <h3 class="mb-4 text-xl">โครงการของเรา</h3>
                <ul class="space-y-2 text-sm text-white/80">
                    <li>ทุนการศึกษา</li>
                    <li>ช่วยเหลือเด็กกำพร้า</li>
                    <li>เครื่องมือแพทย์</li>
                    <li>กิจกรรมชุมชน</li>
                </ul>
            </div>
            <div>
                <h3 class="mb-4 text-xl"><?= h(thaifa_t('contact')) ?></h3>
                <p class="text-sm text-white/80">อาคาร จูเวลเลอรี่ ห้อง 138/32 ชั้น 12 เลขที่ 138 ถนนนเรศ แขวงสี่พระยา เขตบางรัก กรุงเทพฯ 10500</p>
                <p class="text-sm text-white/80 mt-3">thaifafoundation@gmail.com</p>
            </div>
        </div>
        <div class="border-t border-white/15 pt-6 text-sm text-white/70">
            &copy; 2025 มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน (THAIFA Foundation). สงวนลิขสิทธิ์.
        </div>
    </div>
</footer>

<script>
const calendarEvents = <?= json_encode($calendarEvents, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
const isEn = <?= $isEn ? 'true' : 'false' ?>;
const t = {
    agenda: <?= json_encode($isEn ? 'Agenda' : 'กำหนดการกิจกรรม', JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
    itemCount: <?= json_encode($isEn ? 'items' : 'รายการ', JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
    allDay: <?= json_encode($isEn ? 'All day' : 'ทั้งวัน', JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
    noEventsToday: <?= json_encode($isEn ? 'No events today.' : 'ไม่มีกิจกรรมในวันนี้', JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
    noSchedules: <?= json_encode($isEn ? 'No schedule available yet.' : 'ยังไม่มีกำหนดการกิจกรรม', JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
    generalEvent: <?= json_encode($isEn ? 'General Event' : 'กิจกรรมทั่วไป', JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
};
function typeLabel(name) {
    const n = (name || '').trim();
    return n || t.generalEvent;
}
const months = <?= json_encode($isEn ? ['January','February','March','April','May','June','July','August','September','October','November','December'] : ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
const shortMonths = <?= json_encode($isEn ? ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] : ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
const days = <?= json_encode($isEn ? ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] : ['อา','จ','อ','พ','พฤ','ศ','ส'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

let currentDate = new Date();
let currentView = 'month';

function toDateKey(d) {
    return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
}

function parseEventDate(dateString) {
    const s = (dateString || '').replace(' ', 'T');
    const d = new Date(s);
    return isNaN(d.getTime()) ? null : d;
}

function eventByDate() {
    const map = {};
    calendarEvents.forEach(ev => {
        const d = parseEventDate(ev.start_at);
        if (!d) return;
        const key = toDateKey(d);
        if (!map[key]) map[key] = [];
        map[key].push(ev);
    });
    return map;
}

function updateHeaderLabel() {
    const el = document.getElementById('currentLabel');
    if (currentView === 'month') {
        el.textContent = isEn
            ? `${months[currentDate.getMonth()]} ${currentDate.getFullYear()}`
            : `${months[currentDate.getMonth()]} ${currentDate.getFullYear() + 543}`;
        return;
    }
    if (currentView === 'week') {
        const start = getWeekStart(new Date(currentDate));
        const end = new Date(start);
        end.setDate(start.getDate() + 6);
        el.textContent = isEn
            ? `${start.getDate()} ${shortMonths[start.getMonth()]} - ${end.getDate()} ${shortMonths[end.getMonth()]} ${end.getFullYear()}`
            : `${start.getDate()} ${shortMonths[start.getMonth()]} - ${end.getDate()} ${shortMonths[end.getMonth()]} ${end.getFullYear() + 543}`;
        return;
    }
    if (currentView === 'day') {
        el.textContent = isEn
            ? `${days[currentDate.getDay()]} ${currentDate.getDate()} ${months[currentDate.getMonth()]} ${currentDate.getFullYear()}`
            : `${days[currentDate.getDay()]} ${currentDate.getDate()} ${months[currentDate.getMonth()]} ${currentDate.getFullYear() + 543}`;
        return;
    }
    el.textContent = t.agenda;
}

function getWeekStart(date) {
    const d = new Date(date);
    d.setDate(d.getDate() - d.getDay());
    d.setHours(0,0,0,0);
    return d;
}

function renderMonthView() {
    const view = document.getElementById('calendarView');
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startDay = firstDay.getDay();
    const daysInMonth = lastDay.getDate();
    const todayKey = toDateKey(new Date());
    const map = eventByDate();

    let html = '<div class="grid grid-cols-7 border-b border-border bg-[#f8fbff]">';
    days.forEach(d => html += `<div class="py-3 text-center text-foreground/70 text-sm">${d}</div>`);
    html += '</div>';

    html += '<div class="grid grid-cols-7">';
    let dayNum = 1;
    for (let i = 0; i < 42; i++) {
        if (i < startDay || dayNum > daysInMonth) {
            html += '<div class="min-h-[104px] border-r border-b border-border bg-surface/50"></div>';
            continue;
        }

        const d = new Date(year, month, dayNum);
        const key = toDateKey(d);
        const events = map[key] || [];
        const isToday = key === todayKey;

        html += '<div class="min-h-[104px] border-r border-b border-border p-2.5 bg-white hover:bg-[#f8fbff] transition-colors">';
        html += `<div class="mb-2 text-sm ${isToday ? 'calendar-today w-8 h-8 rounded-full flex items-center justify-center' : 'text-foreground/80'}">${dayNum}</div>`;

        if (events.length) {
            const shortEvents = events.slice(0, 2);
            shortEvents.forEach(ev => {
                const color = ev.color_hex || '#233882';
                html += `<div class="event-pill text-xs px-2 py-1 rounded-md mb-1" style="background:${color}1f;color:${color};">${ev.title}</div>`;
            });
            if (events.length > 2) {
                html += `<div class="text-xs text-foreground/60">+${events.length - 2} ${t.itemCount}</div>`;
            }
        }

        html += '</div>';
        dayNum++;
    }
    html += '</div>';
    view.innerHTML = html;
}

function renderWeekView() {
    const view = document.getElementById('calendarView');
    const map = eventByDate();
    const start = getWeekStart(currentDate);
    const todayKey = toDateKey(new Date());

    let html = '<div class="grid grid-cols-7 border-b border-border bg-[#f8fbff]">';
    for (let i = 0; i < 7; i++) {
        const d = new Date(start);
        d.setDate(start.getDate() + i);
        html += `<div class="p-3 text-center text-sm"><div class="text-foreground/70">${days[d.getDay()]}</div><div class="text-foreground">${d.getDate()}</div></div>`;
    }
    html += '</div><div class="grid grid-cols-7">';

    for (let i = 0; i < 7; i++) {
        const d = new Date(start);
        d.setDate(start.getDate() + i);
        const key = toDateKey(d);
        const events = map[key] || [];
        const todayClass = key === todayKey ? 'bg-[#f3f8ff]' : 'bg-white';

        html += `<div class="min-h-[560px] border-r border-b border-border p-2 ${todayClass}">`;
        if (events.length === 0) {
            html += '<div class="text-xs text-foreground/40 px-1">-</div>';
        } else {
            events.forEach(ev => {
                const color = ev.color_hex || '#233882';
                const dt = parseEventDate(ev.start_at);
                const time = ev.is_all_day ? t.allDay : (dt ? `${String(dt.getHours()).padStart(2,'0')}:${String(dt.getMinutes()).padStart(2,'0')}` : '');
                html += `<div class="rounded-lg p-2 mb-2 text-xs" style="background:${color}20;border-left:3px solid ${color}">
                    <div class="font-medium" style="color:${color}">${ev.title}</div>
                    <div class="text-foreground/70 mt-1">${time}</div>
                </div>`;
            });
        }
        html += '</div>';
    }

    html += '</div>';
    view.innerHTML = html;
}

function renderDayView() {
    const view = document.getElementById('calendarView');
    const key = toDateKey(currentDate);
    const map = eventByDate();
    const events = map[key] || [];

    let html = '<div class="p-5">';
    html += `<div class="text-2xl text-primary mb-4">${days[currentDate.getDay()]} ${currentDate.getDate()} ${months[currentDate.getMonth()]} ${isEn ? currentDate.getFullYear() : currentDate.getFullYear() + 543}</div>`;

    if (!events.length) {
        html += `<div class="rounded-xl border border-border p-6 text-foreground/60 text-center">${t.noEventsToday}</div>`;
    } else {
        html += '<div class="space-y-3">';
        events.forEach(ev => {
            const color = ev.color_hex || '#233882';
            const s = parseEventDate(ev.start_at);
            const e = parseEventDate(ev.end_at);
            const range = ev.is_all_day ? t.allDay : `${s ? `${String(s.getHours()).padStart(2,'0')}:${String(s.getMinutes()).padStart(2,'0')}` : ''}${e ? ` - ${String(e.getHours()).padStart(2,'0')}:${String(e.getMinutes()).padStart(2,'0')}` : ''}`;

            html += `<div class="rounded-xl border border-border p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-lg text-primary">${ev.title}</div>
                        <div class="text-sm text-foreground/70 mt-1">${ev.summary || ''}</div>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full" style="background:${color}20;color:${color}">${typeLabel(ev.type_name || t.generalEvent)}</span>
                </div>
                <div class="text-sm text-foreground/80 mt-3">🕒 ${range}</div>
                ${ev.location ? `<div class="text-sm text-foreground/80 mt-1">📍 ${ev.location}</div>` : ''}
                ${ev.description ? `<div class="text-sm text-foreground/70 mt-2">${ev.description}</div>` : ''}
            </div>`;
        });
        html += '</div>';
    }

    html += '</div>';
    view.innerHTML = html;
}

function renderListView() {
    const view = document.getElementById('calendarView');
    const now = new Date();
    const events = [...calendarEvents]
        .map(ev => ({...ev, _start: parseEventDate(ev.start_at)}))
        .filter(ev => ev._start)
        .sort((a, b) => a._start - b._start)
        .filter(ev => ev._start >= new Date(now.getFullYear(), now.getMonth(), now.getDate()));

    let html = '<div class="p-5">';

    if (!events.length) {
        html += `<div class="rounded-xl border border-border p-6 text-foreground/60 text-center">${t.noSchedules}</div>`;
    } else {
        html += '<div class="space-y-3">';
        events.forEach(ev => {
            const color = ev.color_hex || '#233882';
            const d = ev._start;
            html += `<div class="rounded-xl border border-border p-4">
                <div class="flex items-start gap-4">
                    <div class="w-14 text-center">
                        <div class="text-xs text-foreground/60">${shortMonths[d.getMonth()]}</div>
                        <div class="text-2xl text-primary">${d.getDate()}</div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <div class="text-primary text-lg leading-snug">${ev.title}</div>
                            <span class="text-xs px-2 py-1 rounded-full" style="background:${color}20;color:${color}">${typeLabel(ev.type_name || t.generalEvent)}</span>
                        </div>
                        <div class="text-sm text-foreground/70 mt-1">${ev.summary || ''}</div>
                        <div class="text-sm text-foreground/80 mt-2">🕒 ${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}</div>
                        ${ev.location ? `<div class="text-sm text-foreground/80">📍 ${ev.location}</div>` : ''}
                    </div>
                </div>
            </div>`;
        });
        html += '</div>';
    }

    html += '</div>';
    view.innerHTML = html;
}

function renderCalendar() {
    updateHeaderLabel();
    if (currentView === 'month') return renderMonthView();
    if (currentView === 'week') return renderWeekView();
    if (currentView === 'day') return renderDayView();
    return renderListView();
}

document.getElementById('btnToday').addEventListener('click', () => {
    currentDate = new Date();
    renderCalendar();
});

document.getElementById('btnPrev').addEventListener('click', () => {
    if (currentView === 'month') currentDate.setMonth(currentDate.getMonth() - 1);
    else if (currentView === 'week') currentDate.setDate(currentDate.getDate() - 7);
    else if (currentView === 'day') currentDate.setDate(currentDate.getDate() - 1);
    else currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
});

document.getElementById('btnNext').addEventListener('click', () => {
    if (currentView === 'month') currentDate.setMonth(currentDate.getMonth() + 1);
    else if (currentView === 'week') currentDate.setDate(currentDate.getDate() + 7);
    else if (currentView === 'day') currentDate.setDate(currentDate.getDate() + 1);
    else currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
});

document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.view-btn').forEach(x => x.classList.remove('active'));
        btn.classList.add('active');
        currentView = btn.dataset.view;
        renderCalendar();
    });
});

renderCalendar();
</script>

<?php include __DIR__ . '/backend/helpers/floating_contact_widget.php'; ?>

</body>
</html>
