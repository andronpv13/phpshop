<a href="#" data-toggle="modal" data-target="#Panorama360Modal" class="button-icon"><span class="icons icons-big"><img title="360 Panorama" src="phpshop/modules/panorama360/img/360.svg"></span></a>
<!-- Modal -->
<div class="modal fade" id="Panorama360Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title h4" id="exampleModalLongTitle">Панорама 360&deg; &mdash; @productName@</span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">x</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="cd-product-viewer-wrapper" data-frame="@framePanorama@" data-friction="0.33">

                    <figure class="product-viewer">
                        <img src="phpshop/modules/panorama360/src/img-default.png" alt="Product Preview">
                        <div class="product-sprite" data-image="@imgPanorama@"></div>
                    </figure> <!-- .product-viewer -->

                    <div class="cd-product-viewer-handle">
                        <span class="fill"></span>
                        <span class="handle">Handle</span>
                    </div>

                </div> <!-- .cd-product-viewer-wrapper -->


            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="phpshop/modules/panorama360css/style.css">
<style type="text/css">
    .cd-product-viewer-wrapper .product-sprite {
        background: url('@imgPanorama@') no-repeat center center;
    }
</style>

<script src="phpshop/modules/panorama360/lib/jquery.mobile.custom.min.js"></script>
<script src="phpshop/modules/panorama360/lib/main.js"></script>