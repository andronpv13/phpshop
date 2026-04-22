$(document).ready(function () {
   $('#yandex_type_new').on('change', function () {
      // Точки продаж
      if(Number($(this).val()) === 2) {
         $('.yandex-outlets').removeClass('hide');
      } else {
         $('.yandex-outlets').addClass('hide');
      }
   });
});