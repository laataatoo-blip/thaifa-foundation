<!--header-->
<header class="top-header">
    <nav class="navbar navbar-expand">
        <div class="left-topbar d-flex align-items-center">
            <span class="toggle-btn"><i class="bx bx-menu"></i></span>
        </div>

        <div class="right-topbar ms-auto">
            <ul class="navbar-nav">
                <li class="nav-item dropdown dropdown-user-profile">
                    <span class="nav-link dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown" style="cursor: pointer;">
                        <div class="d-flex user-box align-items-center">
                            <div class="user-info">
                                <?php
                                $user_name = "";
                                $user_id   = "";

                                if (isset($_SESSION['StaffLogin']) && is_array($_SESSION['StaffLogin'])) {
                                    $first = $_SESSION['StaffLogin']['Firstname'] ?? '';
                                    $last  = $_SESSION['StaffLogin']['Lastname'] ?? '';
                                    $user_name = trim($first . ' ' . $last);
                                    $user_id   = $_SESSION['StaffLogin']['StaffID'] ?? '';
                                } elseif (isset($_SESSION['AdminLogin']) && is_array($_SESSION['AdminLogin'])) {
                                    $fullName = trim((string)($_SESSION['AdminLogin']['FullName'] ?? ''));
                                    $adminName = trim((string)($_SESSION['AdminLogin']['AdminName'] ?? ''));
                                    $name = trim((string)($_SESSION['AdminLogin']['Name'] ?? ''));
                                    $username = trim((string)($_SESSION['AdminLogin']['Username'] ?? ''));

                                    $user_name = $fullName !== '' ? $fullName :
                                        ($adminName !== '' ? $adminName :
                                        ($name !== '' ? $name : $username));
                                    $user_id   = $_SESSION['AdminLogin']['AdminID'] ?? '';
                                } elseif (isset($_SESSION['ParentLogin']) && is_array($_SESSION['ParentLogin'])) {
                                    $first = $_SESSION['ParentLogin']['ParentName'] ?? '';
                                    $last  = $_SESSION['ParentLogin']['ParentSurname'] ?? '';
                                    $user_name = trim($first . ' ' . $last);
                                    $user_id   = $_SESSION['ParentLogin']['ParentID'] ?? '';
                                } elseif (isset($_SESSION['StudentLogin']) && is_array($_SESSION['StudentLogin'])) {
                                    $first = $_SESSION['StudentLogin']['StudentName'] ?? '';
                                    $last  = $_SESSION['StudentLogin']['StudentSurname'] ?? '';
                                    $user_name = trim($first . ' ' . $last);
                                    $user_id   = $_SESSION['StudentLogin']['StudentID'] ?? '';
                                }
                                ?>
                                <p class="user-name mb-0"><?php echo htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8'); ?></p>
                                <!-- <p class="designattion mb-0"><?php echo htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8'); ?></p> -->
                            </div>

                            <?php
                            $img_path = "./assets/images/icons/user.png";
                            $staffPic = $_SESSION['StaffLogin']['StaffPic'] ?? '';
                            $adminPic = $_SESSION['AdminLogin']['ProfilePic'] ?? '';

                            if ($staffPic !== '') {
                                $staffPicRel = ltrim((string)$staffPic, '/');
                                if ($staffPicRel !== '' && is_file(__DIR__ . '/../' . $staffPicRel)) {
                                    $img_path = "./" . $staffPicRel;
                                }
                            } elseif ($adminPic !== '') {
                                $adminPicRel = ltrim((string)$adminPic, '/');
                                if ($adminPicRel !== '' && is_file(__DIR__ . '/../' . $adminPicRel)) {
                                    $img_path = "./" . $adminPicRel;
                                }
                            }
                            ?>
                            <img src="<?php echo htmlspecialchars($img_path, ENT_QUOTES, 'UTF-8'); ?>" class="user-img" alt="user avatar">
                        </div>
                    </span>

                    <div class="dropdown-menu p-0 m-0">
                        <?php if (isset($_SESSION['StaffLogin']) || isset($_SESSION['AdminLogin'])) : ?>
                            <a class="dropdown-item" href="profile.php">Profile</a>
                        <?php endif; ?>
                        <a class="btn btn-sm btn-danger w-100" href="./logout.php">
                            <i class="fadeIn animated bx bx-log-out"></i><span>ออกจากระบบ</span>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>
<!--end header-->
