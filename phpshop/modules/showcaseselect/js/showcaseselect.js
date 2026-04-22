$().ready(function () {

    if (typeof ($.cookie('showcaseselect')) == 'undefined') {
        $('#ShowcaseMenu').modal('toggle');
    }

    $('#ShowcaseMenu a').on('click', function (event) {
        event.preventDefault();
        $.cookie('showcaseselect', $(this).attr('href'), {
            path: '/',
            expires: 30
        });
        window.location.href = $(this).attr('href');

    });

    $('#ShowcaseMenu').on('hidden.bs.modal', function (e) {
        $.cookie('showcaseselect', 'close', {
            path: '/',
            expires: 30
        });
    });

});