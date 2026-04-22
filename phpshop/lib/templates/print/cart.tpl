<!doctype html>
<html>
    <head>
        <title>{Форма предварительного заказа} - @name@</title>
        <meta name="robots" content="noindex, nofollow">
        <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
        <link href="../style.css" type=text/css rel=stylesheet>
        <style>
            tr.tablerow td.tablerow:last-child {
                border-right: 1px solid #000;
                text-align: right;
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
            <button id="saveCsv">{Сохранить} CSV</button>
            <button id="savePdf">{Сохранить} PDF</button>
            <button onclick="window.print();">{Распечатать}</button> 
            <hr>
        </div>
        <div id="content">
            <div align="center"><table align="center" width="100%">
                    <tr>
                        <td align="center"><img src="@logo@" alt="" style="max-width:150px" border="0"></td>
                        <td align="center"><h2 align=center>{Форма предварительного заказа} -  @date@</h2></td>
                    </tr>
                </table>
            </div>

            <p><br></p>
            <table width="100%" cellpadding="2" cellspacing="0" align="center" id="table2excel">
                <tr class="tablerow">
                    <td class=tablerow><b>&#8470;</b></td>
                    <td width=50% class=tablerow><b>{Наименование}</b></td>
                    <td class=tablerow><b>{Единица измерения}</b></td>
                    <td class=tablerow><b>{Количество}</b></td>
                    <td class=tablerow><b>{Цена}</b></td>
                    <td class=tableright><b>{Сумма}</b></td>
                </tr>
                @cart@
                <tr>
                    <td colspan=5 align=right style="border-top: 1px solid #000000;border-left: 1px solid #000000;">{Скидка}:</td>
                    <td class=tableright nowrap><b>@discount@%</b></td>
                </tr>
                <tr>
                    <td colspan=5 align=right style="border-top: 1px solid #000000;border-left: 1px solid #000000;">{Итого}:</td>
                    <td class=tableright nowrap><b>@total@</b></td>
                </tr>
                <tr class="noExl"><td colspan=6 style="border: 0px; border-top: 1px solid #000000;"><p><b>{Всего наименований} @item@, {на сумму} @total@ @currency@
                            </b></p>&nbsp;</td></tr>
            </table>
        </div>

        <script>
                $().ready(function() {

                    $("#savePdf").click(function() {
                        html2pdf(document.getElementById('content'), {
                            margin: 2,
                            filename: '{Форма предварительного заказа}.pdf',
                            html2canvas: {
                                dpi: 192,
                                letterRendering: true
                            }
                        });
                    });

                    $("#saveCsv").click(function() {
                        $("#table2excel").table2excel({
                            exclude: ".noExl",
                            name: "{Форма предварительного заказа}",
                            filename: "{Форма предварительного заказа}"
                        });
                    });

                });
        </script>

    </body>
</html>