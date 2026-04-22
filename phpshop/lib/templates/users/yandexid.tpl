<p id="yandexid_button"></p>
<script src="https://yastatic.net/s3/passport-sdk/autofill/v1/sdk-suggest-with-polyfills-latest.js"></script>
<script>
    window.onload = function () {
        window.YaAuthSuggest.init(
                {
                    client_id: '@yandex_id_apikey@',
                    response_type: 'token',
                    redirect_uri: 'https://@yandex_redirect_uri@',
                },
                'https://@serverName@',
                {
                    view: "button",
                    parentId: "yandexid_button",
                    buttonSize: 'm',
                    buttonView: 'main',
                    buttonTheme: 'light',
                    buttonBorderRadius: "5",
                    buttonIcon: 'ya',
                }
        )
                .then(function (result) {
                    return result.handler()
                })
                .then(function (data) {

                    $.ajax({
                        url: ROOT_PATH + 'phpshop/ajax/yandexid.php',
                        type: 'post',
                        data: 'access_token=' + data.access_token,
                        dataType: 'json',
                        success: function (json) {

                            if (json['success']) {
                                $('#yandexid_button').html('Login as ' + json['user']);
                                window.location.reload();
                            }

                        }
                    });
                })
                .catch(function (error) {
                    $('#yandexid_button').html('Error...');
                });
    };
</script>