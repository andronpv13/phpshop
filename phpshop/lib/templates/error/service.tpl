<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="windows-1251">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex">
        <title>@title@</title>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <style>
            html {
                position: relative;
                min-height: 100%;
            }
            body {
                margin-bottom: 60px;
            }
            .footer {
                position: absolute;
                bottom: 0;
                width: 100%;
                height: 60px;
                background-color: #f5f5f5;
            }
            .container {
                width: auto;
                max-width: 680px;
                padding: 0 15px;
            }
            .container .text-muted {
                margin: 20px 0;
            }
            a .glyphicon{
                padding-right: 3px;
            }
        </style>
    </head>
    <body role="document">
        <div class="container">
            <div class="page-header">
                <h1 class="default">@title@</h1>
            </div>
            <p class="lead"></p>
            
        <div class="panel panel-default">
      
                <div class="panel-body">
                    @message@
                </div>
            </div>
        </div>
        
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    </body>
</html>