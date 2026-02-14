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
    <link rel="stylesheet" href="css/prayers.css?v=1.2">

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
            <li><a href="prayers.php">مفاتيح محمدية</a></li>
            <li><a href="login.php">المسابقات</a></li>
            <li><a href="biography.php">السيرة</a></li>
            <li><a href="viedos.php">فيديوهات</a></li>
            <li><a href="contact.php">اتصل بنا</a></li>
        </ul>
    </nav>
</header>

<main class="prayers-container">

<?php if($do == 'manage'): ?>

    <h2>مفاتيح محمدية</h2>
    <input type="text" id="searchInput" placeholder="ابحث عن دعاء /مناجات /زيارات..." class="search-input">

    <div id="prayersTree" class="prayers-tree">
        <?php 
        $sections = $con->query("SELECT * FROM tbl_prayer_sections ORDER BY sectionID  ASC")->fetchAll(PDO::FETCH_ASSOC);
        foreach($sections as $sec):
        ?>
        <div class="section">
            <div class="section-title"><?= $sec['title'] ?></div>
            <div class="subsections" style="display:none;">
                <?php 
                $subsections = $con->prepare("SELECT * FROM tbl_prayer_subsections WHERE sectionID=? ORDER BY subsectionID ASC");
                $subsections->execute([$sec['sectionID']]);
                $subs = $subsections->fetchAll(PDO::FETCH_ASSOC);
                foreach($subs as $sub):
                ?>
                    <div class="subsection">
                        <div class="subsection-title"><?= $sub['title'] ?></div>
                        <div class="prayers" style="display:none;">
                            <?php 
                            $prayers = $con->prepare("SELECT * FROM tbl_prayers WHERE subsectionID=? ORDER BY prayerID ASC");
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
        <h2><?= $prayer['prayer_title'] ?> <button class="play-audio" data-id="<?= $prayerID ?>" onclick="playVoice('contant',this)">🔊</button></h2>
        <p id="contant"><?= nl2br($prayer['content']) ?></p>
    </div>
<?php else: ?>
    <p>هذا الدعاء غير موجود.</p>
<?php endif; endif; ?>

</main>

<?php include 'common/jslinks.php' ?>
<script src="js/prayers.js"></script>
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
