
$().ready(function () {

    // Выбрать все категории
    $("body").on('change', "#categories_gm_all", function () {
        if (this.checked)
            $('[name="categories_gm[]"]').selectpicker('selectAll');
        else
            $('[name="categories_gm[]"]').selectpicker('deselectAll');
    });
    
     $("body").on('change', "#categories_cm_all", function () {
        if (this.checked)
            $('[name="categories_cm[]"]').selectpicker('selectAll');
        else
            $('[name="categories_cm[]"]').selectpicker('deselectAll');
    });
    
     $("body").on('change', "#categories_ae_all", function () {
        if (this.checked)
            $('[name="categories_ae[]"]').selectpicker('selectAll');
        else
            $('[name="categories_ae[]"]').selectpicker('deselectAll');
    });
    
    $("body").on('change', "#categories_sm_all", function () {
        if (this.checked)
            $('[name="categories_sm[]"]').selectpicker('selectAll');
        else
            $('[name="categories_sm[]"]').selectpicker('deselectAll');
    });
});