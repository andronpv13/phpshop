<?php

function addModOption_panorama360($data) {
    global $PHPShopGUI;

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['panorama360']['panorama360_system']);
    $option = $PHPShopOrm->select();
    
    if(empty($option['frame']))
        $option['frame']=28;

    $PHPShopGUI->addJSFiles('../modules/panorama360/admpanel/js/liteuploader.js', '../modules/panorama360/admpanel/js/js.js', '../modules/panorama360/lib/jquery.mobile.custom.min.js', '../modules/panorama360/lib/main.js');
    $PHPShopGUI->addCSSFiles('../modules/panorama360/admpanel/css/adm.css', '../modules/panorama360/css/style.css');
    
    if(empty($data['img_panorama360']))
        $data['img_panorama360']=null;

    if (trim($data['img_panorama360']))
        $data['img_panorama360'] = '../../../..' . $data['img_panorama360'];

    $input_upload = '
		<input type="hidden" name="categoryId" value="' . $data['category'] . '">
		<input type="hidden" name="img_panorama360_new" value="' . $data['img_panorama360'] . '">
		
		<label for="fileUpload" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-hdd"></span> Загрузить изображение</label> <a href="#" class="btn btn-default btn-sm" id="delete_panorama_img" title="Удалить изображение"><span class="glyphicon glyphicon-remove"></span></a>
		<input type="file" name="fileUpload" id="fileUpload" class="fileUpload form-control" />
		<div id="files-list"></div>
		<div id="previews"></div>
		<p class="small text-muted"></p>
		<div id="download-img" class="progress hide">
			<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">0%</div>
		</div>
		<div id="display"></div>
	
		<div class="cd-product-viewer-wrapper pull-left" data-frame="'.$option['frame'].'" data-friction="0.33">
			<div class="window-width">
				<figure class="product-viewer">
					<img src="/phpshop/modules/panorama360/src/img-default.png" alt="Product Preview">
					<div class="product-sprite" data-image="' . $data['img_panorama360'] . '"></div>
				</figure> <!-- .product-viewer -->
		
				<div class="cd-product-viewer-handle">
					<span class="fill"></span>
					<span class="handle">Handle</span>
				</div>
			</div> <!-- .cd-product-viewer-handle -->
		</div> <!-- .cd-product-viewer-wrapper -->

		<style type="text/css">
			.cd-product-viewer-wrapper .product-sprite {
   				 background: url(' . $data['img_panorama360'] . ') no-repeat center center;
			}
		</style>
	';


    $download_img = $PHPShopGUI->setField('Загрузка изображения', $input_upload, 1, 'Рекомендованный формат: *.jpg, допустимые форматы: *.png, *.gif, максимальный размер: 25Mb');

    $collapse_download_img = $PHPShopGUI->setCollapse('Загрузка панорамного спрайта', $download_img, 'in', false);

    if (!empty($collapse_download_img))
        $PHPShopGUI->addTab(array("Панорама", $collapse_download_img, true));
}

$addHandler = array(
    'actionStart' => 'addModOption_panorama360',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>