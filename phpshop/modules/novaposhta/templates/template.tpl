<link href="phpshop/modules/novaposhta/templates/css/style.css" rel="stylesheet">
<link href="phpshop/modules/novaposhta/templates/css/jquery-ui.min.css" rel="stylesheet">
<div class="modal fade bs-example-modal" id="novaposhtaModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Доставка</h4>
            </div>
            <div class="modal-body" style="width:100%;">
                <div id="novaposhta" style="height: 600px">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-3">
                            <div class="">
                                <img src="phpshop/modules/novaposhta/templates/img/logo.png" alt="Новая Почта" title="Новая Почта" width="187" height="60">
                            </div>
                            <div class="form-group">
                                <label for="novaposhta_city">Мiсто</label>
                                <input type="text" class="form-control" id="novaposhta_city" value="@novaposhtaDefaultCity@" data-ref="@novaposhtaDefaultCityRef@">
                            </div>
                            <div class="pvz-wrapper">Оберiть мiсто i вiддiленя на картi вашого мiста.</div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-9">
                            <div id="novaposhta-map"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="novaposhta-close">Обрати</button>
            </div>
        </div>
    </div>
</div>
<script src="phpshop/modules/novaposhta/templates/js/jquery-ui.min.js"></script>
<script src="phpshop/modules/novaposhta/templates/js/markerCluster.js"></script>
<script src="phpshop/modules/novaposhta/templates/js/script.js"></script>
<script>
    var NovaPoshtaInstance = new NovaPoshta();
    var NovaPoshtaParams = {
        'weight': '@novaposhtaWeight@',
        'latitude': '@novaposhtaLatitude@',
        'longitude': '@novaposhtaLongitude@'
    };
    NovaPoshtaInstance.init(NovaPoshtaParams);
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=@novaposhtaGoogleKey@&callback=novaposhtaMap" async defer></script>