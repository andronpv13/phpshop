/**
 * JS Библиотека панели заголовков товара tab_headers.gui.php
 */


$().ready(function() {

    $(".seo-button").on('click', function() {
        var seo = $(this).attr("data-seo");
        var area = $('[name=' + $(this).attr("data-target") + ']').val();
        $('[name=' + $(this).attr("data-target") + ']').val(area + seo);
    });

});