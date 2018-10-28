<?php
App::uses('AppHelper', 'View/Helper');

class MenuHelper extends AppHelper
{

    public $helpers = array('Html', 'Session', 'Filter', 'Form');

    public function menuLeftReparadora($t, $id_user, $tipo_taller, $etiquetas_propietario)
    {
        $this->Filter->showFilters($t, $id_user, $tipo_taller, $etiquetas_propietario);
    }

    public function menuRightReparadora($visitas, $usuarios_con_permisos, $operarios){ ?>
        <div id="tableRight">
            <ul class="list-unstyled">
                <li>
                    <a class="btn btn-primary btn-block btn-sm text-uppercase" href="#" role="button" onclick="calendar();">
                        <i class="fa fa-arrows-alt fa-margin-right fa-lg"></i>
                        <strong> Extender ventana </strong>
                    </a>
                </li>
            </ul>
            <div class="row">
                <div class="col-md-12">
                    <?php if(empty($usuarios_con_permisos)){
                        $propietario = (int)$this->Auth->user('FK_PROPIETARIO');
                        $usuarios_con_permisos = Cache::read("p{$propietario}.usuarios_tramitadores");
                    } ?>
                    <?=$this->Form->select('filter-visit-owner', $usuarios_con_permisos, array(
                        'class' => ' text-info form-control input-sm', 'empty' => false
                    ))?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?php
                        $estDaily = array(
                            0 => 'Todos los estados ',
                            1 => 'C - Citadas ',
                            2 => 'O - Por confirmar ',
                            3 => 'E - En curso ',
                            4 => 'P - Pte. Parte ',
                            5 => 'F - Finalizadas ',
                            6 => 'S - Citas próximas ',
                            7 => 'R - Operarios retrasados ',
                            8 => 'M - Vis. retrasadas (en margen) ',
                            9 => 'V - Vis. retrasadas '
                        );
                        echo $this->Form->select('filter-visit-status', $estDaily, array(
                            'class' => ' text-info form-control input-sm', 'default' => 0, 'empty' => false
                        ));
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?=$this->Form->select('filter-visit-oper',
                    $operarios, array(
                        'class' => ' text-info form-control input-sm',
                        'default' => 0,
                        'empty' => array(0 => 'Todos los operarios')
                    ))?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div role="tabpanel">
                        <ul class="nav nav-pills nav-justified" role="tablist">
                            <li role="presentation">
                                <a href="#ayer" aria-controls="home" role="tab" data-toggle="tab" class="button btn-sm">
                                    <strong>
                                        <small>
                                            <i class="fa fa-chevron-left fa-margin-right"></i> AYER
                                        </small>
                                    </strong>
                                </a>
                            </li>
                            <li role="presentation" class="active">
                                <a href="#hoy" aria-controls="home" role="tab" data-toggle="tab" class="button btn-sm">
                                    <strong>
                                        <small>HOY</small>
                                    </strong>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#manana" aria-controls="home" role="tab" data-toggle="tab"
                                   class="button btn-sm">
                                    <strong>
                                        <small>
                                            MAÑ. <i class="fa fa-chevron-right fa-margin-left"></i>
                                        </small>
                                    </strong>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane" id="ayer">
                                <table class="table table-bordered table-striped text-center table-responsive"
                                       cellspacing="0"
                                       role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th class="text-center">H</th>
                                        <th class="text-center">G</th>
                                        <th class="text-center">O</th>
                                        <th class="text-center">T</th>
                                        <th class="text-center">E</th>
                                        <th class="text-center"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php echo $this->generateTableRight($visitas['AYER']); ?>
                                    </tbody>
                                </table>
                            </div>
                            <div role="tabpanel" class="tab-pane active" id="hoy">
                                <table class="table table-bordered table-striped text-center table-responsive"
                                       cellspacing="0"
                                       role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th class="text-center">H</th>
                                        <th class="text-center">G</th>
                                        <th class="text-center">O</th>
                                        <th class="text-center">T</th>
                                        <th class="text-center">E</th>
                                        <th class="text-center"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php echo $this->generateTableRight($visitas['HOY']); ?>
                                    </tbody>
                                </table>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="manana">
                                <table class="table table-bordered table-striped text-center table-responsive"
                                       cellspacing="0"
                                       role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th class="text-center">H</th>
                                        <th class="text-center">G</th>
                                        <th class="text-center">O</th>
                                        <th class="text-center">T</th>
                                        <th class="text-center">E</th>
                                        <th class="text-center"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php echo $this->generateTableRight($visitas['MANANA']); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }

    public function estadoPrioritario($item){
        if (strpos($item['E'],'Citado') !== false) {
            if (strpos($item['E'],'OperarioRetrasado') !== false) {
                return 'R';
            }
            if (strpos($item['E'],'VisitaRetrasada_b') !== false) {
                return 'M';
            }else if (strpos($item['E'],'VisitaRetrasada_r') !== false) {
                return 'V';
            }
            if (strpos($item['E'],'CitaProxima') !== false) {
                return 'S';
            }
            return 'C';
        } else if (strpos($item['E'],'En curso') !== false) {
            return 'E';
        } else if (strpos($item['E'],'Pte. Parte') !== false) {
            return 'P';
        } else if (strpos($item['E'],'Finalizado') !== false) {
            return 'F';
        } else {
            return 'O';
        }
    }

    public function generateTableRight($data)
    {
        if (!empty($data)) {
            foreach ($data as $item) {
                if(is_null($item)){
                    continue;
                }
                $date = date_create($item['H']);
                if (strpos($item['E'],'Citado') !== false) {
                    $class = 'citada';
                    $class2 = 'Citado';
                } else if (strpos($item['E'],'En curso') !== false) {
                    $class = 'curso';
                    $class2 = 'En curso';
                } else if (strpos($item['E'],'Pte. Parte') !== false) {
                    $class = 'parte';
                    $class2 = 'Pte. Parte';
                } else if (strpos($item['E'],'Finalizado') !== false) {
                    $class = 'finalizada';
                    $class2 = 'Finalizado';
                } else {
                    $class = 'confirmar';
                    $class2 = 'Por Confirmar';
                }
                if (strpos($item['E'],'OperarioRetrasado') !== false) {
                    $class .= ' op_retrasado';
                    $class2 .= ' - Operario Retrasado';
                }
                if (strpos($item['E'],'VisitaRetrasada_b') !== false) {
                    $class .= ' margen';
                    $class2 .= ' - Visita Retrasada (dentro de margen)';
                }
                if (strpos($item['E'],'VisitaRetrasada_r') !== false) {
                    $class .= ' v_retrasada';
                    $class2 .= ' - Visita Retrasada';
                }
                if (strpos($item['E'],'CitaProxima') !== false) {
                    $class .= ' c_proxima';
                    $class2 .= ' - Cita Proxima';
                }
                $item['DIR'] = str_replace('"', '', $item['DIR']); ?>
                <tr role="row" class="visit-status visit-owner" data-status="<?php echo $class; ?>"
                    data-owner="<?= $item['OWNER'] ?>" data-oper="<?=$item['OPER']?>">
                    <td class="<?= $item['COLOR'] ?> btn-tooltip" data-title="<?= $item['DIR'] ?>"
                        data-toogle="tooltip" data-placement="left" data-html="true">
                        <strong><?php echo date_format($date, 'H:i'); ?></strong>
                    </td>
                    <td class="btn-tooltip" data-title="<?= h($item['G']) ?>"
                        data-toogle="tooltip"><?= mb_substr($item['G'], 0 ,1) ?>
                    </td>
                    <td class="btn-tooltip" data-title="<?= h($item['O']) ?>"
                        data-toogle="tooltip"><?= mb_substr($item['O'], 0, 1) ?>
                    </td>
                    <td class="btn-tooltip" data-title="<?= h($item['T']) ?>"
                        data-toogle="tooltip"><?= mb_substr($item['T'], 0 , 1) ?>
                    </td>
                    <td class="btn-tooltip" data-title="<?=h($class2)?>" data-toogle="tooltip">
                        <?php echo h($this->estadoPrioritario($item)); ?>
                    </td>
                    <td><?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-link')), array('controller' => 'siniestros', 'action' => 'manageVisit', $item['ID']), array('escape' => false)); ?></td>
                </tr>
            <?php }
        }
    }
}
?>