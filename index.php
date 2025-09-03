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
                    على سيدةٍ حملت سرّ الرسالة، ودمعة المظلومية، وبهاء الخلود.

                    هنا لا نقرأ سيرةً عابرة، بل نرتشف من معينٍ لا ينضب،
                    من روحٍ أضاءت الدهر، وبقيت شمسًا لا تغيب،
                    هنا نلج باب الزهراء… باب الرحمة، باب الولاية، باب الحق.

                    من أراد أن يعرف معنى النقاء، فليصغِ لصوتها،
                    ومن أراد أن يلمس جوهر العدل، فليتأمل دمعتها،
                    ومن أراد أن يقترب من الله… فليدخل من نور فاطمة"
                </p>
            </div>
            <div class="followus">
                <a href="https://www.tiktok.com/@fatme.b.313?_t=ZS-8zQayNh5eVz&_r=1" target="_blank"><img src="images/syinpol/tiktok.png" alt=""></a>
                <a href="https://t.me/yafatimaallzahraa" target="_blank"><img src="images/syinpol/telegram.png" alt=""></a>
                <a href="https://youtube.com/@yafatmezahraaa?si=JQvXpX_PpA3SrKP8" target="_blank"><img src="images/syinpol/youtupe.png" alt=""></a>
                <a href="https://www.instagram.com/fatme.b.313?igsh=MTU5d3Zsb204M2Qwbw%3D%3D&utm_source=qr" target="_blank"><img src="images/syinpol/insta.png" alt=""></a>
                <a href="https://www.facebook.com/share/14GKLnmragE/?mibextid=wwXIfr" target="_blank"><img src="images/syinpol/fb.png" alt=""></a>
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