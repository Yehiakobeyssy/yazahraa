
<?php 
session_start();
include '../settings/connect.php';
include '../common/function.php';
include '../common/head.php';

if(!isset($_SESSION['adminID']) || empty($_SESSION['adminID'])){
    header("Location: index.php");
    exit;
}
?>
<link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
<link href="common/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../common/fcss/all.min.css">
<link rel="stylesheet" href="../common/fcss/fontawesome.min.css">
<link rel="stylesheet" href="../common/zahraastyle.css?v=1.1">
<link rel="stylesheet" href="css/visitors.css">
<link rel="stylesheet" href="common/aside.css">
<title>الزوار</title>
</head>
<body>
<header class="admin-header">
    <div class="hamburger"><span></span><span></span><span></span></div>
    <div class="admin-branding">لوحة تحكم يا فاطمة الزهراء</div>
    <div class="admin-user">مرحبًا، <?= $_SESSION['adminName'] ?></div>
</header>

<?php include 'common/aside.php'; ?>

<main>
    <h2>الزوار</h2>

    <div class="search-bar">
        <input type="text" id="searchUser" placeholder="ابحث باسم الزائر...">
    </div>

    <div id="usersTable">
        <table>
            <thead>
                <tr>
                    <th>الإيميل</th>
                    <th>عدد المسابقات المشاركة</th>
                    <th>عدد الإجابات الصحيحة</th>
                    <th>عدد الإجابات الخاطئة</th>
                    <th>نسبة النجاح</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $con->prepare("SELECT * FROM tblusers ORDER BY userID DESC");
                $stmt->execute();
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach($users as $user){
                    // عدد المسابقات المشاركة
                    $stmtComp = $con->prepare("SELECT COUNT(DISTINCT competitionID) FROM tblanswers WHERE userID=?");
                    $stmtComp->execute([$user['userID']]);
                    $compCount = $stmtComp->fetchColumn();

                    // عدد الاجابات الصحيحة
                    $stmtCorrect = $con->prepare("SELECT COUNT(*) FROM tblanswers a JOIN tbloptions o ON a.optionID=o.optionID WHERE a.userID=? AND o.is_correct=1");
                    $stmtCorrect->execute([$user['userID']]);
                    $correctCount = $stmtCorrect->fetchColumn();

                    // عدد الاجابات الخاطئة
                    $stmtWrong = $con->prepare("SELECT COUNT(*) FROM tblanswers a JOIN tbloptions o ON a.optionID=o.optionID WHERE a.userID=? AND o.is_correct=0");
                    $stmtWrong->execute([$user['userID']]);
                    $wrongCount = $stmtWrong->fetchColumn();

                    // نسبة النجاح
                    $total = $correctCount + $wrongCount;
                    $successRate = ($total>0) ? round(($correctCount/$total)*100) . '%' : '0%';
                ?>
                <tr class="user-row" data-userid="<?= $user['userID'] ?>">
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= $compCount ?></td>
                    <td><?= $correctCount ?></td>
                    <td><?= $wrongCount ?></td>
                    <td><?= $successRate ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div id="userDetails">
        <!-- سيتم ملئه عبر Ajax عند الضغط على أي زائر -->
    </div>

</main>

<?php include '../common/jslinks.php'; ?>
<script src="common/aside.js"></script>
<script src="js/visitors.js"></script>
</body>
</html>
