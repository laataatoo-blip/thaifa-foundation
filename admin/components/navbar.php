<!--header-->
<header class="top-header">
    <nav class="navbar navbar-expand">
        <div class="left-topbar d-flex align-items-center">
            <span href="javascript:;" class="toggle-btn"> <i class="bx bx-menu"></i></span>
        </div>
        <div class="right-topbar ms-auto">
            <ul class="navbar-nav">
                <li class="nav-item dropdown dropdown-user-profile">
                    <span class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;" data-bs-toggle="dropdown" style="cursor: pointer;">
                        <div class="d-flex user-box align-items-center">
                            <div class="user-info">
                                <?php 
                                    $user_name = "";
                                    $user_id = "";
                                    if(isset($_SESSION['StaffLogin'])) {
                                        $user_name = "{$_SESSION['StaffLogin']['Firstname']} {$_SESSION['StaffLogin']['Lastname']}";
                                        $user_id = "{$_SESSION['StaffLogin']['StaffID']}";
                                    }
                                    if(isset($_SESSION['ParentLogin'])) {
                                        $user_name = "{$_SESSION['ParentLogin']['ParentName']} {$_SESSION['ParentLogin']['ParentSurname']}";
                                        $user_id = "{$_SESSION['ParentLogin']['ParentID']}";
                                    }
                                    if(isset($_SESSION['StudentLogin'])) {
                                        $user_name = "{$_SESSION['StudentLogin']['StudentName']} {$_SESSION['StudentLogin']['StudentSurname']}";
                                        $user_id = "{$_SESSION['StudentLogin']['StudentID']}";
                                    }
                                ?>
                                <p class="user-name mb-0"><?php echo $user_name ?></p>
                                <!-- <p class="designattion mb-0"><?php echo $user_id ?></p> -->
                            </div>
                            <?php 
                                $img_path = "./assets/images/icons/user.png";
                                if(isset($_SESSION['StaffLogin']['StaffPic']) && file_exists("../{$_SESSION['StaffLogin']['StaffPic']}")) {
                                    $img_path = "../{$_SESSION['StaffLogin']['StaffPic']}";
                                }
                            ?>
                            <img src="<?php echo "{$img_path}" ?>" class="user-img" alt="user avatar">
                        </div>
                    </span>
                    <div class="dropdown-menu p-0 m-0">
                        <?php if(isset($_SESSION['StaffLogin'])) : ?>
                            <a class="dropdown-item" href="profile.php">Profile</a>
                        <?php endif ?>
                        <a class="btn btn-sm btn-danger w-100" href="./logout.php"><i class="fadeIn animated bx bx-log-out"></i><span>ออกจากระบบ</span></a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>
<!--end header-->