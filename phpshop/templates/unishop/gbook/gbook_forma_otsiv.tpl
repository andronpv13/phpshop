

    <h1 class="page-title hide">{Форма отзыва}</h1>
    


@Error@
<div class="row">
<form method="post" name="forma_gbook">
    <div class="form-group">
        <label for="exampleInputEmail1"></label>
        <div class="col-xs-12 col-sm-6">
            <input type="text" name="name_new" class="form-control" id="exampleInputEmail1" placeholder="{Имя}..." required="">
        </div>
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1"></label>
        <div class="col-xs-12 col-sm-6">
            <input type="email" name="mail_new"  class="form-control" id="exampleInputEmail1" placeholder="Email...">
        </div>
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1"></label>
        <div class="col-xs-12 col-sm-6">
            <input type="text"  name="tema_new"  class="form-control" id="exampleInputEmail1" placeholder="{Заголовок}..." required="">
        </div>
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1"></label>
        <div class="col-xs-12 col-sm-6">
            <textarea name="otsiv_new" class="form-control" maxlength="500" placeholder="{Сообщение}..." required=""></textarea>
        </div>
    </div>
    <div class="form-group">
        <div class="col-xs-12 col-sm-6">
            <p class="small"><label><input name="rule" value="1" required=""  type="checkbox"> @rule@</label></p>
        </div>
    </div>
    <div class="form-group">
        <div class="col-xs-12 col-sm-6">
            @captcha@
        </div>
    </div>  
    <div class="form-group">
        <div class="col-xs-12 col-sm-6">
            <span class="pull-right">
                <input type="hidden" name="send_gb" value="1">
                <button type="submit" class="btn btn-primary">{Отправить отзыв}</button>
            </span>
        </div>
    </div>
</form>
</div>