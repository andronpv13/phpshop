<!-- Чат -->
<link rel="stylesheet" href="phpshop/lib/templates/chat/style.css">
<audio id="play-chat" src="phpshop/lib/templates/chat/chat.mp3"></audio>
<div class="fabs">
    <div class="chat" style="bottom:@margin_dialog@px;right:@right_dialog@px">
        <div class="chat_header" style="background: @color_dialog@">
            <div class="chat_option">
                <div class="header_img">
                    <img src="@icon_dialog@"/>
                </div>
                <span id="chat_head">@title_dialog@</span> <br> 
                <span class="@status_dialog_style@">@status_dialog@</span>
                <span id="chat_fullscreen_loader" class="chat_fullscreen_loader"><i class="zmdi zmdi-window-maximize"></i></span>

            </div>

        </div>
        <div id="chat_converse" class="chat_conversion chat_converse message-list">
        </div>
        <div class="fab_field">
            <a id="fab_send" class="fab send-message" href="#"><i class="zmdi zmdi-mail-send"></i></a>
            <textarea id="message" name="message" placeholder="{Ваше сообщение}" class="chat_field chat_message" required @dialogContent@></textarea>
        </div>
    </div>
    <a id="prime" class="fab-chat" style="background:@color_dialog@;bottom:@margin_button_dialog@px;width:@size_dialog@px;height:@size_dialog@px"><i class="prime zmdi zmdi-comment-outline" style="line-height:@size_dialog@px;font-size:@icon_size_dialog@em;"></i></a>
</div>

<script src="phpshop/lib/templates/chat/chat.js"></script>
<!--/ Чат -->