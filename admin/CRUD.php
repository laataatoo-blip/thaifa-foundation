<?php
session_start();
$MM_authorizedUsers = "1,2,3";
$MM_donotCheckaccess = "false";
include('./components/funcCheckSession.php');

include('./backend/classes/DatabaseManagement.class.php');
include('./backend/classes/Program.class.php');
$DB = new DatabaseManagement();
$Program = new Program();

// filters
$CurrentGradeID    = ($Program->arr_valid($_GET, 'GradeID') ?: "");
if (isset($_GET['SemesterID'])) {
    $CurrentSemesterID = $_GET['SemesterID'];
} else {
    $row_current = $DB->selectOne("SELECT SemesterID FROM semester WHERE isCurrent = 'Y' AND isActive = 'Y'");
    $CurrentSemesterID = isset($row_current['SemesterID']) ? $row_current['SemesterID'] : "";
}

// master data for filters
$rsGrade    = $DB->selectAll("SELECT * FROM grade WHERE isActive='Y' ORDER BY Sort, GradeName");
$rsSemester = $DB->selectAll("SELECT SemesterID, SemesterName, AcademicYear FROM semester WHERE isActive='Y' ORDER BY AcademicYear DESC, SemesterName DESC");

// query data
$rsSubject = [];
$sqlSubject = "SELECT s.*, la.LearningAreaName, st.SubjectTypeName, g.GradeName, c.ClassName,
                   sem.SemesterID, sem.AcademicYear, sem.SemesterName,
                   CONCAT('ปี ', (sem.AcademicYear + 543), ' เทอม ', sem.SemesterName) as SemesterDisplay,
                   CONCAT(p.PrefixName, staff.Firstname, ' ', staff.Lastname) as TeacherName
                   FROM subject s
                   LEFT JOIN class c ON s.ClassID = c.ClassID
                   LEFT JOIN grade g ON c.GradeID = g.GradeID
                   LEFT JOIN learningarea la ON s.LearningAreaID = la.LearningAreaID
                   LEFT JOIN subject_type st ON s.SubjectTypeID = st.SubjectTypeID
                   LEFT JOIN semester sem ON s.SemesterID = sem.SemesterID
                   LEFT JOIN staff ON s.StaffID = staff.StaffID
                   LEFT JOIN prefix p ON staff.PrefixID = p.PrefixID
                   WHERE s.isActive = 'Y' ";

$params = [];

if ($CurrentGradeID != "") {
    $sqlSubject .= " AND c.GradeID = :gid ";
    $params['gid'] = $CurrentGradeID;
}

if ($CurrentSemesterID != "") {
    $sqlSubject .= " AND s.SemesterID = :semid ";
    $params['semid'] = $CurrentSemesterID;
}

$sqlSubject .= " ORDER BY sem.AcademicYear DESC, sem.SemesterName DESC, g.Sort, c.ClassName, s.SubjectCode";
$rsSubject = $DB->selectAll($sqlSubject, $params);

// จัดกลุ่มข้อมูลตามภาคเรียน
$groupedSubjects = [];
foreach ($rsSubject as $subject) {
    $semesterKey = $subject['SemesterDisplay'] ?? 'ไม่ระบุภาคเรียน';
    if (!isset($groupedSubjects[$semesterKey])) {
        $groupedSubjects[$semesterKey] = [];
    }
    $groupedSubjects[$semesterKey][] = $subject;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include('./structure/head.php') ?>
    <title>School Hub | รายวิชาที่เปิดสอน</title>
    <style>
        .subject-card {
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
            background: #fff;
        }

        .subject-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .subject-code {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            color: #667eea;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }

        .subject-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .info-badge {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            color: #4a5568;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .teacher-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .credit-badge {
            background: #edf2f7;
            color: #2d3748;
            font-weight: 700;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .semester-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            box-shadow: 0 4px 6px rgba(102, 126, 234, 0.2);
        }

        .subject-type-core {
            background: #48bb78;
            color: white;
        }

        .subject-type-elective {
            background: #ed8936;
            color: white;
        }

        .subject-type-default {
            background: #718096;
            color: white;
        }

        .info-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.75rem;
        }

        .filter-section {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .stats-badge {
            background: white;
            border: 2px solid #667eea;
            color: #667eea;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
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

                    <div class="row justify-content-center">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">

                                <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                        <div>
                                            <h4 class="mb-1 text-dark fw-bold">
                                                <i class='bx bxs-book-content text-info'></i> รายวิชาที่เปิดสอน
                                            </h4>
                                            <p class="text-muted small mb-0">ดูรายวิชาที่เปิดสอนในแต่ละระดับชั้นและภาคเรียน</p>
                                        </div>
                                        <div>
                                            <span class="stats-badge">
                                                <i class='bx bx-book-bookmark'></i>
                                                ทั้งหมด <?= count($rsSubject) ?> รายวิชา
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body px-4">

                                    <!-- Filters -->
                                    <div class="filter-section">
                                        <form id="filterForm" class="row g-3 align-items-end">
                                            <div class="col-md-5">
                                                <label class="form-label fw-bold text-dark mb-2">
                                                    <i class='bx bx-layer text-primary'></i> ระดับชั้น
                                                </label>
                                                <select name="GradeID" class="form-select shadow-sm" onchange="this.form.submit()">
                                                    <option value="">ทุกระดับชั้น</option>
                                                    <?php foreach ($rsGrade as $r): ?>
                                                        <option value="<?= $r['GradeID'] ?>" <?= $r['GradeID'] == $CurrentGradeID ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($r['GradeName']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label fw-bold text-dark mb-2">
                                                    <i class='bx bx-calendar text-primary'></i> ภาคเรียน
                                                </label>
                                                <select name="SemesterID" class="form-select shadow-sm" onchange="this.form.submit()">
                                                    <option value="">ทุกภาคเรียน</option>
                                                    <?php foreach ($rsSemester as $r): ?>
                                                        <option value="<?= $r['SemesterID'] ?>" <?= $r['SemesterID'] == $CurrentSemesterID ? 'selected' : '' ?>>
                                                            ปี <?= ($r['AcademicYear'] + 543) ?> เทอม <?= $r['SemesterName'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <?php if ($CurrentGradeID || $CurrentSemesterID): ?>
                                                <div class="col-md-2">
                                                    <a href="?" class="btn btn-outline-secondary w-100 shadow-sm">
                                                        <i class='bx bx-reset'></i> ล้างตัวกรอง
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </form>
                                    </div>

                                    <!-- Subject List -->
                                    <?php if (empty($rsSubject)): ?>
                                        <div class="text-center py-5">
                                            <i class='bx bx-book-open text-muted' style="font-size: 5rem; opacity: 0.2;"></i>
                                            <h5 class="text-muted mt-3">ไม่พบข้อมูลรายวิชา</h5>
                                            <p class="text-muted small">ลองเปลี่ยนตัวกรองเพื่อค้นหารายวิชาอื่น</p>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($groupedSubjects as $semesterName => $subjects): ?>
                                            <div class="semester-header">
                                                <i class='bx bx-calendar-event' style="font-size: 1.5rem;"></i>
                                                <div>
                                                    <h5 class="mb-0 fw-bold"><?= htmlspecialchars($semesterName) ?></h5>
                                                    <small class="opacity-75"><?= count($subjects) ?> รายวิชา</small>
                                                </div>
                                            </div>

                                            <div class="row g-3 mb-4">
                                                <?php foreach ($subjects as $r): ?>
                                                    <div class="col-md-6 col-lg-4">
                                                        <div class="card subject-card h-100 shadow-sm">
                                                            <div class="card-body p-3">
                                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                                    <span class="subject-code"><?= htmlspecialchars($r['SubjectCode']) ?></span>
                                                                    <span class="credit-badge">
                                                                        <?= $r['Credit'] ?> น.ก.
                                                                    </span>
                                                                </div>

                                                                <h6 class="subject-name"><?= htmlspecialchars($r['SubjectName']) ?></h6>

                                                                <div class="info-row">
                                                                    <?php
                                                                    $typeClass = 'subject-type-default';
                                                                    if (stripos($r['SubjectTypeName'], 'พื้นฐาน') !== false) {
                                                                        $typeClass = 'subject-type-core';
                                                                    } elseif (stripos($r['SubjectTypeName'], 'เพิ่มเติม') !== false) {
                                                                        $typeClass = 'subject-type-elective';
                                                                    }
                                                                    ?>
                                                                    <span class="badge <?= $typeClass ?> rounded-pill">
                                                                        <?= htmlspecialchars($r['SubjectTypeName'] ?? '-') ?>
                                                                    </span>
                                                                </div>

                                                                <div class="info-row">
                                                                    <span class="info-badge">
                                                                        <i class='bx bx-book-reader'></i>
                                                                        <?= htmlspecialchars($r['LearningAreaName'] ?? '-') ?>
                                                                    </span>
                                                                </div>

                                                                <div class="info-row">
                                                                    <span class="info-badge">
                                                                        <i class='bx bx-door-open'></i>
                                                                        <?= $r['GradeName'] ?> ห้อง <?= htmlspecialchars($r['ClassName'] ?? '-') ?>
                                                                    </span>
                                                                </div>

                                                                <?php if ($r['TeacherName']): ?>
                                                                    <div class="mt-3">
                                                                        <span class="teacher-badge">
                                                                            <i class='bx bx-user-circle'></i>
                                                                            <?= htmlspecialchars($r['TeacherName']) ?>
                                                                        </span>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div class="mt-3">
                                                                        <span class="text-muted small">
                                                                            <i class='bx bx-user-x'></i> ยังไม่ได้กำหนดครู
                                                                        </span>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

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