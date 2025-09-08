
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
<link rel="stylesheet" href="css/competitions.css?v=1.2">
<link rel="stylesheet" href="common/aside.css">
<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
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

<main>

<?php
    $Do = isset($_GET['Do']) ? $_GET['Do'] : 'manage';

    if($Do == 'manage') {
        // جلب كل المسابقات
        $stmt = $con->prepare("SELECT * FROM tblcompetitions ORDER BY competitionID DESC");
        $stmt->execute();
        $competitions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="manage-header">
        <div class="titles">
            <h2>إدارة المسابقات</h2>
            <p>عرض كل المسابقات وإدارتها</p>
        </div>
        <div class="actions">
            <a href="competitions.php?Do=add" class="btn btn-success">إضافة مسابقة</a>
        </div>
    </div>

    <div class="competitions-list">
    <?php if(empty($competitions)): ?>
        <p>لا توجد مسابقات بعد.</p>
    <?php else: ?>
        <?php foreach($competitions as $comp):
            // عدد الأسئلة
            $stmtQ = $con->prepare("SELECT COUNT(*) FROM tblquestions WHERE competitionID = ?");
            $stmtQ->execute([$comp['competitionID']]);
            $questionsCount = $stmtQ->fetchColumn();

            // عدد المستخدمين
            $stmtU = $con->prepare("SELECT COUNT(DISTINCT userID) FROM tblanswers WHERE competitionID = ?");
            $stmtU->execute([$comp['competitionID']]);
            $usersCount = $stmtU->fetchColumn();

            // عدد العلامة التامة
            $stmtFull = $con->prepare("
                SELECT userID
                FROM tblanswers
                WHERE competitionID = ?
                GROUP BY userID
                HAVING SUM(is_correct) = ?
            ");
            $stmtFull->execute([$comp['competitionID'], $questionsCount]);
            $fullMarks = $stmtFull->rowCount();

            // نسبة النجاح
            $stmtSuccess = $con->prepare("
                SELECT userID
                FROM tblanswers
                WHERE competitionID = ?
                GROUP BY userID
                HAVING SUM(is_correct) >= ?
            ");
            $stmtSuccess->execute([$comp['competitionID'], ceil($questionsCount/2)]);
            $successUsers = $stmtSuccess->rowCount();
            $successPercent = $usersCount > 0 ? round($successUsers / $usersCount * 100) : 0;

            // استخراج الفائز
            $stmtWinner = $con->prepare("
                SELECT u.email, 
                       SUM(a.is_correct) AS correct_answers, 
                       SUM(a.time_taken) AS total_time
                FROM tblanswers a
                JOIN tblusers u ON a.userID = u.userID
                WHERE a.competitionID = ?
                GROUP BY a.userID
                ORDER BY correct_answers DESC, total_time ASC
                LIMIT 1
            ");
            $stmtWinner->execute([$comp['competitionID']]);
            $winner = $stmtWinner->fetch(PDO::FETCH_ASSOC);
        ?>
        <div class="competition-card">
            <h3><?= htmlspecialchars($comp['title']) ?></h3>
            <div class="competition-info">
                <p>عدد الأسئلة: <?= $questionsCount ?></p>
                <p>عدد المستخدمين: <?= $usersCount ?></p>
                <p>عدد العلامة التامة: <?= $fullMarks ?></p>
                <p>نسبة النجاح: <?= $successPercent ?>%</p>
            </div>

            <?php if($winner): ?>
            <div class="winner-info">
                🏆 الفائز: <?= htmlspecialchars($winner['email']) ?>  
                | الإجابات الصحيحة: <?= $winner['correct_answers'] ?>  
                | الزمن: <?= round($winner['total_time']/1000, 2) ?> ثانية (<?= $winner['total_time'] ?> مللي ثانية)
            </div>
            <?php endif; ?>

            <div class="competition-actions">
                <button class="btn-action btn-qr"  data-index="<?= $comp['competitionID'] ?>">إنشاء QR</button>
                <button class="btn-action btn-link" data-index="<?= $comp['competitionID'] ?>">نسخ الرابط</button>
                <a href="competitions.php?Do=edit&id=<?= $comp['competitionID'] ?>" class="btn-action btn-edit">تعديل</a>
                <a href="competitions.php?Do=delete&id=<?= $comp['competitionID'] ?>" class="btn-action btn-delete" onclick="return confirm('هل أنت متأكد أنك تريد حذف هذه المسابقة وكل الأسئلة والإجابات المرتبطة بها؟');">حذف</a>
                <a href="competitions.php?Do=view&id=<?= $comp['competitionID'] ?>" class="btn-action btn-view">تفاصيل</a>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>


    <?php
    } elseif($Do == 'add') {
    ?>
<div class="manage-header">
    <div class="titles">
        <h2>إضافة مسابقة جديدة</h2>
        <p>أدخل اسم المسابقة وأضف الأسئلة والاحتمالات</p>
    </div>
</div>

<form id="addCompetitionForm" method="post" action="competitions.php?Do=insert">
    <div class="form-group">
        <label>اسم المسابقة:</label>
        <input type="text" name="competition_title" id="competition_title" class="form-control" required>
    </div>

    <div id="questionsContainer">
        <!-- الأسئلة الديناميكية ستضاف هنا -->
    </div>

    <button type="button" id="addQuestionBtn" class="btn btn-success mt-3">إضافة سؤال</button>
    <br>
    <button type="submit" class="btn btn-primary mt-4">حفظ المسابقة</button>
</form>



    <?php
    } elseif($Do == 'insert') {
if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $title = $_POST['competition_title'] ?? '';
        $questions = $_POST['questions'] ?? [];

        if(empty($title) || empty($questions)){
            echo "<p>يجب إدخال اسم المسابقة والأسئلة</p>";
            exit;
        }

        try {
            // بداية المعاملة
            $con->beginTransaction();

            // إضافة المسابقة
            $stmt = $con->prepare("INSERT INTO tblcompetitions (title) VALUES (?)");
            $stmt->execute([$title]);
            $competitionID = $con->lastInsertId();

            // إضافة الأسئلة
            foreach($questions as $qIndex => $qData){
                $qText = $qData['text'] ?? '';
                if(empty($qText)) continue;

                $stmtQ = $con->prepare("INSERT INTO tblquestions (competitionID, question_text) VALUES (?, ?)");
                $stmtQ->execute([$competitionID, $qText]);
                $questionID = $con->lastInsertId();

                $correctOption = $qData['correct'] ?? 0;

                // إضافة الاحتمالات
                foreach($qData['options'] as $oIndex => $oData){
                    $optionText = $oData['text'] ?? '';
                    if(empty($optionText)) continue;

                    $is_correct = ($oIndex == $correctOption) ? 1 : 0;

                    $stmtO = $con->prepare("INSERT INTO tbloptions (questionID, option_text, is_correct) VALUES (?, ?, ?)");
                    $stmtO->execute([$questionID, $optionText, $is_correct]);
                }
            }

            $con->commit();
            echo "<p>تم حفظ المسابقة بنجاح!</p>";
            echo "<a href='competitions.php'>عودة لإدارة المسابقات</a>";

        } catch(PDOException $e){
            $con->rollBack();
            echo "حدث خطأ: " . $e->getMessage();
        }
    }
    }
    elseif($Do == 'edit') {
    ?>
    <?php
// جلب ID المسابقة
$compID = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($compID <= 0){
    echo "المسابقة غير موجودة.";
    exit;
}

// جلب بيانات المسابقة
$stmt = $con->prepare("SELECT * FROM tblcompetitions WHERE competitionID = ?");
$stmt->execute([$compID]);
$competition = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$competition){
    echo "المسابقة غير موجودة.";
    exit;
}

// جلب الأسئلة والاحتمالات
$stmtQ = $con->prepare("SELECT * FROM tblquestions WHERE competitionID = ? ORDER BY questionID ASC");
$stmtQ->execute([$compID]);
$questions = $stmtQ->fetchAll(PDO::FETCH_ASSOC);

// لكل سؤال جلب الاحتمالات
foreach($questions as &$q){
    $stmtO = $con->prepare("SELECT * FROM tbloptions WHERE questionID = ? ORDER BY optionID ASC");
    $stmtO->execute([$q['questionID']]);
    $q['options'] = $stmtO->fetchAll(PDO::FETCH_ASSOC);
}
unset($q); // تنظيف المرجع بعد foreach
?>


<div class="manage-header">
    <div class="titles">
        <h2>تعديل المسابقة: <?= htmlspecialchars($competition['title']) ?></h2>
        <p>يمكنك تعديل الأسئلة والاحتمالات أو إضافة أسئلة جديدة</p>
    </div>
</div>

<form id="editCompetitionForm" method="post" action="competitions.php?Do=update&id=<?= $compID ?>">

    <div class="form-group">
        <label>اسم المسابقة: </label>
        <input type="text" name="competition_title" class="form-control" value="<?= htmlspecialchars($competition['title']) ?>" required>
    </div>

    <div id="questionsContainer">
        <?php foreach($questions as $qIndex => $q): ?>
        <div class="question-block" data-qid="<?= $qIndex+1 ?>" data-db-id="<?= $q['questionID'] ?>">
            <h4>السؤال <?= $qIndex+1 ?></h4>
            <input type="text" name="questions[<?= $qIndex+1 ?>][text]" class="form-control mb-2" value="<?= htmlspecialchars($q['question_text']) ?>" required>
            
            <div class="optionsContainer" data-qid="<?= $qIndex+1 ?>">
                <?php foreach($q['options'] as $oIndex => $opt): ?>
                <div class="option-block" data-db-id="<?= $opt['optionID'] ?>">
                    <input type="text" name="questions[<?= $qIndex+1 ?>][options][<?= $oIndex+1 ?>][text]" value="<?= htmlspecialchars($opt['option_text']) ?>" required>
                    <label>
                        <input type="radio" name="questions[<?= $qIndex+1 ?>][correct]" value="<?= $oIndex+1 ?>" <?= $opt['is_correct'] ? 'checked' : '' ?> required> الإجابة الصحيحة
                    </label>
                    <button type="button" class="remove-option">حذف الاحتمال</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-secondary addOptionBtn">إضافة احتمال</button>
            <button type="button" class="remove-question">حذف السؤال</button>
        </div>
        <?php endforeach; ?>
    </div>

    <button type="button" id="addQuestionBtn" class="btn btn-success mt-3">إضافة سؤال جديد</button>
    <br>
    <button type="submit" class="btn btn-primary mt-4">حفظ التعديلات</button>
</form>
    <?php
    }elseif($Do=="update") {
    $compID = intval($_GET['id']);
$title = $_POST['competition_title'];
$questions = $_POST['questions'];

// تحديث اسم المسابقة
$stmt = $con->prepare("UPDATE tblcompetitions SET title=? WHERE competitionID=?");
$stmt->execute([$title, $compID]);

// 1. جلب كل الأسئلة الحالية في DB
$stmtAllQ = $con->prepare("SELECT questionID FROM tblquestions WHERE competitionID=?");
$stmtAllQ->execute([$compID]);
$existingQuestions = $stmtAllQ->fetchAll(PDO::FETCH_COLUMN);

// 2. جمع أسئلة POST ids
$postedQuestionIDs = [];
foreach($questions as $qIndex => $qData){
    if(isset($qData['db_id'])){
        $postedQuestionIDs[] = $qData['db_id'];
    }
}

// 3. حذف الأسئلة التي لم تعد موجودة
$questionsToDelete = array_diff($existingQuestions, $postedQuestionIDs);
if(!empty($questionsToDelete)){
    // حذف الخيارات المرتبطة أولاً
    $in = str_repeat('?,', count($questionsToDelete)-1) . '?';
    $stmtDelOpts = $con->prepare("DELETE FROM tbloptions WHERE questionID IN ($in)");
    $stmtDelOpts->execute($questionsToDelete);

    // حذف الأسئلة
    $stmtDelQ = $con->prepare("DELETE FROM tblquestions WHERE questionID IN ($in)");
    $stmtDelQ->execute($questionsToDelete);
}

// الآن معالجة كل سؤال من POST
foreach($questions as $qIndex => $qData){
    if(isset($qData['db_id'])){
        // تحديث سؤال موجود
        $stmtQ = $con->prepare("UPDATE tblquestions SET question_text=? WHERE questionID=?");
        $stmtQ->execute([$qData['text'], $qData['db_id']]);
        $qID = $qData['db_id'];
    } else {
        // سؤال جديد
        $stmtQ = $con->prepare("INSERT INTO tblquestions (competitionID, question_text) VALUES (?,?)");
        $stmtQ->execute([$compID, $qData['text']]);
        $qID = $con->lastInsertId();
    }

    // جلب خيارات السؤال الحالية
    $stmtAllO = $con->prepare("SELECT optionID FROM tbloptions WHERE questionID=?");
    $stmtAllO->execute([$qID]);
    $existingOptions = $stmtAllO->fetchAll(PDO::FETCH_COLUMN);

    $postedOptionIDs = [];
    foreach($qData['options'] as $oIndex => $opt){
        $isCorrect = ($qData['correct']==$oIndex) ? 1 : 0;

        if(isset($opt['db_id'])){
            $postedOptionIDs[] = $opt['db_id'];
            // تحديث خيار موجود
            $stmtO = $con->prepare("UPDATE tbloptions SET option_text=?, is_correct=? WHERE optionID=?");
            $stmtO->execute([$opt['text'],$isCorrect,$opt['db_id']]);
        } else {
            // خيار جديد
            $stmtO = $con->prepare("INSERT INTO tbloptions (questionID, option_text, is_correct) VALUES (?,?,?)");
            $stmtO->execute([$qID, $opt['text'],$isCorrect]);
        }
    }

    // حذف الخيارات التي لم تعد موجودة
    $optionsToDelete = array_diff($existingOptions, $postedOptionIDs);
    if(!empty($optionsToDelete)){
        $inO = str_repeat('?,', count($optionsToDelete)-1) . '?';
        $stmtDelO = $con->prepare("DELETE FROM tbloptions WHERE optionID IN ($inO)");
        $stmtDelO->execute($optionsToDelete);
    }
}

header("Location: competitions.php?Do=manage");
exit;

}
    elseif($Do == 'view') {

    $compID = isset($_GET['id']) ? intval($_GET['id']) : 0;

// جلب بيانات المسابقة
$stmt = $con->prepare("SELECT * FROM tblcompetitions WHERE competitionID=?");
$stmt->execute([$compID]);
$competition = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$competition){
    echo "<p>المسابقة غير موجودة.</p>";
    exit;
}

// جلب الأسئلة
$stmtQ = $con->prepare("SELECT * FROM tblquestions WHERE competitionID=? ORDER BY questionID ASC");
$stmtQ->execute([$compID]);
$questions = $stmtQ->fetchAll(PDO::FETCH_ASSOC);

// جلب الاحتمالات وعدد المستخدمين وإيميلات + وقت الإجابة
foreach($questions as &$q){
    $stmtO = $con->prepare("SELECT * FROM tbloptions WHERE questionID=? ORDER BY optionID ASC");
    $stmtO->execute([$q['questionID']]);
    $q['options'] = $stmtO->fetchAll(PDO::FETCH_ASSOC);

    foreach($q['options'] as &$opt){
        // عدد المستخدمين
        $stmtCount = $con->prepare("SELECT COUNT(*) FROM tblanswers WHERE questionID=? AND optionID=?");
        $stmtCount->execute([$q['questionID'], $opt['optionID']]);
        $opt['user_count'] = $stmtCount->fetchColumn();

        // جلب ايميلات + الوقت (مرتب من الأسرع للأبطأ)
        $stmtUsers = $con->prepare("
            SELECT u.email, a.time_taken
            FROM tblanswers a 
            JOIN tblusers u ON a.userID = u.userID 
            WHERE a.questionID=? AND a.optionID=?
            ORDER BY a.time_taken ASC
        ");
        $stmtUsers->execute([$q['questionID'], $opt['optionID']]);
        $users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

        // تحويل الوقت إلى ثواني + ميلي ثانية
        foreach($users as &$u){
            $u['time_taken_sec'] = $u['time_taken'] ? round($u['time_taken'] / 1000, 2) : 0;
        }

        $opt['users'] = $users;
    }
}
?>

<h2><?= htmlspecialchars($competition['title']) ?></h2>
<p>عرض كل الأسئلة والاحتمالات وعدد المستخدمين الذين اختاروها</p>

<div class="questions-view">
    <?php foreach($questions as $qIndex => $q): ?>
        <div class="question-block">
            <h4>السؤال <?= $qIndex + 1 ?>: <?= htmlspecialchars($q['question_text']) ?></h4>
            <ul class="options-list">
                <?php foreach($q['options'] as $opt): ?>
                    <li class="option-item <?= intval($opt['is_correct']) === 1 ? 'correct' : '' ?>">
                        <?= htmlspecialchars($opt['option_text']) ?> 
                        <span class="user-count">
                            <?= $opt['user_count'] ?>
                            <div class="user-list">
                                <?php foreach($opt['users'] as $u): ?>
                                    <p>
                                        <?= htmlspecialchars($u['email']) ?> 
                                        - <?= $u['time_taken_sec'] ?> ثانية 
                                        (<?= $u['time_taken'] ?> مللي ثانية)
                                    </p>
                                <?php endforeach; ?>
                            </div>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>
</div>

    <?php
    } elseif($Do == 'delete') {
    
    $compID = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if($compID > 0){
        try {
            // ابدأ المعاملة
            $con->beginTransaction();

            // 1. جلب الأسئلة المرتبطة بالمسابقة
            $stmtQ = $con->prepare("SELECT questionID FROM tblquestions WHERE competitionID=?");
            $stmtQ->execute([$compID]);
            $questions = $stmtQ->fetchAll(PDO::FETCH_COLUMN);

            if(!empty($questions)){
                // 2. حذف كل الإجابات المرتبطة بالأسئلة
                $inQ = str_repeat('?,', count($questions)-1) . '?';
                $stmtDelAnswers = $con->prepare("DELETE FROM tblanswers WHERE questionID IN ($inQ)");
                $stmtDelAnswers->execute($questions);

                // 3. جلب كل الاحتمالات المرتبطة بالأسئلة
                $stmtO = $con->prepare("SELECT optionID FROM tbloptions WHERE questionID IN ($inQ)");
                $stmtO->execute($questions);
                $options = $stmtO->fetchAll(PDO::FETCH_COLUMN);

                if(!empty($options)){
                    // حذف أي بيانات إضافية مرتبطة بالاحتمالات إذا موجودة (مثلاً tbloption_stats)
                    // مثال: DELETE FROM tbloption_stats WHERE optionID IN (...)
                }

                // 4. حذف الاحتمالات
                $stmtDelOptions = $con->prepare("DELETE FROM tbloptions WHERE questionID IN ($inQ)");
                $stmtDelOptions->execute($questions);
            }

            // 5. حذف الأسئلة
            $stmtDelQuestions = $con->prepare("DELETE FROM tblquestions WHERE competitionID=?");
            $stmtDelQuestions->execute([$compID]);

            // 6. حذف المسابقة
            $stmtDelComp = $con->prepare("DELETE FROM tblcompetitions WHERE competitionID=?");
            $stmtDelComp->execute([$compID]);

            // اكتمال المعاملة
            $con->commit();

            header("Location: competitions.php?Do=manage&msg=deleted");
            exit;

        } catch(PDOException $e){
            $con->rollBack();
            echo "حدث خطأ أثناء الحذف: " . $e->getMessage();
            exit;
        }
    } else {
        echo "المعرف غير صالح.";
        exit;
    }
    } else {
    ?>
    <h2>قسم غير موجود</h2>
    <?php
    }
    ?>

</main>
<?php include '../common/jslinks.php'; ?>
<script src="common/aside.js"></script>
<script src="js/competitions.js"></script>
<script>
    document.querySelectorAll('.btn-qr').forEach(button => {
        button.addEventListener('click', () => {
            const competitionID = button.getAttribute('data-index');
            const url = `http://fatmeelzahraa.com/competitions.php?quizID=${competitionID}`;

            // Generate QR code as data URL
            QRCode.toDataURL(url, { width: 300 }, function (err, dataUrl) {
                if (err) {
                    console.error(err);
                    return;
                }

                // Create a temporary link and trigger download
                const a = document.createElement('a');
                a.href = dataUrl;
                a.download = `QR_${competitionID}.png`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            });
        });
    });
    document.querySelectorAll('.btn-link').forEach(button => {
        button.addEventListener('click', () => {
            const competitionID = button.getAttribute('data-index');
            const url = `http://fatmeelzahraa.com/competitions.php?quizID=${competitionID}`;

            // Copy to clipboard
            navigator.clipboard.writeText(url)
                .then(() => {
                    alert('تم نسخ الرابط بنجاح!');
                })
                .catch(err => {
                    console.error('فشل النسخ:', err);
                });
        });
    });
</script>
</body>
</html>
