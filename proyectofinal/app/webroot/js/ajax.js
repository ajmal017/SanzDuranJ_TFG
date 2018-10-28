var old_count = 0;

function startTime() {
    var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    m = checkTime(m);
    var months = new Array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sept", "Oct", "Nov", "Dic");
    var days = new Array("Dom", "Lun", "Mar", "Miér", "Jue", "Vie", "Sáb");
    $('.hour-clock').text(h + ":" + m + " " + days[today.getDay()] + ". ");
    $('.date-clock').text(today.getDate() + " " + months[today.getMonth()] + ".");
    var t = setTimeout(function () {
        startTime()
    }, 30000);
}

function checkTime(i) {
    return (i < 10) ? "0" + i : i;
}

function incrementOldCountAjax() {
    old_count = (old_count > 0) ? old_count + 1 : 0;
    var notifications = parseInt($('.notifications-envelope-number').text());
    if (notifications - 1 == 0) {
        $('.notifications-envelope-number').fadeOut();
        $('.notifications-envelope-number').destroy();
    } else if (notifications - 1 > 0) {
        $('.notifications-envelope-number').text(notifications - 1);
    }
}

function buildDropdown() {
    $('.dropdown-container').text('');
    first = '';
    first += '<li>';
    first += '<strong class="text-uppercase text-left">NOTIFICACIONES</strong>';
    first += '<strong class="text-uppercase pull-right list-tooltip" data-toggle="tooltip" data-placement="right" title="Actualizar"><i class="fa fa-refresh refresh-mark"></i></strong>';
    first += '</li>';
    first += '<li class="divider dropdown-divider"></li>';
    $('.dropdown-container').append(first);

    if ($('.notifications-envelope-number').length > 0) {
        var notifications = $('.notifications-envelope-number').text();
        html = '';
        html += '<li>';
        html += '<div class="dropdown-item-container">';
        html += '<div>';
        html += '<strong>John Smith</strong>';
        html += '<span class="pull-right text-muted dropdown-mark list-tooltip" data-toggle="tooltip" data-placement="right" title="Marcar como vista">';
        html += '<i class="fa fa-eye" onclick="incrementOldCountAjax()"></i>';
        html += '</span>';
        html += '</div>';
        html += '<div>';
        html += 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...';
        html += '</div>';
        html += '</div>';
        html += '</li>';
        html += '<li class="divider dropdown-divider"></li>';

        for (var i = 0; i < notifications; i++) {
            $('.dropdown-container').append(html);
        }
    } else {
        html = '';
        html += '<li>';
        html += '<div class="dropdown-item-container">';
        html += '<div>';
        html += 'No tiene notificaciones pendientes.';
        html += '</div>';
        html += '</div>';
        html += '</li>';
        html += '<li class="divider dropdown-divider"></li>';
        $('.dropdown-container').append(html);
    }

    last = '';
    last += '<li class="text-center">';
    last += '<a href="#"><strong class="text-uppercase">Ver todos</strong></a>';
    last += '</li>';
    $('.dropdown-container').append(last);
    $('.list-tooltip').tooltip({container: 'body'});
}