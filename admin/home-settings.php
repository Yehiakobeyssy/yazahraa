<?php 
session_start();
include '../settings/connect.php';
include '../common/function.php';
include '../common/head.php';

if(!isset($_SESSION['adminID']) || empty($_SESSION['adminID'])){
    header("Location: index.php");
    exit;
}

$stmt = $con->prepare("SELECT introduction,finish FROM tblparagraf WHERE phragrafID = 1");
$stmt->execute();
$paragraph = $stmt->fetch(PDO::FETCH_ASSOC);

$introduction = $paragraph['introduction'] ?? '';
$dedication = $paragraph['finish'] ?? '';


$stmt = $con->prepare("SELECT * FROM tblslideshow ORDER BY slideID DESC");
$stmt->execute();
$slides = $stmt->fetchAll(PDO::FETCH_ASSOC);


if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $introduction = $_POST['introduction'] ?? '';
    $dedication   = $_POST['dedication'] ?? '';

    $stmt = $con->prepare("UPDATE tblparagraf SET introduction=?, finish=?, updated_at=NOW() WHERE phragrafID = 1");
    $stmt->execute([$introduction, $dedication]);

    $success = "تم حفظ التغييرات بنجاح!";
}


?>
<link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
<link href="common/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../common/fcss/all.min.css">
<link rel="stylesheet" href="../common/fcss/fontawesome.min.css">
<link rel="stylesheet" href="../common/zahraastyle.css?v=1.1">
<link rel="stylesheet" href="css/home-settings.css">
<link rel="stylesheet" href="common/aside.css">
</head>
<body>
<header class="admin-header">
    <div class="hamburger"><span></span><span></span><span></span></div>
    <div class="admin-branding">لوحة تحكم يا فاطمة الزهراء</div>
    <div class="admin-user">مرحبًا، <?= $_SESSION['adminName'] ?></div>
</header>

<?php include 'common/aside.php'; ?>

<main>
    <h2 class="section-title">الصفحة الرئيسية - إعدادات النصوص</h2>

    <form method="POST" >
        <div class="section-card">
            <h4>مقدمة</h4>
            <textarea name="introduction" rows="6"><?= htmlspecialchars($introduction) ?></textarea>
        </div>

        <div class="section-card">
            <h4>إهداء</h4>
            <textarea name="dedication" rows="6"><?= htmlspecialchars($dedication) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary mt-3">حفظ التغييرات</button>
    </form>

    <div class="section-card mt-4">
        <h4>الأحاديث</h4>
        <div class="input-group mb-3">
            <input type="text" id="newTitle" class="form-control" placeholder="اكتب عنوان الحديث">
            <button class="btn btn-primary" id="addHadithBtn">إضافة الحديث</button>
        </div>
        <div id="hadithList">
            <!-- سيتم تحميل الأحاديث بواسطة AJAX -->
        </div>
    </div>

    <h2 class="section-title">قسم البانر / السلايدشو</h2>

    <!-- إضافة صورة -->
    <div class="section-card mb-4">
        <h4>إضافة صورة جديدة</h4>
        <form id="uploadSlideForm" enctype="multipart/form-data">
            <div class="input-group">
                <input type="file" name="slideImg" class="form-control" accept="image/*" required>
                <button type="submit" class="btn btn-primary">رفع الصورة</button>
            </div>
            <div id="uploadAlert"></div>
        </form>
    </div>

    <!-- عرض الصور -->
    <div class="section-card">
        <h4>الصور الحالية</h4>
        <div id="slideList" class="d-flex flex-wrap gap-3">
            <?php foreach($slides as $slide): ?>
                <div class="slide-card position-relative">
                    <img src="../images/slideshow/<?= htmlspecialchars($slide['slideImg']) ?>" class="slide-img" alt="Slide">
                    <button class="btn btn-sm btn-danger deleteSlideBtn position-absolute" data-id="<?= $slide['slideID'] ?>">حذف</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<?php include '../common/jslinks.php'; ?>
<script src="common/aside.js"></script>
<script src="js/home-settings.js"></script>
</body>
</html>