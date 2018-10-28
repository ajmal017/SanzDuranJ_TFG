<?php
App::uses('AppHelper', 'View/Helper');
App::uses('AclComponent', 'Controller/Component');

class NavbarHelper extends AppHelper
{

    public $helpers = array('Html', 'Session', 'Form');
    public $uses = array('Configpropietarioscia', 'Operario', 'OperariosExterno', 'Usuario', 'OperariosHasOficiotaller', 'PropietariosHasOperariosext');

    public function navbar($companias_propietario, $urlPHP) {
        ?>
        <div class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="container container-navbar">
                <div class="navbar-header">
                    <?php
                    $name = 'Superusuario';
                    $logo = 'Logo_iTramit.png';

                    if (!empty($logo)) {
                        $image = $logo;
                        echo $this->Html->link(
                            $this->Html->image(
                                $image,
                                array(
                                    'class' => 'img-brand-personalized img-rounded',
                                    'height' => '40',
                                )
                            ),
                            array(
                                'controller' => 'main',
                                'action' => 'index'
                            ),
                            array(
                                'escape' => false,
                                'class' => 'navbar-brand btn-tooltip padding-brand',
                                'data-placement' => 'bottom',
                                'data-toggle' => 'tooltip',
                                'data-title' => $name
                            )
                        );
                    } else {
                        echo $this->Html->link($name, array('controller' => 'main', 'action' => 'index'), array('class' => 'navbar-brand'));
                    }
                    ?>
                </div>

                <?php
                // Si el usuario logueado tiene propietario habilitamos el centro de notificaciones y la bandeja de entrada.
                if ($this->Session->read('Auth.User.FK_PROPIETARIO') != null) {
                    ?>
                    <ul class="nav navbar-nav">
                        <li class="dropdown" id="notifications-message-envelope-dropdown">
                            <a href="#" class="dropdown-toggle btn-tooltip" data-target="#" data-toggle="dropdown"
                               data-title="Notificaciones"
                               data-placement="bottom"><i
                                        class="message-notifications-envelope fa fa-envelope fa-lg"></i> <span
                                        class="caret"></span></a>
                            <ul class="dropdown-menu dropdown-message-notifications-container" role="menu">
                            </ul>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav">
                        <li class="dropdown" id="inbox-envelope-dropdown">
                            <a href="#" class="dropdown-toggle btn-tooltip" data-target="#" data-toggle="dropdown">
                                <i class="inbox-envelope fa fa-inbox fa-lg"></i> <span class="caret"></span></a>
                            <ul class="dropdown-menu dropdown-inbox-container" role="menu">
                            </ul>
                        </li>
                    </ul>
                    <?php
                }
                ?>
                <ul class="nav navbar-nav">

                    <li class="dropdown">

                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            Tablas Maestras <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">


                            <li>
                                <?php echo $this->Html->link('Tipos Interesados', array('controller' => 'tiposinteresados', 'action' => 'index')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Parametros', array('controller' => 'parametros', 'action' => 'index')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Mensajes', array('controller' => 'mensajes', 'action' => 'index')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Gremios', array('controller' => 'gremios', 'action' => 'index')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Oficios', array('controller' => 'companiasHasGremios', 'action' => 'index')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Tipos de Incidencias', array('controller' => 'tipoincidencias', 'action' => 'index')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Oficios de Taller', array('controller' => 'oficiostaller', 'action' => 'index')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Baremos', array('controller' => 'baremos', 'action' => 'index')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Duplicar Baremo', array('controller' => 'baremos', 'action' => 'duplicarBaremo')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Importar Baremo desde CSV', array('controller' => 'baremos', 'action' => 'importarcsv')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Zonas', array('controller' => 'zonas', 'action' => 'index')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Plantillas emails', array('controller' => 'plantillasemails', 'action' => 'index')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Ivas', array('controller' => 'ivas', 'action' => 'index')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Tipos de eventos', array('controller' => 'tiposeventos', 'action' => 'index')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Zonas de Baremos', array('controller' => 'zonabaremos', 'action' => 'index')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Baremos - Zonas - Compañías de Asegurado', array('controller' => 'baremosHasZonas', 'action' => 'index')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Carga Datos Implantación', array('controller' => 'siniestrossistemasants', 'action' => 'importarDatosImplantacion')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Cargar Visitas Anteriores', array('controller' => 'siniestrossistemasants', 'action' => 'importarDatosVisitas')); ?>
                            </li>
                        </ul>

                    </li>
                    <li class="dropdown">

                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            Licencias <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">

                            <li>
                                <?php echo $this->Html->link('Propietarios', array('controller' => 'propietarios', 'action' => 'index')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Usuarios', array('controller' => 'usuarios', 'action' => 'adminUsers')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Usuarios desde CSV', array('controller' => 'usuarios', 'action' => 'addLicensedcsv')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('OPExternos desde CSV', array('controller' => 'propietariosHasOperariosexts', 'action' => 'new_opexcsv')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Operarios Externos', array('controller' => 'operariosexternos', 'action' => 'index')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Generador Licencias', array('controller' => 'generadorlicencias', 'action' => 'index')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Consumo', array('controller' => 'propietarios', 'action' => 'consumo')); ?>
                            </li>
                        </ul>

                    </li>
                    <li class="dropdown">

                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            Procesos <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">


                            <li>
                                <?php echo $this->Html->link('Fases/Estados', array('controller' => 'fases', 'action' => 'index')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Procedimientos', array('controller' => 'procedimientos', 'action' => 'index')); ?>
                            </li>
                        </ul>

                    </li>
                    <li class="dropdown">

                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            Compañias y reparadoras <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <?php echo $this->Html->link('Mto. de Compañías de Asegurados', array('controller' => 'companiasasegurados', 'action' => 'index')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Mto. de Compañías/Gestoras', array('controller' => 'companias', 'action' => 'index')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Mto. de Reparadoras', array('controller' => 'reparadoras', 'action' => 'index')); ?>
                            </li>
                        </ul>

                    </li>

                    <li class="dropdown">

                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            Comunicaciones <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">


                            <li>
                                <?php echo $this->Html->link('Tipos de mensajes', array('controller' => 'tiposmensajes', 'action' => 'index')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Controladores', array('controller' => 'controladores', 'action' => 'index')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Tipos de canales', array('controller' => 'tiposcanales', 'action' => 'index')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Canales', array('controller' => 'canales', 'action' => 'index')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Tipos de mensaje por compañía', array('controller' => 'CompaniasHasTiposmensaje', 'action' => 'index')); ?>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            Soporte Técnico <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <?php echo $this->Html->link('Acceso Fantasma', array('controller' => 'usuarios', 'action' => 'listUser')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Memcached', array('controller' => 'auditorias', 'action' => 'memcached')); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link('Auditorías', array('controller' => 'auditorias', 'action' => 'listado')); ?>
                            </li>
                        </ul>
                    </li>
                </ul>

                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <p class="text-white navbar-text fa-margin-right"
                           style="width: 90px; margin-top: 12px; margin-bottom: 17px;margin-left: 0px;margin-bottom: 0px;"><span
                                    class="hour-clock"></span><span class="date-clock"></span></p>
                    </li>
                    <li class="dropdown">
                        <?php
                        $ruta_img = $this->Session->read('Auth.User.D_RUTA');
                        if (!empty($ruta_img)) {
                            ?>
                            <a href="#" class="dropdown-toggle img-user-link" data-toggle="dropdown" role="button"
                               aria-expanded="false">
                                <?php echo $this->Session->read('Auth.User.S_NOMBRE'); ?>
                                <?php

                                if (Configure::read("ENVIROMENT") == 'LOCAL') {
                                    $image = '../uploads/usuarios/thumb_' . $this->Session->read('Auth.User.D_RUTA') . '?' . time();
                                } else {
                                    $image = $urlPHP . 'img' . DS . 'usuarios' . DS . 'thumb_' . $this->Session->read('Auth.User.D_RUTA') . '?' . time();
                                }

                                echo $this->Html->image(
                                    $image,
                                    array(
                                        'class' => 'img-rounded fa-margin-left',
                                        'height' => '32',
                                    )
                                );
                                ?>
                            </a>
                        <?php } else { ?>
                            <a href="#" class="dropdown-toggle navbar-options" data-toggle="dropdown" role="button"
                               aria-expanded="false">
                                <?php
                                echo $this->Session->read('Auth.User.S_NOMBRE');
                                echo $this->Html->tag(
                                    'i',
                                    '',
                                    array(
                                        'class' => 'fa fa-user fa-lg fa-margin-left text-white'
                                    )
                                );
                                ?>
                            </a>
                        <?php } ?>
                        <ul class="dropdown-menu" role="menu">
                            <li>
                                <?php

                                echo $this->Html->link(
                                    '<i class="fa fa-edit fa-lg fa-margin-right text-info"></i><strong class="text-info">Editar Perfil</strong>',
                                    array('controller' => 'usuarios', 'action' => 'editProfile'),
                                    array('escape' => false));
                                ?>
                            </li>
                            <li>
                                <?php
                                echo $this->Html->link(
                                    '<i class="fa fa-edit fa-lg fa-margin-right text-info"></i><strong class="text-info">Reportar incidencia</strong>',
                                    '#',
                                    array(
                                        'escape' => false,
                                        'data-toggle' => 'modal',
                                        'data-target' => '#reportar_incidencia',
                                        'id' => 'menu_reportar_incidencia'
                                    )
                                );
                                ?>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <?php
                                echo $this->Html->link(
                                    '<i class="fa fa-cloud-download fa-lg fa-margin-right text-info"></i><strong class="text-info">Descargar Manual Web</strong>',
                                    'https://static.wixstatic.com/ugd/9fc6d5_3ccaedc860b245bdaafc2094a1aae5cb.pdf?dn=iTramit-Manual_de_Usuarios_Tramitacion-v5.pdf',
                                    array('escape' => false)
                                );
                                ?>
                            </li>
                            <li>
                                <?php
                                echo $this->Html->link(
                                    '<i class="fa fa-cloud-download fa-lg fa-margin-right text-info"></i><strong class="text-info">Descargar Manual APP</strong>',
                                    'https://static.wixstatic.com/ugd/9fc6d5_01fea3615a354e30a437d97f9fb54a16.pdf?dn=iTramit-Manual_APP_de_Operarios-v3.pdf',
                                    array('escape' => false)
                                );
                                ?>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <?php
                                echo $this->Html->link(
                                    '<i class="fa fa-power-off fa-lg fa-margin-right text-danger"></i><strong class="text-danger">Cerrar Sesión</strong>',
                                    array('controller' => 'main', 'action' => 'logout'),
                                    array('escape' => false));
                                ?>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <?php echo $this->_View->element('modal_reportar_incidencia');
    }

    public function navbarAgente() { ?>
        <style type="text/css">
            .mensajes-numero {
                background-color: white !important;
                color: #103184 !important;
                margin: 0 !important;
                padding: 1px 3px !important;
                font-size: 12px !important;
                margin-right: 0px !important;
            }
        </style>
        <div class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="container container-navbar">
                <div class="navbar-header">
                    <?= $this->Html->link($this->Html->image('Logo_iTramit.png', array(
                        'class' => 'img-brand-personalized img-rounded',
                        'height' => '40',
                    )), array(
                        'controller' => 'AgentesExternos',
                        'action' => 'buscador'
                    ),
                        array(
                            'escape' => false,
                            'class' => 'navbar-brand btn-tooltip padding-brand',
                            'data-placement' => 'bottom',
                            'data-toggle' => 'tooltip',
                            'data-title' => 'iTramit - Agente externo'
                        )) ?>
                </div>
                <ul class="nav navbar-nav">
                    <li class="dropdown">
                        <?= $this->Html->link('' . $this->Html->tag('i', '', array(
                                'class' => 'text-white btn-tooltip fa fa-search',
                            )) . '&nbsp; Buscador',
                            array(
                                'controller' => 'AgentesExternos',
                                'action' => 'buscador'
                            ), array(
                                'style' => 'margin-top:20px;',
                                'escape' => false, 'role' => 'button',
                                'aria-expanded' => 'false',
                                'title' => 'Buscador de expedientes',
                                'data-placement' => 'bottom',
                                'data-toggle' => 'tooltip',
                                'class' => 'btn-tooltip navbar-text no-margin-top no-margin-bottom'
                            )
                        ) ?>
                    </li>
                    <li class="dropdown">
                        <?= $this->Html->link('<span class="label fa-margin-left mensajes-numero">0</span> &nbsp;' .
                            $this->Html->tag('i', '', array(
                                'class' => 'text-white btn-tooltip fa fa-envelope-o fa-lg',
                            )) . '&nbsp; Gestión de mensajes',
                            array(
                                'controller' => 'AgentesExternos',
                                'action' => 'mensajes'
                            ), array(
                                'style' => 'margin-top:20px;',
                                'escape' => false, 'role' => 'button',
                                'aria-expanded' => 'false',
                                'title' => 'Gestión de mensajes enviados / recibidos',
                                'data-placement' => 'bottom',
                                'data-toggle' => 'tooltip',
                                'class' => 'btn-tooltip navbar-text no-margin-top no-margin-bottom'
                            )
                        ) ?>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <p class="text-white navbar-text fa-margin-right" style="margin-top:20px;">
                            <?= $this->Html->tag('i', '', array(
                                'class' => 'btn-tooltip fa fa-clock-o fa-lg icon-clock',
                                'title' => '',
                                'data-placement' => 'bottom',
                                'data-toggle' => 'tooltip'
                            )) ?>
                            &nbsp;
                            <span class="clock"></span>
                            &nbsp; &nbsp; &nbsp; &nbsp;
                        </p>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" rel='tooltip'
                           aria-expanded="false">
                            <?php
                            echo $this->Html->tag('i', '', array('class' => 'fa fa-user fa-lg fa-margin-left fa-margin-right text-white'));
                            echo ' ' . CakeSession::read('Auth.User.S_NOMBRE');
                            ?>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li>
                                <?= $this->Html->link(
                                    '<i class="fa fa-edit fa-lg fa-margin-right text-info"></i><strong class="text-info">Editar Perfil</strong>',
                                    array('controller' => 'AgentesExternos', 'action' => 'editarPerfil'), array('escape' => false))
                                ?>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <?= $this->Html->link(
                                    '<i class="fa fa-power-off fa-lg fa-margin-right text-danger"></i><strong class="text-danger">Cerrar Sesión</strong>',
                                    array('controller' => 'main', 'action' => 'logout'), array('escape' => false));
                                ?>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    <?php }

    public function navbarRep($companias_propietario, $urlPHP, $fac, $rolde, $tipo_taller, $respuestaNueva = false) {
        ?>
        <div class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="container container-navbar">
                <div class="navbar-header">
                    <?php
                    if ($this->Session->check('Auth.User.TIPO')) {
                        if ($this->Session->read('Auth.User.TIPO') == 'Reparadora') {
                            $name = $this->Session->read('Auth.User.REPARADORA_NOMBRE');
                            $logo = $this->Session->read('Auth.User.REPARADORA_LOGO');
                        } else if ($this->Session->read('Auth.User.TIPO') == 'Gestora') {
                            $name = $this->Session->read('Auth.User.GESTORA_NOMBRE');
                            $logo = $this->Session->read('Auth.User.GESTORA_LOGO');
                        } else if ($this->Session->read('Auth.User.TIPO') == 'Superusuario') {
                            $name = 'Superusuario';
                            $logo = 'Logo_iTramit.png';
                        }
                    }
                    if (!empty($logo)) {
                        if (Configure::read("ENVIROMENT") == 'LOCAL') {
                            if ($this->Session->read('Auth.User.TIPO') == 'Reparadora') {
                                $image = 'http://localhost/img/logos/reparadoras/' . $logo;
                            } else if ($this->Session->read('Auth.User.TIPO') == 'Gestora') {
                                $image = 'http://localhost/img/logos/companias/' . $logo;
                            }
                        } else {
                            if ($this->Session->read('Auth.User.TIPO') == 'Reparadora') {
                                $image = $urlPHP . 'img' . DS . 'logos' . DS . 'reparadoras' . DS . $logo . '?' . time();
                            } else if ($this->Session->read('Auth.User.TIPO') == 'Gestora') {
                                $image = $urlPHP . 'img' . DS . 'logos' . DS . 'companias' . DS . $logo . '?' . time();
                            }
                        }
                        if ($this->Session->read('Auth.User.TIPO') == 'Superusuario') {
                            $image = $logo;
                        }
                        echo $this->Html->link($this->Html->image(
                            $image,
                            array(
                                'class' => 'img-brand-personalized img-rounded',
                                'height' => '40',
                            )
                        ),
                            array(
                                'controller' => 'main',
                                'action' => 'index'
                            ),
                            array(
                                'escape' => false,
                                'class' => 'navbar-brand btn-tooltip padding-brand',
                                'data-placement' => 'bottom',
                                'data-toggle' => 'tooltip',
                                'data-title' => $name
                            )
                        );
                    } else {
                        echo $this->Html->link($name, array('controller' => 'main', 'action' => 'index'), array('class' => 'navbar-brand'));
                    }
                    ?>
                </div>

                <?php
                // Si el usuario logueado tiene propietario habilitamos el centro de notificaciones y la bandeja de entrada.
                if (in_array(7, $rolde) || in_array(8, $rolde) || in_array(9, $rolde)){
                if ($this->Session->read('Auth.User.FK_PROPIETARIO') != null) {
                    ?>
                    <ul class="nav navbar-nav">
                        <li class="dropdown" id="notifications-message-envelope-dropdown">
                            <a href="#" class="dropdown-toggle btn-tooltip" data-target="#" data-toggle="dropdown"
                               data-title="Notificaciones"
                               data-placement="bottom"><i
                                        class="message-notifications-envelope fa fa-envelope fa-lg"></i> <span
                                        class="caret"></span></a>
                            <ul class="dropdown-menu dropdown-message-notifications-container" role="menu">
                            </ul>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav">
                        <li class="dropdown" id="inbox-envelope-dropdown">
                            <a href="#" class="dropdown-toggle btn-tooltip" data-target="#" data-toggle="dropdown">
                                <table id="tablebandejaentrada">
                                    <tr>
                                        <td id="bandejaentradarojos">

                                        </td>
                                        <td rowspan="2">
                                            <i class="inbox-envelope fa fa-inbox fa-lg" style="margin-left: 6px;"></i>
                                            <span class="caret"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td id="bandejaentradablancos">

                                        </td>

                                    </tr>
                                </table>

                            </a>
                            <ul class="dropdown-menu dropdown-inbox-container" role="menu">
                            </ul>
                        </li>
                    </ul>
                    <?php if (!(sizeof($rolde) == 1 && in_array(9, $rolde))) { ?>
                        <ul class="nav navbar-nav">
                            <li>
                                <?php
                                echo $this->Html->link(
                                    $this->Html->tag('i', '',
                                        array(
                                            'escape' => false,
                                            'class' => 'btn-tooltip fa fa-paper-plane-o fa-lg',
                                            'data-title' => 'Bandeja de salida',
                                            'data-placement' => 'left'
                                        )
                                    ),
                                    array(
                                        'controller' => 'bandejasalidas',
                                        'action' => 'listar'
                                    ),
                                    array(
                                        'id' => 'menuBandejaSalida',
                                        'escape' => false
                                    )
                                );
                                ?>
                            </li>
                        </ul>
                        <?php
                    }
                }

                if ($this->Session->read('Auth.User.TIPO') != 'Superusuario') { ?>
                <?php if (!(sizeof($rolde) == 1 && in_array(9, $rolde))) { ?>
                    <ul class="nav navbar-nav">
                        <li>
                            <?php
                            echo $this->Html->link(
                                $this->Html->tag(
                                    'i',
                                    '',
                                    array(
                                        'escape' => false,
                                        'class' => 'btn-tooltip fa fa-bell fa-lg',
                                        'data-title' => 'Listado de Alarmas',
                                        'data-placement' => 'left',
                                        'data-toggle' => 'tooltip',
                                    )
                                ),
                                array(
                                    'controller' => 'alarmas',
                                    'action' => 'index'
                                ),
                                array(
                                    'escape' => false,
                                )
                            );
                            ?>
                        </li>
                    </ul>
                    <form class="navbar-form navbar-left no-padding-left no-padding-right" role="search">
                        <div class="form-group">
                            <select type="text"
                                    class="form-control input-sm alarm-priority-selector alarm-width-selector"
                                    style="color:white;">
                                <option value="1" class="option-danger">1 - Prioridad máxima</option>
                                <option value="2" class="option-warning">2 - Prioridad media superior</option>
                                <option value="3" class="option-info">3 - Prioridad media</option>
                                <option value="4" class="option-success">4 - Prioridad media inferior</option>
                                <option value="5" class="option-primary">5 - Prioridad mínima</option>
                            </select>
                        </div>
                    </form>
                <?php } ?>
                <ul class="nav navbar-nav">
                    <li>
                        <?php
                        if ((sizeof($rolde) == 1 && in_array(9, $rolde))) {
                            $nuevaPestaña = array(
                                'controller' => 'facturas',
                            );
                        } else {
                            $nuevaPestaña = array(
                                'controller' => 'main',
                                'action' => 'inicio'
                            );
                        }
                        echo $this->Html->link(
                            $this->Html->tag(
                                'i',
                                '',
                                array(
                                    'escape' => false,
                                    'class' => 'btn-tooltip fa fa-mail-forward fa-lg fa-margin-left',
                                    'data-title' => 'Nueva Pestaña',
                                    'data-placement' => 'right',
                                    'data-toggle' => 'tooltip',
                                )
                            ),
                            $nuevaPestaña,
                            array(
                                'escape' => false,
                                'target' => '_blank'
                            )
                        );
                        ?>
                    </li>
                </ul>
                <ul class="nav navbar-nav">
                    <li>
                        <?= $this->Html->link($this->Html->tag('i', '', array(
                            'escape' => false,
                            'class' => 'btn-tooltip fa fa-refresh fa-lg',
                            'data-title' => 'Refrescar',
                            'data-placement' => 'right',
                            'data-toggle' => 'tooltip'
                        )), array(), array(
                            'escape' => false,
                            'onclick' => 'javascript:refreshPage();'
                        )) ?>
                    </li>
                    <?php if (!(sizeof($rolde) == 1 && in_array(9, $rolde))){ ?>
                    <li class="dropdown">
                        <?php
                        echo $this->Html->link(
                            $this->Html->tag(
                                'i',
                                '',
                                array(
                                    'escape' => false,
                                    'class' => 'btn-tooltip fa fa-plus-square fa-lg',
                                    'data-title' => 'Nuevo Siniestro',
                                    'data-placement' => 'right',
                                    'data-toggle' => 'tooltip',
                                )
                            ),
                            '#',
                            array(
                                'escape' => false,
                                'class' => 'dropdown-toggle',
                                'data-toggle' => 'dropdown',
                                'role' => 'button'
                            )
                        );
                        ?>
                        <ul class="dropdown-menu" role="menu">
                            <li>
                                <?php
                                echo $this->Html->link(
                                    'Alta Manual de Siniestros',
                                    array(
                                        'controller' => 'siniestros',
                                        'action' => 'add'
                                    )
                                );
                                ?>
                            </li>
                            <?php
                            if (!empty($companias_propietario)) {
                                echo $this->Html->tag('li', '', array('class' => 'divider'));
                                foreach ($companias_propietario as $cont => $funcion) {
                                    echo $this->Html->tag('li', $this->Html->link($cont, $funcion));
                                }
                            }
                            if ($this->Session->read('parseaexpediente')) {
                                echo $this->Html->tag('li', '', array('class' => 'divider')); ?>
                                <li>
                                    <?php
                                    echo $this->Html->link(
                                        'Alta Semi automática de Siniestros',
                                        array(
                                            'controller' => 'parseoexpedientes',
                                            'action' => 'procesaEntrada'
                                        )
                                    );
                                    ?>
                                </li>
                                <?php
                            }
                            ?>

                            <?php if ($this->Session->read('Auth.User.FK_PROPIETARIO') == '29'): /* Si es ProService... */?>
                                <!-- Alta Delegada AXA -->
                                <li class="divider"></li>
                                <li>
                                    <?php
                                        echo $this->Html->link(
                                            'Alta Delegada AXA',
                                            [
                                                'controller' => 'altadelegadaaxa',
                                                'action' => 'index'
                                            ]
                                        );
                                    ?>
                                </li>
                                <!-- Fin Alta Delegada AXA -->
                            <?php endif; ?>
                        </ul>
                        <?php } ?>
                    </li>
                    <?php
                    //Boton de operarios
                    if (in_array(7, $rolde) || in_array(8, $rolde)) { ?>
                        <li>
                            <?php
                            echo $this->Html->link(
                                $this->Html->tag(
                                    'i',
                                    '',
                                    array(
                                        'escape' => false,
                                        'class' => 'btn-tooltip fa fa-users fa-lg',
                                        'data-title' => 'Operarios',
                                        'data-placement' => 'right',
                                        'data-toggle' => 'tooltip',
                                    )
                                ),
                                array(
                                    'controller' => 'operarios',
                                    'action' => 'listOperarios'
                                ),
                                array(
                                    'escape' => false
                                )
                            );
                            ?>
                        </li>
                    <?php }
                    if ($fac) { ?>
                        <li class="dropdown">
                            <a class="dropdown-toggle fa-lg btn-tooltip" data-title="Utilidades Facturación"
                               data-placement="right" data-toggle="dropdown" href="#">
                                <i class="fa fa-euro fa-lg"></i>
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <?php echo $this->Html->link('Control de Facturación', array('controller' => 'facturas', 'action' => 'lista'), array('escape' => false, 'target' => '_blank')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Creación de Facturas Grupales', array('controller' => 'facturas', 'action' => 'listPreInvoices'), array('escape' => false, 'target' => '_blank')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Reclamación de Facturas', array('controller' => 'facturas', 'action' => 'facturasPendientesCobro'), array('target' => '_blank')); ?>
                                </li>
                                <?php if ($this->Session->read('AppController.nombrePropietario') == 'lacarte' && $this->Session->read('Auth.User.FK_PROPIETARIO') == 20) { ?>
                                    <li>
                                        <?php echo $this->Html->link('Listado de Facturas Emitidas', array('controller' => 'facturas', 'action' => 'controlFacturas'), array('target' => '_blank')); ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </li>
                    <?php }
                    //Boton de Gestion agenda
                    if (in_array(7, $rolde) || in_array(8, $rolde)) {
                        ?>
                        <li class="dropdown">
                            <a class="dropdown-toggle fa-lg btn-tooltip" data-title="Utilidades Tramitador"
                               data-placement="right" data-toggle="dropdown" href="#">
                                <i class="fa fa-paste no-padding-top"></i>
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <?php echo $this->Html->link('Agenda Completa', array('controller' => 'agendas', 'action' => 'agendaGeneral'), array('target' => '_blank')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Agenda Franjas', array('controller' => 'agendas', 'action' => 'franjas'), array('target' => '_blank')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Listado Planificación de la Agenda', array('controller' => 'listadostramitadores', 'action' => 'listVisitasOp')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Listado de Facturas Pendientes de Emisión', array('controller' => 'facturas', 'action' => 'facturasPendientesEmision')); ?>
                                </li>
                                <?php if ($this->Session->read('preventiva')) { ?>
                                    <li>
                                        <?php echo $this->Html->link('Factura Preventiva', array('controller' => 'facturas', 'action' => 'preventiva')); ?>
                                    </li>
                                <?php } ?>
                                <?php
                                if ($this->Session->read('tareaspelayo')) {
                                    echo '<li>';
                                    echo $this->Html->link('Mensajes y Tareas Pelayo', array('controller' => 'pelayo', 'action' => 'mensajesTareasTodos'));
                                    echo '</li>';
                                }
                                ?>
                                <li>
                                    <?php echo $this->Html->link('Listado de Partes', array('controller' => 'listadostramitadores', 'action' => 'listTrabajosHasVisitas')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Listado de Trabajos', array('controller' => 'listadostramitadores', 'action' => 'listTrabajosByGremios')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Mto. Intervinientes', array('controller' => 'interesados', 'action' => 'index')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Listado participación de agentes', array('controller' => 'listadostramitadores', 'action' => 'listPartagentes')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Listado materiales', array('controller' => 'materiales', 'action' => 'listMateriales')); ?>
                                </li>

                                <li>
                                    <?php echo $this->Html->link('Listado de valoraciones realizadas', array('controller' => 'valoraciones', 'action' => 'listValoraciones')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Listado de valoraciones de Operarios', array('controller' => 'listadostramitadores', 'action' => 'listValoracionesOperario')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Listado de datos de Operarios', array('controller' => 'operarios', 'action' => 'listaDatosOperarios')); ?>
                                </li>
                                <?php if ($this->Session->read('Auth.User.Propietario.V_IMPLANTACION') >= 1) { ?>
                                    <li>
                                        <?php echo $this->Html->link('Listado Expedientes Sistema Anterior', array('controller' => 'siniestrossistemasants', 'action' => 'index')); ?>
                                    </li>
                                <?php } ?>

                            </ul>
                        </li>
                        <?php
                    }
                    }
                    }
                    ?>
                </ul>
                <ul class="nav navbar-nav">
                    <?php
                    if (in_array(6, $rolde) || in_array(8, $rolde)) { ?>
                        <li class="dropdown">
                            <a class="dropdown-toggle btn-tooltip" data-toggle="dropdown" href="#"
                               data-title="Utilidades Responsable"
                               data-placement="right">
                                <i class="fa fa-list fa-lg"></i> <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <?php echo $this->Html->link('Alarmas', array('controller' => 'alarmas', 'action' => 'indexAll')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Notificaciones', array('controller' => 'Notificacionmensajes', 'action' => 'listar')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Bandeja de salida', array('controller' => 'bandejasalidas', 'action' => 'all')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Auditorías', array('controller' => 'auditorias', 'action' => 'auditoriasTramitador')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Listado de líneas para liquidación', array('controller' => 'valoracioneslineas', 'action' => 'listByOperario')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Listado de liquidaciones', array('controller' => 'liquidaciones', 'action' => 'index')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Listado de Guardias', array('controller' => 'listadosreparadoras', 'action' => 'listHorasGuardia')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Listado diario de asignacion y cierre', array('controller' => 'listadostramitadores', 'action' => 'listDiaAsigcierre')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Listado dedicación de operarios', array('controller' => 'listadostramitadores', 'action' => 'listDedicacionOperario')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Listado siniestros finalizados', array('controller' => 'listadoscomunes', 'action' => 'listSiniestrosFinalizados')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Listado de expedientes asignados y cerrados por compañía', array('controller' => 'listadostramitadores', 'action' => 'listDiaAsigcierrePorCompania')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Salud de la tramitación', array('controller' => 'listadosreparadoras', 'action' => 'listControlTramitacion')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Plantillas de Valoración', array('controller' => 'plantillasvaloraciones', 'action' => 'index')); ?>
                                </li>
                            </ul>
                        </li>
                    <?php }
                    if (in_array(8, $rolde)) {
                        ?>
                        <li class="dropdown">
                            <a class="dropdown-toggle btn-tooltip" data-toggle="dropdown" href="#"
                               data-title="Listados y Estadísticas" data-placement="right">
                                <i class="fa fa-bar-chart fa-lg"></i> <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="dropdown-submenu">
                                    <a class="test" data-pos="1" href="#">
                                        Mi Empresa
                                        <i class="fa fa-caret-right" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <?php echo $this->Html->link('Rentabilidad', array('controller' => 'listadoscomunes', 'action' => 'listRentabilidad')); ?>
                                        </li>
                                        <li>
                                            <?php echo $this->Html->link('Expedientes', array('controller' => 'listadoscomunes', 'action' => 'listServicios')); ?>
                                        </li>
                                    </ul>
                                </li>
                                <li class="dropdown-submenu">
                                    <a class="test" data-pos="2" href="#">
                                        Clientes
                                        <i class="fa fa-caret-right" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <?php echo $this->Html->link('Tiempos medios de cierre', array('controller' => 'listadoscomunes', 'action' => 'listCompTiempoCierre')); ?>
                                        </li>
                                        <li>
                                            <?php echo $this->Html->link('Costes medios', array('controller' => 'listadoscomunes', 'action' => 'listCostesMed')); ?>
                                        </li>
                                        <!--
                                        <li>
                                            <?php echo $this->Html->link('Costes medios FAP', array('controller' => 'listadoscomunes', 'action' => 'listCostesMediosDa')); ?>
                                        </li>
                                        -->
                                    </ul>
                                </li>
                                <li class="dropdown-submenu">
                                    <a class="test" data-pos="3" href="#">
                                        Rendimiento Colaboradores
                                        <i class="fa fa-caret-right" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <?php echo $this->Html->link('Rendimiento de tramitadores', array('controller' => 'listadoscomunes', 'action' => 'listTramitRend')); ?>
                                        </li>

                                        <li>
                                            <?php echo $this->Html->link('Evolución rendimiento de tramitadores', array('controller' => 'listadoscomunes', 'action' => 'listTramitRendEvolucion')); ?>
                                        </li>
                                        <!--
                                        <li>
                                            <?php echo $this->Html->link('Rendimiento de operarios', array('controller' => 'listadosreparadoras', 'action' => 'listRendOp')); ?>
                                        </li>
                                        <li>
                                            <?php echo $this->Html->link('Evolución de rendimiento de operarios', array('controller' => 'listadosreparadoras', 'action' => 'listEvOp')); ?>
                                        </li>
                                        -->
                                        <li>
                                            <?php echo $this->Html->link('Facturación de operarios', array('controller' => 'listadosreparadoras', 'action' => 'listFacturacionOp')); ?>
                                        </li>

                                        <li>
                                            <?php echo $this->Html->link('Evolución de facturación de operarios', array('controller' => 'listadosreparadoras', 'action' => 'listEvolucionFacturacionOp')); ?>
                                        </li>
                                        <li>
                                            <?php echo $this->Html->link('Costes por operario', array('controller' => 'listadosreparadoras', 'action' => 'listCostOp')); ?>
                                        </li>
                                    </ul>
                                </li>
                                <li class="dropdown-submenu">
                                    <a class="test" data-pos="4" href="#">
                                        Calidad
                                        <i class="fa fa-caret-right" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <?php echo $this->Html->link('Incidencias de usuarios', array('controller' => 'listadoscomunes', 'action' => 'listIncidenciasOp')); ?>
                                        </li>
                                        <li>
                                            <?php echo $this->Html->link('Número de incidencias usuarios', array('controller' => 'listadoscomunes', 'action' => 'listNumIncidenciasOp')); ?>
                                        </li>
                                        <!--
                                        <li>
                                            <?php echo $this->Html->link('Incidencias del propietario', array('controller' => 'listadoscomunes', 'action' => 'listIncidenciasProp')); ?>
                                        </li>

                                        <li>
                                            <?php echo $this->Html->link('Número de incidencias del Propietario', array('controller' => 'listadoscomunes', 'action' => 'listNumIncidenciasProp')); ?>
                                        </li>
                                        <li>
                                        -->
                                        <li>
                                            <?php echo $this->Html->link('Cuestionarios de calidad', array('controller' => 'listadosreparadoras', 'action' => 'listEncuestasOp')); ?>
                                        </li>
                                        <li>
                                            <?php echo $this->Html->link('Análisis de respuestas positivas/negativas', array('controller' => 'listadosreparadoras', 'action' => 'listPositivosNegativos')); ?>
                                        </li>
                                        <li>
                                            <?php echo $this->Html->link('Propuestas de rehúse', array('controller' => 'listadoscomunes', 'action' => 'listPropuestasRehuse')); ?>
                                        </li>
                                        <li>
                                            <?php echo $this->Html->link('Propuestas de fraude', array('controller' => 'listadoscomunes', 'action' => 'listPropuestasFraude')); ?>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <?php
                    }

                    if (in_array(6, $rolde)) {

                        ?>

                        <li class="dropdown">

                            <a class="dropdown-toggle btn-tooltip" data-toggle="dropdown" href="#"
                               data-title="Panel de Control"
                               data-placement="right">
                                <i class="fa fa-cogs fa-lg"></i> <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <?php echo $this->Html->link('Mis datos', array('controller' => 'reparadoras', 'action' => 'profile')); ?>
                                </li>

                                <li>
                                    <?php echo $this->Html->link('Usuarios', array('controller' => 'usuarios', 'action' => 'licensedUsers')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Operarios Externos', array('controller' => 'propietariosHasOperariosexts', 'action' => 'index')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Bajas temporales de operarios', array('controller' => 'visitas', 'action' => 'bajaTemporal')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Cuestionarios', array('controller' => 'cuestionarios', 'action' => 'index')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Fichas con Compañía', array('controller' => 'configpropietarioscia', 'action' => 'index_repairer')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Series de Facturación', array('controller' => 'series', 'action' => 'index')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Configuración con Gestoras Asociadas', array('controller' => 'gestorasHasReparadoras', 'action' => 'index_repairer')); ?>
                                <li>
                                <li>
                                    <?php echo $this->Html->link('Emisores', array('controller' => 'emisoresfacturas', 'action' => 'index')); ?>
                                </li>
                                <?php if ($tipo_taller > 0) { ?>
                                    <li>
                                        <?php echo $this->Html->link('Oficios Taller', array('controller' => 'operariosHasOficiotaller', 'action' => 'index')); ?>
                                    </li>
                                <?php } ?>
                                <li>
                                    <?php echo $this->Html->link('Etiquetas', array('controller' => 'etiquetas', 'action' => 'index')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Agentes externos', array('controller' => 'interesados', 'action' => 'index')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Plantillas de comunicación', array('controller' => 'plantillascomunicaciones', 'action' => 'index')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Zonas', array('controller' => 'zonas', 'action' => 'index')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Tarifas de Operarios', array('controller' => 'baremospropietarios', 'action' => 'index')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Gestión Tarifas Operarios', array('controller' => 'baremosHasOperarios', 'action' => 'index')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Gestión de Umbrales', array('controller' => 'umbrales', 'action' => 'index')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Tipos de Descuentos', array('controller' => 'tiposdescuentos', 'action' => 'index')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Logos y Textos', array('controller' => 'complementos', 'action' => 'index')); ?>
                                </li>
                            </ul>
                        </li>
                    <?php }

                    if (in_array(11, $rolde)) { ?>
                        <li class="dropdown">
                            <a class="dropdown-toggle btn-tooltip" data-toggle="dropdown" href="#" data-title="Taller"
                               data-placement="right">
                                <i class="fa fa-wrench fa-lg"></i> <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <?php echo $this->Html->link('Gestión de Taller', array('controller' => 'agendas', 'action' => 'gestionTaller'), array('target' => '_blank')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Incidencias de Operarios', array('controller' => 'listadoscomunes', 'action' => 'listIncidenciasOp')); ?>
                                </li>

                            </ul>
                        </li>
                    <?php }

                    //Icono de respuesta de incidencia
                    ?>

                    <ul class="nav navbar-nav faa">
                        <li>
                            <?php


                            if ($respuestaNueva) {
                                echo $this->Html->link(
                                    $this->Html->tag(
                                        'i',
                                        '',
                                        array(
                                            'escape' => false,
                                            'class' => 'btn-tooltip fa fa-support fa-lg fa-margin-left alertaCircular',
                                            'data-title' => 'Se ha recibido respuesta a una incidencia',
                                            'data-placement' => 'bottom',
                                            'data-toggle' => 'tooltip',
                                            'style' => 'line-height: 1em;'
                                        )
                                    ),
                                    array(
                                        'controller' => 'main',
                                        'action' => 'inicio#incidencias'
                                    ),
                                    array(
                                        'escape' => false,
                                    )
                                );
                            }
                            ?>
                        </li>
                    </ul>
                </ul>

                <ul class="nav navbar-nav navbar-right">

                    <li>
                        <p class="text-white navbar-text fa-margin-right"
                           style="width: 90px; margin-top: 12px; margin-bottom: 17px;margin-left: 0px;margin-bottom: 0px;">
                            <span class="hour-clock"></span><span class="date-clock"></span></p>
                    </li>
                    <li class="dropdown">
                        <?php
                        $ruta_img = $this->Session->read('Auth.User.D_RUTA');
                        if (!empty($ruta_img)) {
                            $string = (strlen($this->Session->read('Auth.User.S_NOMBRE')) > 29) ? substr($this->Session->read('Auth.User.S_NOMBRE'), 0, 30) . '...' : $this->Session->read('Auth.User.S_NOMBRE');
                            $nombreCompleto = $this->Session->read('Auth.User.S_NOMBRE');
                            ?>
                            <a href="#" class="dropdown-toggle navbar-options" data-toggle="dropdown" role="button"
                               rel='tooltip' title="<?php echo $nombreCompleto ?>" aria-expanded="false">
                                <?php
                                echo $string;
                                if (Configure::read("ENVIROMENT") == 'LOCAL') {
                                    $image = '../uploads/usuarios/thumb_' . $this->Session->read('Auth.User.D_RUTA') . '?' . time();
                                } else {
                                    $image = $urlPHP . 'img' . DS . 'usuarios' . DS . 'thumb_' . $this->Session->read('Auth.User.D_RUTA') . '?' . time();
                                }

                                echo $this->Html->image(
                                    $image,
                                    array(
                                        'class' => 'img-rounded fa-margin-left fa-margin-right',
                                        'height' => '32',
                                    )
                                );
                                ?>
                            </a>
                        <?php } else {
                            $nombreCompleto = $this->Session->read('Auth.User.S_NOMBRE');
                            ?>
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" rel='tooltip'
                               title='<?php echo $nombreCompleto ?>'
                               aria-expanded="false">
                                <?php
                                $string = (strlen($this->Session->read('Auth.User.S_NOMBRE')) > 14) ? substr($this->Session->read('Auth.User.S_NOMBRE'), 0, 15) . '...' : $this->Session->read('Auth.User.S_NOMBRE');
                                echo $string;
                                echo $this->Html->tag(
                                    'i',
                                    '',
                                    array(
                                        'class' => 'fa fa-user fa-lg fa-margin-left fa-margin-right text-white'
                                    )
                                );
                                ?>
                            </a>
                        <?php } ?>
                        <ul class="dropdown-menu" role="menu">
                            <li>
                                <?php

                                echo $this->Html->link(
                                    '<i class="fa fa-edit fa-lg fa-margin-right text-info"></i><strong class="text-info">Editar Perfil</strong>',
                                    array('controller' => 'usuarios', 'action' => 'editProfile'),
                                    array('escape' => false));
                                ?>
                            </li>
                            <li>
                                <?php
                                echo $this->Html->link(
                                    '<i class="fa fa-edit fa-lg fa-margin-right text-info"></i><strong class="text-info">Reportar incidencia</strong>',
                                    '#',
                                    array(
                                        'escape' => false,
                                        'data-toggle' => 'modal',
                                        'data-target' => '#reportar_incidencia',
                                        'id' => 'menu_reportar_incidencia'
                                    )
                                );
                                ?>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <?php
                                echo $this->Html->link(
                                    '<i class="fa fa-cloud-download fa-lg fa-margin-right text-info"></i><strong class="text-info">Descargar Manual Web</strong>',
                                    'https://static.wixstatic.com/ugd/9fc6d5_3ccaedc860b245bdaafc2094a1aae5cb.pdf?dn=iTramit-Manual_de_Usuarios_Tramitacion-v5.pdf',
                                    array('escape' => false)
                                );
                                ?>
                            </li>
                            <li>
                                <?php
                                echo $this->Html->link(
                                    '<i class="fa fa-cloud-download fa-lg fa-margin-right text-info"></i><strong class="text-info">Descargar Manual APP</strong>',
                                    'https://static.wixstatic.com/ugd/9fc6d5_01fea3615a354e30a437d97f9fb54a16.pdf?dn=iTramit-Manual_APP_de_Operarios-v3.pdf',
                                    array('escape' => false)
                                );
                                ?>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <?php
                                echo $this->Html->link(
                                    '<i class="fa fa-power-off fa-lg fa-margin-right text-danger"></i><strong class="text-danger">Cerrar Sesión</strong>',
                                    array('controller' => 'main', 'action' => 'logout'),
                                    array('escape' => false));
                                ?>
                            </li>
                        </ul>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right"style="padding-right:1%";>
                    <li>
                        <?php
                        $ayuda = array(
                            'controller' => 'ayuda',
                            'action' => 'index'
                        );

                        echo $this->Html->link(
                            $this->Html->tag(
                                'i',
                                '',
                                array(
                                    'escape' => false,
                                    'class' => 'btn-tooltip fa fa-question-circle fa-lg fa-margin-right',
                                  'style'=> "font-size:30px;",
                                    'data-title' => 'Ayuda',
                                    'data-placement' => 'right',
                                    'data-toggle' => 'tooltip',
                                )
                            ),
                            $ayuda,
                            array(
                                'escape' => false,
                                'target' => '_blank'
                            )
                        );
                        ?>
                    </li>
                </ul>
            </div>
        </div>
        <?php echo $this->_View->element('modal_reportar_incidencia'); ?>
        <?php
    }
}

?>
<script>
    $(document).ready(function () {
        $('.dropdown-submenu a.test').on("click", function (e) {
            var is_visible = $(this).siblings('ul').first().is(":visible");
            if (!is_visible) {
                var pos = $(this).attr('data-pos');
                $('a.test').each(function (ind, element) {
                    if ($(element).attr('data-pos') != pos) {
                        if ($(element).next('ul').is(':visible')) {
                            $(element).next('ul').hide();
                        }
                    }
                });
            }
            $(this).next('ul').toggle();
            e.stopPropagation();
            e.preventDefault();
        });

        $('.dropdown').on("click", function (e) {
            $(this).children('.dropdown-menu').find('ul').hide();
        });
    });
</script>
