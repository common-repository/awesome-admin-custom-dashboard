
$ = jQuery;
function displayMessage(message) {
   window.Snackbar.show({text: message, pos: 'top-right', duration: 10000});
}

function displayErrorMessage(message) {
    window.Snackbar.show({text: message, pos: 'top-right', backgroundColor : '#f5365c', actionTextColor: '#fff', duration: 10000});
}


function displayAlert (title, message, color = 'red') {
    $.alert({
        title: title,
        content: message,
        type: color,
    });
}

function displayTooltip(object = {}) {
    setTimeout(() => {
        let classElement = object.class !== undefined ? object.class : '.guide';
        window.Tipped.create(classElement, function(element) {
            return {
                content: $(element).data('content')
            };
        },{
            position: object.position !== undefined ? object.position : 'right',
            skin: object.skin !== undefined ? object.skin : 'light',
            size: object.size !== undefined ? object.size : 'large'
        });
    }, 1000);
}
