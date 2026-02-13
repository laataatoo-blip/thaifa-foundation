<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$MM_authorizedUsers = "1,2,3,4";
$MM_donotCheckaccess = "false";
include('./components/funcCheckSession.php');

include('./backend/classes/DatabaseManagement.class.php');
include('./backend/classes/Program.class.php');
$DB = new DatabaseManagement();
$Program = new Program();

// ===== Dashboard Data Queries =====

// 1. Total Students
$totalStudents = $DB->selectOne("SELECT COUNT(*) as total FROM student WHERE isActive='Y'")['total'] ?? 0;

// 2. Gender Breakdown
$genderData = $DB->selectAll("SELECT GenderID, COUNT(*) as cnt FROM student WHERE isActive='Y' GROUP BY GenderID");
$maleCount = 0;
$femaleCount = 0;
foreach ($genderData as $g) {
    if ($g['GenderID'] == 1) $maleCount = $g['cnt'];
    if ($g['GenderID'] == 2) $femaleCount = $g['cnt'];
}

// 3. Total Staff
$totalStaff = $DB->selectOne("SELECT COUNT(*) as total FROM staff WHERE isActive='Y'")['total'] ?? 0;

// 4. Today's Attendance
$today = date('Y-m-d');
// Use attendance table (Subject-based) as proxy for daily attendance for now
// Ideally should filter by Homeroom (SubjectTypeID=6), but for POC we count all check-ins
$attendanceData = $DB->selectAll("SELECT Status, COUNT(DISTINCT MapSubjectStudentID) as cnt FROM attendance WHERE AttendanceDate = :today AND isActive='Y' GROUP BY Status", ['today' => $today]);
$presentCount = 0;
$lateCount = 0;
$absentCount = 0;
$leaveCount = 0;
foreach ($attendanceData as $a) {
    switch ($a['Status']) {
        case 'P':
            $presentCount = $a['cnt'];
            break; // มา
        case 'L':
            $lateCount = $a['cnt'];
            break;    // สาย
        case 'A':
            $absentCount = $a['cnt'];
            break;  // ขาด
        case 'S':
            $leaveCount = $a['cnt'];
            break;   // ลา
    }
}
$totalAttendance = $presentCount + $lateCount + $absentCount + $leaveCount;
$attendanceRate = $totalAttendance > 0 ? (($presentCount + $lateCount) / $totalAttendance) * 100 : 0;
// If no attendance today, default to 100% or 0%? Let's show 0% to indicate no data
if ($totalAttendance == 0) $attendanceRate = 0;

// 5. Students by Grade
$gradeData = $DB->selectAll("
    SELECT g.GradeID, g.GradeName, COUNT(DISTINCT mcs.StudentID) as student_count
    FROM grade g
    LEFT JOIN class c ON g.GradeID = c.GradeID AND c.isActive='Y'
    LEFT JOIN map_class_student mcs ON c.ClassID = mcs.ClassID AND mcs.isActive='Y'
    WHERE g.isActive='Y'
    GROUP BY g.GradeID
    ORDER BY g.GradeID
");

// Thai date
$thaiMonths = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
$thaiDate = date('j') . ' ' . $thaiMonths[(int)date('n')] . ' ' . (date('Y') + 543);

// 6. Total Classes (for Class Density)
$totalClasses = $DB->selectOne("SELECT COUNT(*) as total FROM class WHERE AcademicYear = :year AND isActive='Y'", ['year' => (date('Y') + 543)])['total'] ?? 0;
// Fallback if current year has no classes, check previous? Or just show 0.
if ($totalClasses == 0) {
    // Try to get distinct academic year from class table if current year is empty, just for safety or leave as 0
}

// 7. Real-time Activity Feed (Top 5 Recent Attendance)
// Path: attendance -> map_subject_student -> map_class_student -> student
$recentActivity = $DB->selectAll("
    SELECT 
        s.Name, s.Lastname, s.StudentPic, s.GenderID,
        a.Status, a.InsertDate, a.AttendanceDate
    FROM attendance a
    JOIN map_subject_student mss ON a.MapSubjectStudentID = mss.MapSubjectStudentID
    JOIN map_class_student mcs ON mss.MapClassStudentID = mcs.MapClassStudentID
    JOIN student s ON mcs.StudentID = s.StudentID
    WHERE a.AttendanceDate = :today AND a.isActive = 'Y'
    ORDER BY a.InsertDate DESC
    LIMIT 5
", ['today' => $today]);

// Helper for Thai time "H:i น." (Convert UTC to Thai Time)
function formatThaiTime($datetime)
{
    if (!$datetime) return '-';
    $dt = new DateTime($datetime, new DateTimeZone('UTC'));
    $dt->setTimezone(new DateTimeZone('Asia/Bangkok'));
    return $dt->format('H:i') . ' น.';
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <?php include('./structure/head.php') ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./styles/dashboard.css">
    <title>หน้าแรก</title>
</head>

<body>
    <div class="wrapper">
        <?php include('./components/sidebar.php') ?>
        <?php include('./components/navbar.php') ?>
        <div class="page-wrapper">
            <div class="page-content-wrapper page-content-margin-padding">
                <div class="page-content page-content-margin-padding">

                    <!-- Section Title -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="mb-1 fw-bold text-dark"><i class='bx bxs-home text-primary'></i> ภาพรวมโรงเรียน</h4>
                            <p class="text-muted small mb-0">ข้อมูลประจำวันที่: <span class="fw-bold text-primary"><?= $thaiDate ?></span></p>
                        </div>
                    </div>

                    <!-- Zone 1: Key Metrics & Class Density -->
                    <div class="row g-4 mb-4">
                        <!-- Total Students (Gradient Primary) -->
                        <div class="col-md-6 col-lg-3">
                            <div class="metric-card bg-gradient-primary">
                                <div class="metric-icon-box" style="background: rgba(255,255,255,0.2); color: white;"><i class='bx bxs-graduation'></i></div>
                                <div class="metric-title" style="color: rgba(255,255,255,0.9) !important;">นักเรียนทั้งหมด</div>
                                <div class="d-flex align-items-baseline gap-2">
                                    <div class="metric-value" style="color: white !important;"><?= number_format($totalStudents) ?></div>
                                    <span class="fs-6" style="color: rgba(255,255,255,0.8) !important;">คน</span>
                                </div>
                                <div class="sparkline-trend up">
                                    <i class='bx bx-trending-up'></i> เพิ่มขึ้นจากปีที่แล้ว (Real)
                                </div>
                            </div>
                        </div>

                        <!-- Average Class Size (Gradient Success) -->
                        <div class="col-md-6 col-lg-3">
                            <div class="metric-card bg-gradient-success">
                                <div class="metric-icon-box" style="background: rgba(255,255,255,0.2); color: white;"><i class='bx bxs-school'></i></div>
                                <div class="metric-title" style="color: rgba(255,255,255,0.9) !important;">ขนาดห้องเรียนเฉลี่ย</div>
                                <div class="d-flex align-items-baseline gap-2">
                                    <div class="metric-value" style="color: white !important;">
                                        <?= $totalClasses > 0 ? round($totalStudents / $totalClasses) : 0 ?>
                                    </div>
                                    <span class="fs-6" style="color: rgba(255,255,255,0.8) !important;">คน/ห้อง</span>
                                </div>
                                <div class="sparkline-trend up">
                                    <i class='bx bx-info-circle'></i> จากทั้งหมด <?= number_format($totalClasses) ?> ห้อง
                                </div>
                            </div>
                        </div>

                        <!-- Total Staff (White) -->
                        <div class="col-md-6 col-lg-3">
                            <div class="metric-card">
                                <div class="metric-icon-box info"><i class='bx bxs-user-badge'></i></div>
                                <div class="metric-title">บุคลากรทั้งหมด</div>
                                <div class="metric-value"><?= number_format($totalStaff) ?> <span class="fs-6 text-muted fw-normal">คน</span></div>
                            </div>
                        </div>

                        <!-- Teacher:Student Ratio (White) -->
                        <div class="col-md-6 col-lg-3">
                            <div class="metric-card">
                                <div class="metric-icon-box purple"><i class='bx bx-group'></i></div>
                                <div class="metric-title">อัตราส่วน ครู:นักเรียน</div>
                                <div class="metric-value">1:<?= $totalStaff > 0 ? round($totalStudents / $totalStaff) : 0 ?></div>
                            </div>
                        </div>
                    </div>

                </div>

                <br />

            </div>
        </div>
    </div>
    <div class="overlay toggle-btn-mobile"></div>
    <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>


    <!-- Attendance List Modal -->
    <div class="modal fade" id="attendanceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 py-3 px-4">
                    <h5 class="modal-title fw-bold"><i class='bx bx-list-check text-primary me-2'></i>รายชื่อการมาเรียนวันนี้</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <?php
                    // Query Full Attendance
                    $fullAttendance = $DB->selectAll("
                        SELECT 
                            s.StudentCode, s.Name, s.Lastname, s.StudentPic,
                            c.ClassName, g.GradeName,
                            a.Status, a.InsertDate
                        FROM attendance a
                        JOIN map_subject_student mss ON a.MapSubjectStudentID = mss.MapSubjectStudentID
                        JOIN map_class_student mcs ON mss.MapClassStudentID = mcs.MapClassStudentID
                        JOIN student s ON mcs.StudentID = s.StudentID
                        JOIN class c ON mcs.ClassID = c.ClassID
                        LEFT JOIN grade g ON c.GradeID = g.GradeID
                        WHERE a.AttendanceDate = :today AND a.isActive = 'Y'
                        ORDER BY g.GradeID ASC, c.ClassName ASC, s.StudentCode ASC
                    ", ['today' => $today]);
                    ?>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3 rounded-start">เวลา</th>
                                    <th>รหัสนักเรียน</th>
                                    <th>ชื่อ-นามสกุล</th>
                                    <th class="text-center rounded-end">สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($fullAttendance)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">ยังไม่มีข้อมูลการเช็คชื่อวันนี้</td>
                                    </tr>
                                <?php else: ?>
                                    <?php
                                    $currentGroup = '';
                                    foreach ($fullAttendance as $std):
                                        $groupName = $std['GradeName'] . ' ห้อง ' . $std['ClassName'];

                                        // Section Header
                                        if ($groupName != $currentGroup) {
                                            $currentGroup = $groupName;
                                            echo '<tr><td colspan="4" class="bg-light fw-bold text-primary px-3 py-2"><i class="bx bxs-school me-2"></i>' . $groupName . '</td></tr>';
                                        }

                                        $stClass = '';
                                        $stText = '';
                                        switch ($std['Status']) {
                                            case 'P':
                                                $stClass = 'bg-success bg-opacity-10 text-white';
                                                $stText = 'มาเรียน';
                                                break;
                                            case 'L':
                                                $stClass = 'bg-warning bg-opacity-10 text-dark';
                                                $stText = 'สาย';
                                                break;
                                            case 'A':
                                                $stClass = 'bg-danger bg-opacity-10 text-white';
                                                $stText = 'ขาด';
                                                break;
                                            case 'S':
                                                $stClass = 'bg-info bg-opacity-10 text-white';
                                                $stText = 'ลา';
                                                break;
                                        }
                                    ?>
                                        <tr>
                                            <td class="ps-3 text-muted small"><?= formatThaiTime($std['InsertDate']) ?></td>
                                            <td class="fw-bold text-primary"><?= $std['StudentCode'] ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-2">
                                                        <?php if (!empty($std['StudentPic']) && file_exists('./uploads/student_pic/' . $std['StudentPic'])): ?>
                                                            <img src="./uploads/student_pic/<?= $std['StudentPic'] ?>" class="rounded-circle" width="32" height="32">
                                                        <?php else: ?>
                                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-secondary" style="width: 32px; height: 32px;"><i class='bx bx-user'></i></div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <?= $std['Name'] . ' ' . $std['Lastname'] ?>
                                                </div>
                                            </td>
                                            <td class="text-center"><span class="badge <?= $stClass ?>"><?= $stText ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php include('./structure/script.php') ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        // Pass PHP data to JavaScript
        window.dashboardData = {
            maleCount: <?= $maleCount ?>,
            femaleCount: <?= $femaleCount ?>,
            attendanceRate: <?= round($attendanceRate, 1) ?>,
            gradeData: <?= json_encode(array_map(function ($g) {
                            return ['gradeName' => $g['GradeName'], 'studentCount' => (int)$g['student_count']];
                        }, $gradeData)) ?>
        };
    </script>
    <script src="./scripts/dashboard.js"></script>
</body>

</html>