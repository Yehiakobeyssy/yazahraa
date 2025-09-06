$(document).ready(function(){

    // دالة لجلب الأحاديث
    function loadHadiths(){
        $.get('ajax/news.php', {action: 'list'}, function(data){
            $('#hadithList').html(data);
        });
    }

    loadHadiths(); // تحميل الأحاديث عند فتح الصفحة

    // إضافة حديث جديد
    $('#addHadithBtn').click(function(){
        var title = $('#newTitle').val().trim();
        if(title == ''){
            alert('أدخل عنوان الحديث');
            return;
        }

        $.post('ajax/news.php', {action:'add', title:title}, function(data){
            $('#addAlert').html('<div class="alert alert-success">تم إضافة الحديث بنجاح</div>');
            $('#newTitle').val('');
            loadHadiths();
        });
    });

    // تعديل أو حذف سيتم عبر delegate لأنه العناصر تتغير ديناميكياً
    $('#hadithList').on('click', '.editBtn', function(){
        var newsID = $(this).data('id');
        var currentTitle = $(this).closest('.hadith-card').find('.hadith-title').text();
        var newTitle = prompt("عدل عنوان الحديث:", currentTitle);
        if(newTitle && newTitle.trim() != ''){
            $.post('ajax/news.php', {action:'edit', newsID:newsID, title:newTitle}, function(data){
                loadHadiths();
            });
        }
    });

    $('#hadithList').on('click', '.deleteBtn', function(){
        if(confirm('هل أنت متأكد من حذف الحديث؟')){
            var newsID = $(this).data('id');
            $.post('ajax/news.php', {action:'delete', newsID:newsID}, function(data){
                loadHadiths();
            });
        }
    });
    

    $('#uploadSlideForm').submit(function(e){
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: 'ajax/delete_slide.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                $('#uploadAlert').html('<div class="alert alert-success">تم رفع الصورة بنجاح</div>');
                $('#uploadSlideForm')[0].reset();
                location.reload(); // إعادة تحميل الصفحة لعرض الصورة الجديدة
            }
        });
    });

    // حذف الصورة
    $('.deleteSlideBtn').click(function(){
        if(confirm('هل أنت متأكد من حذف الصورة؟')){
            var slideID = $(this).data('id');
            $.post('ajax/delete_slide.php', {action:'delete', slideID:slideID}, function(res){
                location.reload();
            });
        }
    });

});