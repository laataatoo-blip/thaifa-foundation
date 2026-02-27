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

$errors = [];
$success = '';

$adminId = (int)($_SESSION['AdminLogin']['AdminID'] ?? 0);
if ($adminId <= 0) {
    header('Location: logout.php');
    exit;
}

$admin = $DB->selectOne(
    "SELECT AdminID, Name, Username, Password, ProfilePic, isActive FROM admin WHERE AdminID = :id LIMIT 1",
    [':id' => $adminId]
);

if (!$admin) {
    header('Location: logout.php');
    exit;
}

$name = trim((string)($admin['Name'] ?? ''));
$username = trim((string)($admin['Username'] ?? ''));
$profilePic = trim((string)($admin['ProfilePic'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_profile') {
    $name = trim((string)($_POST['name'] ?? ''));
    $username = trim((string)($_POST['username'] ?? ''));
    $currentPassword = (string)($_POST['current_password'] ?? '');
    $newPassword = (string)($_POST['new_password'] ?? '');
    $confirmPassword = (string)($_POST['confirm_password'] ?? '');

    if ($name === '') {
        $errors[] = 'กรุณากรอกชื่อ';
    }
    if ($username === '') {
        $errors[] = 'กรุณากรอกชื่อผู้ใช้';
    }
    if (mb_strlen($username, 'UTF-8') > 20) {
        $errors[] = 'ชื่อผู้ใช้ต้องไม่เกิน 20 ตัวอักษร';
    }

    if (empty($errors)) {
        $duplicate = $DB->selectOne(
            "SELECT AdminID FROM admin WHERE Username = :username AND AdminID <> :id LIMIT 1",
            [
                ':username' => $username,
                ':id' => $adminId
            ]
        );
        if ($duplicate) {
            $errors[] = 'ชื่อผู้ใช้นี้ถูกใช้แล้ว';
        }
    }

    $isChangingPassword = ($currentPassword !== '' || $newPassword !== '' || $confirmPassword !== '');
    $newPasswordHash = '';
    $isChangingProfilePic = false;
    $newProfilePicPath = '';

    if ($isChangingPassword) {
        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            $errors[] = 'หากต้องการเปลี่ยนรหัสผ่าน กรุณากรอกข้อมูลรหัสผ่านให้ครบทุกช่อง';
        } elseif (md5($currentPassword) !== (string)$admin['Password']) {
            $errors[] = 'รหัสผ่านปัจจุบันไม่ถูกต้อง';
        } elseif (strlen($newPassword) < 6) {
            $errors[] = 'รหัสผ่านใหม่ต้องมีอย่างน้อย 6 ตัวอักษร';
        } elseif ($newPassword !== $confirmPassword) {
            $errors[] = 'ยืนยันรหัสผ่านใหม่ไม่ตรงกัน';
        } else {
            $newPasswordHash = md5($newPassword);
        }
    }

    if (empty($errors)) {
        if (isset($_FILES['profile_image']) && ($_FILES['profile_image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $fileError = (int)($_FILES['profile_image']['error'] ?? UPLOAD_ERR_NO_FILE);
            $fileSize = (int)($_FILES['profile_image']['size'] ?? 0);
            $tmpPath = (string)($_FILES['profile_image']['tmp_name'] ?? '');

            if ($fileError !== UPLOAD_ERR_OK) {
                $errors[] = 'อัปโหลดรูปโปรไฟล์ไม่สำเร็จ';
            } elseif ($fileSize <= 0 || $tmpPath === '') {
                $errors[] = 'ไม่พบไฟล์รูปโปรไฟล์ที่อัปโหลด';
            } elseif ($fileSize > 2 * 1024 * 1024) {
                $errors[] = 'รูปโปรไฟล์ต้องมีขนาดไม่เกิน 2MB';
            } else {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = $finfo ? (string)finfo_file($finfo, $tmpPath) : '';
                if ($finfo) {
                    finfo_close($finfo);
                }

                $allowedMime = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp',
                    'image/gif' => 'gif'
                ];

                if (!isset($allowedMime[$mime])) {
                    $errors[] = 'รองรับเฉพาะไฟล์รูป jpg, png, webp, gif';
                } else {
                    $uploadDirAbs = __DIR__ . '/uploads/profiles';
                    if (!is_dir($uploadDirAbs) && !mkdir($uploadDirAbs, 0775, true)) {
                        $errors[] = 'ไม่สามารถสร้างโฟลเดอร์อัปโหลดรูปโปรไฟล์ได้';
                    } else {
                        $ext = $allowedMime[$mime];
                        $newFileName = 'admin_' . $adminId . '_' . uniqid('', true) . '.' . $ext;
                        $targetAbs = $uploadDirAbs . '/' . $newFileName;

                        if (!move_uploaded_file($tmpPath, $targetAbs)) {
                            $errors[] = 'ไม่สามารถบันทึกรูปโปรไฟล์ได้';
                        } else {
                            $isChangingProfilePic = true;
                            $newProfilePicPath = 'uploads/profiles/' . $newFileName;
                        }
                    }
                }
            }
        }
    }

    if (empty($errors)) {
        $oldProfilePic = $profilePic;

        $sql = "UPDATE admin SET Name = :name, Username = :username";
        $param = [
            ':name' => $name,
            ':username' => $username,
            ':id' => $adminId
        ];

        if ($isChangingPassword) {
            $sql .= ", Password = :password";
            $param[':password'] = $newPasswordHash;
        }

        if ($isChangingProfilePic) {
            $sql .= ", ProfilePic = :profile_pic";
            $param[':profile_pic'] = $newProfilePicPath;
        }

        $sql .= " WHERE AdminID = :id LIMIT 1";
        $DB->query($sql, $param);

        if ($isChangingProfilePic && $oldProfilePic !== '') {
            $oldAbs = __DIR__ . '/' . ltrim($oldProfilePic, '/');
            if (is_file($oldAbs)) {
                @unlink($oldAbs);
            }
        }

        $admin = $DB->selectOne(
            "SELECT AdminID, Name, Username, Password, ProfilePic, isActive FROM admin WHERE AdminID = :id LIMIT 1",
            [':id' => $adminId]
        );

        if ($admin) {
            $_SESSION['AdminLogin'] = array_merge($_SESSION['AdminLogin'], $admin);
            $profilePic = trim((string)($admin['ProfilePic'] ?? ''));
        }
        $success = 'บันทึกข้อมูลโปรไฟล์เรียบร้อยแล้ว';
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include('./structure/head.php') ?>
    <title>โปรไฟล์ผู้ดูแลระบบ</title>
</head>

<body>
<div class="wrapper">
    <?php include('./components/sidebar.php') ?>
    <?php include('./components/navbar.php') ?>

    <div class="page-wrapper">
        <div class="page-content-wrapper page-content-margin-padding">
            <div class="page-content page-content-margin-padding">

                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <div class="breadcrumb-title pe-3">บัญชีผู้ใช้</div>
                    <div class="ps-3">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0">
                                <li class="breadcrumb-item"><a href="index.php"><i class="bx bx-home-alt"></i></a></li>
                                <li class="breadcrumb-item active" aria-current="page">โปรไฟล์</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $er): ?>
                                <li><?= h($er) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($success !== ''): ?>
                    <div class="alert alert-success"><?= h($success) ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4 class="mb-0">แก้ไขโปรไฟล์</h4>
                                </div>
                                <hr />

                                <form method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="update_profile">

                                    <div class="mb-3">
                                        <label class="form-label">ชื่อ</label>
                                        <input type="text" name="name" class="form-control" required value="<?= h($name) ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">ชื่อผู้ใช้</label>
                                        <input type="text" name="username" class="form-control" maxlength="20" required value="<?= h($username) ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">รูปโปรไฟล์</label>
                                        <input type="file" name="profile_image" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif">
                                        <small class="text-muted">รองรับไฟล์ jpg, png, webp, gif (ไม่เกิน 2MB)</small>
                                    </div>

                                    <hr>
                                    <h6 class="mb-3">เปลี่ยนรหัสผ่าน (ไม่บังคับ)</h6>

                                    <div class="mb-3">
                                        <label class="form-label">รหัสผ่านปัจจุบัน</label>
                                        <input type="password" name="current_password" class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">รหัสผ่านใหม่</label>
                                        <input type="password" name="new_password" class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">ยืนยันรหัสผ่านใหม่</label>
                                        <input type="password" name="confirm_password" class="form-control">
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
                                        <a href="index.php" class="btn btn-outline-secondary">กลับหน้าหลัก</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h5 class="mb-0">ข้อมูลบัญชี</h5>
                                </div>
                                <hr>
                                <div class="text-center mb-3">
                                    <?php
                                        $profileImgPath = './assets/images/icons/user.png';
                                        if ($profilePic !== '') {
                                            $profileAbs = __DIR__ . '/' . ltrim($profilePic, '/');
                                            if (is_file($profileAbs)) {
                                                $profileImgPath = './' . ltrim($profilePic, '/');
                                            }
                                        }
                                    ?>
                                    <img src="<?= h($profileImgPath) ?>" alt="profile image" class="rounded-circle" style="width:96px;height:96px;object-fit:cover;border:1px solid #e5e7eb;">
                                </div>
                                <p class="mb-2"><strong>Admin ID:</strong> <?= (int)$admin['AdminID'] ?></p>
                                <p class="mb-2"><strong>สถานะ:</strong>
                                    <span class="badge <?= ($admin['isActive'] === 'Y') ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= ($admin['isActive'] === 'Y') ? 'ใช้งาน' : 'ปิดใช้งาน' ?>
                                    </span>
                                </p>
                                <p class="text-muted mb-0" style="font-size:12px;">หมายเหตุ: หากไม่ต้องการเปลี่ยนรหัสผ่าน ให้ปล่อยช่องรหัสผ่านว่างไว้</p>
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
</body>
</html>
