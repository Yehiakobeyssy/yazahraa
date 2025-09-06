$(document).ready(function(){

    // البحث عن الزوار
    $('#searchUser').on('keyup', function(){
        let query = $(this).val().toLowerCase();
        $('.user-row').each(function(){
            let email = $(this).find('td:first').text().toLowerCase();
            $(this).toggle(email.includes(query));
        });
    });

    // عند الضغط على صف الزائر لجلب التفاصيل
    $('.user-row').on('click', function(){
        let userID = $(this).data('userid');

        $.ajax({
            url: 'ajax/userDetails.php',
            type: 'POST',
            data: { userID: userID },
            success: function(response){
                $('#userDetails').html(response);
            }
        });
    });

});
