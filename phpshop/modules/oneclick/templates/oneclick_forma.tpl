<script>
    function checkModOneClickForma(){
        if(document.getElementById('oneclick_mod_name').value == "" || document.getElementById('oneclick_mod_tel').value == "")
            return false;
    }
</script>
<div style="padding:5px">
    <form action="@ShopDir@/oneclick/" method="post" onsubmit="return checkModOneClickForma();" >
                    <table style="margin:0px 8px 0px 9px" border="0" cellpadding="2" cellspacing="0">
                        <tbody>
                            <tr>
                                <td><b>Имя</b>:</td>
                                <td><input type="text" name="oneclick_mod_name" id="oneclick_mod_name" size="15"></td>
                            </tr>
                            <tr>
                                <td><b>Телефон</b>:</td>
                                <td><input type="text" name="oneclick_mod_tel" id="oneclick_mod_tel" size="15"> </td>
                            </tr>
                            <tr><td colspan="2"><p>@captcha@</p></td></tr>
                            <tr>
                                <td></td>
                                <td>
                                    <input type="hidden" name="oneclick_mod_product_id" value="@productUid@">
                                    <input type="submit" name="oneclick_mod_send" value="Быстрый заказ"></td>
                            </tr>

                        </tbody>
                    </table>

                </form>
</div>