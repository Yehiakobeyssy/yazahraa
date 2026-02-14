<?php
session_start();
include 'settings/connect.php';
include 'common/function.php';
include 'common/head.php';

$do = $_GET['do'] ?? 'login'; // يمكن يكون login أو register
$msg = "";
    if(isset($_SESSION['userID'])){
        if (!empty($_GET['quizID'])) {
            header("Location: competitions.php?quizID=" . intval($_GET['quizID']));
            exit;
        } else {
            header("Location: competitions.php");
            exit;
        }
    }
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if ($do == "login") {
        $email = trim($_POST['email']);
        $pass  = sha1($_POST['password']);

        $stmt = $con->prepare("SELECT userID FROM tblusers WHERE email=? AND password=?");
        $stmt->execute([$email, $pass]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['userID'] = $user['userID'];
            $_SESSION['email']  = $email;

            if (!empty($_GET['quizID'])) {
                header("Location: competitions.php?quizID=" . intval($_GET['quizID']));
                exit;
            } else {
                header("Location: competitions.php");
                exit;
            }
        } else {
            $msg = "الإيميل أو كلمة المرور غير صحيحة.";
        }

    } elseif ($do == "register") {
        $email = trim($_POST['email']);
        $pass1 = $_POST['password'];
        $pass2 = $_POST['password_confirm'];

        if ($pass1 !== $pass2) {
            $msg = "كلمة المرور غير متطابقة.";
        } else {
            $stmt = $con->prepare("SELECT userID FROM tblusers WHERE email=?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
                $msg = "الإيميل مسجل مسبقًا.";
            } else {
                $stmt = $con->prepare("INSERT INTO tblusers (email,password) VALUES (?,?)");
                $stmt->execute([$email, sha1($pass1)]);
                $msg = "تم التسجيل بنجاح، يمكنك تسجيل الدخول الآن.";
            }
        }
    }
}
?>

    
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="common/zahraastyle.css?v=1.1">
    <link rel="stylesheet" href="css/login.css?v=1.1">
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

        <!-- زر المنيو -->
        <div class="menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <!-- القائمة -->
        <nav class="main-menu">
            <ul>
                <li><a href="index.php">الصفحة الرئيسية</a></li>
                <li><a href="prayers.php">مفاتيح محمدية</a></li>
                <li><a href="login.php">المسابقات</a></li>
                <li><a href="biography.php">السيرة</a></li>
                <li><a href="viedos.php">فيديوهات</a></li>
                <li><a href="contact.php">اتصل بنا</a></li>
            </ul>
        </nav>
    </header>
    <div class="auth-container">
        <div class="auth-box">
            <h2><?= $do == 'login' ? 'تسجيل الدخول' : 'تسجيل جديد' ?></h2>
            <?php if($msg): ?>
                <div class="alert"><?= $msg ?></div>
            <?php endif; ?>

            <?php if($do == 'login'): ?>
                <form method="POST">
                    <input type="email" name="email" placeholder="الإيميل" required>
                    <input type="password" name="password" placeholder="كلمة المرور" required>
                    <button type="submit">دخول</button>
                    <p class="switch-link">ليس لديك حساب؟ <a href="?do=register">سجل الآن</a></p>
                </form>
            <?php else: ?>
                <form method="POST">
                    <input type="hidden" name="action" value="register">
                    <input type="email" name="email" placeholder="الإيميل" required>
                    <input type="password" name="password" id="password" placeholder="كلمة المرور" required>
                    <input type="password" name="password_confirm" id="password_confirm" placeholder="تأكيد كلمة المرور" required>
                    <div id="pass_msg" style="color:#ffcc00;margin-top:5px;"></div>
                    <button type="submit">تسجيل جديد</button>
                    <p class="switch-link">لديك حساب؟ <a href="?do=login">تسجيل الدخول</a></p>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'common/jslinks.php'?>
    <script src="js/login.js"></script>
    <script>
        document.addEventListener('contextmenu', event => event.preventDefault());

document.addEventListener('copy', function(e) {
    e.preventDefault();
});

document.addEventListener('cut', function(e) {
    e.preventDefault();
});

document.addEventListener('paste', function(e) {
    e.preventDefault();
});
</script>
</body>