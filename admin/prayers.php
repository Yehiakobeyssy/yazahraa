<?php
session_start();
include '../settings/connect.php';
include '../common/function.php';
include '../common/head.php';

if(!isset($_SESSION['adminID']) || empty($_SESSION['adminID'])){
    header("Location: index.php");
    exit;
}

// جلب كل الأقسام مع عدد الفروع وعدد الأدعية
$sectionsStmt = $con->prepare("
    SELECT s.sectionID, s.title,
        (SELECT COUNT(*) FROM tbl_prayer_subsections sub WHERE sub.sectionID = s.sectionID) AS subsections_count,
        (SELECT COUNT(*) FROM tbl_prayers p JOIN tbl_prayer_subsections sub ON p.subsectionID = sub.subsectionID WHERE sub.sectionID = s.sectionID) AS prayers_count
    FROM tbl_prayer_sections s
    ORDER BY s.sectionID ASC
");
$sectionsStmt->execute();
$sections = $sectionsStmt->fetchAll(PDO::FETCH_ASSOC);

// جلب كل الفروع
$subStmt = $con->prepare("SELECT sub.*, s.title AS section_title FROM tbl_prayer_subsections sub JOIN tbl_prayer_sections s ON sub.sectionID = s.sectionID ORDER BY sub.subsectionID ASC");
$subStmt->execute();
$subsections = $subStmt->fetchAll(PDO::FETCH_ASSOC);

// جلب الأدعية
$pstmt = $con->prepare("
    SELECT 
        p.prayerID, p.prayer_title, p.content, p.created_at,
        sub.subsectionID, sub.title AS sub_title,
        s.sectionID, s.title AS section_title
    FROM tbl_prayers p
    JOIN tbl_prayer_subsections sub ON p.subsectionID = sub.subsectionID
    JOIN tbl_prayer_sections s ON sub.sectionID = s.sectionID
    ORDER BY p.prayerID ASC
");
$pstmt->execute();
$prayers = $pstmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>إدارة الأدعية — لوحة التحكم</title>
<link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
<link href="common/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../common/fcss/all.min.css">
<link rel="stylesheet" href="../common/fcss/fontawesome.min.css">
<link rel="stylesheet" href="../common/zahraastyle.css?v=1.1">
<link rel="stylesheet" href="css/prayers.css">
<link rel="stylesheet" href="common/aside.css">
</head>
<body>
<header class="admin-header">
    <div class="hamburger"><span></span><span></span><span></span></div>
    <div class="admin-branding">لوحة تحكم يا فاطمة الزهراء</div>
    <div class="admin-user">مرحبًا، <?= htmlspecialchars($_SESSION['adminName']) ?></div>
</header>

<?php include 'common/aside.php'; ?>

<main>
    <div class="manage-header">
        <div class="titles">
            <h2>إدارة الأدعية</h2>
            <p>عرض الأقسام، الفروع، والأدعية — أضف/حرر/احذف بسهولة.</p>
        </div>
        <div class="actions">
            <!-- <input type="search" id="globalSearch" placeholder="ابحث حرفياً — اكتب هنا..." aria-label="بحث"> -->
            <button id="openAddSection" class="btn btn-success">إضافة قسم</button>
            <button id="openAddSub" class="btn btn-primary">إضافة فرع</button>
            <button id="openAddPrayer" class="btn btn-info">إضافة دعاء</button>
        </div>
    </div>

    <!-- الأقسام -->
    <section id="sectionsPanel" class="panel">
        <h3>الأقسام</h3>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>اسم القسم</th>
                        <th>عدد الفروع</th>
                        <th>عدد الأدعية</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody id="sectionsTbody">
                    <?php foreach($sections as $s): ?>
                    <tr data-id="<?= $s['sectionID'] ?>">
                        <td><?= $s['sectionID'] ?></td>
                        <td class="col-title"><?= htmlspecialchars($s['title']) ?></td>
                        <td><?= $s['subsections_count'] ?></td>
                        <td><?= $s['prayers_count'] ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-section">تعديل</button>
                            <button class="btn btn-sm btn-danger delete-section">حذف</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- الفروع -->
    <section id="subsPanel" class="panel">
        <h3>الفروع</h3>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>القسم</th>
                        <th>اسم الفرع</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody id="subsTbody">
                    <?php foreach($subsections as $sub): ?>
                    <tr data-id="<?= $sub['subsectionID'] ?>">
                        <td><?= $sub['subsectionID'] ?></td>
                        <td><?= htmlspecialchars($sub['section_title']) ?></td>
                        <td class="col-title"><?= htmlspecialchars($sub['title']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-sub">تعديل</button>
                            <button class="btn btn-sm btn-danger delete-sub">حذف</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- الأدعية -->
    <section id="prayersPanel" class="panel">
        <h3>الأدعية</h3>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>القسم</th>
                        <th>الفرع</th>
                        <th>اسم الدعاء</th>
                        <th>النص</th>
                        <th>تاريخ الإضافة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($prayers as $p): ?>
                    <tr data-id="<?= $p['prayerID'] ?>">
                        <td><?= $p['prayerID'] ?></td>
                        <td><?= htmlspecialchars($p['section_title']) ?></td>
                        <td><?= htmlspecialchars($p['sub_title']) ?></td>
                        <td><?= htmlspecialchars($p['prayer_title']) ?></td>
                        <td><?= mb_strimwidth(strip_tags($p['content']),0,50,'...') ?></td>
                        <td><?= $p['created_at'] ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-prayer">تعديل</button>
                            <button class="btn btn-sm btn-danger delete-prayer">حذف</button>
                            <button class="btn btn-sm btn-secondary view-prayer">عرض</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- المودالات -->
    <div id="modals">

        <!-- إضافة/تعديل قسم -->
        <div class="modal" id="modalSection" style="display:none;">
            <div class="modal-inner">
                <h4 id="modalSectionTitle">إضافة قسم</h4>
                <form id="formSection" data-action="add_section">
                    <input type="hidden" name="sectionID">
                    <label>اسم القسم</label>
                    <input type="text" name="title" required>
                    <div class="modal-actions">
                        <button type="submit" class="btn btn-success">حفظ</button>
                        <button type="button" class="btn btn-secondary close-modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- إضافة/تعديل فرع -->
        <div class="modal" id="modalSub" style="display:none;">
            <div class="modal-inner">
                <h4 id="modalSubTitle">إضافة فرع</h4>
                <form id="formSub" data-action="add_sub">
                    <input type="hidden" name="subsectionID">
                    <label>القسم</label>
                    <select name="sectionID" required>
                        <option value="">اختر القسم</option>
                        <?php foreach($sections as $s): ?>
                        <option value="<?= $s['sectionID'] ?>"><?= htmlspecialchars($s['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>اسم الفرع</label>
                    <input type="text" name="title" required>
                    <div class="modal-actions">
                        <button type="submit" class="btn btn-success">حفظ</button>
                        <button type="button" class="btn btn-secondary close-modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- إضافة/تعديل دعاء -->
        <div class="modal" id="modalPrayer" style="display:none;">
            <div class="modal-inner large">
                <h4 id="modalPrayerTitle">إضافة دعاء</h4>
                <form id="formPrayer" data-action="add_prayer">
                    <input type="hidden" id="prayerID" name="prayerID">

                    <label>القسم</label>
                    <select id="prayerSectionSelect" name="sectionID" required>
                        <option value="">-- اختر القسم --</option>
                        <?php foreach($sections as $sec): ?>
                        <option value="<?= $sec['sectionID'] ?>"><?= htmlspecialchars($sec['title']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label>الفرع</label>
                    <select id="prayerSubSelect" name="subsectionID" required>
                        <option value="">-- اختر الفرع --</option>
                        <?php foreach($subsections as $sub): ?>
                        <option value="<?= $sub['subsectionID'] ?>" data-section="<?= $sub['sectionID'] ?>">
                            <?= htmlspecialchars($sub['title']) ?> (<?= htmlspecialchars($sub['section_title']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <label>اسم الدعاء</label>
                    <input type="text" name="prayer_title" id="prayer_title" maxlength="100" required>

                    <label>نص الدعاء</label>
                    <textarea name="content" id="content" rows="6" required></textarea>

                    <div class="modal-actions">
                        <button type="submit" class="btn btn-success">حفظ</button>
                        <button type="button" class="btn btn-secondary close-modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- عرض الدعاء -->
        <div class="modal" id="modalView" style="display:none;">
            <div class="modal-inner large">
                <h4 id="modalViewTitle">عرض الدعاء</h4>
                <div id="modalViewBody"></div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary close-modal">إغلاق</button>
                </div>
            </div>
        </div>

    </div>
</main>

<?php include '../common/jslinks.php'; ?>
<script>const AJAX_URL = 'ajax/prayers_ajax.php';</script>
<script src="common/aside.js"></script>
<script src="js/prayers.js"></script>
</body>
</html>
