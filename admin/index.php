<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$MM_authorizedUsers = "Admin";
$MM_donotCheckaccess = "false";
include('./components/funcCheckSession.php');
include(__DIR__ . '/../backend/classes/DatabaseManagement.class.php');

$DB = new DatabaseManagement();

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
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include('./structure/head.php') ?>
    <title>ThaiFa Foundation Admin</title>
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

                <div class="alert alert-primary border-0 bg-primary bg-opacity-10">
                    <div class="d-flex align-items-center gap-2">
                        <i class='bx bx-user-circle fs-4 text-primary'></i>
                        <div>
                            <div class="fw-bold">ยินดีต้อนรับ, <?= h($adminName) ?></div>
                            <div class="small text-muted">คุณสามารถจัดการข่าวจากเมนู News ด้านซ้าย</div>
                        </div>
                    </div>
                </div>

                <div class="row row-cols-1 row-cols-md-3 g-3">
                    <div class="col">
                        <div class="card radius-10 border-0 border-start border-primary border-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="mb-1 text-secondary">ข่าวทั้งหมด</p>
                                        <h4 class="mb-0"><?= $totalNews ?></h4>
                                    </div>
                                    <div class="widgets-icons-2 rounded-circle bg-gradient-scooter text-white"><i class='bx bx-news'></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card radius-10 border-0 border-start border-success border-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="mb-1 text-secondary">ข่าวที่แสดงผล</p>
                                        <h4 class="mb-0"><?= $visibleNews ?></h4>
                                    </div>
                                    <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white"><i class='bx bx-show'></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card radius-10 border-0 border-start border-warning border-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="mb-1 text-secondary">รูปข่าวทั้งหมด</p>
                                        <h4 class="mb-0"><?= $totalImages ?></h4>
                                    </div>
                                    <div class="widgets-icons-2 rounded-circle bg-gradient-blooker text-white"><i class='bx bx-image'></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">เมนูลัด</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="news_list.php" class="btn btn-primary"><i class='bx bx-list-ul me-1'></i> รายการข่าว</a>
                            <a href="news_create.php" class="btn btn-outline-primary"><i class='bx bx-plus me-1'></i> เพิ่มข่าวใหม่</a>
                            <a href="profile.php" class="btn btn-outline-secondary"><i class='bx bx-user me-1'></i> โปรไฟล์</a>
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
</body>
</html>
