var navigation_speed = null;
var projectDirectory = '<?= Configure::read("PROJECT_DIRECTORY") ?>';
var ajaxURLs = [
    projectDirectory + "/Main/searchStockPrice"
];


function refreshPage() {
    $.ajax({
        url: projectDirectory + '/app/reloadPage',
        async: true
    }).success(function () {
        location.reload();
    });
}

function filterAjaxUrl(url) {
    return jQuery.inArray(url, ajaxURLs);
}



function filterRows(status, owner, oper) {
    var filtro = '';
    $(".visit-status").fadeOut();
    // Filtro por tramitador
    var option2 = owner;
    Cookies.set("filter-visit-owner", option2);
    if (option2 != 0) {
        filtro += "[data-owner='" + option2 + "']";
    }
    // Filtro por operario
    var option3 = oper;
    Cookies.set("filter-visit-oper", option3);
    if (option3 != 0) {
        filtro += "[data-oper='" + option3 + "']";
    }
    // Filtro por estado
    var option = status;
    Cookies.set("filter-visit-status", option);
    if (option == 0) {
        $(".visit-status" + filtro).fadeIn();
    } else if (option == 1) {
        $(".visit-status[data-status*='citada']" + filtro).fadeIn();
    } else if (option == 2) {
        $(".visit-status[data-status*='confirmar']" + filtro).fadeIn();
    } else if (option == 3) {
        $(".visit-status[data-status*='curso']" + filtro).fadeIn();
    } else if (option == 4) {
        $(".visit-status[data-status*='parte']" + filtro).fadeIn();
    } else if (option == 5) {
        $(".visit-status[data-status*='finalizada']" + filtro).fadeIn();
    } else if (option == 6) {
        $(".visit-status[data-status*='c_proxima']" + filtro).fadeIn();
    } else if (option == 7) {
        $(".visit-status[data-status*='op_retrasado']" + filtro).fadeIn();
    } else if (option == 8) {
        $(".visit-status[data-status*='margen']" + filtro).fadeIn();
    } else if (option == 9) {
        $(".visit-status[data-status*='v_retrasada']" + filtro).fadeIn();
    }
}


$(document).ready(function () {
    // Control de peticiones Ajax
    $.ajaxSetup({
        beforeSend: function (xhr, settings) {
            if (filterAjaxUrl(settings.url) === -1) {
                $('.tooltip:visible').each(function () {
                    $(this).tooltip('hide');
                });
                $('#processLayout').fadeIn();
                $('#processAjax').show();
            }
            console.log('Ajax: ' + settings.url);
        }, complete: function () {
            $('#processAjax').fadeOut();
            $('#processLayout').fadeOut();
        }
    });

    // Prevención de vuelta atrás de página al pulsar BACKSPACE
    $(document).keydown(function (e) {
        if (e.keyCode == 8 && e.target.tagName != 'INPUT' && e.target.tagName != 'TEXTAREA') {
            e.preventDefault();
        }
    });

    // Prevención de submit en los input al pulsar INTRO
    $('form').on("keyup keypress", function (e) {
        var code = e.keyCode || e.which;
        if (code == 13 && e.target.tagName != 'TEXTAREA') {
            if (( this).id == "SiniestroListRecordsAllForm"){
                return true;
            }else{
                e.preventDefault();
                return false;
            }
        }
    });



    /*$.fn.dataTable.moment('HH:mm DD-MM-YYYY');
    $('#dataTable').DataTable();
    $('#dataTableAux').DataTable();*/

    /*$('body').delegate('.btn-tooltip', 'mouseenter', function () {
        $('.btn-tooltip').tooltip({container: 'body'});
    });
    $('.btn-popover').popover();
    $('.modal-message').modal();*/


});


function formatearFecha(fecha) {
    var fecha_formateada = ((Number(fecha.getHours()) >= 10) ? fecha.getHours() : '0' + String(fecha.getHours())) + ':';
    fecha_formateada += ((Number(fecha.getMinutes()) >= 10) ? fecha.getMinutes() : '0' + String(fecha.getMinutes())) + ' ';
    fecha_formateada += ((Number(fecha.getDate()) >= 10) ? fecha.getDate() : '0' + String(fecha.getDate())) + '-';
    fecha_formateada += ((Number(fecha.getMonth() + 1) >= 10) ? fecha.getMonth() + 1 : '0' + String(fecha.getMonth() + 1)) + '-';
    fecha_formateada += fecha.getFullYear();
    return fecha_formateada;
}
