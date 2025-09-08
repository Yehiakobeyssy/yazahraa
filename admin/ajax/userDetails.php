<?php
session_start();
include '../../settings/connect.php';

if(!isset($_SESSION['adminID'])) exit;

$userID = intval($_POST['userID']);

// جلب بيانات الزائر
$stmt = $con->prepare("SELECT * FROM tblusers WHERE userID=?");
$stmt->execute([$userID]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$user) exit;

echo "<h3>تفاصيل الزائر: ".htmlspecialchars($user['email'])."</h3>";

// المسابقات التي شارك فيها
$stmtComp = $con->prepare("
    SELECT DISTINCT c.competitionID, c.title 
    FROM tblanswers a 
    JOIN tblcompetitions c ON a.competitionID = c.competitionID
    WHERE a.userID=?
");
$stmtComp->execute([$userID]);
$competitions = $stmtComp->fetchAll(PDO::FETCH_ASSOC);

foreach($competitions as $comp){
    echo "<h4>المسابقة: ".htmlspecialchars($comp['title'])."</h4>";

    // الأسئلة مع الخيارات التي اختارها الزائر
    $stmtQ = $con->prepare("
        SELECT q.questionID, q.question_text, a.optionID as user_option, a.time_taken
        FROM tblquestions q
        JOIN tblanswers a ON q.questionID = a.questionID
        WHERE a.userID=? AND q.competitionID=?
    ");
    $stmtQ->execute([$userID, $comp['competitionID']]);
    $questions = $stmtQ->fetchAll(PDO::FETCH_ASSOC);

    echo "<table class='userDetailsTable'>
            <thead>
                <tr>
                    <th>السؤال</th>
                    <th>إجابة الزائر</th>
                    <th>الإجابة الصحيحة</th>
                    <th>الوقت المستغرق</th>
                </tr>
            </thead>
            <tbody>";

    foreach($questions as $q){
        // جلب النص الصحيح
        $stmtCorrect = $con->prepare("SELECT option_text FROM tbloptions WHERE questionID=? AND is_correct=1");
        $stmtCorrect->execute([$q['questionID']]);
        $correctText = $stmtCorrect->fetchColumn();

        // جلب نص الإجابة التي اختارها الزائر
        $stmtUserOpt = $con->prepare("SELECT option_text FROM tbloptions WHERE optionID=?");
        $stmtUserOpt->execute([$q['user_option']]);
        $userText = $stmtUserOpt->fetchColumn();

        $class = ($userText == $correctText) ? "correctAnswer" : "wrongAnswer";

        // تحويل الملي ثانية إلى ثواني (مع 2 رقم عشري)
        $timeSeconds = $q['time_taken'] ? round($q['time_taken'] / 1000, 2) : 0;

        echo "<tr class='{$class}'>
                <td>".htmlspecialchars($q['question_text'])."</td>
                <td>".htmlspecialchars($userText)."</td>
                <td>".htmlspecialchars($correctText)."</td>
                <td>{$timeSeconds} ثانية ({$q['time_taken']} ms)</td>
              </tr>";
    }

    echo "</tbody></table>";
}
