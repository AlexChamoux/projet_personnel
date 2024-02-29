var $container = $('#images-container');
var index = $container.find(':input').length;

function addImage($container) {
    var template = $($container).attr('data-prototype')
        .replace(/__name__/g, index);

    var $prototype = $(template);
    $($container).append($prototype);

    index++;
}

function removeImage(button) {
    var imageId = button.value;

    $.ajax({
        type: 'POST',
        url: '/product/remove_image/' + imageId,
        success: function(response) {                
            console.log(response);
            location.reload();
        },
        error: function(error) {
            console.error(error);
        }
    });
}