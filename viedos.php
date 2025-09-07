<?php 
    include 'settings/connect.php';
    include 'common/function.php';
    include 'common/head.php';
?>
    
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="common/zahraastyle.css?v=1.1">
    <link rel="stylesheet" href="index.css?v=1.6">
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
                <li><a href="prayers.php">الأدعية</a></li>
                <li><a href="login.php">المسابقات</a></li>
                <li><a href="biography.php">السيرة</a></li>
                <li><a href="viedos.php">فيديوهات</a></li>
                <li><a href="contact.php">اتصل بنا</a></li>
            </ul>
        </nav>
    </header>
    <?php include 'common/jslinks.php'?>
    <script src="index.js"></script>
</body>