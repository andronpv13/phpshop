<div class="bg-img-hero-center mx-md-auto gradient-x-overlay-sm-white" style="background-image: url('@banerImage@');">
	<a href="@banerLink@">
    <div class="row justify-content-between align-items-center w-xl-60 mx-md-auto pt-5">
        <div class="col-5 mb-lg-0 p-2">
            <div class="m-2">
                <h3 class="h1 banner-title" style="-webkit-filter: invert(@banerColor@%);filter: invert(@banerColor@%);">@banerTitle@</h3>
            </div>
            <span class="m-2 font-size-1 transition-3d-hover font-weight-bold @php __hide('banerDescription'); php@">@banerDescription@ <i class="fas fa-angle-right fa-sm ml-1"></i></span>
        </div>
        <div class="col-6 banner-image text-center">@banerContent@</div>
    </div>
    </a>
</div>
