<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$MM_authorizedUsers = "Admin";
$MM_donotCheckaccess = "false";
include('./components/funcCheckSession.php');
include(__DIR__ . '/../backend/classes/DatabaseManagement.class.php');
include_once(__DIR__ . '/../backend/classes/AnalyticsManagement.class.php');

$DB = new DatabaseManagement();
$analytics = new AnalyticsManagement();

function h($str)
{
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

$adminName = trim((string)(
    $_SESSION['AdminLogin']['Name']
    ?? $_SESSION['AdminLogin']['FullName']
    ?? $_SESSION['AdminLogin']['AdminName']
    ?? $_SESSION['AdminLogin']['Username']
    ?? 'Admin'
));

$totalNews = 0;
$visibleNews = 0;
$totalImages = 0;
$analyticsDays = 14;
$analyticsData = [
    'views' => 0,
    'visitors' => 0,
    'sessions' => 0,
    'daily' => [],
    'top_pages' => [],
    'top_referrers' => [],
];
$viewsDeltaPercent = 0;
$visitorsDeltaPercent = 0;

try {
    $row = $DB->selectOne("SELECT COUNT(*) AS total_news FROM news");
    $totalNews = (int)($row['total_news'] ?? 0);

    $row = $DB->selectOne("SELECT COUNT(*) AS visible_news FROM news WHERE is_visible = 1");
    $visibleNews = (int)($row['visible_news'] ?? 0);

    $row = $DB->selectOne("SELECT COUNT(*) AS total_images FROM news_images");
    $totalImages = (int)($row['total_images'] ?? 0);
} catch (Throwable $e) {
    // Keep dashboard renderable even if some tables are not ready.
}

try {
    $analyticsData = $analytics->overview($analyticsDays);

    $recentViews = 0;
    $prevViews = 0;
    $recentVisitors = 0;
    $prevVisitors = 0;
    $daily = $analyticsData['daily'] ?? [];
    $split = max(1, (int)floor(count($daily) / 2));

    foreach ($daily as $idx => $d) {
        $v = (int)($d['views'] ?? 0);
        $u = (int)($d['visitors'] ?? 0);
        if ($idx >= count($daily) - $split) {
            $recentViews += $v;
            $recentVisitors += $u;
        } else {
            $prevViews += $v;
            $prevVisitors += $u;
        }
    }

    if ($prevViews > 0) {
        $viewsDeltaPercent = (($recentViews - $prevViews) / $prevViews) * 100;
    } elseif ($recentViews > 0) {
        $viewsDeltaPercent = 100;
    }

    if ($prevVisitors > 0) {
        $visitorsDeltaPercent = (($recentVisitors - $prevVisitors) / $prevVisitors) * 100;
    } elseif ($recentVisitors > 0) {
        $visitorsDeltaPercent = 100;
    }
} catch (Throwable $e) {
    // Keep dashboard renderable even if analytics tables are not ready.
}

$chartLabels = [];
$chartViews = [];
$chartVisitors = [];
foreach (($analyticsData['daily'] ?? []) as $d) {
    $chartLabels[] = (string)($d['view_date'] ?? '');
    $chartViews[] = (int)($d['views'] ?? 0);
    $chartVisitors[] = (int)($d['visitors'] ?? 0);
}
if (empty($chartLabels)) {
    $chartLabels = ['ไม่มีข้อมูล'];
    $chartViews = [0];
    $chartVisitors = [0];
}

$topPageLabels = [];
$topPageValues = [];
foreach (array_slice(($analyticsData['top_pages'] ?? []), 0, 6) as $p) {
    $topPageLabels[] = (string)($p['page_path'] ?? '-');
    $topPageValues[] = (int)($p['views'] ?? 0);
}
if (empty($topPageLabels)) {
    $topPageLabels = ['ไม่มีข้อมูล'];
    $topPageValues = [0];
}

$refLabels = [];
$refValues = [];
foreach (array_slice(($analyticsData['top_referrers'] ?? []), 0, 5) as $r) {
    $ref = trim((string)($r['referrer'] ?? ''));
    $refLabels[] = $ref !== '' ? $ref : 'Direct';
    $refValues[] = (int)($r['views'] ?? 0);
}
if (empty($refLabels)) {
    $refLabels = ['Direct'];
    $refValues = [0];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include('./structure/head.php') ?>
    <title>ThaiFa Foundation Admin</title>
    <style>
        .analytics-card {
            border: 1px solid var(--thaifa-border);
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 4px 16px rgba(48, 58, 86, 0.06);
        }
        .analytics-card .card-body {
            padding: 1rem 1.1rem;
        }
        .analytics-chart {
            min-height: 320px;
        }
        .insight-pill {
            border-radius: 999px;
            padding: .28rem .65rem;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid #d9e7ef;
            background: #f8fbff;
            color: #303a56;
        }
        .insight-pill.up {
            border-color: #cbe8d6;
            color: #127a3e;
            background: #eefbf3;
        }
        .insight-pill.down {
            border-color: #f2c4cd;
            color: #ac1e3b;
            background: #fff1f3;
        }
    </style>
</head>

<body>
<div class="wrapper">
    <?php include('./components/sidebar.php') ?>
    <?php include('./components/navbar.php') ?>

    <div class="page-wrapper">
        <div class="page-content-wrapper page-content-margin-padding">
            <div class="page-content page-content-margin-padding">

                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <div class="breadcrumb-title pe-3">Dashboard</div>
                    <div class="ps-3">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0">
                                <li class="breadcrumb-item"><a href="index.php"><i class="bx bx-home-alt"></i></a></li>
                                <li class="breadcrumb-item active" aria-current="page">ภาพรวม</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="thaifa-hero p-4 p-lg-5 mb-3">
                    <div class="d-flex flex-column flex-lg-row gap-3 justify-content-between">
                        <div>
                            <div class="brand-pill mb-2"><i class='bx bx-shield-quarter'></i> THAIFA Foundation Admin</div>
                            <h3 class="mb-2 text-white">ยินดีต้อนรับ, <?= h($adminName) ?></h3>
                            <p class="sub mb-0">คุณสามารถจัดการข่าวจากเมนู News ด้านซ้าย และติดตามสถานะภาพรวมงานมูลนิธิได้ในหน้านี้</p>
                        </div>
                        <div class="text-lg-end">
                            <div class="fw-bold text-white">Tagline</div>
                            <div class="sub">ช่วยเหลืออย่างมีระบบ โปร่งใส ตรวจสอบได้</div>
                        </div>
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-md-3 g-3 mb-3">
                    <div class="col">
                        <div class="thaifa-stat">
                            <div class="label">ข่าวทั้งหมด</div>
                            <div class="value mt-2"><?= $totalNews ?></div>
                            <span class="badge badge-soft mt-2">News</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="thaifa-stat">
                            <div class="label">ข่าวที่แสดงผล</div>
                            <div class="value mt-2"><?= $visibleNews ?></div>
                            <span class="badge badge-soft mt-2">Visible</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="thaifa-stat">
                            <div class="label">รูปข่าวทั้งหมด</div>
                            <div class="value mt-2"><?= $totalImages ?></div>
                            <span class="badge badge-soft mt-2">Media</span>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-xl-8">
                        <div class="analytics-card card h-100">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 thaifa-section-title">แนวโน้มผู้เข้าชมย้อนหลัง <?= (int)$analyticsDays ?> วัน</h5>
                                <span class="badge badge-soft">อัปเดตอัตโนมัติ</span>
                            </div>
                            <div class="card-body">
                                <div id="chartDailyTraffic" class="analytics-chart"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="analytics-card card h-100">
                            <div class="card-header py-3">
                                <h5 class="mb-0 thaifa-section-title">สรุปพฤติกรรมผู้เข้าชม</h5>
                            </div>
                            <div class="card-body d-flex flex-column gap-3">
                                <?php $vDeltaClass = $viewsDeltaPercent >= 0 ? 'up' : 'down'; ?>
                                <?php $uDeltaClass = $visitorsDeltaPercent >= 0 ? 'up' : 'down'; ?>
                                <div>
                                    <div class="text-muted small">Views เทียบช่วงก่อนหน้า</div>
                                    <div class="d-flex align-items-center justify-content-between mt-1">
                                        <div class="h4 mb-0"><?= number_format((int)$analyticsData['views']) ?></div>
                                        <span class="insight-pill <?= $vDeltaClass ?>"><?= $viewsDeltaPercent >= 0 ? '↑' : '↓' ?> <?= number_format(abs($viewsDeltaPercent), 1) ?>%</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-muted small">Visitors เทียบช่วงก่อนหน้า</div>
                                    <div class="d-flex align-items-center justify-content-between mt-1">
                                        <div class="h4 mb-0"><?= number_format((int)$analyticsData['visitors']) ?></div>
                                        <span class="insight-pill <?= $uDeltaClass ?>"><?= $visitorsDeltaPercent >= 0 ? '↑' : '↓' ?> <?= number_format(abs($visitorsDeltaPercent), 1) ?>%</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-muted small mb-2">Sessions ทั้งหมด</div>
                                    <div class="h4 mb-0"><?= number_format((int)$analyticsData['sessions']) ?></div>
                                </div>
                                <a href="analytics.php?days=30" class="btn btn-outline-primary btn-sm mt-auto">
                                    ดูรายงานเชิงลึก
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-xl-6">
                        <div class="analytics-card card h-100">
                            <div class="card-header py-3">
                                <h5 class="mb-0 thaifa-section-title">หน้าที่ได้รับความนิยมสูงสุด</h5>
                            </div>
                            <div class="card-body">
                                <div id="chartTopPages" class="analytics-chart"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="analytics-card card h-100">
                            <div class="card-header py-3">
                                <h5 class="mb-0 thaifa-section-title">แหล่งที่มาของผู้เข้าชม (Referrer)</h5>
                            </div>
                            <div class="card-body">
                                <div id="chartReferrers" class="analytics-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="overlay toggle-btn-mobile"></div>
<a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>

<?php include('./structure/script.php') ?>
<script src="assets/plugins/apexcharts-bundle/js/apexcharts.min.js"></script>
<script>
    (function () {
        const labels = <?= json_encode($chartLabels, JSON_UNESCAPED_UNICODE) ?>;
        const viewsData = <?= json_encode($chartViews) ?>;
        const visitorsData = <?= json_encode($chartVisitors) ?>;
        const topPageLabels = <?= json_encode($topPageLabels, JSON_UNESCAPED_UNICODE) ?>;
        const topPageValues = <?= json_encode($topPageValues) ?>;
        const refLabels = <?= json_encode($refLabels, JSON_UNESCAPED_UNICODE) ?>;
        const refValues = <?= json_encode($refValues) ?>;

        const lineOptions = {
            chart: { type: 'line', height: 320, toolbar: { show: false } },
            colors: ['#3357a3', '#2b75a3'],
            series: [
                { name: 'Views', data: viewsData },
                { name: 'Visitors', data: visitorsData }
            ],
            stroke: { width: [3, 3], curve: 'smooth' },
            grid: { borderColor: '#e8edf5' },
            xaxis: { categories: labels, labels: { rotate: -30 } },
            yaxis: { labels: { formatter: (v) => Math.round(v) } },
            legend: { position: 'top', horizontalAlign: 'right' },
            markers: { size: 4 },
            tooltip: { shared: true }
        };
        new ApexCharts(document.querySelector('#chartDailyTraffic'), lineOptions).render();

        const barOptions = {
            chart: { type: 'bar', height: 320, toolbar: { show: false } },
            series: [{ name: 'Views', data: topPageValues }],
            colors: ['#3357a3'],
            plotOptions: { bar: { horizontal: true, borderRadius: 6, barHeight: '58%' } },
            dataLabels: { enabled: false },
            xaxis: { categories: topPageLabels, labels: { formatter: (v) => Math.round(v) } },
            grid: { borderColor: '#e8edf5' }
        };
        new ApexCharts(document.querySelector('#chartTopPages'), barOptions).render();

        const donutOptions = {
            chart: { type: 'donut', height: 320 },
            labels: refLabels,
            series: refValues,
            colors: ['#3357a3', '#2b75a3', '#303a56', '#d51d3c', '#88a4db', '#9eb3e1'],
            legend: { position: 'bottom' },
            dataLabels: { enabled: true },
            stroke: { colors: ['#fff'] }
        };
        new ApexCharts(document.querySelector('#chartReferrers'), donutOptions).render();
    })();
</script>
</body>
</html>
