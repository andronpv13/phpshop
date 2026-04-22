@php
if(empty(PHPShopParser::get('hideSite')))
echo '<a class="dropdown-item" href="/page/@topMenuLink@.html">@topMenuName@</a>';
else echo '<li class="navbar-nav-item"><a class="nav-link" href="/page/@topMenuLink@.html">@topMenuName@</a></li>';
php@