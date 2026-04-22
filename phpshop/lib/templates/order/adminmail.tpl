<html lang="ru">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
    <meta name="viewport" content="initial-scale=1.0">
    <!-- So that mobile webkit will display zoomed in -->
    <meta name="format-detection" content="telephone=no">
    <!-- disable auto telephone linking in iOS -->
    <title>
        {Поступил заказ} № @ouid@ {на сумму} @sum@ @currency@
    </title>
    <style type="text/css">
        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        table.red-table tbody tr:nth-child(2n) {
            background-color: #f6f6f6;
        }

        table.footer {
            padding: 20px 15px;
        }

        body {
            font-family:  Helvetica, sans-serif;
            margin: 0px;
            font-size: 16px;
            font-weight: 300;
        }

        table[class=gray] {
            background-color: #f9f9f9;
            border-bottom: 1px solid #eaeaea;
            color: #2b2b2b
        }

        img {
            border: none;
            max-width: 100%;
        }

        td,
        table {
            max-width: 100%;
        }
        
        .hide {
	        display: none;
        }
        
        a {
	        text-decoration: none;
	        color:  #337ab7 !important;
        }
    </style>
    <style type="text/css">
        @media (min-width: 605px) {
            .appear {
                display: none;
            }

            .hid-on-dt {
                display: none;
            }
        }

        @media (max-width: 604px) {
            table[class=full] {
                width: 100% !important;
                clear: both;
            }

            .mobile-margin {
                padding-top: 15px;
            }

            .text-left {
                text-align: left !important;
            }

            .erase {
                display: none;
            }

            img.full {
                width: 100%;
            }

            td.full {
                width: 100% !important;
                display: block;
                padding-right: 0px !important;
                clear: both;
            }

            .mb25 {
                margin-bottom: 25px;
            }

            .liquid {
                display: block !important;
            }

            .liquid-center {
                text-align: center !important;
            }

            .liquid-auto {
                width: auto !important;
            }

            table[class=full-width] {
                padding: 0px 15px;
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .footer a {
                float: none !important;
                display: block;
                margin-top: 5px;
            }

            .footer p {
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <table class="full-width" bgcolor="#f3f3f3" style="background: #f3f3f3; width: 100%; " width="100%" cellspacing="0"
        cellpadding="0" border="0">
        <tbody width="100%" style="width: 100%; " align="center">
            <!-- Padding Top -->
            <tr class="erase">
                <td height="30"></td>
            </tr>
            <tr>
                <td align="center">
                    <table class="full" align="center"
                        style="margin: 0px auto; padding: 0px; background: #ffffff; border-left:1px solid #ededed;border-top:1px solid #ededed; border-right: 1px solid #ededed;border-radius: 5px 5px 0px 0px;"
                        bgcolor="#ffffff" width="600" cellspacing="0" cellpadding="0" border="0">
                        <tbody>
                            <tr>
                                <td colspan="1" width="600"></td>
                            </tr>
                            <tr>
                                <td height="25" colspan="5" bgcolor="#ffffff" style="background: #ffffff;"></td>
                            </tr>
                             <tr>
                                <td  align="center" height="60" style="font-size: 18px; color: #464646; font-weight: 500; background: #fff; border-radius:4px;font-family: Helvetica, sans-serif; letter-spacing: 1px; ">
                                    {Новый заказ на сумму} @total@ @currency@ <br> <i>@user_name@</i>  @mail@  <br> <span style="font-size: 14px; font-weight: 100;">@date@</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- Header text -->

                    <table class="full" align="center"
                        style="margin: 0px auto; padding: 0px; font-family:  Helvetica, sans-serif; background: #ffffff;border-left:1px solid #ededed; border-right: 1px solid #ededed;"
                        bgcolor="#ffffff" width="600" cellspacing="0" cellpadding="0" border="0">
                        <tbody>
	                        
                            <tr>
                                <td colspan="5" width="600"></td>
                            </tr>
                            <tr>
                                <td width="15" rowspan="5"></td>
                                <td>
                                </td>
                                <td width="15" rowspan="5"></td>
                            </tr>
                            <tr>
                                <td
                                    style="color: #646464;font-family: Helvetica, sans-serif; font-size: 15px;line-height: 24px; ">
                                    <table align="center" bgcolor="#f64646" style="background-color: #71869d; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; margin: 0 auto; margin-top:50px; " cellspacing="0" cellpadding="0" border="0" background="0">
                                        <tbody>
                                            <tr>
                                                <td height="9" colspan="3"></td>
                                            </tr>
                                            <tr>
                                                <td width="30"></td>
                                                <td align="center">
                                                    <a href="@shop_admin@admin.php?path=order&id=@orderId@" style="color: #fff !important; font-size: 14px;text-decoration: none;font-family: Helvetica,sans-serif;">{Редактировать заказ}</a>
                                                </td>
                                                <td width="30"></td>
                                            </tr>
                                            <tr>
                                                <td height="10" colspan="3"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <p>
                                        @php

                                        if(!empty($_POST["org_name_new"]))
                                        echo __("Наименование организации").": ".$_POST["org_name_new"]."<br>";
                                        if(!empty($_POST["org_inn_new"]))
                                        echo __("ИНН").": ".$_POST["org_inn_new"]."<br>";
                                        if(!empty($_POST["org_kpp_new"]))
                                        echo __("КПП").": ".$_POST["org_kpp_new"]."<br>";
                                        if(!empty($_POST["org_yur_adres_new"]))
                                        echo __("Юридический адрес").": ".$_POST["org_yur_adres_new"]."<br>";
                                        if(!empty($_POST["org_fakt_adres_new"]))
                                        echo __("Фактический адрес").": ".$_POST["org_fakt_adres_new"]."<br>";
                                        if(!empty($_POST["org_ras_new"]))
                                        echo __("Расчётный счёт").": ".$_POST["org_ras_new"]."<br>";
                                        if(!empty($_POST["org_bank_new"]))
                                        echo __("Наименование банка").": ".$_POST["org_bank_new"]."<br>";
                                        if(!empty($_POST["org_kor_new"]))
                                        echo __("Корреспондентский счёт").": ".$_POST["org_kor_new"]."<br>";
                                        if(!empty($_POST["org_bik_new"]))
                                        echo __("БИК").": ".$_POST["org_bik_new"]."<br>";
                                        if(!empty($_POST["org_city_new"]))
                                        echo __("Город").": ".$_POST["org_city_new"]."<br>";

                                        php@</p>                                </td>
                            </tr>
                            <tr>
                                <td height="25"></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="full" align="center"
                        style="margin: 0px auto; padding: 0px; font-family:   Helvetica, sans-serif; background: #ffffff; border-left:1px solid #ededed; border-right: 1px solid #ededed;"
                        bgcolor="#ffffff" width="600" cellspacing="0" cellpadding="0" border="0">
                        <tbody>
                            <tr>
                                <td width="20" rowspan="20"></td>
                                <td colspan="3" width="560"></td>
                                <td width="20" rowspan="20"></td>
                            </tr>
                            <tr>
                                <td bgcolor="#ffffff" style="background:#ffffff;">
                                    <table bgcolor="#ffffff" class="full" width="100%" cellspacing='0' cellpadding='0'
                                        border='0' style="background:#ffffff;">
                                        <tbody>
                                            <tr>
                                                <td height="1" bgcolor="#f3f3f3" style="background: #f3f3f3; "
                                                    colspan="4">
                                                    <p style="margin:0;line-height: 1px; font-size: 1px">&nbsp;</p>
                                                </td>
                                            </tr>
                                            @cart@
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="full" align="center"
                        style="margin: 0px auto; padding: 0px; font-family:    Helvetica, sans-serif; background: #ffffff; border-left:1px solid #ededed; border-right: 1px solid #ededed;"
                        bgcolor="#ffffff" width="600" cellspacing="0" cellpadding="0" border="0">
                        <tbody>
                            <tr>
                                <td colspan="6" width="600"></td>
                            </tr>
                            <tr>
                                <td width="30" class="erase"></td>
                                <td width="20"></td>
                                <td class="text-left"
                                    style="color: #646464; font-weight: 300; font-family:    Helvetica, sans-serif; "
                                    align="right">
                                    {Заказ на сумму}:&nbsp;&nbsp;
                                </td>
                                <td style="color: #646464; font-size: 16px; font-family:    Helvetica, sans-serif;"
                                    align="right" height="35">
                                    @sum@ @currency@
                                </td>
                                <td width="20"></td>
                            </tr>
                            <tr>
                                <td width="30" class="erase"></td>
                                <td width="20"></td>
                                <td class="text-left"
                                    style="color:  #337ab7; font-weight: 300; font-family:  Helvetica, sans-serif;"
                                    align="right">
                                    {Скидки и бонусы}:&nbsp;&nbsp;
                                </td>
                                <td style="color:  #337ab7; font-size: 16px; font-family:    Helvetica, sans-serif; "
                                    align="right" height="35">
                                    - @discount_sum@ @currency@
                                </td>
                                <td width="20"></td>
                            </tr>
                            <tr>
                                <td width="30" class="erase"></td>
                                <td width="20"></td>
                                <td class="text-left"
                                    style="color: #646464; font-weight: 300; font-family:    Helvetica, sans-serif;"
                                    align="right">
                                    {Доставка}:&nbsp;&nbsp;
                                </td>
                                <td style="color: #646464; font-size: 16px; font-family:    Helvetica, sans-serif;"
                                    align="right" height="35">
                                    @deliveryPrice@ @currency@ @deliveryInfo@
                                </td>
                                <td width="20"></td>
                            </tr>
                            <tr>
                                <td width="30" class="erase"></td>
                                <td width="20"></td>
                                <td class="text-left"
                                    style="color: #646464; font-weight: 300; font-family:    Helvetica, sans-serif;"
                                    align="right">
                                    {Итого к оплате с учетом скидки}:&nbsp;&nbsp;
                                </td>
                                <td style="color: #262626; font-size: 16px; font-family:    Helvetica, sans-serif; font-weight: 700;"
                                    align="right" height="35">
                                    <b style="font-weight: 900">@total@ @currency@</b>
                                </td>
                                <td width="20"></td>
                            </tr>
                            <tr>
                                <td height="30" colspan="6"></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="full" align="center"
                        style="margin: 0px auto; padding: 0px; font-family: Helvetica, sans-serif; background: #ffffff; border-left:1px solid #ededed; border-right: 1px solid #ededed;"
                        bgcolor="#ffffff" width="600" cellspacing="0" cellpadding="0" border="0">
                        <tbody>
                            <tr>
                                <td colspan="5" width="600"></td>
                            </tr>
                            <tr>
                                <td width="10" rowspan="10" class="erase"></td>
                                <td colspan="3"></td>
                                <td width="10" rowspan="10" class="erase"></td>
                            </tr>
                            <tr>
                                <td class="full" align="left" style="vertical-align:top;">
                                    <table align="center" style="border: 1px solid #f3f3f3; background: #ffffff; border-radius: 4px; "
                                        bgcolor="#ffffff" width="93%" cellspacing="0" cellpadding="0" border="0">
                                        <tbody>
                                            <tr>
                                                <td width="25" bgcolor="#f3f3f3" style="background: #f3f3f3;"></td>
                                                <td height="60" bgcolor="#f3f3f3"
                                                    style="font-weight: bold; background: #f3f3f3; font-family:    Helvetica, sans-serif; color: #2b2b2b;font-size: 14px;">
                                                    {Способ доставки}:
                                                </td>
                                                <td width="25" bgcolor="#f3f3f3" style="background: #f3f3f3;"></td>
                                            </tr>
                                            <tr>
                                                <td width="25" rowspan="100"></td>
                                                <td></td>
                                                <td width="25" rowspan="100"></td>
                                            </tr>
                                            <tr>
                                                <td height="15"></td>
                                            </tr>
                                            <tr>
                                                <td height="25"
                                                    style="color: #646464; font-family: Helvetica, sans-serif;color: #646464;font-size: 14px; font-weight: 600;">
                                                    @deliveryCity@
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="25"
                                                    style="color: #646464; font-family: Helvetica, sans-serif;color: #646464;font-size: 14px; font-weight: 300;">
                                                    {Контактное лицо}: @user_name@
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="@php __hide('tel'); php@" height="25"
                                                    style="color: #646464; font-family: Helvetica, sans-serif;color: #646464;font-size: 14px; font-weight: 300;">
                                                    {Телефон}: @tel@
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="25"
                                                    style="color: #646464; font-family: Helvetica, sans-serif;color: #646464;font-size: 14px; font-weight: 300;">
                                                    E-mail: @mail@
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="25"
                                                    style="color: #646464; font-family: Helvetica, sans-serif;color: #646464;font-size: 14px; font-weight: 300;">
                                                    {Адрес и информация для доставки}: <br>
                                                    @adresList@
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="25"
                                                    style="color: #646464; font-family: Helvetica, sans-serif;color: #646464;font-size: 14px; font-weight: 300;">
                                                    {Дополнительная информация}: <b>@dop_info@</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="20"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td class="full hid-on-dt" height="20"></td>
                                <td class="full" align="right" style="vertical-align:top;">


                                    <table align="center" style="border: 1px solid #f3f3f3; background: #ffffff; border-radius: 4px; "
                                        bgcolor="#ffffff" width="93%" cellspacing="0" cellpadding="0" border="0">
                                        <tbody>
                                            <tr>
                                                <td width="25" bgcolor="#f3f3f3" style="background: #f3f3f3;"></td>
                                                <td height="60" bgcolor="#f3f3f3"
                                                    style="font-weight: bold; background: #f3f3f3; font-family:    Helvetica, sans-serif; color: #2b2b2b;font-size: 14px;">
                                                    {Тип оплаты}:
                                                </td>
                                                <td width="25" bgcolor="#f3f3f3" style="background: #f3f3f3;"></td>
                                            </tr>
                                            <tr>
                                                <td width="25" rowspan="100"></td>
                                                <td></td>
                                                <td width="25" rowspan="100"></td>
                                            </tr>
                                            <tr>
                                                <td height="15"></td>
                                            </tr>
                                            <tr>
                                                <td height="25" style="color: #646464; font-family: Helvetica, sans-serif; color: #646464;font-size: 14px; font-weight: 300;">
                                                    @payment@
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="20"></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </td>
                            </tr>

                        </tbody>
                    </table>
                    <table class="full" align="center"
                        style="margin: 0px auto; padding: 0px; font-family:    Helvetica, sans-serif; background: #ffffff; border-left:1px solid #ededed; border-right: 1px solid #ededed;border-bottom:1px solid #ededed; border-radius: 0px 0px 5px 5px;"
                        bgcolor="#ffffff" width="600" cellspacing="0" cellpadding="0" border="0">
                        <tbody>
                            <tr>
                                <td colspan="5" width="600"></td>
                            </tr>
                            <tr>
                                <td width="35" rowspan="4" class="erase"></td>
                                <td width="15" rowspan="4"></td>
                                <td></td>
                                <td width="35" rowspan="4" class="erase"></td>
                                <td width="15" rowspan="4"></td>
                            </tr>
                            <tr>
                                <td>
                                    <table align="center" style="margin: 0px auto; padding: 0px;width: 100%; "
                                        cellspacing="0" cellpadding="0" border="0">
                                        <tbody>
                                            <tr>
                             <td align="center" colspan="3">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td height="50"></td>
            </tr>
            <tr>
                <td height="0"></td>
            </tr>
        </tbody>
    </table>
<!-- Footer -->
<table class="full" align="center"
style="margin: 0px auto; padding: 0px; font-family: Helvetica, sans-serif;background: #f3f3f3; "
bgcolor="#f3f3f3" width="600" cellspacing="0" cellpadding="0" border="0">
<tbody>
    <tr>
        <td height="35"></td>
    </tr>
    <tr>
        <td class="full liquid-center" width="60%" style="width: 60%; ">
            <table width="100%" align="left" cellspacing="0" cellpadding="0" border="0">
                <tbody>
                    <tr>
                        <td width="10" rowspan="10"></td>
                        <td align="left" class="liquid-center erase">
                            <a href="http://@serverPath@" target="_blank"><img src="http://@serverPath@@logo@"
                                    border="0" style="display: block;max-width: 150px;height: auto; max-height: 54px; margin-bottom: 15px;"
                                    alt=""></a>
                                    
                            <p style="margin: 0px; text-decoration: none; color: #adadad; font-family: Helvetica,sans-serif; font-size: 12px;">
                                {Интернет-магазин} <a href="http://@serverPath@">@serverShop@</a>
                            </p>
                        </td>
                        </td>
                        <td width="10" rowspan="10">
            <table width="100%" cellspacing="0" cellpadding="0" border="0">
                <tbody>
                    <tr>
                        <td align="right" class="liquid-center" style="min-width: 183px;">

                            <span class="@php __hide('whatsapp'); php@"><a href="@whatsapp@" target="_blank" title="WhatsApp"><img
                                    src="http://@serverPath@/UserFiles/Image/Payments/whatsapp.png"
                                    width="25" height="25" alt=""></a></span>
                                    
                            <span class="@php __hide('telegram'); php@"><a href="@telegram@" target="_blank" title="Telegram"><img
                                    src="http://@serverPath@/UserFiles/Image/Payments/telegram.png"
                                    width="25" height="25" alt=""></a></span>
                                    
                            <span class="@php __hide('vk'); php@"><a href="@vk@" target="_blank" title="Vkontakte"><img
                                    src="http://@serverPath@/UserFiles/Image/Payments/vk.png" width="25"
                                    height="25" alt=""></a></span>
                                    
                            <span class="@php __hide('youtube'); php@"><a href="@youtube@" target="_blank" title="Youtube"><img
                                    src="http://@serverPath@/UserFiles/Image/Payments/youtube.png"
                                    width="25" height="25" alt=""></a></span>
                            <span class="@php __hide('odnoklassniki'); php@"><a href="@odnoklassniki@" target="_blank" title="odnoklassniki"><img
                                    src="http://@serverPath@/UserFiles/Image/Payments/odnoklassniki.png"
                                    width="25" height="25" alt=""></a></span>
                        </td>
                    <tr>
                        <td align="right" style="color: #adadad;line-height: 21px; " class="liquid-center">
                            <p style="margin: 0px;">
                                <a style="text-decoration: none; color: #adadad; font-family: Helvetica,sans-serif; font-size: 12px;"
                                    href="tel:@telNum@">@telNum@</a>
                            </p>
                            <p style="margin: 0px;">
                                <a style="text-decoration: none; color: #adadad; font-family: Helvetica,sans-serif; font-size: 12px;"
                                    href="mailto:@adminMail@">@adminMail@</a>
                            </p>
                            <p style="margin: 0px; text-decoration: none; color: #adadad; font-family: Helvetica,sans-serif; font-size: 12px;">
                                @org_adres@
                            </p>

                        </td>
                    </tr>
		            <tr>
		                <td height="10"></td>
		            </tr>
		        </tbody>
		    </table>
		</td>
	</tr>
    <tr>
        <td height="50"></td>
    </tr>
	</tbody>
	</table>
</td>
</tr>
<tr>
<td height="20">
</td>
</tr>
</tbody>
</table>
        </td>
	</tr>
</tbody>
</table>
<!-- End Footer -->

		</td>
		    </tr>
		</tbody>
		</table>

	</tbody>
	</table>
</body>
</html>