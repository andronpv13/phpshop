<h1 class="page-title d-none">@pageTitle@</h1> 

<div class="container w-lg-100 mx-lg-auto">


    <!-- Конец блока поделиться -->
    <blockquote class="font-size-2 p-5  @php __hide('pageMainPreview'); php@">
        @pageMainPreview@
    </blockquote>



    <div>@pageContent@</div>
</div>


@odnotipDisp@

<!-- Page Section -->
<div class="border-top space-lg-2 @php __hide('pageLast'); php@">

    <div class="space-0 d-none d-sm-block">
        <div class="row row-center">
            @pageLast@
        </div>
    </div>

</div>
<!-- End Page Section -->