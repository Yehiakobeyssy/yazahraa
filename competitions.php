<?php
session_start();
include 'settings/connect.php';
include 'common/function.php';
include 'common/head.php';

if(!isset($_SESSION['userID'])){
    $quizID = $_GET['quizID'] ?? null;
    $redirect = "login.php";
    if($quizID){
        // تمرير quizID للصفحة login.php ليتم إعادة التوجيه بعد تسجيل الدخول
        $redirect .= "?quizID=" . intval($quizID);
    }
    header("Location: $redirect");
    exit;
}

$userID = $_SESSION['userID'];
$quizID = $_GET['quizID'] ?? null;

// إعادة المسابقة: حذف الأجوبة السابقة
if(isset($_GET['restart']) && $quizID && $userID){
    $stmt = $con->prepare("DELETE FROM tblanswers WHERE userID=? AND competitionID=?");
    $stmt->execute([$userID, $quizID]);
}

// إذا دخل على مسابقة محددة
if($quizID){
    // جلب جميع أسئلة المسابقة
    $stmt = $con->prepare("SELECT questionID FROM tblquestions WHERE competitionID=? ORDER BY questionID ASC");
    $stmt->execute([$quizID]);
    $allQuestions = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // تحديد السؤال الحالي عبر index
    $currentIndex = isset($_GET['index']) ? intval($_GET['index']) : 0;
    $questionID = $allQuestions[$currentIndex] ?? null;

    // رابط السؤال التالي أو النهاية
    $nextIndex = $currentIndex + 1;
    $nextUrl = isset($allQuestions[$nextIndex])
        ? "competitions.php?quizID=$quizID&index=$nextIndex"
        : "competitions.php?quizID=$quizID&end=1";

    if($questionID){
        $stmt = $con->prepare("SELECT * FROM tblquestions WHERE questionID=? AND competitionID=?");
        $stmt->execute([$questionID, $quizID]);
        $question = $stmt->fetch();

        $stmt = $con->prepare("SELECT * FROM tbloptions WHERE questionID=? ORDER BY RAND()");
        $stmt->execute([$questionID]);
        $options = $stmt->fetchAll();
    }
}
?>
<link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
<link href="common/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="common/fcss/all.min.css">
<link rel="stylesheet" href="common/fcss/fontawesome.min.css">
<link rel="stylesheet" href="common/zahraastyle.css?v=1.1">
<link rel="stylesheet" href="css/competitions.css">
</head>
<body>
<header class="site-header">
    <div class="branding">
        <div class="coin">
            <img src="images/logo.png" alt="" srcset="">
        </div>
        <div>
            <div class="site-title">يا فاطمة الزهراء </div>
            <div class="site-sub">موقع توثيقي عن مظلومية السيدة فاطمة الزهراء عليها السلام</div>
        </div>
    </div>

    <div class="menu-toggle">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <nav class="main-menu">
        <ul>
            <li><a href="index.php">الصفحة الرئيسية</a></li>
            <li><a href="prayers.php">مفاتيح المحمدية</a></li>
            <li><a href="login.php">المسابقات</a></li>
            <li><a href="biography.php">السيرة</a></li>
            <li><a href="viedos.php">فيديوهات</a></li>
            <li><a href="contact.php">اتصل بنا</a></li>
        </ul>
    </nav>
</header>
<div class="container mt-5">

<?php if(!$quizID): ?>
    <h2 class="mb-4">جميع المسابقات</h2>
    <div class="row">
        <?php
        $stmt = $con->query("SELECT * FROM tblcompetitions");
        while($comp = $stmt->fetch()):
        ?>
        <div class="col-md-4 mb-3">
            <div class="card p-3 shadow-sm text-center">
                <h5><?= htmlspecialchars($comp['title']) ?></h5>
                <a href="competitions.php?quizID=<?= $comp['competitionID'] ?>&index=0" class="btn btn-primary mt-2">ابدأ المسابقة</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

<?php elseif(isset($_GET['end']) && $_GET['end'] == 1): 
    // عرض النتيجة النهائية
    $stmt = $con->prepare("SELECT COUNT(*) as total, SUM(is_correct) as correct FROM tblanswers WHERE userID=? AND competitionID=?");
    $stmt->execute([$userID, $quizID]);
    $res = $stmt->fetch();
    $total = $res['total'] ?? 0;
    $correct = $res['correct'] ?? 0;
    $wrong = $total - $correct;
    $percent = $total ? round($correct/$total*100) : 0;
?>
    <h3 class="mt-4">النتيجة النهائية</h3>
    <p>عدد الإجابات الصحيحة: <?= $correct ?></p>
    <p>عدد الإجابات الخاطئة: <?= $wrong ?></p>
    <p>النسبة المئوية للإجابات الصحيحة: <?= $percent ?>%</p>
    <a href="competitions.php?quizID=<?= $quizID ?>&restart=1" class="btn btn-warning">إعادة المسابقة</a>
    <a href="competitions.php" class="btn btn-warning">رجوع</a>

<?php elseif($questionID): ?>
    <h4 class="mt-4"><?= htmlspecialchars($question['question_text']) ?></h4>
    <div id="timer" class="mb-3" data-next-url="<?= $nextUrl ?>" data-time="20">
        <svg class="timer-svg" width="80" height="80">
            <circle cx="40" cy="40" r="36" stroke="#ffffff" stroke-width="8" fill="none"/>
            <circle cx="40" cy="40" r="36" stroke="#f44336" stroke-width="8" fill="none" 
                    stroke-dasharray="226.194" stroke-dashoffset="0" transform="rotate(-90 40 40)"/>
        </svg>
        <div class="timer-text" style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); font-weight:bold; font-size:18px; color:#fff;">
            20
        </div>
    </div>
    
    <div class="list-group mb-5"
         data-competitionid="<?= $quizID ?>"
         data-questionid="<?= $questionID ?>"
         data-userid="<?= $userID ?>">
        <?php foreach($options as $opt): ?>
        <button class="list-group-item option-btn"
                data-optionid="<?= $opt['optionID'] ?>"
                data-correct="<?= $opt['is_correct'] ?>">
            <?= htmlspecialchars($opt['option_text']) ?>
        </button>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

</div> <!-- container -->

<?php include 'common/jslinks.php'?>
<script src="js/competition.js?v=1.3"></script>
</body>


