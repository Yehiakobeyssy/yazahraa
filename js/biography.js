$(function(){
    // فتح/إغلاق القائمة عند الضغط على الهامبرغر
    $(".menu-toggle").click(function(e){
        e.stopPropagation(); // منع اختفاء القائمة عند الضغط على الزر
        $(".main-menu ul").toggleClass("show");
    });

    // منع اختفاء القائمة عند الضغط داخلها
    $(".main-menu ul").click(function(e){
        e.stopPropagation();
    });

    // إخفاء القائمة عند الضغط خارجها
    $(document).click(function(){
        $(".main-menu ul").removeClass("show");
    });

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if(searchInput){
        searchInput.addEventListener('input', function(){
            const query = this.value.toLowerCase();
            const cards = document.querySelectorAll('.bio-card');
            cards.forEach(card => {
                const title = card.querySelector('h2').textContent.toLowerCase();
                const desc = card.querySelector('p').textContent.toLowerCase();
                if(title.includes(query) || desc.includes(query)){
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }


});
