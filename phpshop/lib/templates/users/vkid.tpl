<p id="vkid_button"></p>
<div>
    <script src="https://unpkg.com/@vkid/sdk@<3.0.0/dist-sdk/umd/index.js"></script>
    <script type="text/javascript">
        if ('VKIDSDK' in window) {
            const VKID = window.VKIDSDK;

            VKID.Config.init({
                app: @vk_app@,
                        redirectUrl: 'https://@vk_redirect_uri@',
                responseMode: VKID.ConfigAuthMode.Callback,
                source: VKID.ConfigSource.LOWCODE,
                scope: 'email phone'
            });

            const oAuth = new VKID.OAuthList();

            oAuth.render({
                container: document.getElementById('vkid_button'),
                oauthList: [
                    'vkid',
                    'ok_ru',
                    'mail_ru'
                ]
            })
                    .on(VKID.WidgetEvents.ERROR, vkidOnError)
                    .on(VKID.OAuthListInternalEvents.LOGIN_SUCCESS, function (payload) {
                        const code = payload.code;
                        const deviceId = payload.device_id;

                        VKID.Auth.exchangeCode(code, deviceId)
                                .then(vkidOnSuccess)
                                .catch(vkidOnError);
                    });

            function vkidOnSuccess(data) {
                // Обработка полученного результата
                $.ajax({
                    url: 'https://@vk_redirect_uri@?access_token=' + data.access_token,
                    type: "GET",
                    async: false,
                    success: function () {
                        location.reload();
                    }
                });

            }

            function vkidOnError(error) {
                // Обработка ошибки
            }
        }
    </script>
</div>