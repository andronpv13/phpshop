
<li class="list-inline-item nav-item  dropdown ">
    <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-user-friends"></i>
    </a>

        <!-- Navbar Vertical -->
        <div class="dropdown-menu navbar-vertical dropdown-menu-right" aria-labelledby="navbarDropdown" style="width:200px">
            <!-- Card -->
            <div class="">
                <div class="card-body">
                    <h6 class="text-cap small">{Кабинет}</h6>

                    <!-- List -->
                    <ul class="nav nav-sub nav-sm nav-tabs nav-list-y-2 mb-4">
                        <li class="nav-item">
                            <a class="" href="/users/">
                                <i class="fas fa-id-card nav-icon"></i>
                                {Настройки}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="" href="/users/order.html">
                                <i class="fas fa-shopping-basket nav-icon"></i>
                                {Заказы}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="" href="/users/message.html">
                                <i class="fas fa-comments nav-icon"></i>
                                {Диалоги}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="" href="/users/notice.html">
                                <i class="fas fa-bell nav-icon"></i>
                                {Уведомления}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="" href="?logout=true">
                                <i class="fas fa-sign-out-alt nav-icon"></i>
                                {Выход}
                            </a>
                        </li>
                    </ul>
                    <!-- End List -->

                    <h6 class="text-cap small">{Товары}</h6>

                    <!-- List -->
                    <ul class="nav nav-sub nav-sm  nav-list-y-2 mb-0">
                        <li class="nav-item">
                            <a class="" href="/order/">
                                <i class="fas fa-shopping-cart nav-icon"></i>
                                {Корзина}
                                <b class="badge badge-soft-dark badge-pill nav-link-badge cartnum">@num@</b>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="" href="/compare/">
                                <i class="fas fa-cogs nav-icon"></i>
                                {Сравнить}
                                <b class="badge badge-soft-dark badge-pill nav-link-badge numcompare">@numcompare@</b>
                            </a>
                        </li>
                        <li class="nav-item">
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
