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
<link rel="stylesheet" href="css/prayers.css">

<body>
<header class="site-header">
    <div class="branding">
        <div class="coin"><img src="images/logo.png" alt=""></div>
        <div>
            <div class="site-title">يا فاطمة الزهراء</div>
            <div class="site-sub">موقع توثيقي عن مظلومية السيدة فاطمة الزهراء عليها السلام</div>
        </div>
    </div>
    <div class="menu-toggle"><span></span><span></span><span></span></div>
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

<main class="prayers-container">

<?php if($do == 'manage'): ?>

    <h2>الأدعية</h2>
    <input type="text" id="searchInput" placeholder="ابحث عن دعاء..." class="search-input">

    <div id="prayersTree" class="prayers-tree">
        <?php 
        $sections = $con->query("SELECT * FROM tbl_prayer_sections ORDER BY title ASC")->fetchAll(PDO::FETCH_ASSOC);
        foreach($sections as $sec):
        ?>
        <div class="section">
            <div class="section-title"><?= $sec['title'] ?></div>
            <div class="subsections" style="display:none;">
                <?php 
                $subsections = $con->prepare("SELECT * FROM tbl_prayer_subsections WHERE sectionID=? ORDER BY title ASC");
                $subsections->execute([$sec['sectionID']]);
                $subs = $subsections->fetchAll(PDO::FETCH_ASSOC);
                foreach($subs as $sub):
                ?>
                    <div class="subsection">
                        <div class="subsection-title"><?= $sub['title'] ?></div>
                        <div class="prayers" style="display:none;">
                            <?php 
                            $prayers = $con->prepare("SELECT * FROM tbl_prayers WHERE subsectionID=? ORDER BY prayer_title ASC");
                            $prayers->execute([$sub['subsectionID']]);
                            foreach($prayers as $pr):
                            ?>
                                <div class="prayer" data-id="<?= $pr['prayerID'] ?>"><?= $pr['prayer_title'] ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

<?php elseif($do == 'read' && isset($_GET['prayerID'])): 

    $prayerID = intval($_GET['prayerID']);
    $stmt = $con->prepare("SELECT * FROM tbl_prayers WHERE prayerID=?");
    $stmt->execute([$prayerID]);
    $prayer = $stmt->fetch(PDO::FETCH_ASSOC);

    if($prayer):
?>
    <div class="prayer-read">
        <h2><?= $prayer['prayer_title'] ?> <button class="play-audio" data-id="<?= $prayerID ?>" onclick="playVoice('contant')">🔊</button></h2>
        <p id="contant"><?= nl2br($prayer['content']) ?></p>
    </div>
<?php else: ?>
    <p>هذا الدعاء غير موجود.</p>
<?php endif; endif; ?>

</main>

<?php include 'common/jslinks.php' ?>
<script src="js/prayers.js"></script>
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
        window.playVoice = function(id){
        const text = document.getElementById(id).innerText;
        const utter = new SpeechSynthesisUtterance(text);
        utter.lang = 'ar-SA';
        speechSynthesis.speak(utter);
        }
    </script>
</body>
