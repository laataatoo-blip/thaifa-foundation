<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$MM_authorizedUsers = "Admin";
$MM_donotCheckaccess = "false";
include('./components/funcCheckSession.php');

include_once(__DIR__ . '/../backend/classes/TeamManagement.class.php');

$team = new TeamManagement();
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$errors = [];
$success = '';
$groups = TeamManagement::groupLabels();
$activeGroup = trim((string)($_GET['group'] ?? ''));
if ($activeGroup !== '' && !isset($groups[$activeGroup])) {
    $activeGroup = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'save_member') {
            $id = (int)($_POST['member_id'] ?? 0);
            $team->saveMember($id, $_POST);
            $success = 'บันทึกข้อมูลบุคลากรเรียบร้อย';
        }
        if ($action === 'delete_member') {
            $team->deleteMember((int)($_POST['member_id'] ?? 0));
            $success = 'ลบข้อมูลบุคลากรเรียบร้อย';
        }
        if ($action === 'apply_template') {
            $inserted = $team->applyTemplateMembers(true);
            $success = 'โหลดเทมเพลตบุคลากรเรียบร้อย (' . (int)$inserted . ' รายการ)';
        }
    } catch (Throwable $e) {
        $errors[] = $e->getMessage();
    }
}

$members = $team->listMembers($activeGroup, false);
$editId = (int)($_GET['edit_id'] ?? 0);
$edit = $editId > 0 ? $team->getMemberById($editId) : null;
if (!$edit) {
    $edit = ['id'=>0,'group_key'=>($activeGroup !== '' ? $activeGroup : 'committee'),'member_name'=>'','member_title'=>'','member_bio'=>'','photo_url'=>'','sort_order'=>0,'is_active'=>1];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include('./structure/head.php') ?>
    <title>จัดการบุคลากรมูลนิธิ</title>
    <style>
        .team-action-cell { min-width: 160px; }
        .team-member-title { max-width: 320px; }
        .team-group-menu {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        .team-group-link {
            border: 1px solid #d9e7ef;
            background: #fff;
            color: #303a56;
            border-radius: 999px;
            padding: .45rem .95rem;
            text-decoration: none;
            font-size: .9rem;
            font-weight: 500;
        }
        .team-group-link.active {
            background: #233882;
            color: #fff;
            border-color: #233882;
        }
        .team-group-select {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .45rem;
        }
        .team-group-select input { display: none; }
        .team-group-select label {
            border: 1px solid #d9e7ef;
            border-radius: 10px;
            text-align: center;
            padding: .45rem .55rem;
            cursor: pointer;
            color: #303a56;
            background: #fff;
            font-size: .9rem;
            font-weight: 500;
        }
        .team-group-select input:checked + label {
            background: #eef3ff;
            color: #233882;
            border-color: #315d9f;
        }
        @media (max-width: 767px) {
            .team-group-select { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include('./components/sidebar.php') ?>
    <?php include('./components/navbar.php') ?>

    <div class="page-wrapper"><div class="page-content-wrapper page-content-margin-padding"><div class="page-content page-content-margin-padding">

        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3"><div class="breadcrumb-title pe-3">Foundation</div><div class="ps-3"><nav><ol class="breadcrumb mb-0 p-0"><li class="breadcrumb-item"><a href="index.php"><i class="bx bx-home-alt"></i></a></li><li class="breadcrumb-item active">บุคลากรมูลนิธิ</li></ol></nav></div></div>
        <?php if (!empty($errors)): ?><div class="alert alert-danger"><?php foreach($errors as $e): ?><div><?= h($e) ?></div><?php endforeach; ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>
        <div class="team-group-menu">
            <a class="team-group-link <?= $activeGroup === '' ? 'active' : '' ?>" href="team_members.php">ทั้งหมด</a>
            <?php foreach($groups as $gk => $gv): ?>
                <a class="team-group-link <?= $activeGroup === $gk ? 'active' : '' ?>" href="team_members.php?group=<?= h($gk) ?>"><?= h($gv) ?></a>
            <?php endforeach; ?>
            <form method="post" class="ms-auto" onsubmit="return confirm('ยืนยันโหลดเทมเพลตบุคลากร? ระบบจะลบข้อมูลบุคลากรเดิมทั้งหมดก่อนเพิ่มรายชื่อใหม่')">
                <input type="hidden" name="action" value="apply_template">
                <button type="submit" class="team-group-link">โหลดเทมเพลต 7/6/10</button>
            </form>
        </div>

        <div class="row g-3">
            <div class="col-xl-4">
                <div class="card thaifa-card"><div class="card-body">
                    <h5 class="mb-3"><?= (int)$edit['id']>0?'แก้ไขบุคลากร':'เพิ่มบุคลากร' ?></h5>
                    <form method="post">
                        <input type="hidden" name="action" value="save_member">
                        <input type="hidden" name="member_id" value="<?= (int)$edit['id'] ?>">
                        <div class="mb-2">
                            <label class="form-label">หมวด</label>
                            <div class="team-group-select">
                                <?php foreach($groups as $k=>$v): ?>
                                    <div>
                                        <input type="radio" id="group_<?= h($k) ?>" name="group_key" value="<?= h($k) ?>" <?= $edit['group_key']===$k?'checked':'' ?> required>
                                        <label for="group_<?= h($k) ?>"><?= h($v) ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="mb-2"><label class="form-label">ชื่อ</label><input name="member_name" class="form-control" value="<?= h($edit['member_name']) ?>" required></div>
                        <div class="mb-2"><label class="form-label">ตำแหน่ง</label><input name="member_title" class="form-control" value="<?= h($edit['member_title']) ?>"></div>
                        <div class="mb-2"><label class="form-label">รูป (URL)</label><input name="photo_url" class="form-control" value="<?= h($edit['photo_url']) ?>"></div>
                        <div class="mb-2"><label class="form-label">Sort</label><input name="sort_order" type="number" class="form-control" value="<?= (int)$edit['sort_order'] ?>"></div>
                        <div class="mb-2"><label class="form-label">Bio</label><textarea name="member_bio" rows="3" class="form-control"><?= h($edit['member_bio']) ?></textarea></div>
                        <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="is_active" value="1" <?= !empty($edit['is_active'])?'checked':'' ?>><label class="form-check-label">แสดงบนเว็บ</label></div>
                        <div class="d-flex gap-2"><button class="btn btn-primary" type="submit">บันทึก</button><a class="btn btn-outline-secondary" href="team_members.php">เพิ่มใหม่</a></div>
                    </form>
                </div></div>
            </div>
            <div class="col-xl-8">
                <div class="card thaifa-card"><div class="card-body">
                    <h5 class="mb-3">รายการบุคลากร<?= $activeGroup !== '' ? h($groups[$activeGroup]) : 'ทั้งหมด' ?></h5>
                    <div class="table-responsive"><table class="table align-middle table-hover">
                        <thead><tr><th>#</th><th>บุคลากร</th><th>หมวด</th><th>Sort</th><th>สถานะ</th><th>จัดการ</th></tr></thead>
                        <tbody>
                        <?php if(empty($members)): ?><tr><td colspan="6" class="text-center text-muted">ยังไม่มีข้อมูล</td></tr><?php else: ?>
                            <?php foreach($members as $m): ?>
                                <tr>
                                    <td><?= (int)$m['id'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="<?= h($m['photo_url'] ?: 'https://via.placeholder.com/56x56?text=Member') ?>" class="admin-thumb-sm" alt="<?= h($m['member_name']) ?>">
                                            <div class="team-member-title"><div class="fw-semibold"><?= h($m['member_name']) ?></div><small class="text-muted"><?= h($m['member_title']) ?></small></div>
                                        </div>
                                    </td>
                                    <td><?= h($groups[$m['group_key']] ?? $m['group_key']) ?></td>
                                    <td><?= (int)$m['sort_order'] ?></td>
                                    <td><span class="badge <?= (int)$m['is_active']===1?'bg-success':'bg-secondary' ?>"><?= (int)$m['is_active']===1?'แสดง':'ซ่อน' ?></span></td>
                                    <td class="team-action-cell">
                                        <div class="admin-inline-actions">
                                            <a href="team_members.php?edit_id=<?= (int)$m['id'] ?>" class="btn btn-sm btn-outline-primary">แก้ไข</a>
                                            <form method="post" class="d-inline" onsubmit="return confirm('ยืนยันการลบ?')">
                                                <input type="hidden" name="action" value="delete_member"><input type="hidden" name="member_id" value="<?= (int)$m['id'] ?>">
                                                <button class="btn btn-sm btn-outline-danger" type="submit">ลบ</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table></div>
                </div></div>
            </div>
        </div>

    </div></div></div>
</div>
<div class="overlay toggle-btn-mobile"></div>
<a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
<?php include('./structure/script.php') ?>
</body>
</html>
