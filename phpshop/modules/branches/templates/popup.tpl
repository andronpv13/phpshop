<link href="phpshop/modules/branches/templates/jquery-ui.css" rel="stylesheet">
<link href="phpshop/modules/branches/templates/style.css" rel="stylesheet">
<div class="modal fade bs-example-modal" id="geolocationModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Выберите город</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="text" name="geolocation-search" class="form-control" placeholder="Например: Москва">
                        </div>
                    </div>
                    <div class="row geolocation-block">
                        <div class="col-md-12">
                            @favoriteCities@
                        </div>
                    </div>
                    <div class="row geolocation-block">
                        <div class="col-md-6">
                            <div class="geolocation-title">Регион</div>
                            <ul class="geolocation-regions"></ul>
                        </div>
                        <div class="col-md-6">
                            <div class="geolocation-title">Город</div>
                            <ul class="geolocation-cities"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>