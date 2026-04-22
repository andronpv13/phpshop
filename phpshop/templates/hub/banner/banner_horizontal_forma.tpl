<div class="bg-img-hero-center" style="background-image: url('@banerImage@');">
	<a href="@banerLink@">
    <div class="row banner-align">
        <div class="col-md-7">
            <div class="m-2">
                <h3 class="h1 banner-title" style="-webkit-filter: invert(@banerColor@%);filter: invert(@banerColor@%);">@banerTitle@</h3>
            </div>
            <span class="m-2 font-size-1 transition-3d-hover font-weight-bold @php __hide('banerDescription'); php@">@banerDescription@ </span>
        </div>
        <div class="col-md-4 banner-image">@banerContent@</div>
    </div>
    </a>
</div>