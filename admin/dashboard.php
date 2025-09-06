<?php 
    session_start();
    include '../settings/connect.php';
    include '../common/function.php';
    include '../common/head.php';

    if(!isset($_SESSION['adminID']) && empty($_SESSION['adminID'])){
        header("Location: index.php");
        exit;
    }
?>
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../common/fcss/all.min.css">
    <link rel="stylesheet" href="../common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="../common/zahraastyle.css?v=1.1">
    <link rel="stylesheet" href="css/dashboard.css">
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
    <main class="admin-main">
        <h1>مرحبًا بك في لوحة التحكم</h1>
        <p>اختر من القائمة الجانبية للوصول إلى الأقسام.</p>

        <?php
        // نفترض أنك متصل بـ PDO في $con

        // عدد الزوار
        $stmtUsers = $con->prepare("SELECT COUNT(*) FROM tblusers");
        $stmtUsers->execute();
        $totalUsers = $stmtUsers->fetchColumn();

        // عدد المسابقات
        $stmtComp = $con->prepare("SELECT COUNT(*) FROM tblcompetitions");
        $stmtComp->execute();
        $totalCompetitions = $stmtComp->fetchColumn();

        // نسبة النجاح: عدد الإجابات الصحيحة ÷ إجمالي الإجابات
        $stmtCorrect = $con->prepare("
            SELECT COUNT(*) 
            FROM tblanswers a
            JOIN tbloptions o ON a.optionID = o.optionID
            WHERE o.is_correct = 1
        ");
        $stmtCorrect->execute();
        $totalCorrect = $stmtCorrect->fetchColumn();

        $stmtTotal = $con->prepare("SELECT COUNT(*) FROM tblanswers");
        $stmtTotal->execute();
        $totalAnswers = $stmtTotal->fetchColumn();

        $successRate = $totalAnswers > 0 ? round(($totalCorrect / $totalAnswers) * 100, 2) : 0;

        $statcount_duaa= $con->prepare('SELECT COUNT(prayerID) AS count FROM tbl_prayers ');
        $statcount_duaa->execute();
        $total_duaa= $statcount_duaa->fetch();
        ?>

        <div class="dashboard-cards">
            <div class="card">
    <div class="card-icon"><i class="fa fa-users"></i></div>
    <div class="card-info">
        <h3><?= $totalUsers ?></h3>
        <p>عدد الزوار</p>
    </div>
</div>

<div class="card">
    <div class="card-icon"><i class="fa fa-trophy"></i></div>
    <div class="card-info">
        <h3><?= $totalCompetitions ?></h3>
        <p>عدد المسابقات</p>
    </div>
</div>

<div class="card">
    <div class="card-icon"><i class="fa fa-percent"></i></div>
    <div class="card-info">
        <h3><?= $successRate ?>%</h3>
        <p>نسبة النجاح</p>
    </div>
</div>


            <div class="card">
                <div class="card-icon"><i class="fa fa-book"></i></div>
                <div class="card-info">
                    <h3><?php echo $total_duaa['count'] ?></h3>
                    <p>عدد الأدعية</p>
                </div>
            </div>
        </div>
        <div class="statistic">
            <div class="chart-container">
                <h2>عدد الزوار خلال آخر 7 أيام</h2>
                <canvas id="visitorsChart"></canvas>
            </div>

            <!-- الرسم البياني لنسب النجاح -->
            <div class="chart-container">
                <h2>نسب النجاح</h2>
                <canvas id="successChart"></canvas>
            </div>
        </div>
        <div class="compherstion">
            <div class="competitions-header">
                <h2>آخر 3 مسابقات</h2>
                <a href="competitions.php?Do=add" class="btn-add">إضافة مسابقة</a>
            </div>

            <div class="latest-competitions">
                <?php
// آخر 3 مسابقات
$stmtComp = $con->prepare("SELECT * FROM tblcompetitions ORDER BY competitionID DESC LIMIT 3");
$stmtComp->execute();
$competitions = $stmtComp->fetchAll(PDO::FETCH_ASSOC);
?>

<table>
    <thead>
        <tr>
            <th>اسم المسابقة</th>
            <th>عدد المشاركين</th>
            <th>عدد العلامة التامة</th>
            <th>نسبة النجاح</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($competitions as $comp): 
            $compID = $comp['competitionID'];

            // عدد المشاركين الفريدين
            $stmtUsers = $con->prepare("SELECT COUNT(DISTINCT userID) FROM tblanswers WHERE competitionID=?");
            $stmtUsers->execute([$compID]);
            $totalParticipants = $stmtUsers->fetchColumn();

            // عدد الأسئلة
            $stmtQ = $con->prepare("SELECT COUNT(*) FROM tblquestions WHERE competitionID=?");
            $stmtQ->execute([$compID]);
            $totalQuestions = $stmtQ->fetchColumn();

            // عدد المستخدمين الذين حصلوا على العلامة التامة
            $stmtPerfect = $con->prepare("
                SELECT COUNT(*) 
                FROM tblusers u
                WHERE NOT EXISTS (
                    SELECT 1 
                    FROM tblquestions q
                    LEFT JOIN tblanswers a ON a.questionID=q.questionID AND a.userID=u.userID
                    LEFT JOIN tbloptions o ON a.optionID=o.optionID
                    WHERE q.competitionID=? AND (o.is_correct IS NULL OR o.is_correct=0)
                )
            ");
            $stmtPerfect->execute([$compID]);
            $perfectCount = $stmtPerfect->fetchColumn();

            // نسبة النجاح: متوسط نسبة الإجابات الصحيحة لكل مستخدم
            $stmtCorrect = $con->prepare("
                SELECT COUNT(*) 
                FROM tblanswers a
                JOIN tbloptions o ON a.optionID=o.optionID
                JOIN tblquestions q ON a.questionID=q.questionID
                WHERE q.competitionID=? AND o.is_correct=1
            ");
            $stmtCorrect->execute([$compID]);
            $totalCorrect = $stmtCorrect->fetchColumn();

            $stmtTotal = $con->prepare("SELECT COUNT(*) FROM tblanswers a JOIN tblquestions q ON a.questionID=q.questionID WHERE q.competitionID=?");
            $stmtTotal->execute([$compID]);
            $totalAnswers = $stmtTotal->fetchColumn();

            $successRate = $totalAnswers > 0 ? round(($totalCorrect / $totalAnswers) * 100, 2) : 0;
        ?>
        <tr>
            <td><?= htmlspecialchars($comp['title']) ?></td>
            <td><?= $totalParticipants ?></td>
            <td><?= $perfectCount ?></td>
            <td><?= $successRate ?>%</td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
            </div>
        </div>
    </main>
    <?php
// مثال لجلب عدد الزوار يوميًا لآخر 7 أيام
$visitorsData = [];
$visitorsLabels = [];
for($i=6;$i>=0;$i--){
    $date = date('Y-m-d', strtotime("-$i days"));
    $stmt = $con->prepare("SELECT COUNT(*) FROM tblusers WHERE DATE(created_at)=?");
    $stmt->execute([$date]);
    $count = $stmt->fetchColumn();
    $visitorsData[] = $count;
    $visitorsLabels[] = "اليوم ".(7-$i);
}

// نسبة النجاح العامة
$stmtCorrect = $con->prepare("
    SELECT COUNT(*) FROM tblanswers a
    JOIN tbloptions o ON a.optionID=o.optionID
    WHERE o.is_correct=1
");
$stmtCorrect->execute();
$totalCorrect = $stmtCorrect->fetchColumn();

$stmtTotal = $con->prepare("SELECT COUNT(*) FROM tblanswers");
$stmtTotal->execute();
$totalAnswers = $stmtTotal->fetchColumn();

$successRate = $totalAnswers > 0 ? $totalCorrect : 0;
$failRate = $totalAnswers > 0 ? $totalAnswers - $totalCorrect : 0;
?>
    <?php include '../common/jslinks.php'?>
    <script src="common/aside.js"></script>
    <script src="js/dashboard.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
const visitorsData = <?= json_encode($visitorsData) ?>;
const visitorsLabels = <?= json_encode($visitorsLabels) ?>;

new Chart(document.getElementById('visitorsChart'), {
    type: 'line',
    data: {
        labels: visitorsLabels,
        datasets: [{
            label: 'عدد الزوار',
            data: visitorsData,
            borderColor: 'blue',
            backgroundColor: 'rgba(0,0,255,0.1)',
            borderWidth: 2,
            tension: 0.3,
            fill: true
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
});

// نسبة النجاح
const successData = [<?= $totalCorrect ?>, <?= $totalAnswers - $totalCorrect ?>];
const successLabels = ["نجاح", "رسوب"];

new Chart(document.getElementById('successChart'), {
    type: 'doughnut',
    data: {
        labels: successLabels,
        datasets: [{ data: successData, backgroundColor: ['#28a745','#dc3545'] }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
</script>


</body>
</html>