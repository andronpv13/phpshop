<?php
session_start();
$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass(array("base", "system", "yandexcloud", "orm", "date", "security", "string", "parser", "lang", "admgui"));

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopBase->chekAdmin();

$PHPShopSystem = new PHPShopSystem();
$PHPShopGUI = new PHPShopGUI();

$_SESSION['lang'] = $PHPShopSystem->getSerilizeParam("admoption.lang_adm");
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'admin'));

if(empty($_GET['send']))
 $_GET['text']=PHPShopString::utf8_win1251($_GET['text']);


$YandexSearch = new YandexSearch();
$result = $YandexSearch->search_img(PHPShopString::win_utf8($_GET['text']), $_GET['itype'], $_GET['iorient'], $_GET['isize'], $_GET['page']);

// Тема оформления
if (empty($_SESSION['admin_theme']))
    $theme = PHPShopSecurity::TotalClean($PHPShopSystem->getSerilizeParam('admoption.theme'));
else
    $theme = $_SESSION['admin_theme'];
if (!file_exists('../css/bootstrap-theme-' . $theme . '.css'))
    $theme = 'default';
?>

<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['PHPShopLang']->code; ?>">
    <head>
        <meta charset="<?php echo $GLOBALS['PHPShopLang']->charset; ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Yandex Search</title>

        <!-- Bootstrap -->
        <link  href="../../css/bootstrap-theme-<?php echo $theme; ?>.css" rel="stylesheet">
        <link  href="../../css/admin.css" rel="stylesheet">

    </head>
    <body role="document">

        <script src="../../js/jquery-1.11.0.min.js" data-rocketoptimized="false" data-cfasync="false"></script>
        <script src="../../js/bootstrap.min.js" data-rocketoptimized="false" data-cfasync="false"></script>

        <form class="" style="padding:10px">
            <div class="input-group" >
                <input name="text" maxlength="256" value="<?php echo $_GET['text']; ?>" class="form-control input-sm" required="" type="search">
                <input name="send" value="1" type="hidden">
                <span class="input-group-btn">
                    <button class="btn btn-default btn-sm" type="submit"><span class="glyphicon glyphicon-search"></span> <?php _e('Поиск') ?></button>
                </span>
            </div>

            <div class="btn-group " data-toggle="buttons" style="padding-top:5px">
                <label class="btn btn-default btn-sm <?php if (empty($_GET['itype'])) echo 'active'; ?>">
                    <input type="radio" name="itype" value="" autocomplete="off" <?php if (empty($_GET['itype'])) echo 'checked=""' ?>> <?php _e('Все') ?>
                </label>
                <label class="btn btn-default btn-sm <?php if ($_GET['itype'] == 'IMAGE_FORMAT_JPEG') echo 'active'; ?>">
                    <input type="radio" name="itype" value="IMAGE_FORMAT_JPEG" autocomplete="off" <?php if ($_GET['itype'] == 'IMAGE_FORMAT_JPEG') echo 'checked=""' ?>> JPG
                </label>
                <label class="btn btn-default btn-sm <?php if ($_GET['itype'] == 'IMAGE_FORMAT_PNG') echo 'active'; ?>">
                    <input type="radio" name="itype" value="IMAGE_FORMAT_PNG" autocomplete="off" <?php if ($_GET['itype'] == 'IMAGE_FORMAT_PNG') echo 'checked=""' ?>> PNG
                </label>
            </div>

            <div class="btn-group " data-toggle="buttons" style="padding-top:5px">
                <label class="btn btn-default btn-sm <?php if (empty($_GET['iorient'])) echo 'active'; ?>">
                    <input type="radio" name="iorient" value="" autocomplete="off" <?php if (empty($_GET['iorient'])) echo 'checked=""' ?>> <?php _e('Все') ?>
                </label>
                <label class="btn btn-default btn-sm <?php if ($_GET['iorient'] == 'IMAGE_ORIENTATION_HORIZONTAL') echo 'active'; ?>">
                    <input type="radio" name="iorient" value="IMAGE_ORIENTATION_HORIZONTAL" autocomplete="off" <?php if ($_GET['iorient'] == 'IMAGE_ORIENTATION_HORIZONTAL') echo 'checked=""' ?>> <?php _e('Горизонтальные') ?>
                </label>
                <label class="btn btn-default btn-sm <?php if ($_GET['iorient'] == 'IMAGE_ORIENTATION_VERTICAL') echo 'active'; ?>">
                    <input type="radio" name="iorient" value="IMAGE_ORIENTATION_VERTICAL" autocomplete="off" <?php if ($_GET['iorient'] == 'IMAGE_ORIENTATION_VERTICAL') echo 'checked=""' ?>> <?php _e('Вертикальные') ?>
                </label>
            </div>

            <div class="btn-group " data-toggle="buttons" style="padding-top:5px">
                <label class="btn btn-default btn-sm <?php if (empty($_GET['isize'])) echo 'active'; ?>">
                    <input type="radio" name="isize" value="" autocomplete="off" <?php if (empty($_GET['isize'])) echo 'checked=""' ?>> <?php _e('Все') ?>
                </label>
                <label class="btn btn-default btn-sm <?php if ($_GET['isize'] == 'IMAGE_SIZE_LARGE') echo 'active'; ?>">
                    <input type="radio" name="isize" value="IMAGE_SIZE_LARGE" autocomplete="off" <?php if ($_GET['isize'] == 'IMAGE_SIZE_LARGE') echo 'checked=""' ?>> <?php _e('Большие') ?>
                </label>
                <label class="btn btn-default btn-sm <?php if ($_GET['isize'] == 'IMAGE_SIZE_MEDIUM') echo 'active'; ?>">
                    <input type="radio" name="isize" value="IMAGE_SIZE_MEDIUM" autocomplete="off" <?php if ($_GET['isize'] == 'IMAGE_SIZE_MEDIUM') echo 'checked=""' ?>> <?php _e('Средние') ?>
                </label>
                <label class="btn btn-default btn-sm <?php if ($_GET['isize'] == 'IMAGE_SIZE_SMALL') echo 'active'; ?>">
                    <input type="radio" name="isize" value="IMAGE_SIZE_SMALL" autocomplete="off" <?php if ($_GET['isize'] == 'IMAGE_SIZE_SMALL') echo 'checked=""' ?>> <?php _e('Маленькие') ?>
                </label>
            </div>
            
            <div class="btn-group " data-toggle="buttons" style="padding-top:5px">
                <label class="btn btn-default btn-sm <?php if (empty($_GET['page'])) echo 'active'; ?>">
                    <input type="radio" name="page" value="0" autocomplete="off" <?php if (empty($_GET['isize'])) echo 'checked=""' ?>> 1
                </label>
                <label class="btn btn-default btn-sm <?php if ($_GET['page'] == 1) echo 'active'; ?>">
                    <input type="radio" name="page" value="1" autocomplete="off" <?php if ($_GET['page'] == 1) echo 'checked=""' ?>> 2
                </label>
                <label class="btn btn-default btn-sm <?php if ($_GET['page'] == 2) echo 'active'; ?>">
                    <input type="radio" name="page" value="2" autocomplete="off" <?php if ($_GET['page'] == 2) echo 'checked=""' ?>>3
                </label>
                <label class="btn btn-default btn-sm <?php if ($_GET['page'] == 3) echo 'active'; ?>">
                    <input type="radio" name="page" value="3" autocomplete="off" <?php if ($_GET['page'] == 3) echo 'checked=""' ?>> 4
                </label>
                <label class="btn btn-default btn-sm <?php if ($_GET['page'] == 4) echo 'active'; ?>">
                    <input type="radio" name="page" value="4" autocomplete="off" <?php if ($_GET['page'] == 4) echo 'checked=""' ?>> 5
                </label>
                 <label class="btn btn-default btn-sm <?php if ($_GET['page'] == 5) echo 'active'; ?>">
                    <input type="radio" name="page" value="5" autocomplete="off" <?php if ($_GET['page'] == 5) echo 'checked=""' ?>> 6
                </label>
            </div>

        </form>

        <div class="item-block">

            <?php
            if (is_array($result))
                foreach ($result as $images) {
                    $header = '<span class="label label-info">' . strtoupper($images['type']) . '</span> <span class="label label-success">' . round($images['size'] / 100) . ' KB</span> <span class="label label-primary hidden-xs">' . $images['width'] . ' * ' . $images['height'] . '</span> <a href="' . $images['url'] . '" title="' . __('Перейти') . '"  target="_blank" class="btn btn-default btn-xs pull-right hidden-xs"><span class="glyphicon glyphicon-eye-open"></span></a>';
                    $content = '<a href="#" class="yandexsearch-select" data-target="' . $_GET['target'] . '" data-file="' . $images['url'] . '"><img title="' . __('Выбрать') . '" alt=""  src="' . $images['thumbnail'] . '" class="img-responsive"></a>';
                    echo '<div class="item-wrap">' . $PHPShopGUI->setPanel($header, $content) . '</div>';
                } 
                else
                echo $PHPShopGUI->setAlert(PHPShopString::utf8_win1251 ($result),'danger',true, false, false ,'margin:10px;width:100%') ;
            ?>
        </div>

        <script>
            $().ready(function () {

                // Выбор изображения в Яндексе
                $('body').on('click', '.yandexsearch-select', function (event) {
                    event.preventDefault();

                    var id = $(this).attr('data-target');
                    var file = $(this).attr('data-file');

                    parent.window.$('[data-icon="' + id + '"]').html(file);
                    parent.window.$('[data-icon="' + id + '"]').prev('.glyphicon').removeClass('hide');
                    parent.window.$("input[name='" + id + "']").val(file);
                    parent.window.$('[data-thumbnail="' + id + '"]').attr('src', file);
                    parent.window.$("input[name=img_new]").val(file);
                    parent.window.$("input[name=furl]").val(file);
                    parent.window.$('#adminModal').modal('hide');
                });
            });

        </script>
    </body>
</html>