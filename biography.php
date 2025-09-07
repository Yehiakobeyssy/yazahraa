<?php 
    include 'settings/connect.php';
    include 'common/function.php';
    include 'common/head.php';
    $do = $_GET['do'] ?? 'manage';
?>
    
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="common/zahraastyle.css?v=1.1">
    <link rel="stylesheet" href="css/biography.css">
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
    <main class="biography-container">
<?php if($do == 'manage'): ?>
    <h1 class="page-title">سيرة أهل البيت</h1>
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="ابحث عن السيرة...">
    </div>
    <div class="biography-cards">
        <?php
        $stmt = $con->prepare("SELECT b.*, COUNT(s.sectionID) as section_count 
                               FROM tbl_biography b 
                               LEFT JOIN tbl_biography_sections s ON b.bioID = s.bioID 
                               GROUP BY b.bioID");
        $stmt->execute();
        $bios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($bios as $bio):
        ?>
        <div class="bio-card" onclick="location.href='biography.php?do=read&bioID=<?= $bio['bioID'] ?>'">
            <h2><?= $bio['title'] ?></h2>
            <p><?= mb_strimwidth($bio['description'], 0, 120, "...") ?></p>
            <span>عدد الفصول: <?= $bio['section_count'] ?></span>
        </div>
        <?php endforeach; ?>
    </div>

<?php elseif($do == 'read' && isset($_GET['bioID'])): 
    $bioID = intval($_GET['bioID']);
    $stmtBio = $con->prepare("SELECT * FROM tbl_biography WHERE bioID=?");
    $stmtBio->execute([$bioID]);
    $bio = $stmtBio->fetch(PDO::FETCH_ASSOC);

    $stmtSections = $con->prepare("SELECT * FROM tbl_biography_sections WHERE bioID=? ORDER BY created_at ASC");
    $stmtSections->execute([$bioID]);
    $sections = $stmtSections->fetchAll(PDO::FETCH_ASSOC);
?>
    <h1 class="page-title"><?= $bio['title'] ?></h1>
    <div class="sections-container">
        <?php foreach($sections as $section): ?>
        <div class="section-card">
            <h2><?= $section['title'] ?> 
                <button onclick="playVoice('section_<?= $section['sectionID'] ?>')">🔊</button>
            </h2>
            <p id="section_<?= $section['sectionID'] ?>"><?= nl2br($section['content']) ?></p>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
</main>
    <?php include 'common/jslinks.php'?>
    <script src="js/biography.js"></script>
    <script>
        async function playVoice(id) {
            let text = document.getElementById(id).innerText;
            console.log("النص المرسل:", text);

            try {
                let response = await fetch("tts.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "text=" + encodeURIComponent(text)
                });

                let data = await response.json();

                if (data.error) {
                    console.log("Error: " + data.error);
                    alert("حدث خطأ: " + data.error);
                    return;
                }

                let audio = new Audio("data:audio/mp3;base64," + data.audio);
                audio.play();

            } catch (err) {
                console.error(err);
                console.log("حدث خطأ أثناء تشغيل الصوت");
                alert("حدث خطأ أثناء تشغيل الصوت");
            }
        }
    </script>
</body>