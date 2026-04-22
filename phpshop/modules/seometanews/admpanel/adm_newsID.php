<?php

function addSeoNews($data) {
    global $PHPShopGUI;

    // SEO
    $Tab3 = $PHPShopGUI->setField("Title:", $PHPShopGUI->setInput("text", "meta_title_new", $data['meta_title']));
    $Tab3 .= $PHPShopGUI->setField("Keywords:", $PHPShopGUI->setInput("text", "meta_keywords_new", $data['meta_keywords']));
    $Tab3 .= $PHPShopGUI->setField("Description:", $PHPShopGUI->setTextArea("meta_description_new", $data['meta_description']));
    $PHPShopGUI->addTab(array("SEO", $Tab3));
}

$addHandler = array(
    'actionStart' => 'addSeoNews',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>