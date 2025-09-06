document.addEventListener("DOMContentLoaded", function(){
    const hamburger = document.querySelector(".admin-header .hamburger");
    const aside = document.querySelector(".admin-aside");
    const closeBtn = document.querySelector(".admin-aside .aside-close");

    if(hamburger){
        hamburger.addEventListener("click", () => {
            aside.classList.add("active");
        });
    }

    if(closeBtn){
        closeBtn.addEventListener("click", () => {
            aside.classList.remove("active");
        });
    }

    // إغلاق عند الضغط خارج الـ aside
    document.addEventListener("click", (e) => {
        if(window.innerWidth <= 768){
            if(!aside.contains(e.target) && !hamburger.contains(e.target)){
                aside.classList.remove("active");
            }
        }
    });
});
