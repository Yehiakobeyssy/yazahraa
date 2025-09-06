<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require '../../settings/connect.php'; // تأكد من المسار الصحيح

$action = $_REQUEST['action'] ?? '';

function json_response($success, $data=[]){
    echo json_encode(array_merge(['success'=>$success], $data));
    exit;
}

// ===== الأقسام =====
if($action === 'add_section'){
    $title = trim($_POST['title'] ?? '');
    if(!$title) json_response(false,['msg'=>'الرجاء إدخال اسم القسم']);
    $stmt = $GLOBALS['con']->prepare("INSERT INTO tbl_prayer_sections(title, created_at) VALUES(:title, NOW())");
    $stmt->execute([':title'=>$title]);
    json_response(true);
}

if($action === 'edit_section'){
    $id = intval($_POST['sectionID'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    if(!$id || !$title) json_response(false,['msg'=>'بيانات غير صالحة']);
    $stmt = $GLOBALS['con']->prepare("UPDATE tbl_prayer_sections SET title=:title WHERE sectionID=:id");
    $stmt->execute([':title'=>$title, ':id'=>$id]);
    json_response(true);
}

if($action === 'delete_section'){
    $id = intval($_POST['sectionID'] ?? 0);
    if(!$id) json_response(false);

    // حذف الأدعية التابعة للفروع أولاً
    $GLOBALS['con']->prepare("
        DELETE p FROM tbl_prayers p
        JOIN tbl_prayer_subsections sub ON p.subsectionID=sub.subsectionID
        WHERE sub.sectionID=:id
    ")->execute([':id'=>$id]);

    $GLOBALS['con']->prepare("DELETE FROM tbl_prayer_subsections WHERE sectionID=:id")->execute([':id'=>$id]);
    $GLOBALS['con']->prepare("DELETE FROM tbl_prayer_sections WHERE sectionID=:id")->execute([':id'=>$id]);
    json_response(true);
}

// ===== الفروع =====
if($action === 'add_sub'){
    $title = trim($_POST['title'] ?? '');
    $sectionID = intval($_POST['sectionID'] ?? 0);
    if(!$title || !$sectionID) json_response(false,['msg'=>'بيانات غير صالحة']);
    $stmt = $GLOBALS['con']->prepare("INSERT INTO tbl_prayer_subsections(sectionID, title, created_at) VALUES(:sectionID, :title, NOW())");
    $stmt->execute([':sectionID'=>$sectionID, ':title'=>$title]);
    json_response(true);
}

if($action === 'edit_sub'){
    $id = intval($_POST['subsectionID'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $sectionID = intval($_POST['sectionID'] ?? 0);
    if(!$id || !$title || !$sectionID) json_response(false,['msg'=>'بيانات غير صالحة']);
    $stmt = $GLOBALS['con']->prepare("UPDATE tbl_prayer_subsections SET title=:title, sectionID=:sectionID WHERE subsectionID=:id");
    $stmt->execute([':title'=>$title, ':sectionID'=>$sectionID, ':id'=>$id]);
    json_response(true);
}

if($action === 'delete_sub'){
    $id = intval($_POST['subsectionID'] ?? 0);
    if(!$id) json_response(false);
    $GLOBALS['con']->prepare("DELETE FROM tbl_prayers WHERE subsectionID=:id")->execute([':id'=>$id]);
    $GLOBALS['con']->prepare("DELETE FROM tbl_prayer_subsections WHERE subsectionID=:id")->execute([':id'=>$id]);
    json_response(true);
}

// ===== الأدعية =====
if($action === 'add_prayer'){
    $title = trim($_POST['prayer_title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $subsectionID = intval($_POST['subsectionID'] ?? 0);
    if(!$title || !$content || !$subsectionID) json_response(false,['msg'=>'الرجاء ملء جميع الحقول']);
    $stmt = $GLOBALS['con']->prepare("INSERT INTO tbl_prayers(prayer_title, content, subsectionID, created_at) VALUES(:title, :content, :subsectionID, NOW())");
    $stmt->execute([':title'=>$title, ':content'=>$content, ':subsectionID'=>$subsectionID]);
    json_response(true);
}

if($action === 'edit_prayer'){
    $id = intval($_POST['prayerID'] ?? 0);
    $title = trim($_POST['prayer_title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $subsectionID = intval($_POST['subsectionID'] ?? 0);
    if(!$id || !$title || !$content || !$subsectionID) json_response(false,['msg'=>'بيانات غير صالحة']);
    $stmt = $GLOBALS['con']->prepare("UPDATE tbl_prayers SET prayer_title=:title, content=:content, subsectionID=:subsectionID WHERE prayerID=:id");
    $stmt->execute([':title'=>$title, ':content'=>$content, ':subsectionID'=>$subsectionID, ':id'=>$id]);
    json_response(true);
}

if($action === 'delete_prayer'){
    $id = intval($_POST['prayerID'] ?? 0);
    if(!$id) json_response(false);
    $stmt = $GLOBALS['con']->prepare("DELETE FROM tbl_prayers WHERE prayerID=:id");
    $stmt->execute([':id'=>$id]);
    json_response(true);
}

if($action === 'get_prayer'){
    $id = intval($_GET['id'] ?? 0);
    if(!$id) json_response(false,['msg'=>'لا يوجد دعاء']);
    $stmt = $GLOBALS['con']->prepare("
        SELECT p.prayerID, p.prayer_title, p.content, sub.subsectionID, sub.title AS sub_title, sec.sectionID, sec.title AS section_title
        FROM tbl_prayers p
        JOIN tbl_prayer_subsections sub ON sub.subsectionID = p.subsectionID
        JOIN tbl_prayer_sections sec ON sec.sectionID = sub.sectionID
        WHERE p.prayerID=:id
    ");
    $stmt->execute([':id'=>$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$row) json_response(false,['msg'=>'الدعاء غير موجود']);
    json_response(true, [
        'row'=>$row,
        'subsectionID'=>$row['subsectionID'],
        'sectionID'=>$row['sectionID'],
        'sub_title'=>$row['sub_title'],
        'section_title'=>$row['section_title']
    ]);
}

// ===== البحث الحي =====
if($action === 'search'){
    $q = trim($_GET['q'] ?? '');
    $like = "%$q%";

    $sections = $GLOBALS['con']->prepare("SELECT *, 
        (SELECT COUNT(*) FROM tbl_prayer_subsections sub WHERE sub.sectionID=tbl_prayer_sections.sectionID) AS subsections_count,
        (SELECT COUNT(*) FROM tbl_prayers p JOIN tbl_prayer_subsections sub ON p.subsectionID=sub.subsectionID WHERE sub.sectionID=tbl_prayer_sections.sectionID) AS prayers_count
        FROM tbl_prayer_sections WHERE title LIKE :q");
    $sections->execute([':q'=>$like]);
    $sections = $sections->fetchAll(PDO::FETCH_ASSOC);

    $subsections = $GLOBALS['con']->prepare("SELECT sub.*, sec.title AS section_title 
        FROM tbl_prayer_subsections sub
        JOIN tbl_prayer_sections sec ON sec.sectionID=sub.sectionID
        WHERE sub.title LIKE :q");
    $subsections->execute([':q'=>$like]);
    $subsections = $subsections->fetchAll(PDO::FETCH_ASSOC);

    $prayers = $GLOBALS['con']->prepare("SELECT p.*, sub.title AS sub_title, sec.title AS section_title
        FROM tbl_prayers p
        JOIN tbl_prayer_subsections sub ON sub.subsectionID=p.subsectionID
        JOIN tbl_prayer_sections sec ON sec.sectionID=sub.sectionID
        WHERE p.prayer_title LIKE :q OR p.content LIKE :q");
    $prayers->execute([':q'=>$like]);
    $prayers = $prayers->fetchAll(PDO::FETCH_ASSOC);

    json_response(true,['sections'=>$sections,'subsections'=>$subsections,'prayers'=>$prayers]);
}

json_response(false,['msg'=>'إجراء غير معروف']);
