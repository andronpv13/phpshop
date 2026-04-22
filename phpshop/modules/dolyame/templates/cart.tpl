<div class="product-page-button">
    <form action="@dolyame_link@" method="get" name="dolyameForm">
        <div class="">
            Ожидайте перехода на сайт Долями, либо нажмите кнопку&nbsp;&nbsp;
            <input type="submit" value="{Перейти на сайт Долями}"  class="btn btn-default" />
        </div>
    </form>
</div>
<script>
    setTimeout(function(){
        document.dolyameForm.submit();
    }, 3000);    
</script>