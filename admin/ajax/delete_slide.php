<?php
session_start();
include '../../settings/connect.php';

if(!isset($_SESSION['adminID'])) exit;

$action = $_POST['action'] ?? 'add';

// إضافة صورة
if($action == 'add' && isset($_FILES['slideImg'])){
    $file = $_FILES['slideImg'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('slide_') . '.' . $ext;

    $target = '../../images/slideshow/'.$filename;

    if(move_uploaded_file($file['tmp_name'], $target)){
        $stmt = $con->prepare("INSERT INTO tblslideshow (slideImg) VALUES (?)");
        $stmt->execute([$filename]);
    }
    exit;
}

// حذف صورة
if($action == 'delete'){
    $slideID = $_POST['slideID'] ?? 0;
    if($slideID){
        // جلب اسم الصورة
        $stmt = $con->prepare("SELECT slideImg FROM tblslideshow WHERE slideID=?");
        $stmt->execute([$slideID]);
        $slide = $stmt->fetch(PDO::FETCH_ASSOC);
        if($slide){
            $filePath = '../../images/slideshow/'.$slide['slideImg'];
            if(file_exists($filePath)) unlink($filePath);

            // حذف من قاعدة البيانات
            $stmt = $con->prepare("DELETE FROM tblslideshow WHERE slideID=?");
            $stmt->execute([$slideID]);
        }
    }
    exit;
}
?>
