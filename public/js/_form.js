var $container = $('#images-container');
var index = $container.find(':input').length;

function addImage($container) {
    var template = $($container).attr('data-prototype')
        .replace(/__name__/g, index);

    var $prototype = $(template);
    $($container).append($prototype);

    index++;
}