<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$MM_authorizedUsers = "Admin";
$MM_donotCheckaccess = "false";
include('./components/funcCheckSession.php');

include_once(__DIR__ . '/../backend/classes/AnalyticsManagement.class.php');

$analytics = new AnalyticsManagement();
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$days = max(1, (int)($_GET['days'] ?? 7));
$data = $analytics->overview($days);

$chartLabels = [];
$chartViews = [];
$chartVisitors = [];
foreach (($data['daily'] ?? []) as $d) {
    $chartLabels[] = (string)($d['view_date'] ?? '');
    $chartViews[] = (int)($d['views'] ?? 0);
    $chartVisitors[] = (int)($d['visitors'] ?? 0);
}
if (empty($chartLabels)) {
    $chartLabels = ['ไม่มีข้อมูล'];
    $chartViews = [0];
    $chartVisitors = [0];
}

$topLabels = [];
$topValues = [];
foreach (array_slice(($data['top_pages'] ?? []), 0, 8) as $p) {
    $topLabels[] = (string)($p['page_path'] ?? '-');
    $topValues[] = (int)($p['views'] ?? 0);
}
if (empty($topLabels)) {
    $topLabels = ['ไม่มีข้อมูล'];
    $topValues = [0];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include('./structure/head.php') ?>
    <title>สถิติเข้าเว็บ</title>
    <style>
        .analytics-chart { min-height: 320px; }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include('./components/sidebar.php') ?>
    <?php include('./components/navbar.php') ?>

    <div class="page-wrapper"><div class="page-content-wrapper page-content-margin-padding"><div class="page-content page-content-margin-padding">

        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3"><div class="breadcrumb-title pe-3">Analytics</div><div class="ps-3"><nav><ol class="breadcrumb mb-0 p-0"><li class="breadcrumb-item"><a href="index.php"><i class="bx bx-home-alt"></i></a></li><li class="breadcrumb-item active">สถิติเข้าเว็บ</li></ol></nav></div></div>
        <form method="get" class="mb-3 admin-filter-bar">
            <div>
                <label class="form-label">ช่วงเวลา</label>
                <select name="days" class="form-select">
                    <?php foreach([7,14,30,90] as $d): ?><option value="<?= $d ?>" <?= $days===$d?'selected':'' ?>>ย้อนหลัง <?= $d ?> วัน</option><?php endforeach; ?>
                </select>
            </div>
            <button class="btn btn-outline-primary" type="submit">ดูสถิติ</button>
        </form>

        <div class="row row-cols-1 row-cols-md-3 g-3 mb-3">
            <div class="col"><div class="thaifa-stat"><div class="label">Page Views</div><div class="value mt-2"><?= number_format((int)$data['views']) ?></div></div></div>
            <div class="col"><div class="thaifa-stat"><div class="label">ผู้เข้าชม (IP ไม่ซ้ำ)</div><div class="value mt-2"><?= number_format((int)$data['visitors']) ?></div></div></div>
            <div class="col"><div class="thaifa-stat"><div class="label">Sessions</div><div class="value mt-2"><?= number_format((int)$data['sessions']) ?></div></div></div>
        </div>

        <div class="row g-3">
            <div class="col-xl-8">
                <div class="card thaifa-card"><div class="card-body">
                    <h5 class="mb-3">แนวโน้มรายวัน</h5>
                    <div id="trafficChart" class="analytics-chart"></div>
                </div></div>
            </div>
            <div class="col-xl-4">
                <div class="card thaifa-card"><div class="card-body">
                    <h5 class="mb-3">หน้าที่มีผู้เข้าชมสูงสุด</h5>
                    <div id="topPagesChart" class="analytics-chart"></div>
                </div></div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-xl-6">
                <div class="card thaifa-card"><div class="card-body">
                    <h5 class="mb-3">หน้าที่มีผู้เข้าชมสูงสุด (ตาราง)</h5>
                    <div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>หน้า</th><th>Views</th><th>Visitors</th></tr></thead><tbody>
                    <?php if(empty($data['top_pages'])): ?><tr><td colspan="3" class="text-center text-muted">ยังไม่มีข้อมูล</td></tr><?php else: foreach($data['top_pages'] as $p): ?>
                        <tr><td><?= h($p['page_path']) ?></td><td><?= number_format((int)$p['views']) ?></td><td><?= number_format((int)$p['visitors']) ?></td></tr>
                    <?php endforeach; endif; ?>
                    </tbody></table></div>
                </div></div>
            </div>
            <div class="col-xl-6">
                <div class="card thaifa-card"><div class="card-body">
                    <h5 class="mb-3">แหล่งที่มา (Referrer)</h5>
                    <div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>Referrer</th><th>Views</th></tr></thead><tbody>
                    <?php if(empty($data['top_referrers'])): ?><tr><td colspan="2" class="text-center text-muted">ยังไม่มีข้อมูล</td></tr><?php else: foreach($data['top_referrers'] as $r): ?>
                        <tr><td><?= h($r['referrer']) ?></td><td><?= number_format((int)$r['views']) ?></td></tr>
                    <?php endforeach; endif; ?>
                    </tbody></table></div>
                </div></div>
            </div>
        </div>

        <div class="card thaifa-card mt-3"><div class="card-body">
            <h5 class="mb-3">แนวโน้มรายวัน (ตาราง)</h5>
            <div class="table-responsive"><table class="table align-middle"><thead><tr><th>วันที่</th><th>Views</th><th>Visitors</th></tr></thead><tbody>
                <?php if(empty($data['daily'])): ?><tr><td colspan="3" class="text-center text-muted">ยังไม่มีข้อมูล</td></tr><?php else: foreach($data['daily'] as $d): ?>
                    <tr><td><?= h($d['view_date']) ?></td><td><?= number_format((int)$d['views']) ?></td><td><?= number_format((int)$d['visitors']) ?></td></tr>
                <?php endforeach; endif; ?>
            </tbody></table></div>
        </div></div>

    </div></div></div>
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
        const topLabels = <?= json_encode($topLabels, JSON_UNESCAPED_UNICODE) ?>;
        const topValues = <?= json_encode($topValues) ?>;

        const traffic = new ApexCharts(document.querySelector('#trafficChart'), {
            chart: { type: 'line', height: 320, toolbar: { show: false } },
            colors: ['#3357a3', '#2b75a3'],
            stroke: { width: 3, curve: 'smooth' },
            series: [
                { name: 'Views', data: viewsData },
                { name: 'Visitors', data: visitorsData }
            ],
            xaxis: { categories: labels, labels: { rotate: -25 } },
            grid: { borderColor: '#e8edf5' },
            dataLabels: { enabled: false }
        });
        traffic.render();

        const topPages = new ApexCharts(document.querySelector('#topPagesChart'), {
            chart: { type: 'bar', height: 320, toolbar: { show: false } },
            series: [{ name: 'Views', data: topValues }],
            xaxis: { categories: topLabels },
            colors: ['#3357a3'],
            plotOptions: { bar: { horizontal: true, borderRadius: 6, barHeight: '60%' } },
            dataLabels: { enabled: false },
            grid: { borderColor: '#e8edf5' }
        });
        topPages.render();
    })();
</script>
</body>
</html>
