$(document).ready(function() {
    function adjustMenuItems() {
        const $menuContainer = $('.header-menu-wrapper');
        const $menuItems = $('.main-navbar-top > li:not(.more-menu-dropdown)');
        const $moreDropdown = $('.more-menu-dropdown');
        const $moreMenu = $('.more-dropdown-menu');
        
        const containerWidth = $menuContainer.width();
        let totalWidth = 0;
        let visibleItems = [];
        let hiddenItems = [];
        
        // Сбрасываем все пункты меню
        $menuItems.show().css('visibility', 'visible');
        $moreDropdown.hide();
        $moreMenu.empty();
        
        // Измеряем ширину каждого пункта
        $menuItems.each(function(index) {
            const $item = $(this);
            const itemWidth = $item.outerWidth(true);
            
            if (totalWidth + itemWidth + 60 <= containerWidth) {
                // Пункт помещается
                totalWidth += itemWidth;
                visibleItems.push($item);
            } else {
                // Пункт не помещается
                hiddenItems.push($item);
            }
        });
        
        // Если есть скрытые пункты - показываем бургер
        if (hiddenItems.length > 0) {
            $moreDropdown.show();
            
            // Добавляем скрытые пункты в выпадающее меню
            hiddenItems.forEach(function($item) {
                const $clone = $item.clone();
                const $link = $clone.find('a').first();
                const $subMenu = $clone.find('ul').first();
                
                // Обрабатываем клик по пункту
                $link.on('click', function(e) {
                    if ($subMenu.length) {
                        e.preventDefault();
                        $subMenu.toggle();
                    }
                });
                
                // Создаем пункт для выпадающего меню
                const $dropdownItem = $('<li>').append($link);
                if ($subMenu.length) {
                    $subMenu.addClass('dropdown-submenu');
                    $dropdownItem.append($subMenu);
                }
                
                $moreMenu.append($dropdownItem);
                
                // Скрываем оригинальный пункт
                $item.hide();
            });
        }
    }
    
    // Запускаем при загрузке и изменении размера окна
    adjustMenuItems();
    $(window).on('resize', adjustMenuItems);
    
    // Обработка подменю в выпадающем меню
    $(document).on('click', '.more-dropdown-menu .dropdown-submenu', function(e) {
        e.stopPropagation();
        $(this).find('ul').first().toggle();
    });
});