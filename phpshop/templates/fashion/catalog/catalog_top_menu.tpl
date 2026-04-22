@php
if(empty(PHPShopParser::get('hideSite')))
echo '<li class="navbar-nav-item d-none d-xl-block"><a class="nav-link" href="/shop/CID_@catalogUid@.html">@catalogName@</a></li>';
php@