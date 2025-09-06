<?php
session_start();
include '../../settings/connect.php';

if(!isset($_SESSION['adminID'])) exit;

$action = $_REQUEST['action'] ?? '';

if($action == 'list'){
    $stmt = $con->prepare("SELECT * FROM tblnews ORDER BY newsID DESC");
    $stmt->execute();
    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if($news){
        foreach($news as $n){
            echo '<div class="hadith-card mb-2 p-2 border rounded d-flex justify-content-between align-items-center">';
            echo '<span class="hadith-title">'.htmlspecialchars($n['Title']).'</span>';
            echo '<div>';
            echo '<button class="btn btn-sm btn-primary editBtn" data-id="'.$n['newsID'].'">تعديل</button> ';
            echo '<button class="btn btn-sm btn-danger deleteBtn" data-id="'.$n['newsID'].'">حذف</button>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>لا توجد أحاديث حتى الآن.</p>';
    }
}

if($action == 'add'){
    $title = $_POST['title'] ?? '';
    if($title != ''){
        $stmt = $con->prepare("INSERT INTO tblnews (Title) VALUES (?)");
        $stmt->execute([$title]);
    }
    exit;
}

if($action == 'edit'){
    $newsID = $_POST['newsID'] ?? 0;
    $title = $_POST['title'] ?? '';
    if($newsID && $title != ''){
        $stmt = $con->prepare("UPDATE tblnews SET Title=? WHERE newsID=?");
        $stmt->execute([$title, $newsID]);
    }
    exit;
}

if($action == 'delete'){
    $newsID = $_POST['newsID'] ?? 0;
    if($newsID){
        $stmt = $con->prepare("DELETE FROM tblnews WHERE newsID=?");
        $stmt->execute([$newsID]);
    }
    exit;
}
?>
