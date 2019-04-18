$('.add-to-wishlist-button').on('click', function(){
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