<!-- Вывод ProductList -->
    <!-- Product -->

                <div class="carousel-single-item">
                    <div class="col-12">
                        <div class="product-box mb-40">
                            <div class="product-box-wrapper">
                                <div class="product-img">
                                    <img src="@productlist_product_pic_small@" class="w-100" alt="">
                                    <a href="@shopDir@@productlist_product_url@.html" title="@productlist_product_name@" class="d-block">
                                        <div class="second-img">
                                            <img src="@productlist_product_pic_small@" alt="@productlist_product_name@" loading="lazy" class="w-100">
                                        </div>
                                    </a>
                                    <a href="javascript:void(0)"
                                        class="product-img-link quick-view-1 text-capitalize">Quick view</a>
                                </div>

                                <div class="product-desc pb-20">
                                    <div class="product-desc-top">
                                        <a href="wishlist.html" class="wishlist float-right"><span><i
                                                    class="fal fa-heart"></i></span></a>
                                    </div>
									<a class="product-title" title="@productlist_product_name@" href="@shopDir@@productlist_product_url@.html">@productlist_product_name@ </a>                                    
                                    <div class="price-switcher">
                    <span class="text-dark font-weight-bold">@productPrice@<span class="rubznak">@productValutaName@</span></span>
                    <span class="text-body ml-1 @php __hide('productPriceOld'); php@" ><del>@productPriceOld@</del></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    <!-- End Product -->
<!-- Конец ProductList -->
