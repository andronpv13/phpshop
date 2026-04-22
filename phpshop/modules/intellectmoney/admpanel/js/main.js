$(document).ready(function() {
    getInputContainer('#eshopId').hide();
    getInputContainer('#accountId').hide();
    getInputContainer('#formId').hide();
    getInputContainer('#testMode').hide();

    onIntegrationMethodChange($('#integrationMethod').val());
    $('#integrationMethod').on('change', function() {
        onIntegrationMethodChange($(this).val());
    });

});

function getInputContainer(selector) {
    return $(selector).closest('.form-group');
}

function getTabContainer(tabIndex) {
    return $("[aria-controls='tabs-" + tabIndex + "']").closest('li');
}

function onIntegrationMethodChange(value) {
    switch(value) {
        case 'P2P':
            getInputContainer('#formId').show();
            getInputContainer('#accountId').show();
            getInputContainer('#eshopId').hide();
            getInputContainer('#testMode').hide();
            getTabContainer(1).hide();
            break;
        default:
            getInputContainer('#formId').hide();
            getInputContainer('#accountId').hide();
            getInputContainer('#eshopId').show();
            getInputContainer('#testMode').show();
            getTabContainer(1).show();
            break;
    }
}