
<li class="list-inline-item nav-item  dropdown ">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fal fa-user-friends"></i>
    </a>

    <!-- Navbar Vertical -->
    <div class="dropdown-menu navbar-vertical dropdown-menu-right" aria-labelledby="navbarDropdown" style="width:200px">
        <!-- Card -->
        <div class="">
            <div class="card-body">
                <h6 class="text-cap small">{Кабинет}</h6>

                <!-- List -->
                <ul class="nav nav-sub nav-sm nav-tabs nav-list-y-2 mb-4">

                    <!-- Account Login -->
                    <li class="nav-item">

                        <a class="" href="javascript:;" data-toggle="modal" data-target="#userModal">
                            <i class="fas fa-sign-in-alt nav-icon"></i>
                            {Вход}
                        </a>

                    </li>
                    <!-- End Account Login -->

                </ul>
                <!-- End List -->

                <h6 class="text-cap small @hideSite@">{Товары}</h6>

                <!-- List -->
                <ul class="nav nav-sub nav-sm  nav-list-y-2 mb-0">
                    <li class="nav-item @hideCatalog@">
                        <a class="" href="/order/">
                            <i class="fas fa-shopping-cart nav-icon"></i>
                            {Корзина}
                            <b class="badge badge-soft-dark badge-pill nav-link-badge cartnum">@num@</b>
                        </a>
                    </li>
                    <li class="nav-item @hideSite@">
                        <a class="" href="/compare/">
                            <i class="fas fa-cogs nav-icon"></i>
                            {Сравнить}
                            <b class="badge badge-soft-dark badge-pill nav-link-badge numcompare">@numcompare@</b>
                        </a>
                    </li>
                    <li class="nav-item @hideSite@">
                        <a class="" href="/users/wishlist.html">
                            <i class="fas fa-heart nav-icon"></i>
                            {Избранное}
                            <b class="badge badge-soft-dark badge-pill nav-link-badge wishlistcount">@wishlistCount@</b>
                        </a>
                    </li>
                </ul>
                <!-- End List -->

            </div>
        </div>
        <!-- End Card -->
    </div>
    <!-- End Navbar Vertical -->
</li>