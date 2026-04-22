<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="windows-1251">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@pageTitl@</title>
        <meta name="description" content="@pageDesc@">
        <meta name="keywords" content="@pageKeyw@">
        <meta name="copyright" content="@pageReg@">

        <!-- Bootstrap -->
        <link href="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@css/bootstrap.min.css" rel="stylesheet">

    </head
    <body>

        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h3>@nameShop@</h3>
                    @descripShop@
                    <button onclick="window.print();" class="btn btn-default pull-right">
                        <span class="glyphicon glyphicon-print"></span> {Печать}
                    </button> 
                </div>
            </div>
            <hr>
            <div class="media">
                <div class="media-left">
                    <a href="//@serverShop@/shop/UID_@productId@.html"><IMG src="//@serverShop@@productImg@" alt="@productName@" title="@productName@" border="0" hspace="10" style="max-width:200px;height:auto"></a>
                </div>
                <div class="media-body">
                    <div class="panel panel-default">
                        <div class="panel-body">

                            <h3 class="media-heading">@productName@</h3>

                            <p>@vendorDisp@</p>
                            <p>@productDes@</p>
                            <h4>Цена: @productPrice@ @productValutaName@</h4>

                            <a href="//@serverShop@/shop/UID_@productId@.html" class="pull-right"><span class="glyphicon glyphicon-share-alt"></span> http://@serverShop@/shop/UID_@productId@.html</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="hide nonprint">
