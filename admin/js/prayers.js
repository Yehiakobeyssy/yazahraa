document.addEventListener('DOMContentLoaded', function(){

    // عناصر المودالات
    const modalSection = document.getElementById('modalSection');
    const modalSub = document.getElementById('modalSub');
    const modalPrayer = document.getElementById('modalPrayer');
    const modalView = document.getElementById('modalView');

    // فتح المودالات بحسب الأزرار
    document.getElementById('openAddSection').addEventListener('click', ()=> openSectionModal('add'));
    document.getElementById('openAddSub').addEventListener('click', ()=> openSubModal('add'));
    document.getElementById('openAddPrayer').addEventListener('click', ()=> openPrayerModal('add'));

    // إغلاق المشترك
    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', closeAllModals);
    });

    // --- فتح مودال قسم ---
    function openSectionModal(mode, row=null){
        modalSection.style.display='flex';
        const title = document.getElementById('modalSectionTitle');
        const form = document.getElementById('formSection');
        form.dataset.action = (mode==='add') ? 'add_section' : 'edit_section';
        if(mode==='add'){
            title.textContent='إضافة قسم';
            form.sectionID.value='';
            form.title.value='';
        } else {
            title.textContent='تعديل قسم';
            form.sectionID.value = row.dataset.id;
            form.title.value = row.querySelector('.col-title').textContent.trim();
        }
    }

    // --- فتح مودال فرع ---
    function openSubModal(mode, row=null){
        modalSub.style.display='flex';
        const title = document.getElementById('modalSubTitle');
        const form = document.getElementById('formSub');
        form.dataset.action = (mode==='add') ? 'add_sub' : 'edit_sub';
        if(mode==='add'){
            title.textContent='إضافة فرع';
            form.subsectionID.value='';
            form.sectionID.value='';
            form.title.value='';
        } else {
            title.textContent='تعديل فرع';
            form.subsectionID.value = row.dataset.id;
            form.title.value = row.querySelector('.col-title').textContent.trim();
            // تعيين القسم
            const secName = row.children[1].textContent.trim();
            for(let i=0;i<form.sectionID.options.length;i++){
                if(form.sectionID.options[i].textContent.trim()===secName){
                    form.sectionID.value=form.sectionID.options[i].value;
                    break;
                }
            }
        }
    }

    // --- فتح مودال دعاء ---
    function openPrayerModal(mode, row=null){
        modalPrayer.style.display='flex';
        const title = document.getElementById('modalPrayerTitle');
        const form = document.getElementById('formPrayer');
        form.dataset.action = (mode==='add') ? 'add_prayer' : 'edit_prayer';
        const sectionSelect = document.getElementById('prayerSectionSelect');
        const subSelect = document.getElementById('prayerSubSelect');

        if(mode==='add'){
            title.textContent='إضافة دعاء';
            form.prayerID.value='';
            form.prayer_title.value='';
            form.content.value='';
            sectionSelect.value='';
            subSelect.value='';
        } else {
            title.textContent='تعديل دعاء';
            form.prayerID.value = row.dataset.id;

            // جلب بيانات الدعاء عبر AJAX
            fetch(AJAX_URL+'?action=get_prayer&id='+row.dataset.id)
                .then(res=>res.json())
                .then(data=>{
                    if(data.success){
                        form.prayer_title.value = data.row.prayer_title;
                        form.content.value = data.row.content;
                        sectionSelect.value = data.sectionID;

                        // إظهار الفرع الصحيح وتعيينه
                        Array.from(subSelect.options).forEach(opt=>{
                            opt.style.display = (opt.dataset.section === data.sectionID.toString()) ? '' : 'none';
                        });
                        subSelect.value = data.subsectionID;
                    } else alert('خطأ في جلب بيانات الدعاء');
                });
        }
    }

    // --- عرض دعاء ---
    function openViewModal(html){
        modalView.style.display='flex';
        document.getElementById('modalViewBody').innerHTML = html;
    }

    function closeAllModals(){
        document.querySelectorAll('.modal').forEach(m=> m.style.display='none');
    }

    // --- أحداث الجداول (edit/delete/view) ---
    document.body.addEventListener('click', function(e){
        const row = e.target.closest('tr');

        // --- الأقسام ---
        if(e.target.matches('.edit-section')){ openSectionModal('edit', row); return; }
        if(e.target.matches('.delete-section')){
            if(!confirm('هل أنت متأكد؟')) return;
            fetch(AJAX_URL,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'action=delete_section&sectionID='+encodeURIComponent(row.dataset.id)})
            .then(r=>r.json()).then(res=>{if(res.success) row.remove(); else alert(res.msg||'خطأ');});
            return;
        }

        // --- الفروع ---
        if(e.target.matches('.edit-sub')){ openSubModal('edit', row); return; }
        if(e.target.matches('.delete-sub')){
            if(!confirm('هل أنت متأكد؟')) return;
            fetch(AJAX_URL,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'action=delete_sub&subsectionID='+encodeURIComponent(row.dataset.id)})
            .then(r=>r.json()).then(res=>{if(res.success) row.remove(); else alert(res.msg||'خطأ');});
            return;
        }

        // --- الأدعية ---
        if(e.target.matches('.edit-prayer')){ openPrayerModal('edit', row); return; }
        if(e.target.matches('.delete-prayer')){
            if(!confirm('هل أنت متأكد؟')) return;
            fetch(AJAX_URL,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'action=delete_prayer&prayerID='+encodeURIComponent(row.dataset.id)})
            .then(r=>r.json()).then(res=>{if(res.success) row.remove(); else alert(res.msg||'خطأ');});
            return;
        }
        if(e.target.matches('.view-prayer')){
            fetch(AJAX_URL+'?action=get_prayer&id='+encodeURIComponent(row.dataset.id))
                .then(r=>r.json())
                .then(res=>{
                    if(res.success){
                        const html = '<h4>'+res.section_title+' › '+res.sub_title+'</h4>'
                                   + '<div style="white-space:pre-wrap; margin-top:10px;">'+escapeHtml(res.row.content)+'</div>';
                        openViewModal(html);
                    } else alert('خطأ في جلب الدعاء');
                });
            return;
        }
    });

    // --- تصفية الفروع حسب القسم في مودال الدعاء ---
    const prayerSectionSelect = document.getElementById('prayerSectionSelect');
    const prayerSubSelect = document.getElementById('prayerSubSelect');
    if(prayerSectionSelect){
        prayerSectionSelect.addEventListener('change', function(){
            const sec = this.value;
            Array.from(prayerSubSelect.options).forEach(opt=>{
                if(!opt.value) return;
                opt.style.display = (opt.dataset.section === sec) ? '' : 'none';
            });
            prayerSubSelect.value = '';
        });
    }

    // --- إرسال النماذج عبر AJAX ---
    const initForm = form => {
        form.addEventListener('submit', e=>{
            e.preventDefault();
            const action = form.dataset.action;
            const formData = new FormData(form);
            formData.append('action', action);

            fetch(AJAX_URL,{method:'POST',body:formData})
                .then(r=>r.json())
                .then(res=>{
                    if(res.success) location.reload();
                    else alert(res.msg||'خطأ غير متوقع');
                }).catch(err=>{ console.error(err); alert('خطأ في الاتصال'); });
        });
    };

    ['formSection','formSub','formPrayer'].forEach(id=>{
        const f=document.getElementById(id);
        if(f) initForm(f);
    });

    function escapeHtml(str){
        return (str+'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }
});
