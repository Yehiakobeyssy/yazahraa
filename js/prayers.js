document.addEventListener('DOMContentLoaded', function(){

    // Toggle Sections
    document.querySelectorAll('.section-title').forEach(section => {
        section.addEventListener('click', function(){
            const sub = section.nextElementSibling;
            if(sub) sub.style.display = (sub.style.display === 'block') ? 'none' : 'block';
        });
    });

    // Toggle Subsections
    document.querySelectorAll('.subsection-title').forEach(sub => {
        sub.addEventListener('click', function(){
            const prayers = sub.nextElementSibling;
            if(prayers) prayers.style.display = (prayers.style.display === 'block') ? 'none' : 'block';
        });
    });

    // فتح صفحة قراءة الدعاء عند الضغط
    document.querySelectorAll('.prayer').forEach(pr => {
        pr.addEventListener('click', function(){
            const id = pr.getAttribute('data-id');
            window.location.href = 'prayers.php?do=read&prayerID=' + id;
        });
    });

    // البحث
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', function(){
        const val = this.value.toLowerCase();

        document.querySelectorAll('.section').forEach(section => {
            let sectionText = section.querySelector('.section-title').textContent.toLowerCase();
            let showSection = sectionText.includes(val);

            section.querySelectorAll('.subsection').forEach(sub => {
                let subText = sub.querySelector('.subsection-title').textContent.toLowerCase();
                let showSub = subText.includes(val);

                sub.querySelectorAll('.prayer').forEach(pr => {
                    let prText = pr.textContent.toLowerCase();
                    pr.style.display = prText.includes(val) ? 'block' : 'none';
                    if(prText.includes(val)) showSub = true;
                });

                sub.style.display = showSub ? 'block' : 'none';
                if(showSub) showSection = true;
            });

            section.style.display = showSection ? 'block' : 'none';
        });
    });

    // Menu toggle
    const menuToggle = document.querySelector('.menu-toggle');
    menuToggle.addEventListener('click', e=>{
        e.stopPropagation();
        document.querySelector('.main-menu ul').classList.toggle('show');
    });
    document.addEventListener('click', ()=>{
        document.querySelector('.main-menu ul').classList.remove('show');
    });
});
