<?php

/**
 * ƒополнительна€ навигаци€
 */
function tab_menu_sort() {
    global $PHPShopInterface, $SortCategoryArray, $help;

    $tree = '<table class="tree table table-hover">
        <tr class="treegrid-0 data-tree">
		<td class="no_tree"><a href="?path=sort">'.__('ѕоказать все').'</a></td>
	</tr>';
    if (is_array($SortCategoryArray))
        foreach ($SortCategoryArray as $k => $v) {
            $tree.='<tr class="treegrid-' . $k . ' data-tree">
		<td class="no_tree"><a href="?path=sort&cat=' . $k . '">' . $v['name'] . '</a><span class="pull-right">' . $PHPShopInterface->setDropdownAction(array('edit', '|', 'delete', 'id' => $k)) . '</span></td>
	</tr>';
        }
    $tree.='</table><script>
    var cat="' . intval($_GET['cat']) . '";
    </script>';

    $help = '<p class="text-muted">'.__('—лева √руппы характеристик, справа - характеристики, если открыть характеристику, внутри будут ее значени€ и настройки.<br><br>Ћюба€ характеристика может быть добавлена в несколько групп и содержать значени€ дл€ нескольких каталогов. Ќа витрине будут выводитьс€ значени€ только дл€ текущего каталога, если включить <a href="?path=system#2" target="_blank">кеширование фильтра</a> и затем создать задачу на кеширование сразу всего сайта.<br><br>
ѕри пакетном импорте данных можно выбрать, <a href="?path=system#2" target="_blank">как загружать характеристики</a>: записывать все значени€ в одну характеристику, или дл€ каждого каталога создавать свой набор характеристик (свою √руппу характеристик) - это удобнее, если характеристик много.').'</p>';

    return $tree;
}

?>