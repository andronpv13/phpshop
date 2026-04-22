<?php

function setModOptionGUI($name, $format, $value) {
    global $PHPShopGUI;
    
        switch ($format) {
            
            case 'textarea':
                $result = $PHPShopGUI->setTextarea($name,$value);
                break;

            case 'radio':
                $result = $PHPShopGUI->setRadio($name, 1, 'Да',$value).$PHPShopGUI->setRadio($name, 2, 'Нет',$value);
                break;

            case 'editor':
                $result = setEditor($name, $value);
                break;
            default:
                $result = $PHPShopGUI->setInput($format, $name, $value);
                break;
        }

    return $result;
}

function addModOption($data) {
    global $PHPShopGUI, $PHPShopModules;

    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.productoption.productoption_system"));
    $m_data = $PHPShopOrm->select();
    $vendor = unserialize($m_data['option']);
    
    $data = $PHPShopGUI->valid($data,'option6','option2','option8','option9','option10');

    $Tab10 = '';
    if (is_array($vendor)) {

        if (!empty($vendor['option_6_name']))
            if($vendor['option_6_format'] == 'editor')
                $Tab10 .= $PHPShopGUI->setCollapse($vendor['option_6_name'], setModOptionGUI("option6_new", $vendor['option_6_format'], $data['option6']), 'in', false, true, false, false);
            else
                $Tab10 .= $PHPShopGUI->setField($vendor['option_6_name'], setModOptionGUI("option6_new", $vendor['option_6_format'], $data['option6']),1, false,  false, 'control-label', false);

        if (!empty($vendor['option_7_name']))
            if($vendor['option_7_format'] == 'editor')
                $Tab10 .= $PHPShopGUI->setCollapse($vendor['option_7_name'], setModOptionGUI("option7_new", $vendor['option_7_format'], $data['option7']), 'in', false, true, false, false);
            else
                $Tab10.= $PHPShopGUI->setField($vendor['option_7_name'], setModOptionGUI("option7_new", $vendor['option_7_format'], $data['option7']),1, false,  false, 'control-label', false);

        if (!empty($vendor['option_8_name']))
            if($vendor['option_8_format'] == 'editor')
                $Tab10 .= $PHPShopGUI->setCollapse($vendor['option_8_name'], setModOptionGUI("option8_new", $vendor['option_8_format'], $data['option8']), 'in', false, true, false, false);
            else
                $Tab10.= $PHPShopGUI->setField($vendor['option_8_name'], setModOptionGUI("option8_new", $vendor['option_8_format'], $data['option8']),1, false,  false, 'control-label', false);

        if (!empty($vendor['option_9_name']))
            if($vendor['option_9_format'] == 'editor')
                $Tab10 .= $PHPShopGUI->setCollapse($vendor['option_9_name'], setModOptionGUI("option9_new", $vendor['option_9_format'], $data['option9']), 'in', false, true, false, false);
            else
                $Tab10.= $PHPShopGUI->setField($vendor['option_9_name'], setModOptionGUI("option9_new", $vendor['option_9_format'], $data['option9']),1, false,  false, 'control-label', false);
        if (!empty($vendor['option_10_name']))
            if($vendor['option_10_format'] == 'editor')
                $Tab10 .= $PHPShopGUI->setCollapse($vendor['option_10_name'], setModOptionGUI("option10_new", $vendor['option_10_format'], $data['option10']), 'in', false);
            else
                $Tab10.= $PHPShopGUI->setField($vendor['option_10_name'], setModOptionGUI("option10_new", $vendor['option_10_format'], $data['option10']),1, false,  false, 'control-label', false);
    }

   
    if(!empty($Tab10))
    $PHPShopGUI->addTab(array("Дополнительно", $Tab10,true));
}

function setEditor($name, $value){
    global $PHPShopSystem, $PHPShopBase;
    $oFCKeditor2 = new Editor($name);
    $oFCKeditor2->Height = '450';
    $oFCKeditor2->Config['EditorAreaCSS'] = chr(47) . "phpshop" . chr(47) . "templates" . chr(47) . $PHPShopSystem->getValue('skin') . chr(47) . $PHPShopBase->getParam('css.default');
    $oFCKeditor2->ToolbarSet = 'Normal';
    $oFCKeditor2->Value = $value;
    $editor = $oFCKeditor2->AddGUI();
    $editor .= '<hr>';
    return $editor;
}

$addHandler = array(
    'actionStart' => 'addModOption',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>