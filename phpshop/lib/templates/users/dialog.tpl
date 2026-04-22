<!-- Диалоги-->
<script src="phpshop/lib/templates/users/dialog.js"></script>
<link href="phpshop/lib/templates/users/dialog.css" rel="stylesheet">
<audio id="play-chat" src="phpshop/lib/templates/users/dialog.mp3"></audio>
<div class="modal fade bs-example-modal-sm" id="userDialog" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-message">
        <div class="modal-content">
            <div class="">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                <h4 class="" style="padding-bottom:5px">@title_dialog@</h4>
            </div>
            <div class="">
                <div id="message-list"></div>
                <form method="post" id="message-edit" class="form-horizontal" role="form" data-toggle="validator">
                    <div>
                        <p>
                            <textarea class="form-control" name="message" id="message" placeholder="{Введите текст}" required @dialogContent@></textarea>
                        </p>
                        <p class="clearfix">

                        <div class="row">
                            <div class="col-md-3">
                                <button class="btn btn-default pull-left text-muted" data-dismiss="modal">{Закрыть}</button>
                            </div> 
                            <div class="col-md-9 text-right">

                                <a class="btn btn-default @telegram_enabled@ btn-telegram" href="@telegram_path@" target="_blank" title="{Открыть чат в} Telegram"><img src="phpshop/lib/templates/messenger/telegram.svg" width="18" height="18" alt=" "></a>
                                <a class="btn btn-default @vk_enabled@ btn-vk"  href="@vk_path@" target="_blank" title="{Открыть чат в} VK"><img src="phpshop/lib/templates/messenger/vk.svg" width="18" height="18" alt=" "></a>
                                <div class="btn-group dropup">

                                    <button class="btn btn-primary send-message" type="button"><span class="glyphicon glyphicon-comment"></span> {Отправить}</button>
                                    @dialogAnswer@

                                </div>
                            </div>   

                        </div>

                        </p>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="visible-lg visible-md visible-xs" >
    <a href="#" id="dialog-widget" title="@title_dialog@" data-toggle="modal" data-target="#userDialog" style="background-image: url(@icon_dialog@);"></a>
</div>
<!--/ Диалоги-->