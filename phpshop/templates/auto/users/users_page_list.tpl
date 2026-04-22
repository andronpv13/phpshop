<h1 class="h2 page-title d-none">@formaTitle@</h1> 

@php

if(empty($_SESSION['UsersId'])){
  $GLOBALS['user_toggle'] = 'data-toggle="modal" data-target="#signupModal"';
  $GLOBALS['user_style'] = 'js-hs-unfold-invoker';
  $GLOBALS['user_hide'] = 'd-none';
}
else $GLOBALS['user_toggle'] = $GLOBALS['user_style'] = $GLOBALS['user_hide'] = null;

php@

<div class="row">
    <div class="col-lg-3 mb-5 mb-lg-0 d-none d-lg-block d-xl-block">

        <div class="navbar-vertical">
            <!-- Card -->
            <div class="card">
                <div class="card-body">
                    <h6 class="text-cap small">{Кабинет}</h6>

                    <!-- List -->
                    <ul class="nav nav-sub nav-sm nav-tabs nav-list-y-2 mb-4">
                        <li class="nav-item">
                            <a class="nav-link @php echo $GLOBALS['user_style'];  php@" href="/users/" @php echo $GLOBALS['user_toggle']; php@>
                                <i class="fas fa-id-card nav-icon"></i>
                                {Настройки}
                            </a>
                        </li>
                        <li class="nav-item  @hideCatalog@">
                            <a class="nav-link @php echo $GLOBALS['user_style'];  php@" href="/users/order.html" @php echo $GLOBALS['user_toggle']; php@>
                                <i class="fas fa-shopping-basket nav-icon"></i>
                                {Заказы}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @php echo $GLOBALS['user_style'];  php@" href="/users/message.html" @php echo $GLOBALS['user_toggle']; php@>
                                <i class="fas fa-comments nav-icon"></i>
                                {Диалоги}
                            </a>
                        </li>
                        <li class="nav-item @hideCatalog@">
                            <a class="nav-link @php echo $GLOBALS['user_style'];  php@" href="/users/notice.html" @php echo $GLOBALS['user_toggle']; php@>
                                <i class="fas fa-bell nav-icon"></i>
                                {Уведомления}
                            </a>
                        </li>
                        <li class="nav-item @php echo $GLOBALS['user_hide']; php@">
                            <a class="nav-link" href="?logout=true">
                                <i class="fas fa-sign-out-alt nav-icon"></i>
                                {Выход}
                            </a>
                        </li>
                    </ul>
                    <!-- End List -->

                    <h6 class="text-cap small @hideSite@">{Товары}</h6>

                    <!-- List -->
                    <ul class="nav nav-sub nav-sm nav-tabs nav-list-y-2 mb-2">
                        <li class="nav-item @hideCatalog@">
                            <a class="nav-link" href="/order/">
                                <i class="fas fa-shopping-cart nav-icon"></i>
                                {Корзина}
                                <span class="badge badge-soft-dark badge-pill nav-link-badge" id="num">@num@</span>
                            </a>
                        </li>
                        <li class="nav-item @hideSite@">
                            <a class="nav-link" href="/compare/">
                                <i class="fas fa-cogs nav-icon"></i>
                                {Сравнить}
                                <span class="badge badge-soft-dark badge-pill nav-link-badge">@numcompare@</span>
                            </a>
                        </li>
                        <li class="nav-item @hideSite@">
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

    </div>
    <div class="col-lg-9 mb-5 mb-lg-0">
        @formaContent@
    </div>
</div>