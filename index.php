<?php 
    //include 'settings/connect.php';
    include 'common/function.php';
    include 'common/head.php';
?>
    
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="common/zahraastyle.css">
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <audio id="myAudio" autoplay muted>
        <source src="sound/123.mp3" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
    <div class="container">
        <div class="input">
            <div class="title">
                
                <h3>يا فاطمة الزهراء</h3>
            </div>
            <div class="coin">
                <img src="images/logo.png" alt="" srcset="">
            </div>
            <div class="para">
                <p>"هنا، حيث تسكن الأرواح الباحثة عن النور،
                    وحيث تتعطّر القلوب بذكر فاطمة الزهراء عليها السلام،
                    نفتح نافذةً على عالم الطهر،
                    على سيدةٍ حملت سرّ الرسالة،  حقيقة المظلومية، وبهاء الخلود.

                    هنا لا نقرأ سيرةً عابرة، بل نرتشف من معينٍ لا ينضب،
                    من روحٍ أضاءت الدهر، وبقيت شمسًا لا تغيب،
                    هنا نلج باب الزهراء… باب الرحمة، باب الولاية، باب الحق.

                    من أراد أن يعرف معنى النقاء، فليصغِ لصوتها،
                    ومن أراد أن يلمس جوهر العدل، فليتأمل دمعتها،
                    ومن أراد أن يقترب من الله… فليدخل من نور فاطمة"
                </p>
            </div>
            <div class="followus">
              <!-- TikTok -->
              <a href="https://www.tiktok.com/@fatme.b.313?_t=ZS-8zQayNh5eVz&_r=1" target="_blank">
                <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                  <!-- دائرة بيضاء حول الأيقونة -->
                  <circle cx="24" cy="24" r="22" fill="none" stroke="white" stroke-width="2"/>
                  <!-- لوغو أبيض داخل الدائرة -->
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
            <div class="comingsoon">
                <p>قريبًا</p>
            </div>
        </div>

        
        
    </div>
</body>
<script>
  window.addEventListener("load", function() {
    let audio = document.getElementById("myAudio");
    setTimeout(() => {
      audio.muted = false; 
      audio.play();
    }, 1000); // try to unmute after 1s
  });
</script>