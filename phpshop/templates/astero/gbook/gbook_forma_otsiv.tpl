<ol class="breadcrumb visible-lg" itemscope itemtype="http://schema.org/BreadcrumbList">
    <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a href="/" itemprop="item">
            <span itemprop="name">{Главная}</span>
        </a>
        <meta itemprop="position" content="1" />
    </li>
    <li itemscope itemtype="https://schema.org/NewsArticle" itemtype="http://schema.org/ListItem">
        <a href="/gbook/" itemprop="item">
            <span itemprop="name">{Отзывы}</span>
        </a>
        <meta itemprop="position" content="2" />
    </li>
    <li class="active"{Форма отзыва}</li>
</ol>

    <h1 class="main-heading2">{Форма отзыва}</h1>
    


@Error@
<div class="row">
<form method="post" name="forma_gbook">
    <div class="form-group">
        <label for="exampleInputEmail1"></label>
        <div class="col-xs-12">
            <input type="text" name="name_new" class="form-control" id="exampleInputEmail1" placeholder="{Имя}" required="">
        </div>
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1"></label>
        <div class="col-xs-12">
            <input type="email" name="mail_new"  class="form-control" id="exampleInputEmail1" placeholder="Email">
        </div>
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1"></label>
        <div class="col-xs-12">
            <input type="text"  name="tema_new"  class="form-control" id="exampleInputEmail1" placeholder="{Заголовок}" required="">
        </div>
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1"></label>
        <div class="col-xs-12">
            <textarea name="otsiv_new" class="form-control" maxlength="500" placeholder="{Сообщение}" required=""></textarea>
        </div>
    </div>
                        <div class="form-group">
                            <div class="col-xs-12">
                            <input type="checkbox" value="on" name="rule" class="req" > 
                            {Я согласен}  <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html">{на обработку моих персональных данных}</a>
                            </div>
                        </div>
    <div class="form-group">
        <div class="col-xs-12">
            @captcha@
        </div>
    </div>  
    <div class="form-group">
        <div class="col-xs-12">
            <span class="pull-right">
                <input type="hidden" name="send_gb" value="1">
                <button type="submit" class="btn btn-primary">{Отправить отзыв}</button>
            </span>
        </div>
    </div>
</form>
</div>