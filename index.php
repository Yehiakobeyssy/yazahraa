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
    <link rel="stylesheet" href="index.css?v=1.7">
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
                <li><a href="prayers.php">مفاتيح المحمدية</a></li>
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
              for($i = 0; $i < 20; $i++){
                    foreach($news as $item){
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
          <button onclick="playVoice('col1', this)">🔊</button>
      </div>

      <div class="column">
          <h2>إهداء</h2>
          <p id="col2"><?php  echo nl2br(htmlspecialchars($result['finish'])) ?></p>
          <button onclick="playVoice('col2', this)">🔊</button>
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
    <script src="index.js?v=1.1"></script>
    <script>
        let currentAudio = null;
let currentBlobUrl = null;
let currentTextId = null;

function base64ToBlob(base64, mime = 'audio/mpeg') {
  const binary = atob(base64);
  const len = binary.length;
  const bytes = new Uint8Array(len);
  for (let i = 0; i < len; i++) bytes[i] = binary.charCodeAt(i);
  return new Blob([bytes], { type: mime });
}

async function playVoice(id, btn) {
  const el = document.getElementById(id);
  if (!el) {
    console.error('Element not found:', id);
    alert('Element not found: ' + id);
    return;
  }
  const text = el.innerText.trim();
  console.log('Requested text:', text);

  // If same text's audio is loaded -> toggle
  if (currentAudio && currentTextId === id) {
    if (!currentAudio.paused) {
      // stop
      currentAudio.pause();
      currentAudio.currentTime = 0;
      btn.textContent = '🔊';
      return;
    } else {
      // resume/replay
      try {
        await currentAudio.play();
        btn.textContent = '⏹';
      } catch (err) {
        console.error('Playback error (resume):', err);
        alert('Playback error: ' + err.message);
      }
      return;
    }
  }

  // If another audio exists, stop + cleanup
  if (currentAudio) {
    currentAudio.pause();
    currentAudio.src = '';
    if (currentBlobUrl) {
      URL.revokeObjectURL(currentBlobUrl);
      currentBlobUrl = null;
    }
    currentAudio = null;
    currentTextId = null;
    btn.textContent = '🔊';
  }

  // fetch new audio
  btn.disabled = true;
  btn.textContent = '...';

  try {
    const form = new URLSearchParams();
    form.append('text', text);

    const response = await fetch('tts.php', {
      method: 'POST',
      body: form
    });

    console.log('Fetch response:', response.status, response.headers.get('content-type'));

    if (!response.ok) {
      const txt = await response.text();
      throw new Error('Server returned ' + response.status + ': ' + txt);
    }

    const ct = (response.headers.get('content-type') || '').toLowerCase();
    let blob;

    if (ct.includes('application/json')) {
      const data = await response.json();
      console.log('Server JSON:', data);
      if (data.error) throw new Error(data.error);
      if (!data.audio) throw new Error('No "audio" field in JSON response');
      blob = base64ToBlob(data.audio, 'audio/mpeg');
    } else if (ct.startsWith('audio/')) {
      blob = await response.blob();
    } else {
      // try to parse as JSON as a fallback
      try {
        const data = await response.json();
        console.log('Fallback parsed JSON:', data);
        if (!data.audio) throw new Error('No "audio" field in fallback JSON');
        blob = base64ToBlob(data.audio, 'audio/mpeg');
      } catch (err) {
        const bodyText = await response.text();
        throw new Error('Unexpected response content-type (' + ct + '). Body: ' + bodyText);
      }
    }

    currentBlobUrl = URL.createObjectURL(blob);
    currentAudio = new Audio(currentBlobUrl);
    currentTextId = id;

    currentAudio.onended = () => {
      btn.textContent = '🔊';
      if (currentBlobUrl) {
        URL.revokeObjectURL(currentBlobUrl);
        currentBlobUrl = null;
      }
      currentAudio = null;
      currentTextId = null;
    };

    await currentAudio.play();
    btn.textContent = '⏹';

  } catch (err) {
    console.error('playVoice error:', err);
    alert('Error: ' + err.message);
    btn.textContent = '🔊';
  } finally {
    btn.disabled = false;
  }
}
        
    </script>
</body>
