
<li class="list-inline-item nav-item  dropdown ">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-user-circle"></i>
    </a>

    <!-- Navbar Vertical -->
    <div class="dropdown-menu navbar-vertical dropdown-menu-right" aria-labelledby="navbarDropdown" style="width:200px">
        <!-- Card -->
        <div class="card">
            <div class="card-body">
                <h6 class="text-cap small">{Кабинет}</h6>

                <!-- List -->
                <ul class="nav nav-sub nav-sm nav-tabs nav-list-y-2 mb-4">

                    <!-- Account Login -->
                    <li class="nav-item">

                        <a class="js-hs-unfold-invoker nav-link" href="javascript:;" data-toggle="modal" data-target="#signupModal">
                            <i class="fas fa-sign-in-alt nav-icon"></i>
                            {Вход}
                        </a>

                    </li>
                    <!-- End Account Login -->

                </ul>
                <!-- End List -->

                <h6 class="text-cap small">{Товары}</h6>

                <!-- List -->
                <ul class="nav nav-sub nav-sm nav-tabs nav-list-y-2 mb-2">
                    <li class="nav-item">
                        <a class="nav-link" href="/order/">
                            <i class="fas fa-shopping-cart nav-icon"></i>
                            {Корзина}
                            <span class="badge badge-soft-dark badge-pill nav-link-badge" id="num">@num@</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/compare/">
                            <i class="fas fa-cogs nav-icon"></i>
                            {Сравнить}
                            <span class="badge badge-soft-dark badge-pill nav-link-badge">@numcompare@</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/users/wishlist.html">
                            <i class="fas fa-heart nav-icon"></i>
                            {Избранное}
                            <span class="badge badge-soft-dark badge-pill nav-link-badge">@wishlistCount@</span>
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