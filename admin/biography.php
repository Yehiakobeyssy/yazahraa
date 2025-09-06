<?php 
    session_start();
    include '../settings/connect.php';
    include '../common/function.php';
    include '../common/head.php';

    if(!isset($_SESSION['adminID']) && empty($_SESSION['adminID'])){
        header("Location: index.php");
        exit;
    }
    $do = isset($_GET['do']) ? $_GET['do'] : 'manage';
?>
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../common/fcss/all.min.css">
    <link rel="stylesheet" href="../common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="../common/zahraastyle.css?v=1.1">
    <link rel="stylesheet" href="css/biography.css">
    <link rel="stylesheet" href="common/aside.css">
</head>
<body>
    <header class="admin-header">
        <div class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="admin-branding">لوحة تحكم يا فاطمة الزهراء</div>
        <div class="admin-user">
            مرحبًا، <?= $_SESSION['adminName'] ?>
        </div>
    </header>
    <?php include 'common/aside.php'; ?>
    <main class="container mt-4">

<?php
switch($do){

    // إدارة السيرة
    case 'manage':
        $stmt = $con->prepare("SELECT * FROM tbl_biography ORDER BY created_at DESC");
        $stmt->execute();
        $biographies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>إدارة سيرة</h2>
            <a href="?do=add" class="btn btn-success">إضافة سيرة جديدة</a>
        </div>
        <table class="table ">
            <thead >
                <tr>
                    <th>#</th>
                    <th>العنوان</th>
                    <th>تاريخ الإنشاء</th>
                    <th>العمليات</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($biographies as $bio){ ?>
                <tr>
                    <td><?= $bio['bioID'] ?></td>
                    <td><?= htmlspecialchars($bio['title']) ?></td>
                    <td><?= $bio['created_at'] ?></td>
                    <td>
                        <a href="?do=view&id=<?= $bio['bioID'] ?>" class="btn btn-info btn-sm">عرض</a>
                        <a href="?do=edit&id=<?= $bio['bioID'] ?>" class="btn btn-primary btn-sm">تعديل</a>
                        <a href="?do=delete&id=<?= $bio['bioID'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</a>
                        <a href="?do=add_section&bioID=<?= $bio['bioID'] ?>" class="btn btn-success btn-sm">إضافة قسم</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php
        break;

    // إضافة سيرة جديدة
    case 'add':
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $title = $_POST['title'];
            $description = $_POST['description'];
            $stmt = $con->prepare("INSERT INTO tbl_biography (title, description, created_at) VALUES (?,?,NOW())");
            $stmt->execute([$title, $description]);
            echo "<div class='alert alert-success'>تمت الإضافة بنجاح <a href='?do=manage'>العودة للإدارة</a></div>";
        } else {
        ?>
        <h2>إضافة سيرة جديدة</h2>
        <form action="" method="POST">
            <div class="mb-3">
                <label>العنوان</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>الوصف</label>
                <textarea name="description" class="form-control" rows="5"></textarea>
            </div>
            <button type="submit" class="btn btn-success">حفظ</button>
            <a href="?do=manage" class="btn btn-secondary">إلغاء</a>
        </form>
        <?php } 
        break;

    // عرض السيرة
    case 'view':
    // التأكد من وجود id
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if($id <= 0){
        echo "<div class='alert alert-danger'>رقم السيرة غير صالح.</div>";
        break;
    }

    // جلب السيرة
    $stmt = $con->prepare("SELECT * FROM tbl_biography WHERE bioID=?");
    $stmt->execute([$id]);
    $bio = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$bio){
        echo "<div class='alert alert-warning'>السيرة المطلوبة غير موجودة.</div>";
        break;
    }

    // جلب المقاطع
    $stmt2 = $con->prepare("SELECT * FROM tbl_biography_sections WHERE bioID=? ORDER BY created_at ASC");
    $stmt2->execute([$id]);
    $sections = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    ?>
    <h2><?= htmlspecialchars($bio['title']) ?></h2>
    <p><?= nl2br(htmlspecialchars($bio['description'])) ?></p>
    <hr>
    <h4>المقاطع:</h4>
    <?php if($sections){ 
        foreach($sections as $sec){ ?>
            <div class="card mb-2">
                <div class="card-header d-flex justify-content-between">
                    <?= htmlspecialchars($sec['title']) ?>
                    <div>
                        <a href="?do=edit_section&id=<?= $sec['sectionID'] ?>" class="btn btn-primary btn-sm">تعديل</a>
                        <a href="?do=delete_section&id=<?= $sec['sectionID'] ?>&bioID=<?= $id ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</a>
                    </div>
                </div>
                <div class="card-body">
                    <?= nl2br(htmlspecialchars($sec['content'])) ?>
                </div>
            </div>
        <?php } 
    } else { 
        echo "<p>لا توجد مقاطع بعد.</p>"; 
    } ?>
    <a href="?do=manage" class="btn btn-secondary mt-2">العودة للإدارة</a>
    <?php
    break;

    // هنا ستكمل بقية العمليات مثل edit, delete, add_section, edit_section, delete_section
    default:
        echo "<div class='alert alert-warning'>العملية غير موجودة.</div>";
        break;
    case 'edit':
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // التحقق من وجود السيرة
    $stmt = $con->prepare("SELECT * FROM tbl_biography WHERE bioID=?");
    $stmt->execute([$id]);
    $bio = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$bio){
        echo "<div class='alert alert-danger'>السيرة غير موجودة.</div>";
        break;
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $title = $_POST['title'];
        $description = $_POST['description'];
        $stmt = $con->prepare("UPDATE tbl_biography SET title=?, description=?, updated_at=NOW() WHERE bioID=?");
        $stmt->execute([$title, $description, $id]);
        echo "<div class='alert alert-success'>تم تعديل السيرة بنجاح <a href='?do=manage'>العودة للإدارة</a></div>";
    } else {
        ?>
        <h2>تعديل السيرة: <?= htmlspecialchars($bio['title']) ?></h2>
        <form method="POST">
            <div class="mb-3">
                <label>عنوان السيرة</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($bio['title']) ?>" required>
            </div>
            <div class="mb-3">
                <label>الوصف</label>
                <textarea name="description" class="form-control" rows="5"><?= htmlspecialchars($bio['description']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">تعديل</button>
            <a href="?do=manage" class="btn btn-secondary">إلغاء</a>
        </form>
        <?php
    }
    break;
    case 'delete':
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // التحقق من وجود السيرة
    $stmt = $con->prepare("SELECT * FROM tbl_biography WHERE bioID=?");
    $stmt->execute([$id]);
    $bio = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$bio){
        echo "<div class='alert alert-danger'>السيرة غير موجودة.</div>";
    } else {
        // أولاً حذف جميع المقاطع التابعة للسيرة
        $stmt = $con->prepare("DELETE FROM tbl_biography_sections WHERE bioID=?");
        $stmt->execute([$id]);

        // ثم حذف السيرة نفسها
        $stmt = $con->prepare("DELETE FROM tbl_biography WHERE bioID=?");
        $stmt->execute([$id]);

        echo "<div class='alert alert-success'>تم حذف السيرة وجميع مقاطعها بنجاح <a href='?do=manage'>العودة للإدارة</a></div>";
    }
    break;

    case 'add_section':
    $bioID = isset($_GET['bioID']) ? intval($_GET['bioID']) : 0;

    // التأكد من وجود السيرة
    $stmt = $con->prepare("SELECT * FROM tbl_biography WHERE bioID=?");
    $stmt->execute([$bioID]);
    $bio = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$bio){
        echo "<div class='alert alert-danger'>السيرة غير موجودة.</div>";
        break;
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $title = $_POST['title'];
        $content = $_POST['content'];
        $stmt = $con->prepare("INSERT INTO tbl_biography_sections (bioID, title, content, created_at) VALUES (?,?,?,NOW())");
        $stmt->execute([$bioID, $title, $content]);
        echo "<div class='alert alert-success'>تمت إضافة القسم بنجاح <a href='?do=view&id=$bioID'>عرض السيرة</a></div>";
    } else {
        ?>
        <h2>إضافة قسم جديد للسيرة: <?= htmlspecialchars($bio['title']) ?></h2>
        <form method="POST">
            <div class="mb-3">
                <label>عنوان القسم</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>المحتوى</label>
                <textarea name="content" class="form-control" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-success">حفظ</button>
            <a href="?do=view&id=<?= $bioID ?>" class="btn btn-secondary">إلغاء</a>
        </form>
        <?php
    }
    break;
case 'edit_section':
    $sectionID = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // جلب بيانات القسم
    $stmt = $con->prepare("SELECT * FROM tbl_biography_sections WHERE sectionID=?");
    $stmt->execute([$sectionID]);
    $section = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$section){
        echo "<div class='alert alert-danger'>القسم غير موجود.</div>";
        break;
    }

    $bioID = $section['bioID'];

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $title = $_POST['title'];
        $content = $_POST['content'];
        $stmt = $con->prepare("UPDATE tbl_biography_sections SET title=?, content=?, updated_at=NOW() WHERE sectionID=?");
        $stmt->execute([$title, $content, $sectionID]);
        echo "<div class='alert alert-success'>تم تعديل القسم بنجاح <a href='?do=view&id=$bioID'>عرض السيرة</a></div>";
    } else {
        ?>
        <h2>تعديل القسم: <?= htmlspecialchars($section['title']) ?></h2>
        <form method="POST">
            <div class="mb-3">
                <label>عنوان القسم</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($section['title']) ?>" required>
            </div>
            <div class="mb-3">
                <label>المحتوى</label>
                <textarea name="content" class="form-control" rows="5" required><?= htmlspecialchars($section['content']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">تعديل</button>
            <a href="?do=view&id=<?= $bioID ?>" class="btn btn-secondary">إلغاء</a>
        </form>
        <?php
    }
    break;
case 'delete_section':
    $sectionID = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $bioID = isset($_GET['bioID']) ? intval($_GET['bioID']) : 0;

    // التأكد من وجود القسم
    $stmt = $con->prepare("SELECT * FROM tbl_biography_sections WHERE sectionID=?");
    $stmt->execute([$sectionID]);
    $section = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$section){
        echo "<div class='alert alert-danger'>القسم غير موجود.</div>";
    } else {
        $stmt = $con->prepare("DELETE FROM tbl_biography_sections WHERE sectionID=?");
        $stmt->execute([$sectionID]);
        echo "<div class='alert alert-success'>تم حذف القسم بنجاح <a href='?do=view&id=$bioID'>عودة للسيرة</a></div>";
    }
    break;

}

?>

</main>
    <?php include '../common/jslinks.php'?>
    <script src="common/aside.js"></script>
    <script src="js/biography.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>
</html>