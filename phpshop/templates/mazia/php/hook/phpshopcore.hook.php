<?php

// Блоки нового редактора lego
function template_compile_hook($obj, $data, $rout) {
    global $PHPShopNav;

   
    if ($rout == 'START') {

        if(!empty($_GET['mazia_h'])){
            $_SESSION['editor'][SkinName]['h']=intval($_GET['mazia_h']);
            header('Location: '.$PHPShopNav->objNav['truepath']);
        }
        if(!empty($_GET['mazia_f'])){
            $_SESSION['editor'][SkinName]['f']=intval($_GET['mazia_f']);
            header('Location: '.$PHPShopNav->objNav['truepath']);
        }
        if(!empty($_GET['mazia_c'])){
            $_SESSION['editor'][SkinName]['c']=intval($_GET['mazia_c']);
            header('Location: '.$PHPShopNav->objNav['truepath']);
        }
        
        
        // Шапка
        if (!empty($_SESSION['editor'][SkinName]['h']))
            $h = intval($_SESSION['editor'][SkinName]['h']);
        else
            $h = $_SESSION['editor'][SkinName]['h'] = 1;

        if (PHPShopParser::checkFile('header/header_' . $h . '.tpl')) {
            PHPShopParser::set('header', PHPShopParser::file($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/header/header_' . $h . '.tpl', true, false));
        }

        //  Подвал
        if (!empty($_SESSION['editor'][SkinName]['f']))
            $f = intval($_SESSION['editor'][SkinName]['f']);
        else
            $f = $_SESSION['editor'][SkinName]['f'] = 1;

        if (PHPShopParser::checkFile('footer/footer_' . $f . '.tpl')) {
            PHPShopParser::set('footer', PHPShopParser::file($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/footer/footer_' . $f . '.tpl', true, false));
        }
        

        // Центральная часть
        if (!empty($_SESSION['editor'][SkinName]['c']))
            $c = intval($_SESSION['editor'][SkinName]['c']);
        else
            $c = $_SESSION['editor'][SkinName]['c'] = 1;

        if (PHPShopParser::checkFile('container/container_' . $c . '.tpl')) {
            PHPShopParser::set('container', PHPShopParser::file($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/container/container_' . $c . '.tpl', true, false));
        }

    }
}

$addHandler = array
    (
    'Compile' => 'template_compile_hook',
);
?>