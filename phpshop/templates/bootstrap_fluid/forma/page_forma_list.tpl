
@Error@

<form method="post" name="forma_message">
    <div class="form-group">
        <input type="text" name="tema" placeholder="{Заголовок}"  value="@php  echo $_POST['tema']; php@" class="form-control" id="exampleInputEmail1"  required="">
    </div>
    <div class="form-group">
        <input type="text" name="name" placeholder="{Имя}" value="@php  echo $_POST['name']; php@" class="form-control" id="exampleInputEmail1"  required="">
    </div>
    <div class="form-group">
        <input type="email" name="mail" placeholder="E-mail"  value="@php  echo $_POST['mail']; php@" class="form-control" id="exampleInputEmail1">
    </div>
    <div class="form-group">
        <input type="text" name="tel" placeholder="{Телефон}" value="@php  echo $_POST['tel']; php@" class="form-control" id="exampleInputEmail1">
    </div>
    <div class="form-group">
        <textarea name="content" class="form-control" placeholder="{Сообщение}" required="">@php  echo $_POST['content']; php@</textarea>
    </div>
    <div class="form-group">
        <p class="small">
            <input type="checkbox" value="on" name="rule" class="req"  required> 
            {Я согласен}  <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html">{на обработку моих персональных данных}</a>
        </p>
    </div>    <div class="form-group">
        @captcha@
        <br/>
        <span class="pull-right">
            <input type="hidden" name="send" value="1">
            <button type="submit" class="btn btn-primary">{Отправить сообщение}</button>
        </span>

    </div>
</form>    