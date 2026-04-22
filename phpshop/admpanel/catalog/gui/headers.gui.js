/**
 * JS Библиотека панели заголовков товара tab_headers.gui.php
 */

function ShablonAdd(pole, id) {
    var Shablon = document.getElementById(id).value;
    Shablon = Shablon + pole;
    document.getElementById(id).value = Shablon;
}


function ShablonPromt(id) {
    var pole = window.prompt("Введите слово", "");
    if (pole != null) {
        var Shablon = document.getElementById(id).value;
        Shablon = Shablon + pole;
        document.getElementById(id).value = Shablon;
    }
}

function ShablonDell(id) {
    document.getElementById(id).value = "";
}



$().ready(function() {

    $('.buttonSh').addClass('btn btn-default btn-sm');

});