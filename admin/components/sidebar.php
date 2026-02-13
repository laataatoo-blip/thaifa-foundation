<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div class="">
            <img src="./assets/images/SchoolHubLogo.png" class="logo-icon-2" alt="Logo"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" />
            <i class='bx bxs-school logo-icon-2 text-danger' style="display:none; font-size: 2rem;"></i>
        </div>
        <div>
            <h4 class="logo-text text-danger"><strong>School Hub</strong></h4>
        </div>
        <a href="javascript:;" class="toggle-btn ms-auto"> <i class="bx bx-menu"></i></a>
    </div>

    <ul class="metismenu" id="menu">
        <?php if (isset($_SESSION['StaffLogin'])) : ?>

            <li>
                <a href="./index.php">
                    <div class="parent-icon text-danger"><i class="bx bx-home-circle"></i></div>
                    <div class="menu-title">หน้าแรก</div>
                </a>
            </li>

            <?php
            // --- เมนูสำหรับ Admin / เจ้าหน้าที่ (UserType 1, 2, 3) ---
            if (in_array($_SESSION['StaffLoginType']['SchoolHub'], array(1, 2, 3))) :
            ?>
                <li class="menu-label">งานทะเบียน</li>
                <li>
                    <a href="./semester.php">
                        <div class="parent-icon"><i class="bx bx-calendar"></i></div>
                        <div class="menu-title">ภาคการศึกษา</div>
                    </a>
                </li>
                <li>
                    <a href="./grade.php">
                        <div class="parent-icon"><i class="bx bx-layer"></i></div>
                        <div class="menu-title">ระดับชั้น</div>
                    </a>
                </li>
                <li>
                    <a href="./subject.php">
                        <div class="parent-icon"><i class="bx bx-book-content"></i></div>
                        <div class="menu-title">รายวิชา</div>
                    </a>
                </li>
                <li>
                <li>
                    <a href="./class.php">
                        <div class="parent-icon"><i class="bx bx-door-open"></i></div>
                        <div class="menu-title">ห้องเรียน</div>
                    </a>
                </li>

                <!-- <a href="./mapSubject.php">
                        <div class="parent-icon"><i class="bx bx-map-alt"></i></div>
                        <div class="menu-title">หลักสูตร</div>
                    </a> -->
                </li>

                <li class="menu-label">ข้อมูลบุคคล</li>
                <li><a href="./staff.php">
                        <div class="parent-icon"><i class="bx bx-id-card"></i></div>
                        <div class="menu-title">บุคลากร</div>
                    </a></li>
                <li><a href="./student.php">
                        <div class="parent-icon"><i class="bx bx-user"></i></div>
                        <div class="menu-title">นักเรียน</div>
                    </a></li>
            <?php endif; ?>

            <li class="menu-label">ครูผู้สอน</li>
            <li>
                <a href="./teacherHomeroom.php">
                    <div class="parent-icon"><i class='bx bx-chalkboard'></i></div>
                    <div class="menu-title">ครูประจำชั้น</div>
                </a>
            </li>
            <li>
                <a href="./teacherSubject.php">
                    <div class="parent-icon"><i class='bx bx-book-reader'></i></div>
                    <div class="menu-title">ครูประจำวิชา</div>
                </a>
            </li>




            <?php if (isset($_GET['need-report']) || true) : ?>
                <li class="menu-label">รายงาน</li>
                <li><a href="./semesterReport.php">
                        <div class="parent-icon text-success"><i class="bx bx-chart"></i></div>
                        <div class="menu-title">รายงานประจำภาค</div>
                    </a></li>
                <li><a href="./monthlyReport.php">
                        <div class="parent-icon text-success"><i class="bx bx-bar-chart-alt-2"></i></div>
                        <div class="menu-title">รายงานประจำเดือน</div>
                    </a></li>
            <?php endif ?>

        <?php endif ?>
    </ul>
</div>