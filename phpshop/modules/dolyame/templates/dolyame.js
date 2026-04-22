$().ready(function () {

    $("body").on('click', ".btn-dolyame", function (event) {
        event.preventDefault();

        $.ajax({
            url: ROOT_PATH + '/phpshop/modules/dolyame/status/create.php',
            type: 'post',
            data: 'id=' + $(this).attr('data-id'),
            dataType: 'json',
            success: function (json) {
                if (json['success']) {
                    window.location.replace(json['link']);
                }
            }
        });
    });

});