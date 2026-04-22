<?php

function setModOptionGUI($name, $format, $value) {
    global $PHPShopGUI;
    
        switch ($format) {
            
            case 'textarea':
                $result = $PHPShopGUI->setTextarea($name,$value);
                break;

            case 'radio':
                if(empty($value))
                    $value=2;
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
    
    $data = $PHPShopGUI->valid($data,'option1','option2','option3','option4','option5');

    $Tab10 = '';
    if (is_array($vendor)) {

        if (!empty($vendor['option_1_name']))
            if($vendor['option_1_format'] == 'editor'){
                
                if($data['parent_enabled'] == 0 and !empty($data['parent']))
                    true;
                    else 
               $PHPShopGUI->addTab(array($vendor['option_1_name'], setModOptionGUI("option1_new", $vendor['option_1_format'], $data['option1']), true));
            }
            else
                $Tab10 = $PHPShopGUI->setField($vendor['option_1_name'], setModOptionGUI("option1_new", $vendor['option_1_format'], $data['option1']),1, false,  false, 'control-label', false);

        if (!empty($vendor['option_2_name']))
            if($vendor['option_2_format'] == 'editor'){
                
                if($data['parent_enabled'] == 0 and !empty($data['parent']))
                    true;
                    else 
                  $PHPShopGUI->addTab(array($vendor['option_2_name'], setModOptionGUI("option2_new", $vendor['option_2_format'], $data['option2']), true));
            }
            else
                $Tab10.= $PHPShopGUI->setField($vendor['option_2_name'], setModOptionGUI("option2_new", $vendor['option_2_format'], $data['option2']),1, false,  false, 'control-label', false);


        if (!empty($vendor['option_3_name']))
            if($vendor['option_3_format'] == 'editor'){
                if($data['parent_enabled'] == 0 and !empty($data['parent']))
                    true;
                    else 
                $PHPShopGUI->addTab(array($vendor['option_3_name'], setModOptionGUI("option3_new", $vendor['option_3_format'], $data['option3']), true));
            }
            else
                $Tab10.= $PHPShopGUI->setField($vendor['option_3_name'], setModOptionGUI("option3_new", $vendor['option_3_format'], $data['option3']),1, false,  false, 'control-label', false);

        if (!empty($vendor['option_4_name']))
            if($vendor['option_4_format'] == 'editor'){
                
                if($data['parent_enabled'] == 0 and !empty($data['parent']))
                    true;
                    else 
                $PHPShopGUI->addTab(array($vendor['option_4_name'], setModOptionGUI("option4_new", $vendor['option_4_format'], $data['option4']), true));
            }
            else
                $Tab10.= $PHPShopGUI->setField($vendor['option_4_name'], setModOptionGUI("option4_new", $vendor['option_4_format'], $data['option4']),1, false,  false, 'control-label', false);

        if (!empty($vendor['option_5_name']))
            if($vendor['option_5_format'] == 'editor'){
                
                if($data['parent_enabled'] == 0 and !empty($data['parent']))
                    true;
                    else 
               $PHPShopGUI->addTab(array($vendor['option_5_name'], setModOptionGUI("option5_new", $vendor['option_5_format'], $data['option5']), true));
            }
            else
                $Tab10.= $PHPShopGUI->setField($vendor['option_5_name'], setModOptionGUI("option5_new", $vendor['option_5_format'], $data['option5']),1, false,  false, 'control-label', false);
    }


    if(!empty($Tab10))
    $PHPShopGUI->addTab(array("Дополнительно", $Tab10, true));
}

function addModOptionEdit($data){
    global $PHPShopGUI, $PHPShopSystem;
    $PHPShopGUI->masonry_grid = false;
    $PHPShopSystem->setSerilizeParam("admoption.editor",'default');
    addModOption($data);

}

function setEditor($name, $value){
    global $PHPShopSystem, $PHPShopBase,$PHPShopGUI;
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
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
    'actionUpdate' => false,
    'actionOptionEdit' => 'addModOptionEdit'
);
?>