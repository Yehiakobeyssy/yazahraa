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
            <span>عدد الاقسام: <?= $bio['section_count'] ?></span>
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
    <p><?= nl2br($bio['description'])?></p>
    <div class="sections-container">
        <?php foreach($sections as $section): ?>
        <div class="section-card">
            <h2><?= $section['title'] ?> 
                <button onclick="playVoice('section_<?= $section['sectionID'] ?>',this)">🔊</button>
            </h2>
            <p id="section_<?= $section['sectionID'] ?>"><?= nl2br($section['introduction']) .'<br>'.nl2br($section['content']) ?></p>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
</main>
    <?php include 'common/jslinks.php'?>
    <script src="js/biography.js?v=1.1"></script>
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