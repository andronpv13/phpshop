<div class="navbar-banner" style="background-image: url('@banerImage@');">
    <div class="navbar-banner-content">
        <div class="mb-4">
            <span style="-webkit-filter: invert(@banerColor@%);filter: invert(@banerColor@%);" class="h2 banner-title">@banerTitle@</span>
        @banerContent@
        </div>
        <a class="btn btn-primary btn-sm transition-3d-hover @php __hide('banerDescription'); php@" href="@banerLink@">@banerDescription@ <i class="fas fa-angle-right fa-sm ml-1"></i></a>
    </div>
</div>