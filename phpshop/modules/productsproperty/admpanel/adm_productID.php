<?php

function addProductIDProductsproperty($data) {
    global $PHPShopGUI;

    $productsproperty_array = unserialize($data['productsproperty_array']);
    
    $Tab = $PHPShopGUI->setField('Наименование свойства 1', $PHPShopGUI->setInputText(null, 'productsproperty_array[0][name]', $productsproperty_array[0]['name']));
    $Tab .= $PHPShopGUI->setField('ID товаров', 
            $PHPShopGUI->setInputText(null, 'productsproperty_array[0][id][0]', $productsproperty_array[0]['id'][0], 90, '<a href="#" data-target="[name=\'productsproperty_array[0][id][0]\']" class="tag-search"><span class="glyphicon glyphicon-search"></span></a>', 'left') .
            '<span style="float:left">&nbsp;</span>' .
            $PHPShopGUI->setInputText(null, 'productsproperty_array[0][id][1]', $productsproperty_array[0]['id'][1], 90, '<a href="#" data-target="[name=\'productsproperty_array[0][id][1]\']" class="tag-search"><span class="glyphicon glyphicon-search"></span></a>', 'left') .
            '<span style="float:left">&nbsp;</span>' .
            $PHPShopGUI->setInputText(null, 'productsproperty_array[0][id][2]', $productsproperty_array[0]['id'][2], 90, '<a href="#" data-target="[name=\'productsproperty_array[0][id][2]\']" class="tag-search"><span class="glyphicon glyphicon-search"></span></a>', 'left') .
            '<span style="float:left">&nbsp;</span>' .
            $PHPShopGUI->setInputText(null, 'productsproperty_array[0][id][3]', $productsproperty_array[0]['id'][3], 90, '<a href="#" data-target="[name=\'productsproperty_array[0][id][3]\']" class="tag-search"><span class="glyphicon glyphicon-search"></span></a>', 'left')
    );

    $Tab .= $PHPShopGUI->setField('Названия отображений', 
            $PHPShopGUI->setInputText(null, 'productsproperty_array[0][property][0]', $productsproperty_array[0]['property'][0], 90, false, 'left') .
            '<span style="float:left">&nbsp;</span>' .
            $PHPShopGUI->setInputText(null, 'productsproperty_array[0][property][1]', $productsproperty_array[0]['property'][1], 90, false, 'left') .
            '<span style="float:left">&nbsp;</span>' .
            $PHPShopGUI->setInputText(null, 'productsproperty_array[0][property][2]', $productsproperty_array[0]['property'][2], 90, false, 'left') .
            '<span style="float:left">&nbsp;</span>' .
            $PHPShopGUI->setInputText(null, 'productsproperty_array[0][property][3]', $productsproperty_array[0]['property'][3], 90, false, 'left')
    );

    $Tab .= $PHPShopGUI->setField('Наименование свойства 2', $PHPShopGUI->setInputText(null, 'productsproperty_array[1][name]', $productsproperty_array[1]['name']));
    $Tab .= $PHPShopGUI->setField('ID товаров', 
            $PHPShopGUI->setInputText(null, 'productsproperty_array[1][id][0]', $productsproperty_array[1]['id'][0], 90, '<a href="#" data-target="[name=\'productsproperty_array[1][id][0]\']" class="tag-search"><span class="glyphicon glyphicon-search"></span></a>', 'left') .
            '<span style="float:left">&nbsp;</span>' .
            $PHPShopGUI->setInputText(null, 'productsproperty_array[1][id][1]', $productsproperty_array[1]['id'][1], 90, '<a href="#" data-target="[name=\'productsproperty_array[1][id][1]\']" class="tag-search"><span class="glyphicon glyphicon-search"></span></a>', 'left') .
            '<span style="float:left">&nbsp;</span>' .
            $PHPShopGUI->setInputText(null, 'productsproperty_array[1][id][2]', $productsproperty_array[1]['id'][2], 90, '<a href="#" data-target="[name=\'productsproperty_array[1][id][2]\']" class="tag-search"><span class="glyphicon glyphicon-search"></span></a>', 'left') .
            '<span style="float:left">&nbsp;</span>' .
            $PHPShopGUI->setInputText(null, 'productsproperty_array[1][id][3]', $productsproperty_array[1]['id'][3], 90, '<a href="#" data-target="[name=\'productsproperty_array[1][id][3]\']" class="tag-search"><span class="glyphicon glyphicon-search"></span></a>', 'left')
    );

    $Tab .= $PHPShopGUI->setField('Названия отображений', 
            $PHPShopGUI->setInputText(null, 'productsproperty_array[1][property][0]', $productsproperty_array[1]['property'][0], 90, false, 'left') .
            '<span style="float:left">&nbsp;</span>' .
            $PHPShopGUI->setInputText(null, 'productsproperty_array[1][property][1]', $productsproperty_array[1]['property'][1], 90, false, 'left') .
            '<span style="float:left">&nbsp;</span>' .
            $PHPShopGUI->setInputText(null, 'productsproperty_array[1][property][2]', $productsproperty_array[1]['property'][2], 90, false, 'left') .
            '<span style="float:left">&nbsp;</span>' .
            $PHPShopGUI->setInputText(null, 'productsproperty_array[1][property][3]', $productsproperty_array[1]['property'][3], 90, false, 'left')
    );
    
    $Tab .= $PHPShopGUI->setField('Наименование свойства 3', $PHPShopGUI->setInputText(null, 'productsproperty_array[2][name]', $productsproperty_array[2]['name']));
    $Tab .= $PHPShopGUI->setField('ID товаров', 
            $PHPShopGUI->setInputText(null, 'productsproperty_array[2][id][0]', $productsproperty_array[2]['id'][0], 90, '<a href="#" data-target="[name=\'productsproperty_array[2][id][0]\']" class="tag-search"><span class="glyphicon glyphicon-search"></span></a>', 'left') .
            '<span style="float:left">&nbsp;</span>' .
            $PHPShopGUI->setInputText(null, 'productsproperty_array[2][id][1]', $productsproperty_array[2]['id'][1], 90, '<a href="#" data-target="[name=\'productsproperty_array[2][id][1]\']" class="tag-search"><span class="glyphicon glyphicon-search"></span></a>', 'left') .
            '<span style="float:left">&nbsp;</span>' .
            $PHPShopGUI->setInputText(null, 'productsproperty_array[2][id][2]', $productsproperty_array[2]['id'][2], 90, '<a href="#" data-target="[name=\'productsproperty_array[2][id][2]\']" class="tag-search"><span class="glyphicon glyphicon-search"></span></a>', 'left') .
            '<span style="float:left">&nbsp;</span>' .
            $PHPShopGUI->setInputText(null, 'productsproperty_array[2][id][3]', $productsproperty_array[2]['id'][3], 90, '<a href="#" data-target="[name=\'productsproperty_array[2][id][3]\']" class="tag-search"><span class="glyphicon glyphicon-search"></span></a>', 'left')
    );

    $Tab .= $PHPShopGUI->setField('Названия отображений', 
            $PHPShopGUI->setInputText(null, 'productsproperty_array[2][property][0]', $productsproperty_array[2]['property'][0], 90, false, 'left') .
            '<span style="float:left">&nbsp;</span>' .
            $PHPShopGUI->setInputText(null, 'productsproperty_array[2][property][1]', $productsproperty_array[2]['property'][1], 90, false, 'left') .
            '<span style="float:left">&nbsp;</span>' .
            $PHPShopGUI->setInputText(null, 'productsproperty_array[2][property][2]', $productsproperty_array[2]['property'][2], 90, false, 'left') .
            '<span style="float:left">&nbsp;</span>' .
            $PHPShopGUI->setInputText(null, 'productsproperty_array[2][property][3]', $productsproperty_array[2]['property'][3], 90, false, 'left')
    );

    $PHPShopGUI->addTab(array("Свойства", $Tab, true));
}

function updateProductIDProductsproperty() {

    if (is_array($_POST['productsproperty_array'])){
        
        
        
        $_POST['productsproperty_array'][0]['name']=urldecode($_POST['productsproperty_array'][0]['name']);
        $_POST['productsproperty_array'][1]['name']=urldecode($_POST['productsproperty_array'][1]['name']);
        
        foreach($_POST['productsproperty_array'][0]['property'] as $k=>$v)
                $_POST['productsproperty_array'][0]['property'][$k] = urldecode($v);
        
        foreach($_POST['productsproperty_array'][1]['property'] as $k=>$v)
                $_POST['productsproperty_array'][1]['property'][$k] = urldecode($v);
        
        $_POST['productsproperty_array_new'] = serialize($_POST['productsproperty_array']);
    }
}

$addHandler = array(
    'actionStart' => 'addProductIDProductsproperty',
    'actionDelete' => false,
    'actionUpdate' => 'updateProductIDProductsproperty'
);
?>