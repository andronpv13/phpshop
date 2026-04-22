
@Error@

<form method="post" name="forma_message" class="template-sm registration-area">
    <div class="form-group">
        <div class="">
            <input placeholder="{Заголовок}" type="text" name="tema" value="@php  echo $_POST['tema']; php@" class="form-control" id="exampleInputEmail1"  required="">
        </div>
    </div>
    <div class="form-group">
        <div class="">
            <input placeholder="{Имя}" type="text" name="name" value="@php  echo $_POST['name']; php@" class="form-control" id="exampleInputEmail1"  required="">
        </div>
    </div>
    <div class="form-group">
        <div class="">
            <input placeholder="E-mail" type="email" name="mail" value="@php  echo $_POST['mail']; php@" class="form-control" id="exampleInputEmail1">
        </div>
    </div>
    <div class="form-group">
        <div class="">
            <input placeholder="{Телефон}" type="text" name="tel" value="@php  echo $_POST['tel']; php@" class="form-control" id="exampleInputEmail1">
        </div>
    </div>
    <div class="form-group">
        <div class="">
            <input placeholder="{Компания}" type="text" name="company" value="@php  echo $_POST['company']; php@" class="form-control" id="exampleInputEmail1">
        </div>
    </div>
    <div class="form-group">
        <div class="">
            <textarea placeholder="{Сообщение}" name="content" class="form-control" required="">@php  echo $_POST['content']; php@</textarea>
        </div>
    </div>
    <div class="form-group">
        <div class=""></div>
        <div class="">
            <p class="small"><label><input type="checkbox" value="on" name="rule" class="req"  required>  {Я согласен} <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html">{на обработку моих персональных данных}</a></label></p>        </div>
    </div>
    <div class="form-group">
        <div class=""></div>
        <div class="">
            @captcha@
        </div>
    </div>
    <div class="form-group">
        <div class=""></div>
        <div class="">
            <span class="pull-right">
                <input type="hidden" name="send" value="1">
                <button type="submit" class="btn btn-primary">{Отправить сообщение}</button>
            </span>
        </div>
    </div>

</form>    