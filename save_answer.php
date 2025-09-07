<?php
session_start();
include 'settings/connect.php';

$competitionID = $_POST['competitionID'];
$questionID = $_POST['questionID'];
$userID = $_POST['userID'];
$optionID = $_POST['optionID'];
$is_correct = $_POST['is_correct'];

// Delete previous answers if this is the first question of the quiz
if (isset($_POST['first_question']) && $_POST['first_question'] == 1) {
    $stmt = $con->prepare("DELETE FROM tblanswers WHERE competitionID=? AND userID=?");
    $stmt->execute([$competitionID, $userID]);
}

// Insert the current answer
$stmt = $con->prepare("INSERT INTO tblanswers (competitionID, questionID, userID, optionID, is_correct) VALUES (?,?,?,?,?)");
$stmt->execute([$competitionID, $questionID, $userID, $optionID, $is_correct]);

echo "ok";
?>
