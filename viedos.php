<?php 
    include 'settings/connect.php';
    include 'common/function.php';
    include 'common/head.php';

    $channelID = "UCORmv51RfJPLlVXfrR6ii9A";

    // جلب الفيديوهات من RSS
    $rss = simplexml_load_file("https://www.youtube.com/feeds/videos.xml?channel_id={$channelID}");

    $videos = [];
    foreach ($rss->entry as $entry) {
        $title = (string) $entry->title;
        $description = (string) $entry->children('media', true)->group->description;
        $videoId = str_replace("yt:video:", "", (string) $entry->id);

        $videos[] = [
            "id" => $videoId,
            "title" => $title,
            "description" => $description
        ];
    }
?>
    
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="common/zahraastyle.css?v=1.1">
    <link rel="stylesheet" href="css/viedos.css?v=1.2">
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
    <div class="videos-container">
        <h1 class="page-title">فيديوهات القناة</h1>

        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="ابحث عن فيديو...">
        </div>

        <div class="video-list">
            <?php foreach ($videos as $v): ?>
            <div class="video-card" data-title="<?= htmlspecialchars($v['title']) ?>" data-description="<?= htmlspecialchars($v['description']) ?>">
                <div class="video-frame">
                    <iframe src="https://www.youtube.com/embed/<?= $v['id'] ?>" allowfullscreen></iframe>
                </div>
                <div class="video-info">
                    <h3><?= htmlspecialchars($v['title']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($v['description'])) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php include 'common/jslinks.php'?>
    <script src="index.js"></script>
    <script>
        const searchInput = document.getElementById("searchInput");
        const videoCards = document.querySelectorAll(".video-card");

        searchInput.addEventListener("input", () => {
            const query = searchInput.value.toLowerCase();
            videoCards.forEach(card => {
                const title = card.getAttribute("data-title").toLowerCase();
                const desc = card.getAttribute("data-description").toLowerCase();
                if (title.includes(query) || desc.includes(query)) {
                    card.style.display = "flex";
                } else {
                    card.style.display = "none";
                }
            });
        });
    </script>
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