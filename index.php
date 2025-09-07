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
    <div class="news-bar">
      <div class="news-wrapper">
          <?php
          $stmt = $con->prepare("SELECT Title FROM tblnews ORDER BY newsID DESC LIMIT 10");
          $stmt->execute();
          $news = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if($news) {
              // نكرر الأخبار مرتين لضمان حركة سلسة
              foreach([$news,$news] as $set){
                  foreach($set as $item){
                      echo '<span class="news-item">'.$item['Title'].'</span>';
                      echo '<img src="images/logo.png" alt="" srcset="">';
                  }
              }
          } else {
              echo '<span class="news-item">مرحبا بكم في موقعنا، تابعوا آخر الأخبار والمسابقات!</span>';
          }
          ?>
      </div>
    </div>

    <div class="slideshow-container">
        <?php
        $stmt = $con->prepare("SELECT slideImg FROM tblslideshow ORDER BY slideID ASC");
        $stmt->execute();
        $slides = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if($slides) {
            foreach($slides as $index => $slide){
                echo '<div class="mySlide fade">';
                echo '<img src="images/slideshow/'.$slide['slideImg'].'" alt="Slide '.$index.'">';
                echo '</div>';
            }
        } else {
            echo '<div class="mySlide fade"><img src="images/slideshow/default.jpg" alt="Default Slide"></div>';
        }
        ?>
    </div>
        <?php 
            $sql=$con->prepare('SELECT introduction,finish FROM  tblparagraf WHERE phragrafID = 1');
            $sql->execute();
            $result= $sql->fetch();
        ?>
    <section class="content-section-columns">
      <div class="column">
          <h2>مقدمة</h2>
          <p id="col1"><?php  echo nl2br(htmlspecialchars($result['introduction'])) ?></p>
          <button onclick="playVoice('col1')">🔊 </button>
      </div>

      <div class="column">
          <h2>إهداء</h2>
          <p id="col2"><?php  echo nl2br(htmlspecialchars($result['finish'])) ?></p>
          <button onclick="playVoice('col2')">🔊</button>
      </div>
    </section>

    <footer class="site-footer">
        <div class="footer-content">
            <div class="followus">
                <!-- TikTok -->
                <a href="https://www.tiktok.com/@fatme.b.313?_t=ZS-8zQayNh5eVz&_r=1" target="_blank">
                    <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                      <circle cx="24" cy="24" r="22" fill="none" stroke="white" stroke-width="2"/>
                      <path fill="white" d="M27 6h3c.22 1.1.82 2.48 1.87 3.77C33.92 11.99 35.3 13 37 13v3c-2.66 0-4.66-1.24-6-2.78V32a8 8 0 1 1-8-8v3a5 5 0 1 0 5 5z"/>
                    </svg>
                </a>

                <!-- Telegram -->
                <a href="https://t.me/yafatimaallzahraa" target="_blank">
                    <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                      <circle cx="24" cy="24" r="22" fill="none" stroke="white" stroke-width="2"/>
                      <path fill="white" d="M34.6 13.2 11.9 21.6c-1.1.4-1.1 1.1-.2 1.4l5.6 1.8 2.1 6.7c.2.5.5.6 1 .3l3-2.4 5.8 4.3c.7.4 1.2.2 1.4-.7l3.2-16.1c.3-1.1-.3-1.6-1.2-1.3z"/>
                    </svg>
                </a>

                <!-- YouTube -->
                <a href="https://youtube.com/@yafatmezahraaa?si=JQvXpX_PpA3SrKP8" target="_blank">
                    <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                      <circle cx="24" cy="24" r="22" fill="none" stroke="white" stroke-width="2"/>
                      <path fill="white" d="M20 17v14l12-7z"/>
                    </svg>
                </a>

                <!-- Instagram -->
                <a href="https://www.instagram.com/fatme.b.313?igsh=MTU5d3Zsb204M2Qwbw%3D%3D&utm_source=qr" target="_blank">
                    <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                      <circle cx="24" cy="24" r="22" fill="none" stroke="white" stroke-width="2"/>
                      <path fill="white" d="M24 16a8 8 0 1 0 0 16 8 8 0 0 0 0-16zm0 13a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm7-13a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                    </svg>
                </a>

                <!-- Facebook -->
                <a href="https://www.facebook.com/share/14GKLnmragE/?mibextid=wwXIfr" target="_blank">
                    <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                      <circle cx="24" cy="24" r="22" fill="none" stroke="white" stroke-width="2"/>
                      <path fill="white" d="M27 17h-3c-.5 0-1 .5-1 1v3h4v4h-4v12h-4V25h-3v-4h3v-3c0-2.2 1.8-4 4-4h4z"/>
                    </svg>
                </a>
            </div>
            <p class="footer-copy">جميع الحقوق محفوظة © 2025 يا فاطمة الزهراء</p>
        </div>
    </footer>

    <?php include 'common/jslinks.php'?>
    <script src="index.js"></script>
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
