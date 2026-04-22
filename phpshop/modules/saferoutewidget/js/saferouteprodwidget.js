function saferouteprodwidgetStart() {
    // Инициализация виджета
    new SafeRouteCardWidget("saferoute-card-widget", {
        apiScript: "/phpshop/modules/saferoutewidget/api/saferoute-widget-api.php"
    });

    $("#saferoutewidgetModal").modal("toggle");
}