<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<?php 
    session_start();
    include '../settings/connect.php';
    include '../common/function.php';
    include '../common/head.php';


    if(isset($_SESSION['adminID']) && !empty($_SESSION['adminID'])){
        header("Location: dashboard.php");
        exit;
    }


    $error = '';

    if(isset($_POST['login'])){
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if($username != '' && $password != ''){
            $stmt = $con->prepare("SELECT * FROM tbladmin WHERE username = :username LIMIT 1");
            $stmt->execute([':username' => $username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if($admin){
                // تحقق من كلمة المرور باستخدام SHA1
                if(sha1($password) === $admin['adminPassword']){
                    $_SESSION['adminID'] = $admin['adminID'];
                    $_SESSION['adminName'] = $admin['FName'] . ' ' . $admin['LName'];
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $error = "كلمة المرور غير صحيحة";
                }
            } else {
                $error = "اسم المستخدم غير موجود";
            }
        } else {
            $error = "يرجى تعبئة جميع الحقول";
        }
    }

    ?>
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="common/zahraastyle.css?v=1.1">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>

<div class="login-box">
    <h2>تسجيل دخول الادمين</h2>
    <?php if($error != ''): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <form method="post" action="">
        <input type="text" name="username" placeholder="اسم المستخدم" required>
        <input type="password" name="password" placeholder="كلمة المرور" required>
        <button type="submit" name="login">دخول</button>
    </form>
</div>

</body>
</html>
