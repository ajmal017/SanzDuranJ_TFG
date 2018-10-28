function showModalSelectionJS(title, html_id, inputs, skel, url, id, onElementSelected) {
    showModalSelectionJS(title, html_id, inputs, skel, url, id, onElementSelected, false);
}

function showModalSelectionJS(title, html_id, inputs, skel, url, id, onElementSelected, multipleToken) {
    if (multipleToken) {
        id = JSON.parse(id);
    }
    $.ajax({
        type: "POST",
        url: projectDirectory + '/' + url,
        data: {token: id},
        async: true
    }).done(function (data) {
        fillModal(title, html_id, inputs, skel, JSON.parse(data), onElementSelected);
    });
}

function showModalListAllNotificationsJS(title, html_id, skel, url) {
    $.ajax({
        type: "POST",
        url: projectDirectory + '/' + url,
        async: true
    })
        .done(function (data) {
            fillListAllNotificationsModal(title, html_id, skel, JSON.parse(data));
        });
}

function showModalListAllMessagesJS(title, html_id, skel, url) {
    $.ajax({
        type: "POST",
        url: projectDirectory + '/' + url,
        async: true
    }).done(function (data) {
        fillListAllMessagesModal(title, html_id, skel, JSON.parse(data));
    });
}

function showModalListAllExpedientsJS(title, html_id, skel, url, id) {
    $.ajax({
        type: "POST",
        url: projectDirectory + '/' + url,
        data: {
            token: id
        },
        async: true
    }).done(function (data) {
        fillListAllExpedientsModal(title, html_id, skel, JSON.parse(data));
    });
}

function showModalListAllSendedMessagesJS(title, html_id, skel, url) {
    $.ajax({
        type: "POST",
        url: projectDirectory + '/' + url,
        async: true
    })
        .done(function (data) {
            // console.log(data);
            fillListAllSendedMessagesModal(title, html_id, skel, JSON.parse(data));
        });
}

function showModalBasicJS(title, html_id, skel, url, id) {
    $.ajax({
        type: "POST",
        url: projectDirectory + '/' + url,
        data: {token: id},
        async: true
    })
        .done(function (data) {
            fillBasicModal(title, html_id, skel, JSON.parse(data), {size: 'normal'} /* {size: 'extra'} */);
        });
}


// Funcion que crea el modal con confirmación de petición Ajax.
function fillModalForAjax(id, title, type_id, content, operation_call, operation_name, reopen) {
    $('.modal-backdrop').remove();
    var type = ['modal-header-success', 'modal-header-info', 'modal-header-warning', 'modal-header-danger'];
    var icons = ['fa-check', 'fa-info', 'fa-exclamation', 'fa-ban'];
    var buttons = ['btn-success', 'btn-info', 'btn-warning', 'btn-danger'];
    var html = "";
    var ids = id.split(/-/);
    if (ids.length == 1) {
        ids = ids[0];
    }
    html += "<div class='modal fade' id='" + id + "' tabindex='-1' role='dialog' aria-hidden='false'>";
    html += "<div class='modal-dialog'>";
    html += "<div class='modal-content'>";
    html += '<div class="modal-header ' + type[type_id] + '">';
    html += "<button type='button' class='close modal-close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Cerrar</span></button>";
    html += "<h4 class='modal-title'><i class='fa " + icons[type_id] + " fa-margin-right'></i><strong>" + title + "</strong></h4>";
    html += "</div>";
    html += "<div class='modal-body'>";
    html += "<p style='text-align: justify'>" + content + "</p>"
    html += "</div>";
    html += "<div class='modal-footer'>";

    //Condicion para solo agragarle un boton al modal, el de cerrar.
    html += operation_name.length == 0 ? '<button type="button" class="btn btn-default" data-dismiss="modal" id="' + id + '-close">Cerrar</button>' : '<div class="col-md-6 text-left"><button type="button" class="btn btn-default" data-dismiss="modal" id="' + id + '-close">Cerrar</button></div>';

    if (operation_name.length > 0) {
        html += '<div class="col-md-6 text-right"><a id="' + id + '-opcall" class="btn ' + buttons[type_id] + '" data-dismiss="modal">' + operation_name + '</a></div>';

    }
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    $('.js-modal').html(html);
    //Si el nombre de la funcion que le asignaremos al clik de boton es distinto de null
    if (operation_call != null) {
        $('#' + id).delegate('#' + id + '-opcall', 'click', function () {
            window[operation_call](ids);
        });
    }
    $('#' + id).modal();
    if (reopen === true) {
        $('#' + id).on('hidden.bs.modal', function () {
            listMessagesInbox();
        });
    }
}

function fillModal(title, html_id, inputs, skel, data, onElementSelected) {
    var model = skel[0];
    skel = skel.splice(1, skel.length - 1);
    var html = "";
    var value = "";
    html += "<div class='modal fade in' id='" + html_id + "' tabindex='-1' role='dialog' aria-hidden='false'>";
    html += "<div class='modal-dialog modal-lg'>";
    html += "<div class='modal-content'>";
    html += "<div class='modal-header modal-header-info'>";
    html += "<button type='button' class='close modal-close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>";
    html += "<h4 class='modal-title' id='myModalLabel'>" + title + "</h4>";
    html += "</div>";
    html += "<div class='modal-body'>";
    html += "<table id='" + html_id + "-selection' class='table table-striped table-hover' cellspacing='0' role='grid'>";
    html += "<thead>";
    html += "<tr role='row'>";
    skel.forEach(function (s) {
        html += "<th class='sorting' tabindex='0' rowspan='1' colspan='1'>" + s[1] + "</th>";
    });
    html += "<th class='sorting' tabindex='0' rowspan='1' colspan='1'>Acción</th>";
    html += "</tr>";
    html += "</thead>";
    html += "<tbody>";
    var i = 1;
    data.forEach(function (d) {
        html += "<tr role='row'>";
        skel.forEach(function (s) {
            html += "<td>" + d[model][s[0]] + "</td>";
            value = d[model][s[0]];
        });
        html += "<td>";
        if (html_id == "modal-table-poliza") {
            html += "<a href='#' class='dataTables_link label label-primary' data-token='" + d[model][inputs[0]] + "' data-text='" + d[model][inputs[1]] + "' data-dismiss='modal' id='" + html_id + "-link-" + i + "'>Seleccionar</a>";
        } else {
            html += "<a href='#' class='dataTables_link label label-primary' data-token='" + d[model][inputs[0]] + "' data-text='" + d[model][inputs[1]] + "' data-dismiss='modal' id='" + html_id + "-link-" + i + "'>Seleccionar</a>";
        }
        html += "</td>";
        html += "</tr>";
        i++;
    });
    html += "<tfoot>";
    html += "<tr role='row'>";
    skel.forEach(function (s) {
        html += "<th rowspan='1' colspan='1'>" + s[1] + "</th>";
    });
    html += "<th rowspan='1' colspan='1'>Acción</th>";
    html += "</tr>";
    html += "</tfoot>";
    html += "</tbody>";
    html += "</table>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    $("#" + html_id).remove();
    $('.js-modal').html(html);
    var name = html_id + "-link-";
    if (typeof onElementSelected != 'function') {
        // Si onElementSelected no es una función le asignamos la implementación por defecto.
        onElementSelected = function (e) {
            $("." + html_id + "-input").val($(this).attr('data-text'));
            $("." + html_id + "-input-hidden").val($(this).attr('data-token'));
            if (html_id == "modal-table-poliza") {
                buscaPoliza(value);
            }
        };
    }
    $("a[id^='" + name + "']").click(onElementSelected);
    $("#" + html_id + "-selection").DataTable();
    if(html_id != 'modal-table-baremo-prop') {
        $('#' + html_id).modal();
    }
}

// funcion para generar el modal para listar todas las notificaciones
function fillListAllNotificationsModal(title, html_id, skel, data) {
    var model = skel[0];
    skel = skel.splice(1, skel.length - 1);
    var html = "";
    html += "<div class='modal fade' id='" + html_id + "' tabindex='-1' role='dialog' aria-hidden='true'>";
    html += "<div class='modal-dialog'>";
    html += "<div class='modal-content modal-lg modal-custom-large'>";
    html += "<div class='modal-header modal-header-info'>";
    html += "<button type='button' class='close modal-close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>";
    html += "<h4 class='modal-title' id='myModalLabel'><i class='fa fa-comments-o'></i>&nbsp;&nbsp;&nbsp;" + title + "</h4>";
    html += "<a type='button' style='float:right;margin-top:-24px;padding-right:30px;color:white;text-decoration:none;' href='"+projectDirectory+"/Notificacionmensajes/listar' target='_blank'>Ver todos</a>";
    html += "</div>";
    html += "<div class='modal-body'>";
    //Codigo nuevo para mostrar boton de seleccionar en bandeja de entrada
    html += "<div class='row'>";
    html += "<div class='col-md-3 pull-right' style='margin-right:-5%'> ";
    html += "<div class='btn-group btn-group-vertical' role='group' id='selectionButtonsGroupNotifi' style='width: 210px;'>";
    html += "<div class='btn btn-primary btn-sm' id='processSelectionNotfi' data-selection='disabled' onclick='iniciarSeleccionNotifi()'>Iniciar modo de selección múltiple";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "<div class='row'>";
    html += "<div class='col-md-3 pull-right' style='margin-right:-5%'>";
    html += "<div class='checkbox allCheckboxDivNotifi'>";
    html += "<label><input type='checkbox' disabled id='AllCheckboxNotifi' style='zoom: 1.5;' onclick='marcarTodosNotifi()'> <strong>Marcar / Desmarcar todas</strong> </label>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    //end codigo nuevo
    html += "<table id='datableNotificaciones' class='table table-striped table-hover' cellspacing='0' role='grid'>";
    html += "<thead>";
    html += "<tr role='row'>";
    skel.forEach(function (s) {
        if (s[0] != "ORIGEN_SISTEMA")
            html += "<th class='sorting' tabindex='0' rowspan='1' colspan='1'>" + s[1] + "</th>";
    });
    html += "<th class='sorting' tabindex='0' rowspan='1' colspan='1'>Acción</th>";
    html += "</tr>";
    html += "</thead>";
    html += "<tbody>";
    var i = 1;
    var mensaje;
    var direccion;
    data.forEach(function (d) {
        html += "<tr id='not-all-" + d[model]['PK_NOTIFICACIONMENSAJE'] + "' role='row'>";
        skel.forEach(function (s) {
            if (s[0] != "ORIGEN_SISTEMA") {
                if (s[0] == "T_CONTENIDO") {
                    mensaje = d[model][s[0]];
                    mensaje = mensaje.replace(/\n/g, '<br>');
                    html += "<td class='col-md-6' style='text-align: justify'> <strong>Asunto:</strong> " + d[model]['D_ASUNTO'] + '<br><br>' + mensaje + '</td>';
                } else if (s[0] == "DIRECCION_ASEGURADO") {
                    if (d[model]['D_NOMVIA'].trim() != '-') {
                        direccion = d[model]['D_NOMVIA'] + ', CP: ' + d[model]['S_CODPOST'] + ', ' + d[model]['S_MUNICIPIO'];
                        html += "<td class='col-md-2'> " + direccion + "</td>";
                    } else {
                        html += "<td class='col-md-2'> " + d[model]['D_NOMVIA'] + "</td>";
                    }
                } else if (s[0] == "FK_EXPEDIENTE") {
                    if (d[model]['S_NUMEXPPROPIO'].trim() != '-') {
                        html += "<td class='col-md-1'><a title= 'Ir al Expediente' class ='btn btn-xs btn-primary' href='" + projectDirectory + "/siniestros/manageRecord/" + d[model][s[0]] + "' class='btn btn-xs btn-primary btn-tooltip' data-toogle='tooltip' data-placement='top'  data-original-title='Ir al expediente'>" + d[model]['S_EXPEDIENTECIA'] + "</a> <br>" + d[model]['S_NUMEXPPROPIO'] + " </td>";
                    } else {
                        html += "<td class='col-md-1'>" + d[model]['S_NUMEXPPROPIO'] + "</td>";
                    }
                } else if (s[0] == "FH_ENVIADO") {
                    html += "<td class='col-md-2'>" + d[model][s[0]] + "</td>";
                } else {
                    html += "<td class='col-md-2'>" + d[model][s[0]] + "</td>";
                }
            }
        });
        html += "<td>";
        d[model]['T_CONTENIDO'] = escape(d[model]['T_CONTENIDO']);
        // si el origen es el sistema no mostramos el boton para responder
        if (!d[model]['ORIGEN_SISTEMA']) {
            html += "<button type='button' onclick='replyMessageNotification(&quot;" + d[model]['PK_NOTIFICACIONMENSAJE'] + "&quot;,&quot;" + d[model]['FK_USUARIO_ORIGEN'] + "&quot;,&quot;" + d[model]['D_ASUNTO'] + "&quot;,&quot;" + d[model]['T_CONTENIDO'] + "&quot;);' class='btn btn-xs btn-success btn-tooltip btn-table' data-toogle='tooltip' data-placement='top' title='' data-original-title='Responder'><i class='fa fa-reply'></i></button> &nbsp;";
        }
        html += "<button type='button' onclick='confirmMessageNotification(&quot;" + d[model]['PK_NOTIFICACIONMENSAJE'] + "&quot;);' class='btn btn-xs btn-primary btn-tooltip btn-table' data-toogle='tooltip' data-placement='top' title='' data-original-title='Confirmar'><i class='fa fa-check'></i></button>";

        html += "<div class='checkbox checkbox_selector_notifi' style='margin-left: 4px;'>";
        html += "<label class='btn-tooltip data-toggle='tooltip title='Seleccionar'>";
        html += "<input type='checkbox' class='checkboxNotifi' data-pk='" + d[model]['PK_NOTIFICACIONMENSAJE'] + "' style='zoom: 2.1' onclick='seleccionarNotifi(this)'>";
        html += "</label>";
        html += "</div>";

        html += "</td>";
        html += "</tr>";
        i++;
    });
    html += "<tfoot>";
    html += "<tr role='row'>";
    skel.forEach(function (s) {
        html += "<th rowspan='1' colspan='1'>" + s[1] + "</th>";
    });
    html += "<th rowspan='1' colspan='1'>Acción</th>";
    html += "</tr>";
    html += "</tfoot>";
    html += "</tbody>";
    html += "</table>";
    html += "</div>";
    html += "<div class='modal-footer'>";
    html += "<div class='btn btn-default data-btn-cerrar' data-dismiss='modal'>Cerrar</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";

    //Modal de confirmacion para cancelar la seleccion actual
    html += "<div class='modal fade' id='modalConfirmarAccionNotifi' tabindex='-1' role='dialog' aria-hidden='true'>";
    html += "<div class='modal-dialog'>";
    html += "<div class='modal-content'>";
    html += "<div class='modal-header modal-header-danger'>";
    html += "<button type='button' class='close modal-close' data-dismiss='modal'>";
    html += "<span aria-hidden='true'>&times;</span>";
    html += "<span class='sr-only icon-white'>Close</span>";
    html += "</button>";
    html += "<h5 class='modal-title'>Cancelar selección</h5>";
    html += "</div>";
    html += "<div class='modal-body'>";
    html += "<div class='row no-margin-bottom'>";
    html += "<div class='col-md-12'>";
    html += "<strong><p id='textoAccion'></p></strong>";
    html += "<strong><p>¿Desea continuar con el proceso?</p></strong>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "<div class='modal-footer'>";
    html += "<div class='col-md-6 text-left'>";
    html += "<button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Cerrar</button>";
    html += "</div>";
    html += "<div class='col-md-6 text-right'>";
    html += "<button type='button' id='confirmarAccionNitifi' class='btn btn-danger btn-sm' data-dismiss='modal'>Confirmar</button>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    //Fin modal de confirmacion

    $("#" + html_id).remove();
    $('.js-modal').html(html);

    /* $("#" + html_id + "-table").DataTable({
     "order": [[0, "desc"]]
     });*/

    $("#datableNotificaciones").ready(function () {
        tabla_notificaciones = $("#datableNotificaciones").DataTable({
            "order": []

        });
        cantidad_filas = tabla_notificaciones.data().length;
        $("#datableNotificaciones").on('page.dt', function () {
            if (checks_visibles_notifi) {
                $('.checkbox_selector_notifi, .AllCheckboxNotifi').fadeIn();
            } else {
                $('.checkboxNotifi').prop('checked', false);
                $('.checkbox_selector_notifi').fadeOut();
            }
        });

    });
    $('.checkbox_selector_notifi, .allCheckboxDivNotifi').fadeOut();


    $('#' + html_id).modal();
    $(".btn-tooltip").tooltip({container: 'body'});
}

// Funcion para generar el modal para listar todas los mensajes de la bandeja de entrada.
function fillListAllMessagesModal(title, html_id, skel, data) {
    var model = skel[0];
    skel = skel.splice(1, skel.length - 1);
    var html = "";
    html += "<div class='modal fade' id='" + html_id + "' tabindex='-1' role='dialog' aria-hidden='true'>";
    html += "<div class='modal-dialog'>";
    html += "<div class='modal-content modal-lg modal-custom-large' id='modal_mensajes'>";
    html += "<div class='modal-header modal-header-warning'>";
    html += "<div class='pull-right'>";
    html += "<button type='button' class='close modal-close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>";
    html += "<a type='button' class='text-white btn btn-warning btn-xs' style='text-decoration: none;' href='" + projectDirectory + "/bandejaentradas/mensajes/' target='_blank'>Ver todos</a>&nbsp;&nbsp;&nbsp;&nbsp;";
    html += "</div>";
    html += "<h4 class='modal-title' id='myModalLabel'><i class='fa fa-inbox'></i>&nbsp;&nbsp;&nbsp;" + title + "</h4>";
    html += "</div>";
    html += "<div class='modal-body'>";

    //Codigo nuevo para mostrar boton de seleccionar en bandeja de entrada
    html += "<div class='row'>";
    html += "<div class='col-md-3 pull-right' style='margin-right:-5%'> ";
    html += "<div class='btn-group btn-group-vertical' role='group' id='selectionButtonsGroup' style='width: 210px;'>";
    html += "<div class='btn btn-primary btn-sm' id='processSelection' data-selection='disabled' onclick='iniciarSeleccion()'>Iniciar modo de selección múltiple";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "<div class='row'>";
    html += "<div class='col-md-3 pull-right' style='margin-right:-5%'>";
    html += "<div class='checkbox allCheckboxDiv'>";
    html += "<label><input type='checkbox' disabled id='AllCheckbox' style='zoom: 1.5;' onclick='marcarTodos()'> <strong>Marcar / Desmarcar todas</strong> </label>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    //end codigo nuevo

    html += "<table id='datableBandejaEntrada' class='table table-striped table-hover' cellspacing='0' role='grid'>";
    html += "<thead>";
    html += "<tr role='row'>";
    skel.forEach(function (s) {
        html += "<th class='sorting' tabindex='0' rowspan='1' colspan='1'>" + s[1] + "</th>";
    });
    html += "<th class='sorting' tabindex='0' rowspan='1' colspan='1'>Acciones</th>";
    html += "</tr>";
    html += "</thead>";
    html += "<tbody>";
    var i = 1;
    data.forEach(function (d) {
        html += "<tr id='message-all-" + d[model]['PK_BANDEJAENTRADA'] + "' role='row'>";
        skel.forEach(function (s) {
            if (s[0] == "D_DESCRIPCION") {
                html += "<td class='col-md-4' style='max-width:300px;overflow-x:hidden;'>" + $.nl2br(d[model][s[0]]) + "</td>";
            } else if (s[0] == "EXPEDIENTE") {
                if (d[model]['S_NUMEXPPROPIO'] != null) {
                    html += '<td class="col-md-1"><a  class="btn btn-xs btn-primary" title="Ir al Expediente" href="' + projectDirectory + '/siniestros/manageRecord/' + d[model]['FK_EXPEDIENTE'] + '">' + d[model]['S_NUMEXPPROPIO'] + '</a> <br> ' + d[model]['S_EXPEDIENTECIA'] + '</td>';
                } else {
                    html += '<td class="col-md-1">No disponible</td>';
                }
            } else if (s[0] == 'DIRECCION_ASEGURADO') {
                html += '<td class="col-md-2" style="text-align: justify"> ' + d[model]['DIR_ASEGURADO'] + '</td>';
            } else if (s[0] == "FH_FECHA") {
                html += "<td class='col-md-1'>" + d[model][s[0]] + "</td>";
            } else if (s[0] == "ADJUNTO") {
                html += "<td class='col-md-1'>";
                //Creamos un contador para los tipos de ficheros adjuntos.
                var imagen = 1;
                var fichero = 1;
                var audio = 1;
                var comprimido = 1;
                var k = 0;
                for (var j = 0; j < d['Fichero'].length; j++) {
                    switch (file_type[d['Fichero'][j]['V_EXTENSION'].toLowerCase()]) {
                        case 'Fichero':
                            k = fichero;
                            fichero = k + 1;
                            break;
                        case 'Imagen':
                            k = imagen;
                            imagen = k + 1;
                            break;
                        case 'Audio':
                            k = audio;
                            audio = k + 1;
                            break;
                        case 'Archivo comprimido':
                            k = comprimido;
                            comprimido = k + 1;
                            break;
                    }
                    // Dependiendo del entorno accedemos a una ruta.
                    if (enviroment == 'LOCAL') {
                        html += '<a style="margin-right: 10px;" target="_blank" href="' + projectDirectory + '/uploads/propietarios/' + d['Directorio']['RUTA'] + '/' + d['Fichero'][j]['D_RUTA'] + '.' + d['Fichero'][j]['V_EXTENSION'] + '" class="btn btn-default btn-xs btn-tooltip btn-table" data-toogle="tooltip" data-placement="top" title="' + file_type[d['Fichero'][j]['V_EXTENSION'].toLowerCase()] + ' ' + k + '" data-original-title=""><i class="fa fa-margin-right ' + file_icons[d['Fichero'][j]['V_EXTENSION'].toLowerCase()] + '"></i></a>'
                    } else {
                        html += '<a style="margin-right: 10px;" target="_blank" href="' + urlJS + 'img/propietarios/' + d['Directorio']['RUTA'] + '/' + d['Fichero'][j]['D_RUTA'] + '.' + d['Fichero'][j]['V_EXTENSION'] + '" class="btn btn-default btn-xs  btn-tooltip btn-table" data-toogle="tooltip" data-placement="top" title="' + file_type[d['Fichero'][j]['V_EXTENSION'].toLowerCase()] + ' ' + k + '" data-original-title=""><i class="fa fa-margin-right ' + file_icons[d['Fichero'][j]['V_EXTENSION'].toLowerCase()] + '"></i></a>'
                    }
                }
                html += "</td>";
            } else {
                if (d[model][s[0]] != null) {
                    html += "<td class='col-md-2' style='text-align: justify'>" + d[model][s[0]] + "</td>";
                } else {
                    html += '<td class="col-md-2" style="text-align: justify">Sin identificar</td>';
                }
            }
        });

        html += "<td>";
        if (d[model]['FK_TRAMITADOR'] == null || d[model]['FK_EXPEDIENTE'] == null) {
            html += '<button type="button" onclick="listExpedientsTramit(&quot;' + d[model]["PK_BANDEJAENTRADA"] + '&quot;);" class="btn btn-xs btn-primary btn-tooltip btn-table" data-dismiss="modal" data-toogle="tooltip" data-placement="top" title="Asignar expediente/tramitador" data-original-title=""><i class="fa fa-user"></i></button>';
            html += '<button type="button" onclick="fillModalForAjax(&quot;' + d[model]['PK_BANDEJAENTRADA'] + '&quot;, &quot;Descartar mensaje&quot;, 3, &quot;¿Está seguro que quiere descartar el mensaje?&quot;, &quot;deleteMessage&quot;, &quot;Descartar&quot;, true);" class="btn btn-xs btn-danger btn-tooltip btn-table fa-margin-left" data-dismiss="modal" data-toogle="tooltip" data-placement="top" title="Descartar" data-original-title="Descartar"><i class="fa fa-remove"></i></button>';
        } else {
            if (d[model]['V_ESTADO'] != 3) {
                html += '<button type="button" onclick="fillModalForAjax(&quot;' + d[model]['PK_BANDEJAENTRADA'] + '&quot;, &quot;Marcar como gestionado&quot;, 0, &quot;¿Está seguro que quiere marcar como gestionado este mensaje?&quot;, &quot;confirmMessageGestion&quot;, &quot;Gestionado&quot;, true);" class="btn btn-xs btn-primary btn-tooltip btn-table" data-dismiss="modal" data-toogle="tooltip" data-placement="top" title="Gestionado" data-original-title=""><i class="fa fa-check"></i></button>';
            }
        }

        var dato_generico = JSON.parse(d[model]['D_DATOGENERICO']);
        if(dato_generico != null && typeof dato_generico["idsiniestroantiguo"] != 'undefined'){
            html += ' <button type="button" onclick="verSiniestroSistemaAnterior(\'' + dato_generico["idsiniestroantiguo"] + '\')" class="btn btn-xs btn-success btn-tooltip btn-table" data-dismiss="modal" data-toogle="tooltip" data-placement="top" title="Siniestro de sistema anterior"><i class="fa fa-fast-backward"></i></button>';
        }

        html += "<div class='checkbox checkbox_selector' style='margin-left: 4px;'>";
        html += "<label class='btn-tooltip data-toggle='tooltip title='Seleccionar'>";
        html += "<input type='checkbox' class='checkboxMesg' data-pk='" + d[model]['PK_BANDEJAENTRADA'] + "' style='zoom: 2.1' onclick='seleccionar(this)'>";
        html += "</label>";
        html += "</div>";

        html += "</td>";
        html += "</tr>";
        i++;
    });
    html += "<tfoot>";
    html += "<tr role='row'>";
    skel.forEach(function (s) {
        html += "<th rowspan='1' colspan='1'>" + s[1] + "</th>";
    });
    html += "<th rowspan='1' colspan='1'>Acciones</th>";
    html += "</tr>";
    html += "</tfoot>";
    html += "</tbody>";
    html += "</table>";
    html += "</div>";
    html += "<div class='modal-footer'>";
    html += "<div class='btn btn-default data-btn-cerrar' data-dismiss='modal'>Cerrar</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";

    //Modal de confirmacion para cancelar la seleccion actual
    html += "<div class='modal fade' id='modalConfirmarAccion' tabindex='-1' role='dialog' aria-hidden='true'>";
    html += "<div class='modal-dialog'>";
    html += "<div class='modal-content'>";
    html += "<div class='modal-header modal-header-danger'>";
    html += "<button type='button' class='close modal-close' data-dismiss='modal'>";
    html += "<span aria-hidden='true'>&times;</span>";
    html += "<span class='sr-only icon-white'>Close</span>";
    html += "</button>";
    html += "<h5 class='modal-title'>Cancelar selección</h5>";
    html += "</div>";
    html += "<div class='modal-body'>";
    html += "<div class='row no-margin-bottom'>";
    html += "<div class='col-md-12'>";
    html += "<strong><p id='textoAccion'></p></strong>";
    html += "<strong><p>¿Desea continuar con el proceso?</p></strong>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "<div class='modal-footer'>";
    html += "<div class='col-md-6 text-left'>";
    html += "<button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Cerrar</button>";
    html += "</div>";
    html += "<div class='col-md-6 text-right'>";
    html += "<button type='button' id='confirmarAccion' class='btn btn-danger btn-sm' data-dismiss='modal'>Confirmar</button>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    //Fin modal de confirmacion

    $("#" + html_id).remove();

    $('.js-modal').html(html);

    $('#datableBandejaEntrada').ready(function () {

        tabla_bandeja_entrada = $("#datableBandejaEntrada").DataTable({
            "order": []

        });
        cantidad_filas = tabla_bandeja_entrada.data().length;
        $('#datableBandejaEntrada').on('page.dt', function () {
            if (checks_visibles) {
                $('.checkbox_selector, .allCheckboxDiv').fadeIn();
            } else {
                $('.checkboxMesg').prop('checked', false);
                $('.checkbox_selector').fadeOut();
            }
        });

    });
    $('.checkbox_selector, .allCheckboxDiv').fadeOut();
    $('#' + html_id).modal();
    $(".btn-tooltip").tooltip();
}

function showModaExpedientsFilter(id, skel, data) {
    var html = "<div class='modal fade' id='modal-table-allExpedientsTramitFilter' tabindex='-1' role='dialog' aria-hidden='true'>";
    html += "<div class='modal-dialog'>";
    html += "<div class='modal-content modal-lg modal-custom-large'>";
    html += "<div class='modal-header modal-header-info'>";
    html += "<button type='button' class='close modal-close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>";
    html += "<h4 class='modal-title' id='myModalLabel'><i class='fa fa-comments-o'></i>&nbsp;&nbsp;&nbsp;Asignar expediente a mensaje de bandeja de entrada</h4>";
    html += "</div>";
    html += "<div class='modal-body col-md-12'>";
    html += "<div class='col-md-12'><h4>" + data.bandejaEntrada.Bandejaentrada.D_ASUNTO + "</h4>";
    var str = data.bandejaEntrada.Bandejaentrada.D_DESCRIPCION.replace(/(?:\r\n|\r|\n)/g, '<br />');
    html += str + "</div>";
    html += "<div class='col-md-12'><br/></div>";
    html += "<div class='col-md-3'><div class='input text'><label>F.Entrada desde</label><input id='inputFechaEntradaDesde' class='form-control input-sm datedayonly datetimepickerstart' type='text'></div></div>";
    html += "<div class='col-md-3'><div class='input text'><label>F.Entrada hasta</label><input id='inputFechaEntradaHasta' class='form-control input-sm datedayonly datetimepickerstart' type='text'></div></div>";
    html += "<div class='col-md-3'><div class='input text'><label>NºExpediente</label><input id='inputExpediente' class='form-control input-sm' type='text'></div></div>";
    html += "<div class='col-md-3'><div class='input text'><label>NºCompañía</label><input id='inputParte' class='form-control input-sm' type='text'></div></div>";
    html += "<div class='col-md-3'><label>Estado</label><select class='form-control input-sm' id='inputEstado'>";
    html += "<option value=''>Seleccionar estado...</option>";
    var estados = data['estados'];
    for (var i in estados) {
        html += "<option value='" + i + "'>" + estados[i] + "</option>";
    }
    html += "</select></div>";
    html += "<div class='col-md-3'><label>Tramitador</label><select class='form-control input-sm' id='inputTramitador'>";
    html += "<option value=''>Seleccionar tramitador...</option>";
    var tramitadores = data['tramitadores'];
    for (var i in tramitadores) {
        html += "<option value='" + i + "'>" + tramitadores[i] + "</option>";
    }
    html += "</select></div>";
    html += "<div class='col-md-2'><br/><div class='btn btn-success data-btn-cerrar' onclick='filtraExpedientes(\"" + id + "\");'>Filtrar expedientes</div></div>";
    html += "<div class='col-md-12'><br/><br/></div>";
    html += "<div class='col-md-12'>";
    html += "<table id='modal-table-allExpedientsTramitFilter-table' class='table table-striped table-hover' cellspacing='0' role='grid'>";
    html += "<thead><tr role='row'>";
    skel = skel.splice(1, skel.length - 1);
    skel.forEach(function (s) {
        html += "<th class='sorting'>" + s[1] + "</th>";
    });
    html += "<th class='sorting' tabindex='0' rowspan='1' colspan='1'>Acción</th>";
    html += "</tr></thead>";
    html += "<tbody></tbody>";
    html += "</table>";
    html += "</div>";
    html += "</div>";
    html += "<div class='modal-footer'>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";

    //Modal para mostrar el listado de compañías o operarios externos cuando vamos a asignar una bandeja de entrada
    html += "<div class='modal fade' id='modalAsingarBandejaEntrada' tabindex='-1' role='dialog' aria-hidden='true' style='display: none;'>";
    html += "<div class='modal-dialog modal-lg'>";
    html += "<div class='modal-content'>";
    html += "<div class='modal-header modal-header-info'>";
    html += "<button type='button' class='close modal-close' data-dismiss='modal'><span  aria-hidden='true'>×</span><span class='sr-only'>Close</span></button>";
    html += "<h4 class='modal-title'>Origen del Mensaje</h4>";
    html += "</div>";
    html += "<div class='modal-body'>";
    html += "<div class='row'>";
    html += "<div class='col-md-4 form-group'>";
    html += "<label><input type='checkbox' id='checkCompania' style='zoom: 1.5;' onclick='check_origenCompania(this)'> <strong>Compañia</strong> </label>";
    html += "</div>"
    html += "<div class='col-md-4 form-group'>";
    html += "<label><input type='checkbox' id='checkOperExt' style='zoom: 1.5;' onclick='check_origenOperExt(this)'> <strong>Operario Externo</strong> </label>";
    html += "</div>"
    html += " <input type='hidden'  id='expediente_origen' value=''>";
    html += " <input type='hidden'  id='bandeja_entrada_origen' value=''>";
    html += " <input type='hidden'  id='operario_ext_origen' value=''>";
    html += " <input type='hidden'  id='compania_origen_entrada_origen' value=''>";
    html += " <input type='hidden'  id='nombre_compania_entrada_origen' value=''>";
    html += "</div>"
    html += "<div class='row' id='div-table-lista-companias'>";
    html += "<div class='col-md-12'>";
    html += "<table id='modal-table-list-origen-mensaje' class='table table-striped table-hover'></table>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    //Fin de modal
    //Modal para confirmar la asignacion de la bandeja de entrada al expediente
    html += "<div class='modal fade' id='modalConfirmarAsignacion' tabindex='-1' role='dialog' aria-hidden='true'>";
    html += "<div class='modal-dialog'>";
    html += "<div class='modal-content'>";
    html += "<div class='modal-header modal-header-success'>";
    html += "<button type='button' class='close modal-close' data-dismiss='modal'>";
    html += "<span aria-hidden='true'>&times;</span>";
    html += "<span class='sr-only icon-white'>Close</span>";
    html += "</button>";
    html += "<h5 class='modal-title'>Asignar Expediente-Tramitador</h5>";
    html += "</div>";
    html += "<div class='modal-body'>";
    html += "<div class='row no-margin-bottom'>";
    html += "<div class='col-md-12'>";
    html += "<strong><p id='textoAsignación'></p></strong>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "<div class='modal-footer'>";
    html += "<div class='col-md-6 text-left'>";
    html += "<button type='button' class='btn btn-default btn-sm' id = 'cancelarAsiganacion' data-dismiss='modal' onclick='cancelarAsignacion()'>Cerrar</button>";
    html += "</div>";
    html += "<div class='col-md-6 text-right'>";
    html += "<button type='button' id='confirmarAsiganacion' class='btn btn-success btn-sm' data-dismiss='modal'>Confirmar</button>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";

    $("#modal-table-allExpedientsTramitFilter").remove();
    $('.js-modal').html(html);
    $("#modal-table-allExpedientsTramitFilter-table").DataTable();
    $('#modal-table-allExpedientsTramitFilter').modal();
    $(".btn-tooltip").tooltip({container: 'body'});
    $('.datedayonly').datetimepicker({
        format: 'DD-MM-YYYY',
        language: 'es',
        pickTime: false
    });
}

// Funcion para generar el modal para listar todas los mensajes de la bandeja de salida.
function fillListAllSendedMessagesModal(title, html_id, skel, data) {
    var model = skel[0];
    skel = skel.splice(1, skel.length - 1);
    var html = "";
    html += "<div class='modal fade' id='" + html_id + "' tabindex='-1' role='dialog' aria-hidden='true'>";
    html += "<div class='modal-dialog'>";
    html += "<div class='modal-content modal-lg modal-custom-large'>";
    html += "<div class='modal-header modal-header-warning'>";
    html += "<button type='button' class='close modal-close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>";
    html += "<h4 class='modal-title' id='myModalLabel2'><i class='fa fa-inbox'></i>&nbsp;&nbsp;&nbsp;" + title + "</h4>";
    html += "</div>";
    html += "<div class='modal-body'>";
    html += "<table id='" + html_id + "-table' class='table table-striped table-hover' cellspacing='0' role='grid'>";
    html += "<thead>";
    html += "<tr role='row'>";
    skel.forEach(function (s) {
        html += "<th class='sorting' tabindex='0' rowspan='1' colspan='1'>" + s[1] + "</th>";
    });
    // html += "<th class='sorting' tabindex='0' rowspan='1' colspan='1'>Acción</th>";
    html += "</tr>";
    html += "</thead>";
    html += "<tbody>";
    var i = 1;
    data.forEach(function (d) {
        html += "<tr id='message-all-" + d[model]['PK_BANDEJASALIDA'] + "' role='row'>";
        skel.forEach(function (s) {
            if (s[0] == "D_DESCRIPCION")
                html += "<td class='col-md-5'>" + d[model][s[0]] + "</td>";
            else if (s[0] == "EXPEDIENTE")
                html += '<td class="col-md-1">' + d[model][s[0]] + ' <a type="button" style="text-decoration: none; float: right;" class="btn btn-xs btn-primary btn-tooltip" href="' + projectDirectory + '/siniestros/manageRecord/' + d[model]['FK_EXPEDIENTE'] + '" data-toogle="tooltip" data-placement="top" title="" data-original-title="Ir al expediente"><i class="fa fa-hand-o-right"></i></a></td>';
            else if (s[0] == "FH_FECHA")
                html += "<td class='col-md-2'>" + d[model][s[0]] + "</td>";
            else if (s[0] == "ADJUNTO") {
                html += "<td class='col-md-3'>";
                //Creamos un contador para los tipos de ficheros adjuntos.
                var imagen = 1;
                var fichero = 1;
                var audio = 1;
                var comprimido = 1;
                var k = 0;
                for (var j = 0; j < d['Fichero'].length; j++) {
                    switch (file_type[d['Fichero'][j]['V_EXTENSION'].toLowerCase()]) {
                        case 'Fichero':
                            k = fichero;
                            fichero = k + 1;
                            break;
                        case 'Imagen':
                            k = imagen;
                            imagen = k + 1;
                            break;
                        case 'Audio':
                            k = audio;
                            audio = k + 1;
                            break;
                        case 'Archivo comprimido':
                            k = comprimido;
                            comprimido = k + 1;
                            break;
                    }
                    // Dependiendo del entorno accedemos a una ruta.
                    if (enviroment == 'LOCAL') {
                        html += '<a style="margin-right: 10px;" target="_blank" href="' + projectDirectory + '/uploads/propietarios/' + d['Directorio']['RUTA'] + '/' + d['Fichero'][j]['D_RUTA'] + '.' + d['Fichero'][j]['V_EXTENSION'] + '" class="btn btn-default btn-xs btn-tooltip btn-table" data-toogle="tooltip" data-placement="top" title="' + file_type[d['Fichero'][j]['V_EXTENSION'].toLowerCase()] + ' ' + k + '" data-original-title=""><i class="fa fa-margin-right ' + file_icons[d['Fichero'][j]['V_EXTENSION'].toLowerCase()] + '"></i></a>'
                    } else {
                        html += '<a style="margin-right: 10px;" target="_blank" href="' + urlJS + 'img/propietarios/' + d['Directorio']['RUTA'] + '/' + d['Fichero'][j]['D_RUTA'] + '.' + d['Fichero'][j]['V_EXTENSION'] + '" class="btn btn-default btn-xs  btn-tooltip btn-table" data-toogle="tooltip" data-placement="top" title="' + file_type[d['Fichero'][j]['V_EXTENSION'].toLowerCase()] + ' ' + k + '" data-original-title=""><i class="fa fa-margin-right ' + file_icons[d['Fichero'][j]['V_EXTENSION'].toLowerCase()] + '"></i></a>'
                    }

                }
                html += "</td>";
            } else
                html += "<td class='col-md-2'>" + d[model][s[0]] + "</td>";
        });
        html += "</tr>";
        i++;

    });
    html += "</tbody>";
    html += "<tfoot>";
    html += "<tr role='row'>";
    skel.forEach(function (s) {
        html += "<th rowspan='1' colspan='1'>" + s[1] + "</th>";
    });
    //html += "<th rowspan='1' colspan='1'>Acción</th>";
    html += "</tr>";
    html += "</tfoot>";
    html += "</table>";
    html += "</div>";
    html += "<div class='modal-footer'>";
    html += "<div class='btn btn-default data-btn-cerrar' data-dismiss='modal'>Cerrar</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    $("#" + html_id).remove();
    $('.js-modal').html(html);
    $("#" + html_id + "-table").DataTable({
        "order": []
    });
    $(".btn-tooltip").tooltip();
    $('#' + html_id).modal();
}

// funcion para construir un modal basico con un listado
function fillBasicModal(title, html_id, skel, data, options) {

    if (typeof options === 'undefined') {
        options = {size: 'normal'};
    }

    var model = skel[0];
    skel = skel.splice(1, skel.length - 1);
    var html = "";

    html += "<div class='modal fade' id='" + html_id + "' tabindex='-1' role='dialog' aria-hidden='true'>";
    html += "<div class='modal-dialog'>";
    if (options['size'] == 'normal')
        html += "<div class='modal-content modal-lg'>";
    else if (options['size'] == 'extra')
        html += "<div class='modal-content modal-lg modal-custom-large'>";
    else
        html += "<div class='modal-content modal-lg'>";

    html += "<div class='modal-header modal-header-info'>";
    html += "<button type='button' class='close modal-close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>";
    html += "<h4 class='modal-title' id='myModalLabel'>" + title + "</h4>";
    html += "</div>";
    html += "<div class='modal-body'>";
    html += "<table id='" + html_id + "-table' class='table table-striped table-hover' cellspacing='0' role='grid'>";
    html += "<thead>";
    html += "<tr role='row'>";
    skel.forEach(function (s) {
        html += "<th class='sorting' tabindex='0' rowspan='1' colspan='1'>" + s[1] + "</th>";
    });
    html += "<th class='sorting' tabindex='0' rowspan='1' colspan='1'>Acción</th>";
    html += "</tr>";
    html += "</thead>";
    html += "<tbody>";
    var i = 1;
    data.forEach(function (d) {
        html += "<tr role='row'>";
        skel.forEach(function (s) {
            html += "<td>" + d[model][s[0]] + "</td>";
        });
        html += "<td>";
        html += "<button type='button' data-toggle='modal'  data-target='#modalReplyNotificacion' class='btn btn-xs btn-success btn-tooltip' data-toogle='tooltip' data-placement='top' title='' data-original-title='Responder'><i class='fa fa-reply'></i></button>";
        html += "<button type='button' data-toggle='modal' class='btn btn-xs btn-danger btn-tooltip' data-toogle='tooltip' data-placement='top' title='' data-original-title='Ver'><i class='fa fa-trash'></i></button>";
        html += "</td>";
        html += "</tr>";
        i++;
    });
    html += "<tfoot>";
    html += "<tr role='row'>";
    skel.forEach(function (s) {
        html += "<th rowspan='1' colspan='1'>" + s[1] + "</th>";
    });
    html += "<th rowspan='1' colspan='1'>Acción</th>";
    html += "</tr>";
    html += "</tfoot>";
    html += "</tbody>";
    html += "</table>";
    html += "</div>";
    html += "<div class='modal-footer'>";
    html += "<div class='btn btn-default data-btn-cerrar' data-dismiss='modal'>Cerrar</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    $("#" + html_id).remove();
    $('.js-modal').html(html);
    $("#" + html_id + "-table").DataTable();
    $('#' + html_id).modal();
}


// Se encarga de añadir campos de archivo al formulario de nueva incidencia cuando se añade un archivo
// Args: id del tag del que cuelgan los campos de archivo
//       input_id_format_string: formato de propiedad id, por ejemplo 'FormIncidencia{0}BARCHIVO'
//       input_name_format_string: formato de propieda name, por ejemplo, 'data[FormIncidencia][{0}][BARCHIVO]'.
function formIncidencia_ManejaMultiArchivos(id_parent_tag, input_id_format_string, input_name_format_string) {
    var parent_tag = $('#' + id_parent_tag);
    var input_tags = [];

    String.prototype.format = function () {
        var args = arguments;
        return this.replace(/\{\{|\}\}|\{(\d+)\}/g, function (m, n) {
            if (m == "{{") {
                return "{";
            }
            if (m == "}}") {
                return "}";
            }
            return args[n];
        });
    };

    $('document').ready(function () {
        input_tags[0] = getInputTag(0);
        input_tags[0].change(onChangeEventHandler);
    });

    function getInputTag(index) {
        return $('#' + input_id_format_string.format(index));
    };

    function onChangeEventHandler() {
        // Contamos número de tags de archivo vacíos
        var numEmpty = 0;
        var emptyTags = [];
        for (i = 0; i < input_tags.length; i++) {
            if (input_tags[i].val() == '') {
                numEmpty++;
                emptyTags.push(i);
            }
        }

        // Nos aseguramos de que hay al menos uno vacío
        if (numEmpty < 1) {
            var html = '<div class="input file">'
                + '<input type="file" name="' + input_name_format_string.format(input_tags.length) + '" id="' + input_id_format_string.format(input_tags.length) + '" />'
                + '</div>';
            parent_tag.append(html);
            $('document').ready(function () {
                var index = input_tags.length;
                var tag = getInputTag(index);
                input_tags[index] = tag;
                input_tags[index].change(onChangeEventHandler);
            });
        }
    };
}