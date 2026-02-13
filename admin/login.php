<?
echo $_POST['username'];
echo $_POST['password'];
?>




<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// --- 1. Auto Login Logic ---
// ตรวจสอบว่ามี Session เดิมอยู่แล้วหรือไม่ ถ้ามีให้ Redirect ไปเลย ไม่ต้อง Query ใหม่
if (isset($_SESSION['StaffLogin']['Username']) && isset($_SESSION['StaffLoginType']['SchoolHub'])) {
    header("Location: index.php");
    exit;
}

// --- 2. Handle Login Submission ---
$state = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['BtnSubmit'])) {
    
    include('./backend/classes/DatabaseManagement.class.php');
    // include('./backend/classes/Database.class.php'); // ปกติ DatabaseManagement มักจะ include Database มาแล้ว หรือ inherit มา
    $DB = new DatabaseManagement();

    $username = trim($_POST['username']);
    $password = md5(trim($_POST['password'])); // *แนะนำเปลี่ยนเป็น password_verify ในอนาคต

    $sql = "SELECT s.*, ut.UserTypeName 
            FROM staff s 
            INNER JOIN usertype ut ON s.UserTypeID = ut.UserTypeID 
            WHERE s.Username = :username 
              AND s.Password = :password
              AND s.isActive = 'Y'";
    
    $param = [
        ':username' => $username,
        ':password' => $password
    ];

    $StaffLogin = $DB->selectOne($sql, $param);

    if ($StaffLogin) {
        // Login Success
        $_SESSION['StaffLogin'] = $StaffLogin;
        $_SESSION['StaffLoginType']['SchoolHub'] = $StaffLogin['UserTypeID'];

        // Redirect
        $Redirect = "index.php";
        if (isset($_GET['accesscheck'])) {
            $Redirect = htmlspecialchars(urldecode($_GET['accesscheck']), ENT_QUOTES, "UTF-8");
        }
        header("Location: {$Redirect}");
        exit;
    } else {
        // Login Failed
        $state = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Hub | Login</title>
    <link rel="icon" href="./assets/images/SchoolHubLogo.png" type="image/png" />
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #f0f2f5; /* สีพื้นหลังสบายตา */
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            background: #fff;
            overflow: hidden;
        }
        .brand-logo {
            width: 80px;
            height: auto;
            margin-bottom: 1rem;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
            padding: 10px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
        }
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #dee2e6;
        }
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
        }
        .input-group-text {
            border-radius: 0 8px 8px 0;
            cursor: pointer;
            background-color: #fff;
        }
        /* Floating Label Adjustments */
        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            color: #0d6efd;
            transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card login-card p-4">
                    <div class="card-body">
                        
                        <div class="text-center mb-4">
                            <?php if(file_exists('./assets/images/SchoolHubLogo.png')): ?>
                                <img src="./assets/images/SchoolHubLogo.png" alt="Logo" class="brand-logo">
                            <?php else: ?>
                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class='bx bxs-school fs-1'></i>
                                </div>
                            <?php endif; ?>
                            
                            <h4 class="fw-bold text-dark">Thaifa FD Admin</h4>
                            <p class="text-muted small">สำหรับแอดมินมูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน</p>
                        </div>

                        <?php if (isset($state) && $state == 'error') : ?>
                            <div class="alert alert-danger d-flex align-items-center mb-3" role="alert">
                                <i class='bx bxs-error-circle me-2 fs-4'></i>
                                <div>
                                    ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง
                                </div>
                            </div>
                        <?php endif ?>

                        <form method="POST" action="" class="needs-validation" novalidate>
                            
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required autofocus value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                                <label for="username"><i class='bx bxs-user me-1'></i> ชื่อผู้ใช้</label>
                            </div>

                            <div class="input-group mb-4">
                                <div class="form-floating flex-grow-1">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" style="border-radius: 8px 0 0 8px;" required>
                                    <label for="password"><i class='bx bxs-lock-alt me-1'></i> รหัสผ่าน</label>
                                </div>
                                <span class="input-group-text border-start-0" id="togglePassword">
                                    <i class='bx bx-show fs-5 text-muted'></i>
                                </span>
                            </div>

                            <div class="d-grid gap-2">
                                <button class="btn btn-primary btn-lg" id="BtnSubmit" type="submit" name="BtnSubmit">
                                    <i class='bx bx-log-in-circle me-2'></i> เข้าสู่ระบบ
                                </button>
                            </div>

                        </form>

                        <div class="text-center mt-4">
                            <small class="text-muted">© 2026 มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน (THAIFA Foundation). สงวนลิขสิทธิ์.</small>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle Password Visibility
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const icon = togglePassword.querySelector('i');

        togglePassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // toggle the icon
            if(type === 'text'){
                icon.classList.remove('bx-show');
                icon.classList.add('bx-hide');
            } else {
                icon.classList.remove('bx-hide');
                icon.classList.add('bx-show');
            }
        });

        // Bootstrap Validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
</body>
</html>