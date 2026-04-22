<script>
    function checkModOneClickForma(){
        if(document.getElementById('oneclick_mod_name').value == "" || document.getElementById('oneclick_mod_tel').value == "")
            return false;
    }
</script>
<div style="position:relative">
    <div id="mod_oneclick_forma" style="display: none;width:350px;position:absolute;z-index:100">
        <div>
            <div style="position:relative; border:1px solid #ccc; background:#ebf1f6 ;border-radius:5px;">
                <form action="@ShopDir@/oneclick/" method="post" onsubmit="return checkModOneClickForma();" >
                    <table style="margin:0px 8px 0px 19px" border="0" cellpadding="2" cellspacing="0">
                        <tbody>
                            <tr>
                                <td colspan="2" style=" padding:10px;" align="right"><a style="padding:0px; font-size:11px;margin:0px 0px 0px 14px;color:#086ebd" href="javascript:void(0)" onclick="document.getElementById('mod_oneclick_forma').style.display='none';"><img src="phpshop/modules/oneclick/templates/close.png" alt="" border="0" align="absmiddle"></a></td>
                            </tr>
                            <tr>
                                <td><b>{Ваше имя}</b>:</td>
                                <td><input type="text" name="oneclick_mod_name" id="oneclick_mod_name" size="15"></td>
                            </tr>
                            <tr>
                                <td><b>{Телефон}</b>:</td>
                                <td><input type="text" name="oneclick_mod_tel" id="oneclick_mod_tel" size="15"> </td>
                            </tr>
                            <tr><td colspan="2"><p>@captcha@</p></td></tr>
                            <tr>
                                <td></td>
                                <td>
                                    <p><input type="hidden" name="oneclick_mod_product_id" value="@productUid@">
                                    <input type="submit" name="oneclick_mod_send" value="{Купить}"></p></td>
                            </tr>

                        </tbody>
                    </table>

                </form>
            </div>

        </div>

    </div>
</div>
<div style="padding-top:5px"><img src="phpshop/modules/oneclick/templates/oneclick.png" alt="" border="0" align="absmiddle"> <a href="javascript:void(0)" onclick="document.getElementById('mod_oneclick_forma').style.display='block';">Купить в 1 клик!</a></div>
