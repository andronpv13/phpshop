<!DOCTYPE html>
<html lang="@code@">
    <head>
        <meta charset="@charset@">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="@title@ - @version@">
        <meta name="author" content="PHPShop Software">
        <link rel="apple-touch-icon" href="./apple-touch-icon.png">
        <link rel="icon" href="./favicon.ico"> 
        <title>@title@</title>

        <!-- Bootstrap -->
        <link id="bootstrap_theme" href="./css/bootstrap-theme-@theme@.css" rel="stylesheet">
        <link href="./css/bootstrap-select.min.css" rel="stylesheet">
        <link href="./css/signin.css" rel="stylesheet">
        <link href="./css/bar.css" rel="stylesheet">
        <link href="./css/messagebox.min.css" rel="stylesheet">

    </head>

    <body id="form-signin">

        <!-- jQuery -->
        <script src="js/jquery-1.11.0.min.js"></script>
        
        <!-- Localization -->
        <script src="../locale/@lang@/gui.js" data-rocketoptimized="false" data-cfasync="false"></script>

        <header class="bar bar-nav navbar-action visible-xs">
            <h1 class="title">{Авторизация}</h1>
        </header>

        <!-- container -->
        <div class="container">
            
            <div class="row">

            <form class="form-signin "  method="post" action="./">

                <h3 class="form-signin-heading hidden-xs">{Авторизация}<a class="pull-right hidden-xs @hide_home@" href="../../" tabindex="-1" title="{Вернуться в магазин}"><span class="glyphicon glyphicon-home"></span></a> <div class="pull-right">@themeSelect@</div></h3>

                <div class="input-group @error@">
                    <span class="input-group-addon" id="input-group-addon1"><span class="glyphicon glyphicon-user"></span></span>
                    <input autocomplete="off" type="text" name="log" class="form-control" value="@user@" placeholder="{Пользователь}" required @autofocus@ @readonly@ tabindex="1">
                </div>
                <div class="input-group @error@">
                    <span class="input-group-addon" id="input-group-addon2"><a href="#" class="password-view" tabindex="-1"><span class="glyphicon glyphicon-eye-close"></span></a></span>
                    <input autocomplete="off" type="password" name="pas" class="form-control" value="@password@" placeholder="{Пароль}"  required @readonly@ tabindex="2">
                </div>

                <div class="checkbox">
                    <label  class="@hide@">
                        <input type="checkbox" name="actionHash" value="true" id="remember-me" @disabled@ tabindex="-1"> {Восстановить пароль}
                        
                    </label>
                    <small class="hidden-xs pull-right text-muted text-uppercase @hide@"><img src="../locale/@lang@/icon.png" /> @code@</small>
                    
                </div>
                <button class="btn btn-lg btn-primary btn-block" type="submit" tabindex="3">{Вход}</button>
                <input type="hidden" name="actionID" value="true">
                <input type="hidden" name="actionList[actionHash]" value="actionHash">
                <input type="hidden" name="actionList[actionID]" value="actionEnter">
            </form>
                
                
                </div>
        </div> <!-- /container -->
        <!-- Fixed mobile bar -->
        <div class="bar-padding-fix visible-xs"> </div>
        <nav class="navbar navbar-statick navbar-fixed-bottom bar bar-tab visible-xs" role="navigation">
            <a class="tab-item active" href="../../">
                <span class="icon icon-home"></span>
                <span class="tab-label">{Домой}</span>
            </a>
            <a class="tab-item" href="?path=order">
                <span class="icon icon-download"></span>
                <span class="tab-label">{Заказы}</span>
            </a>
            <a class="tab-item" href="?path=catalog">
                <span class="icon icon-compose"></span>
                <span class="tab-label">{Цены}</span>
            </a>
            <a class="tab-item"  href="?path=shopusers">
                <span class="icon icon-person"></span>
                <span class="tab-label">{Покупатели}</span>
            </a>
        </nav>
        <!--/ Fixed mobile bar -->
        
       
        <!-- Notification -->
        <div id="message" class="hide">@notification@</div>
        <!--/ Notification -->

        <script src="./js/bootstrap.min.js" data-rocketoptimized="false" data-cfasync="false"></script>
        <script src="./js/bootstrap-select.min.js" data-rocketoptimized="false" data-cfasync="false"></script>
        <script src="./js/messagebox.min.js" data-rocketoptimized="false" data-cfasync="false"></script>
        <script src="./js/signin.js" data-rocketoptimized="false" data-cfasync="false"></script>
    </body>
</html>