<!doctype html>
<html>
    <head>
        <title>{Прайс-лист} - @name@</title>
        <META http-equiv="Content-Type" content="text-html; charset=windows-1251">
        <style>
            BODY {
                FONT-FAMILY: tahoma,verdana,arial,sans-serif;
                olor:#000000;
                font-size: 11px;
            }
            td {
                font-size: 11px;
                font-family:Tahoma;
                color:#000000;
            }
            a {
                font-size: 11px;
                font-family:Tahoma;
                color:#000000;
                text-decoration: none;
            }
            a:hover {
                font-size: 11px;
                font-family:Tahoma;
                color:#000000;
                text-decoration: underline;
            }

            .bor {
                border: 0px;
                border-top: 1px solid #000000;
                border-left: 1px solid #000000;
                border-right: 1px solid #000000;
                text-align: right;
            }
            .hidden{
                display: none;
            }
            
        </style>
        <style media="print" type="text/css">
            <!--
            .nonprint {
                display: none;
            }
            -->
        </style>
        <script src="../../lib/templates/print/js/jquery-1.11.0.min.js"></script>
        <script src="../../lib/templates/print/js/html2pdf.bundle.min.js"></script>
        <script src="../../lib/templates/print/js/jquery.table2excel.min.js"></script>
    </head>
    <body>
        <div align="right" class="nonprint">
            <button id="saveCsv" class="@hidden@">{Сохранить} CSV</button>
            <button id="savePdf" class="@hidden@">{Сохранить} PDF</button>
            <button onclick="window.print();">{Распечатать}</button>
            <hr>
        </div>
        <div id="content">
            <h2>{Прайс-лист} "@name@" / @date@</h2>

            <table cellpadding="2" cellspacing="1" width="100%" align="center" border="1" id="table2excel">
                @price@
            </table>
        </div>
        <script>
                $().ready(function() {

                    $("#savePdf").click(function() {
                        html2pdf(document.getElementById('content'), {
                            margin: 2,
                            filename: '{Прайс-лист}.pdf',
                            html2canvas: {
                                dpi: 192,
                                letterRendering: true
                            }
                        });
                    });

                    $("#saveCsv").click(function() {
                        $("#table2excel").table2excel({
                            exclude: ".noExl",
                            name: "{Прайс-лист}",
                            filename: "{Прайс-лист}"
                        });
                    });

                });
        </script>

    </body>
</html>