<?php
App::uses('AppHelper', 'View/Helper');
App::uses('CakeSession', 'Model/Datasource');

class FilterHelper extends AppHelper
{
    public $helpers = array('Html', 'Form', 'Session');
    public $uses = array('Parametro');

    public function showFilters($t, $id_user, $tipo_taller, $etiquetas_propietario)
    { ?>
        <script type="text/javascript">
            $('.col-md-2 .sidebar-left').ready(function () {
                allFilters();
                $("#filter").change(function() {
                    var option = $(this).val();
                    $('#usercookie').val(option);
                    $('#SiniestroSetCookieForm').submit();
                });
            });
            $(document).ready(function () {
                var option = $("#filter").val();
                if (!isNaN(option)) {
                    var operarioRojo = $('#operarioRetrasado-red');
                    if (typeof operarioRojo != 'undefined') {
                        enlaceActual = operarioRojo.attr('href');
                        operarioRojo.attr('href', enlaceActual + '&tramitador=' + option)
                    }
                    var trabajoRojo = $('#trabajoRetrasado-red');
                    if (typeof trabajoRojo != 'undefined') {
                        enlaceActual = trabajoRojo.attr('href');
                        trabajoRojo.attr('href', enlaceActual + '&tramitador=' + option)
                    }
                    var porConfirmarRojo = $('#porConfirmar-red');
                    if (typeof porConfirmarRojo != 'undefined') {
                        enlaceActual = porConfirmarRojo.attr('href');
                        porConfirmarRojo.attr('href', enlaceActual + '&tramitador=' + option)
                    }
                    var citasProximasRojo = $('#proximas-red');
                    if (typeof citasProximasRojo != 'undefined') {
                        enlaceActual = citasProximasRojo.attr('href');
                        citasProximasRojo.attr('href', enlaceActual + '&tramitador=' + option)
                    }
                }
            });
        </script>
        <?php echo $this->allFilters($t, $id_user, 'agrupaFiltros', $tipo_taller, $etiquetas_propietario);
    }


    function allFilters($t, $id_user, $url, $tipo_taller, $etiquetas_propietario){ ?>
        <script type="text/javascript">
            var activeTabFilter = (function () {
                var here = window.location.href;
                if(here.indexOf('listadoscomunes')>-1 || here.indexOf('listadosreparadoras')>-1){
                    return function() {
                        return false;
                    }
                }
                var stateKey, eventKey, keys = {
                    hidden: "visibilitychange",
                    webkitHidden: "webkitvisibilitychange",
                    mozHidden: "mozvisibilitychange",
                    msHidden: "msvisibilitychange"
                };
                for (stateKey in keys) {
                    if (stateKey in document) {
                        eventKey = keys[stateKey];
                        break;
                    }
                }
                return function (c) {
                    if (c) document.addEventListener(eventKey, c);
                    return !document[stateKey];
                }
            })();

            function allFilters() {
                if (activeTabFilter()) {
                    $.ajax({
                        type: "POST",
                        url: projectDirectory + '/filter/' + '<?php echo $url;?>',
                        async: true
                    }).success(function (data) {
                        data = JSON.parse(data);
                        console.log('agrupaFiltros ('+data.user+'): '+data.time+' mill. ('+data.from+')');
                        $.each(data.data, function (filtro) {
                            if (data.data[filtro].DATOS.azul !== '0' && typeof(data.data[filtro].DATOS.azul) !== 'undefined') {
                                $('#' + data.data[filtro].ID + '-blue').text(data.data[filtro].DATOS.azul);
                                $('#' + data.data[filtro].ID + '-blue').fadeIn();
                            } else {
                                $('#' + data.data[filtro].ID + '-blue').hide();
                            }
                            if (typeof(data.data[filtro].DATOS.rojo) !== 'undefined' && data.data[filtro].DATOS.rojo !== '0') {
                                $('#' + data.data[filtro].ID + '-red').text(data.data[filtro].DATOS.rojo);
                                $('#' + data.data[filtro].ID + '-red').fadeIn();
                                if (filtro == "POR CONFIRMAR") {
                                    $('#' + data.data[filtro].ID + '-red').css("background-color", "#103184");
                                    $('#list-por-confirmar').css("background-color", "#e40009");
                                    $('#list-por-confirmar').css("color", "white");
                                }
                            } else {
                                if (filtro == "POR CONFIRMAR") {
                                    $('#list-por-confirmar').css("background-color", "white");
                                    $('#list-por-confirmar').css("color", "#103184");
                                }
                                $('#' + data.data[filtro].ID + '-red').hide();
                            }
                        });
                        setTimeout(function () {
                            allFilters();
                        }, 60000);
                    }).error(function (x, status, error) {
                        if (x.status == 403) {
                            window.location.reload();
                        }
                        // reintentamos la conexion
                        setTimeout(function () {
                            seguimiento(id);
                        }, 60000);
                    });
                } else {
                    setTimeout(function () {
                        allFilters();
                    }, 30000);
                }

                var count = 0;
                if (localStorage.getItem("expedientes_recientes") !== null) {
                    var expedientes_recientes = JSON.parse(localStorage.getItem("expedientes_recientes"));
                    if (typeof expedientes_recientes['<?= $id_user ?>'] !== typeof undefined) {
                        count = Object.keys(expedientes_recientes['<?= $id_user ?>']).length;
                        if (count > 0) {
                            var pk_expedientes_recientes = Object.keys(expedientes_recientes['<?= $id_user ?>']);
                            var pk_expedientes_recientes_url = pk_expedientes_recientes.join(";");
                            url_exp_reciente = '<?php echo Router::url(array('controller' => 'siniestros', 'action' => 'listRecords','recientes_b')); ?>';
                            url_exp_reciente += "/" + pk_expedientes_recientes_url;
                            $('#recientes-blue').removeClass("cursor-not-allowed");
                            $('#recientes-blue').attr("href", url_exp_reciente);
                        }
                    }
                }
                if(count>0){
                    $('#recientes-blue').text(count);
                }else{
                    $('#recientes-blue').text('');
                }
            }
        </script>
        <?php
        echo $this->Form->create('Siniestro', array('action' => 'setCookie'));
        echo $this->Form->hidden('User', array('id' => 'usercookie'));
        echo $this->Form->end();
        echo $this->Form->select('filter', $t, array('class' => 'input-sm text-info form-control margin-bottom', 'value' => CakeSession::read('Userselected'), 'default' => '0', 'empty' => false));
        ?>
        <li class="list-group-item">
            <span class="text-uppercase">
            <small>
                <strong>
                    <?php
                    echo $this->Html->link("Todos", array("controller" => "siniestros", "action" => "listRecordsAll"), array("class" => "text-primary"));
                    ?>
                </strong>
            </small>
            </span>
        </li>
        <ul class="list-group">

            <li class="list-group-item">
                <span class="text-uppercase"><small><strong> ABIERTOS </strong></small></span>

                <?php

                echo $this->Html->link(
                    '',
                    array('controller' => 'siniestros', 'action' => 'listRecords', 'abiertos_b'),
                    array(
                        'class' => 'label label-primary btn-tooltip pull-right text-center',
                        'data-toggle' => 'tooltip',
                        'data-title' => 'Abiertos',
                        'data-placement' => 'right',
                        'id' => 'abiertos-blue'
                    )
                );
                ?>
            </li>

            <li class="list-group-item">
                <span class="text-uppercase"><small><strong> PTE. ABRIR </strong></small></span>
                <?php

                echo $this->Html->link(
                    '',
                    array('controller' => 'siniestros', 'action' => 'listRecords', 'pteAbrir_r'),
                    array(
                        'class' => 'label label-danger btn-tooltip pull-right text-center',
                        'data-toggle' => 'tooltip',
                        'data-title' => 'Pte. Abrir (+30min)',
                        'data-placement' => 'right',
                        'id' => 'pteAbrir-red'
                    )
                );

                echo $this->Html->link(
                    '',
                    array('controller' => 'siniestros', 'action' => 'listRecords', 'pteAbrir_b'),
                    array(
                        'class' => 'label label-primary btn-tooltip pull-right text-center fa-margin-right',
                        'data-toggle' => 'tooltip',
                        'data-title' => 'Pte. Abrir',
                        'data-placement' => 'left',
                        'id' => 'pteAbrir-blue'
                    )
                );
                ?>
            </li>

            <li class="list-group-item">
                <span class="text-uppercase"><small><strong> SEGUIMIENTO </strong></small></span>
                <?php

                echo $this->Html->link(
                    '',
                    array('controller' => 'siniestros', 'action' => 'listRecords', 'seguimiento_r'),
                    array(
                        'class' => 'label label-danger btn-tooltip pull-right text-center',
                        'data-toggle' => 'tooltip',
                        'data-title' => 'Caduca mañana',
                        'data-placement' => 'right',
                        'id' => 'seguimiento-red'
                    )
                );

                echo $this->Html->link(
                    '',
                    array('controller' => 'siniestros', 'action' => 'listRecords', 'seguimiento_b'),
                    array(
                        'class' => 'label label-primary btn-tooltip pull-right text-center fa-margin-right',
                        'data-toggle' => 'tooltip',
                        'data-title' => 'Caduca en 2 o 3 días',
                        'data-placement' => 'left',
                        'id' => 'seguimiento-blue'
                    )
                );

                ?>
            </li>

            <li class="list-group-item">
                <span class="text-uppercase"><small><strong> CITAS PROX. </strong></small></span>
                <?php
                echo $this->Html->link(
                    '',
                    array('controller' => 'trabajos', 'action' => 'calendar?f=citas_proximas'),
                    array(
                        'class' => 'label label-danger btn-tooltip pull-right text-center',
                        'data-toggle' => 'tooltip',
                        'data-title' => 'Citas sin confirmar llegada por el operario',
                        'data-placement' => 'right',
                        'id' => 'proximas-red',
                        'target' => '_blank'
                    )
                );
                ?>
            </li>

            <li class="list-group-item" id="list-por-confirmar">
                <span class="text-uppercase"><small><strong> POR CONFIRMAR </strong></small></span>
                <?php

                echo $this->Html->link(
                    '',
                    array('controller' => 'trabajos', 'action' => 'calendar?f=por_confirmar'),
                    array(
                        'class' => 'label label-danger btn-tooltip pull-right text-center',
                        'data-toggle' => 'tooltip',
                        'data-title' => 'Citas sin confirmar llegada por el operario (Urgentes)',
                        'data-placement' => 'right',
                        'id' => 'porConfirmar-red',
                        'target' => '_blank'
                    )
                );
                ?>
            </li>

            <li class="list-group-item">
                <span class="text-uppercase"><small><strong> OPER. RETRASADO </strong></small></span>
                <?php

                echo $this->Html->link(
                    '',
                    array('controller' => 'trabajos', 'action' => 'calendar?f=operarios_retrasados'),
                    array(
                        'class' => 'label label-danger btn-tooltip pull-right text-center',
                        'data-toggle' => 'tooltip',
                        'data-title' => 'Operario ha confirmado que no llegará a tiempo a una cita',
                        'data-placement' => 'right',
                        'id' => 'operarioRetrasado-red',
                        'target' => '_blank'
                    )
                );
                ?>
            </li>

            <li class="list-group-item">
                <span class="text-uppercase"><small><strong> VIS. RETRASADAS </strong></small></span>
                <?php
                echo $this->Html->link(
                    '',
                    array('controller' => 'trabajos', 'action' => 'calendar?f=visitas_retrasadas_r'),
                    array(
                        'class' => 'label label-danger btn-tooltip pull-right text-center',
                        'data-toggle' => 'tooltip',
                        'data-title' => 'Citas retrasadas fuera del margen',
                        'data-placement' => 'right',
                        'id' => 'trabajoRetrasado-red',
                        'target' => '_blank'
                    )
                );
                echo $this->Html->link(
                    '',
                    array('controller' => 'trabajos', 'action' => 'calendar?f=visitas_retrasadas_b'),
                    array(
                        'class' => 'label label-primary btn-tooltip pull-right text-center fa-margin-right',
                        'data-toggle' => 'tooltip',
                        'data-title' => 'Citas retrasadas dentro del margen',
                        'data-placement' => 'left',
                        'id' => 'trabajoRetrasado-blue',
                        'target' => '_blank'
                    )
                );
                ?>
            </li>

            <li class="list-group-item">
                <span class="text-uppercase"><small><strong> CITADO </strong></small></span>
                <?php
                echo $this->Html->link(
                    '',
                    array('controller' => 'siniestros', 'action' => 'listRecords', 'citado_r'),
                    array(
                        'class' => 'label label-danger btn-tooltip pull-right text-center',
                        'data-toggle' => 'tooltip',
                        'data-title' => 'Citado hoy',
                        'data-placement' => 'right',
                        'id' => 'citado-red'
                    )
                );
                echo $this->Html->link(
                    '',
                    array('controller' => 'siniestros', 'action' => 'listRecords', 'citado_b'),
                    array(
                        'class' => 'label label-primary btn-tooltip pull-right text-center fa-margin-right',
                        'data-toggle' => 'tooltip',
                        'data-title' => 'Citado',
                        'data-placement' => 'left',
                        'id' => 'citado-blue'
                    )
                );
                ?>
            </li>

            <li class="list-group-item">
                <span class="text-uppercase"><small><strong> PTE. PARTE </strong></small></span>
                <?php
                echo $this->Html->link(
                    '',
                    array('controller' => 'siniestros', 'action' => 'listRecordsPteParte', 'rojo'),
                    array(
                        'class' => 'label label-danger btn-tooltip pull-right text-center',
                        'data-toggle' => 'tooltip',
                        'data-title' => 'Pte. Parte (+2h)',
                        'data-placement' => 'right',
                        'id' => 'pteParte-red'
                    )
                );
                echo $this->Html->link(
                    '',
                    array('controller' => 'siniestros', 'action' => 'listRecordsPteParte', 'azul'),
                    array(
                        'class' => 'label label-primary btn-tooltip pull-right text-center fa-margin-right',
                        'data-toggle' => 'tooltip',
                        'data-title' => 'Pte. Parte',
                        'data-placement' => 'left',
                        'id' => 'pteParte-blue'
                    )
                );
                ?>
            </li>

            <li class="list-group-item">
                <span class="text-uppercase">
                    <small><strong> RECLAMACION </strong></small>
                </span>
                <?=$this->Html->link('', array(
                    'controller' => 'siniestros',
                    'action' => 'listRecords', 'reclamacion'
                ), array(
                    'class' => 'label label-danger btn-tooltip pull-right text-center',
                    'data-toggle' => 'tooltip',
                    'data-title' => 'Expedientes con reclamación',
                    'data-placement' => 'right',
                    'id' => 'reclamacion-red'
                ))?>
            </li>

            <?php if($tipo_taller > 0){ ?>
            <li class="list-group-item">
                <span class="text-uppercase">
                    <small><strong> PTE. CONCERTAR </strong></small>
                </span>
                <?=$this->Html->link('', array(
                    'controller' => 'siniestros',
                    'action' => 'listRecordsPteConcertar'
                ), array(
                    'class' => 'label label-danger btn-tooltip pull-right text-center',
                    'data-toggle' => 'tooltip',
                    'data-title' => 'Visitas sin concertar fecha',
                    'data-placement' => 'right',
                    'id' => 'pteConcertar-red'
                ))?>
            </li>
            <li class="list-group-item">
                <span class="text-uppercase">
                    <small><strong> PTE. TALLER </strong></small>
                </span>
                <?= $this->Html->link('',
                    array('controller' => 'siniestros', 'action' => 'listRecordsPteTaller', 'rojo'),
                    array(
                        'class' => 'label label-danger btn-tooltip pull-right text-center',
                        'data-toggle' => 'tooltip',
                        'data-title' => 'Pte. Taller (Retrasados)',
                        'data-placement' => 'right',
                        'id' => 'pteTaller-red'
                    )
                ).$this->Html->link('',
                    array('controller' => 'siniestros', 'action' => 'listRecordsPteTaller', 'azul'),
                    array(
                        'class' => 'label label-primary btn-tooltip pull-right text-center fa-margin-right',
                        'data-toggle' => 'tooltip',
                        'data-title' => 'Pte. Taller (En hora)',
                        'data-placement' => 'left',
                        'id' => 'pteTaller-blue'
                    )
                ) ?>
            </li>
            <?php } ?>

            <li class="list-group-item">
                <span class="text-uppercase"><small><strong> PTE. MATERIALES </strong></small></span>
                <?php
                echo $this->Html->link(
                    '',
                    array('controller' => 'siniestros', 'action' => 'listRecordsPteMateriales', 'rojo'),
                    array(
                        'class' => 'label label-danger btn-tooltip pull-right text-center',
                        'data-toggle' => 'tooltip',
                        'data-title' => 'Pte. Materiales (Pendiente de buscar)',
                        'data-placement' => 'right',
                        'id' => 'pteMateriales-red'
                    )
                );
                echo $this->Html->link(
                    '',
                    array('controller' => 'siniestros', 'action' => 'listRecordsPteMateriales', 'azul'),
                    array(
                        'class' => 'label label-primary btn-tooltip pull-right text-center fa-margin-right',
                        'data-toggle' => 'tooltip',
                        'data-title' => 'Pte. Materiales',
                        'data-placement' => 'left',
                        'id' => 'pteMateriales-blue'
                    )
                );
                ?>
            </li>

            <li class="list-group-item">
                <span class="text-uppercase"><small><strong> RECIENTES </strong></small></span>
                <?php echo $this->Html->link('', '#', array(
                    'class' => 'label label-primary btn-tooltip pull-right text-center cursor-not-allowed',
                    'data-toggle' => 'tooltip',
                    'data-title' => 'Recientes',
                    'data-placement' => 'right',
                    'id' => 'recientes-blue'
                )); ?>
            </li>

            <div class="panel-group" id="more_filters" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingOne">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#more_filters" href="#showFilters" id="mas_filtros"
                               aria-expanded="true"
                               aria-controls="collapseOne">
                                <small><strong class="text-uppercase text-primary">Más filtros</strong></small>
                                <i class="fa fa-chevron-down pull-right"></i>
                            </a>
                        </h4>
                    </div>
                    <div id="showFilters" class="panel-collapse collapse" role="tabpanel">
                        <div class="panel-body no-padding">

                            <li class="list-group-item">
                                <span class="text-uppercase"><small><strong> PTE. CITA </strong></small></span>
                                <?php echo $this->Html->link('',
                                    array('controller' => 'siniestros', 'action' => 'listRecords', 'pteCita_r'),
                                    array(
                                        'class' => 'label label-danger btn-tooltip pull-right text-center',
                                        'data-toggle' => 'tooltip',
                                        'data-title' => 'Pte. Cita (2h)',
                                        'data-placement' => 'right',
                                        'id' => 'pteCita-red'
                                    )
                                );
                                echo $this->Html->link('',
                                    array('controller' => 'siniestros', 'action' => 'listRecords', 'pteCita_b'),
                                    array(
                                        'class' => 'label label-primary btn-tooltip pull-right text-center fa-margin-right',
                                        'data-toggle' => 'tooltip',
                                        'data-title' => 'Pte. Cita',
                                        'data-placement' => 'left',
                                        'id' => 'pteCita-blue'
                                    )
                                );
                                ?>
                            </li>

                            <li class="list-group-item">
                                <span class="text-uppercase"><small><strong>C. POR CONCERTAR</strong>
                                </small></span>
                                <?=$this->Html->link('', array(
                                    'controller' => 'siniestros', 'action' => 'listRecordsPteCitaOperario'
                                ), array(
                                    'class' => 'label label-primary btn-tooltip pull-right text-center fa-margin-right',
                                    'data-toggle' => 'tooltip',
                                    'data-title' => 'Cita por concertar',
                                    'data-placement' => 'left',
                                    'id' => 'pteCitaOperario-blue'
                                ))?>
                            </li>

                            <li class="list-group-item">
                                <span class="text-uppercase"><small><strong> PTE. ASEGUR. </strong></small></span>
                                <?php
                                $diasParametro = CakeSession::read('AppController.diasParametro');
                                echo $this->Html->link('',
                                    array('controller' => 'siniestros', 'action' => 'listRecords', 'pteAsegurado_r'),
                                    array(
                                        'class' => 'label label-danger btn-tooltip pull-right text-center',
                                        'data-toggle' => 'tooltip',
                                        'data-title' => 'Pte. Asegurado ('.$diasParametro.' días)',
                                        'data-placement' => 'right',
                                        'id' => 'pteAsegurado-red'
                                    )
                                );
                                echo $this->Html->link('',
                                    array('controller' => 'siniestros', 'action' => 'listRecords', 'pteAsegurado_b'),
                                    array(
                                        'class' => 'label label-primary btn-tooltip pull-right text-center fa-margin-right',
                                        'data-toggle' => 'tooltip',
                                        'data-title' => 'Pte. Asegurado (Todos)',
                                        'data-placement' => 'left',
                                        'id' => 'pteAsegurado-blue'
                                    )
                                );
                                ?>
                            </li>

                            <li class="list-group-item">
                                <span class="text-uppercase"><small><strong> PTE. INSTRUC. </strong>
                                    </small></span>
                                <?php
                                echo $this->Html->link('',
                                    array('controller' => 'siniestros', 'action' => 'listRecordsPteInstrucciones'),
                                    array(
                                        'class' => 'label label-primary btn-tooltip pull-right text-center fa-margin-right',
                                        'data-toggle' => 'tooltip',
                                        'data-title' => 'Pte. Instruc',
                                        'data-placement' => 'left',
                                        'id' => 'pteInstruc-blue'
                                    )
                                );
                                ?>
                            </li>

                            <li class="list-group-item">
                                <span class="text-uppercase"><small><strong> PTE. PERITO </strong></small></span>
                                <?php
                                echo $this->Html->link(
                                    '',
                                    array('controller' => 'siniestros', 'action' => 'listRecords', 'ptePerito_b'),
                                    array(
                                        'class' => 'label label-primary btn-tooltip pull-right text-center fa-margin-right',
                                        'data-toggle' => 'tooltip',
                                        'data-title' => 'Pte. Perito',
                                        'data-placement' => 'left',
                                        'id' => 'ptePerito-blue'
                                    )
                                );
                                ?>
                            </li>

                            <li class="list-group-item">
                                <span class="text-uppercase"><small><strong> PTE. COLABORADOR </strong></small></span>
                                <?=$this->Html->link('', array(
                                    'controller' => 'siniestros', 'action' => 'listRecords', 'pteColaborador_b'),
                                    array(
                                        'class' => 'label label-primary btn-tooltip pull-right text-center fa-margin-right',
                                        'data-toggle' => 'tooltip',
                                        'data-title' => 'Pte. Colaborador',
                                        'data-placement' => 'left',
                                        'id' => 'pteColaborador-blue'
                                    )
                                );
                                ?>
                            </li>

                            <li class="list-group-item">
                                <span class="text-uppercase"><small><strong> PTE. GREMIO </strong></small></span>
                                <?php
                                echo $this->Html->link(
                                    '',
                                    array('controller' => 'siniestros', 'action' => 'listRecords', 'pteGremio_r'),
                                    array(
                                        'class' => 'label label-danger btn-tooltip pull-right text-center',
                                        'data-toggle' => 'tooltip',
                                        'data-title' => 'Pte. Gremio (10 días)',
                                        'data-placement' => 'right',
                                        'id' => 'pteGremio-red'
                                    )
                                );
                                echo $this->Html->link('',
                                    array('controller' => 'siniestros', 'action' => 'listRecords', 'pteGremio_b'),
                                    array(
                                        'class' => 'label label-primary btn-tooltip pull-right text-center fa-margin-right',
                                        'data-toggle' => 'tooltip',
                                        'data-title' => 'Pte. Gremio (Todos)',
                                        'data-placement' => 'left',
                                        'id' => 'pteGremio-blue'
                                    )
                                );
                                ?>
                            </li>

                            <li class="list-group-item">
                                <span class="text-uppercase"><small><strong> PTE. CALIDAD </strong></small></span>
                                <?php
                                echo $this->Html->link(
                                    '',
                                    array('controller' => 'siniestros', 'action' => 'listRecords', 'pteCalidad_b'),
                                    array(
                                        'class' => 'label label-primary btn-tooltip pull-right text-center fa-margin-right',
                                        'data-toggle' => 'tooltip',
                                        'data-title' => 'Pte. Calidad',
                                        'data-placement' => 'left',
                                        'id' => 'pteCalidad-blue'
                                    )
                                );
                                ?>
                            </li>

                            <li class="list-group-item">
                                <span class="text-uppercase"><small><strong> PTE. CIERRE </strong></small></span>
                                <?php

                                echo $this->Html->link(
                                    '',
                                    array('controller' => 'siniestros', 'action' => 'listRecords', 'pteCierre_r'),
                                    array(
                                        'class' => 'label label-danger btn-tooltip pull-right text-center',
                                        'data-toggle' => 'tooltip',
                                        'data-title' => 'Pte. Cierre',
                                        'data-placement' => 'right',
                                        'id' => 'pteCierre-red'
                                    )
                                );

                                ?>
                            </li>

                            <li class="list-group-item">
                                <span class="text-uppercase"><small><strong> PTE. FACTURA </strong></small></span>
                                <?php
                                echo $this->Html->link(
                                    '',
                                    array('controller' => 'siniestros', 'action' => 'listRecords', 'pteFactura_b'),
                                    array(
                                        'class' => 'label label-primary btn-tooltip pull-right text-center fa-margin-right',
                                        'data-toggle' => 'tooltip',
                                        'data-title' => 'Pte. Factura',
                                        'data-placement' => 'left',
                                        'id' => 'pteFactura-blue'
                                    )
                                );
                                ?>
                            </li>

                            <?php if(!empty($etiquetas_propietario)){ ?>
                            <li class="list-group-item">
                                <span class="text-uppercase"><small><strong> ETIQUETADOS </strong></small></span>
                                <?= $this->Html->link('',
                                    array('controller' => 'siniestros', 'action' => 'listRecordsEtiquetados'),
                                    array(
                                        'class' => 'label label-primary btn-tooltip pull-right text-center fa-margin-right',
                                        'data-toggle' => 'tooltip',
                                        'data-title' => 'Expedientes con etiquetas asignadas',
                                        'data-placement' => 'right',
                                        'id' => 'etiquetados-blue'
                                    ));
                                ?>
                            </li>
                            <?php } ?>

                            <li class="list-group-item" id="list-anulados">
                                <span class="text-uppercase"><small><strong> ANULADOS </strong></small></span>
                                <?php
                                echo $this->Html->link(
                                    '',
                                    array('controller' => 'siniestros', 'action' => 'listRecords', 'anulados_b'),
                                    array(
                                        'class' => 'label label-primary btn-tooltip pull-right text-center fa-margin-right',
                                        'data-toggle' => 'tooltip',
                                        'data-title' => 'Anulados',
                                        'data-placement' => 'left',
                                        'id' => 'anulados-blue'
                                    )
                                );
                                ?>
                            </li>

                        </div>
                    </div>
                </div>
            </div>
        </ul>
    <?php
    }
}

?>