$(document).ready(function () {

    async function postData(url = '', data = {}) {
        // Default options are marked with *
        const response = await fetch(url, {
            method: 'POST', // *GET, POST, PUT, DELETE, etc.
            mode: 'cors', // no-cors, *cors, same-origin
            cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
            credentials: 'same-origin', // include, *same-origin, omit
            headers: {
                'Content-Type': 'application/json'
                        // 'Content-Type': 'application/x-www-form-urlencoded',
            },
            redirect: 'follow', // manual, *follow, error
            referrerPolicy: 'no-referrer', // no-referrer, *client
            body: JSON.stringify(data) // body data type must match "Content-Type" header
        });


        return await response.json(); // parses JSON response into native JavaScript objects
    }

    var categoryId = $('[name="categoryId"]').val();

    $('.fileUpload').liteUploader({
        url: '/phpshop/modules/panorama360/admpanel/ajax/loader.php',
        params: {
            categoryId: categoryId
        },
        rules: {
            allowedFileTypes: "image/jpeg,image/png,image/gif",
            maxSize: 2500000
        }
    })
            .on('lu:errors', function (e, {errors}) {
                var isErrors = false;
                //console.log(errors);
                $('#display').html('');

                $.each(errors, function (i, error) {
                    if (error.errors.length > 0) {
                        isErrors = true;
                        $.each(error.errors, function (i, errorInfo) {
                            $('#display').append('<div class="text-danger">Файл: ' + error.name + ' имеет недопустимый формат</div>');
                        });
                    }
                });

                if (!isErrors) {
                    //console.log('test');
                    //$('#download-price').removeClass('hide');
                    //$('#display').append('<br /><span class="text-success">Файл(ы) успешно загружен(ы)</span>');
            }
            })

            .on('lu:progress', function (e, {percentage}) {
                //console.log(percentage);

                $('#download-img > .progress-bar').attr('aria-valuenow', percentage).css('width', percentage + '%').text(percentage + '%');

            })
            .on("lu:before", function (e, {files}) {

                $('#download-img').removeClass('hide');
                $('#download-img > .progress-bar').css('width', '0%').text('0%');

            })
            .on('lu:success', function (e, {response}) {
                //console.log(response);
                //var response = $.parseJSON(response);

                $.each(response.urls, function (i, url) {

                    var url = '../../../..' + url;
                    $('#files-list').append("<input type='hidden' id='input-upload-file-img' name='input-upload-file-img' value='" + url + "'>");
                    $('.cd-product-viewer-wrapper .product-sprite').css('background-image', 'url(' + url + ')');
                    $('.product-sprite').attr('data-image', url);
                    $('[name="img_panorama360_new"]').val(url);

                    //$('#previews').append($('<div><i class="fa fa-'+ico+' text-success"></i> <span class="text-default">'+name+'</span>  <button data-id="'+i+'" class="btn btn-danger btn-sm delete-file"> <i class="fal fa-times"></i> Удалить</button> </div>'));
                });


                $('label.btn-success').removeClass('disabled');

                $('#download-img').addClass('hide');
                $('#download-img > .progress-bar').css('width', '0%').text('0%');

                $('#display').append(response.message);
                $('#display').append('<div class="alert alert-success" id="upload-file-img" role="alert">Загрузка изображения выполнена успешно</div>');
                setTimeout(function () {
                    $("#upload-file-img").slideUp()
                }, 3000)

            });

    $(".fileUpload").change(function () {
        $(this).data("liteUploader").startUpload();
    });

    $('#delete_panorama_img').on('click', function (e) {
        e.preventDefault();
        $('[name="img_panorama360_new"]').val('');
        $('.cd-product-viewer-wrapper .product-sprite').css('background-image', 'none');
    });

});