/*
 This file was created by developers working at BitBag
 Do you need more information about us and what we do? Visit our https://bitbag.io website!
 We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

$('.bitbag-add-variant-to-wishlist').on('click', function(){
    var data = $(this).parents('form').first().serializeArray();
    data.push( {'name':$(this).attr('name')});
    $.ajax({
        type: 'POST',
        url: $(this).parents('form').first().attr('action'),
        dataType: 'html',
        data : data,
        success: function(answer) {
            window.location.href = answer;
        },
        error: function() {
        },
        complete: function() {
        }
    });
    return false;
});
