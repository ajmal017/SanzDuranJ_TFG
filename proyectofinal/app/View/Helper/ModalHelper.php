<?php
App::uses('AppHelper', 'View/Helper');

class ModalHelper extends AppHelper
{

    public $helpers = array('Html', 'Text');

    public $type = array(0 => 'modal-header-success', 1 => 'modal-header-info', 2 => 'modal-header-warning', 3 => 'modal-header-danger');
    public $icons = array(0 => 'fa-check', 1 => 'fa-info', 2 => 'fa-exclamation', 3 => 'fa-ban');
    public $buttons = array(0 => 'btn-success', 1 => 'btn-info', 2 => 'btn-warning', 3 => 'btn-danger');

    public function showModal($content, $url = NULL)
    {
        if (!Hash::check($this->type, $content['type'])) {
            $content = array(
                'title' => 'Error al crear modal',
                'content' => 'Los parámetros utilizados para crear el componente no son correctos.',
                'type' => 3
            );
            $url = NULL;
        }
        $content['encode'] = !empty($content['encode']) ? $content['encode'] : 'text';
        ?>
        <div id="<?php echo $content['id']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header <?php echo h($this->type[$content['type']]); ?>">
                        <button type="button" class="close modal-close" data-dismiss="modal"><span
                                aria-hidden="true">×</span><span class="sr-only">Cerrar</span></button>
                        <h4 class="modal-title"><i
                                class="fa <?php echo h($this->icons[$content['type']]); ?> fa-margin-right"></i>
                            <strong><?php echo h($content['title']); ?></strong>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <p>
                            <?= $content['encode']=='text' ? h($content['content']) : $content['content'] ?>
                        </p>
                        <?php if (!empty($content['content_ext'])) { ?>
                            <div class="panel-group" id="accordion" role="tablist">
                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab">
                                        <h6 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#content_ext">
                                                Mensaje ampliado
                                            </a>
                                        </h6>
                                    </div>
                                    <div id="content_ext" class="panel-collapse collapse" role="tabpanel">
                                        <div class="panel-body">
                                            <?= h($content['content_ext']) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }
                        if (isset($content['datetime'])) { ?>
                            <p>
                                <br>Fecha de pago:
                                <div class="input-group">
                                    <input name="datetime" class="form-control datedayhour" value="<?=date('H:i d-m-Y')?>" type="text" id="datetime-modal">                                        <div class="input-group-addon">
                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </p>
                        <?php }
                        if (isset($content['payform'])) { ?>
                            <p>
                                <br>Forma de pago:
                                <input name="payform" class="form-control" type="text" id="payform-modal" />
                            </p>
                        <?php } ?>
                    </div>
                    <?php if (!empty($url)) { ?>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cerrar</button>
                            <?php
                            if (!empty($url)) {
                                if (!empty($url['param'])) {
                                    if(is_array($url['param']) && count($url['param'])>1){
                                        echo $this->Html->link(h($content['button']), array('controller' => $url['controller'], 'action' => $url['action'], $url['param'][0], $url['param'][1]), array('class' => 'btn btn-sm ' . h($this->buttons[$content['type']]), 'escape' => false));
                                    }else{
                                        echo $this->Html->link(h($content['button']), array('controller' => $url['controller'], 'action' => $url['action'], $url['param']), array('class' => 'btn btn-sm ' . h($this->buttons[$content['type']]), 'escape' => false));
                                    }
                                } else {
                                    echo $this->Html->link(h($content['button']), array('controller' => $url['controller'], 'action' => $url['action']), array('class' => 'btn btn-sm ' . h($this->buttons[$content['type']]), 'escape' => false));
                                }
                            }
                            ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php if (!empty($url)): ?>
            <script>
                $(document).ready(function () {
                    $("div#<?php echo $content['id']; ?> div.modal-footer a.btn").on('click', function (e) {
                        $("div#<?php echo $content['id']; ?> div.modal-footer a.btn").attr('disabled', true);
                    });
                });
            </script>
        <?php endif; ?>
        <?php
    }

    public function showModalSelection($title, $id, $input, $data_skel, $data, $extra = null)
    {
        $model = array_keys($data_skel);
        $model = $model[0];
        $size = sizeof(array_values($data_skel[$model]));
        $fields = array_keys($data_skel[$model]);
        $fields_name = array_values($data_skel[$model]);
        ?>
        <div class="modal fade" id="<?php echo $id; ?>" tabindex="-1" role="dialog" aria-hidden="false">
            <script>
                $(document).ready(function () {
                    window.tableData_<?=str_replace('-','_',$id)?> = $(<?php echo '"#'.$id.'-selection"';?>).DataTable();
                });
            </script>
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header modal-header-info">
                        <button type="button" class="close modal-close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span
                                class="sr-only">Close</span></button>
                        <h4 class="modal-title"><?php echo h($title); ?></h4>
                    </div>
                    <div class="modal-body">
                        <table id="<?php echo $id . '-selection'; ?>" class="table table-striped table-hover"
                               cellspacing="0"
                               role="grid">
                            <thead>
                            <tr role="row">
                                <?php foreach ($fields as $key => $field) { ?>
                                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">
                                        <?php echo h($fields_name[$key]); ?>
                                    </th>
                                <?php } ?>
                                <th class="sorting" tabindex="0" rowspan="1" colspan="1">
                                    Acción
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($data as $key => $value): ?>
                                <tr role="row" class="<?php echo 'value-' . $key; ?>"
                                    <?php if(!empty($input['cod'])){
                                        echo 'cod="'.$value[$model][$input['cod']].'"';
                                    } ?> >
                                    <?php for ($i = 0; $i < $size; $i++) { ?>
                                        <td><?php echo $this->Text->truncate(h($value[$model][$fields[$i]]), 50, array('ellipsis' => '...')); ?></td>
                                    <?php } ?>
                                    <td><a href="#" id="<?php echo $id . '-link-' . $key; ?>"
                                           class="dataTables_link label label-primary extra"
                                           data-dismiss="modal">Seleccionar</a>
                                    </td>
                                    <script>
                                        $(<?php echo '"#'.$id.'-link-'.$key.'"';?>).on("click", function (event) {
                                            event.preventDefault();
                                            $(<?php echo '".'.$id.'-input"';?>).val(<?php echo '"'.$value[$model][$input[1]].'"';?>);
                                            $(<?php echo '".'.$id.'-input-hidden"';?>).val(<?php echo '"'.$value[$model][$input[0]].'"';?>);
                                            $(<?php echo '".'.$id.'-input-hidden"';?>).change();

                                            <?php if(!empty($input[2])){?>
                                            $(<?php echo '".'.$id.'-input-hidden-extra"';?>).val(<?php echo '"'.$value[$model][$input[2]].'"';?>);
                                            <?php } ?>

                                            <?php
                                            if(!(empty($extra))){
                                               if(in_array('siniestro_add_compania',$extra)){
                                           ?>

                                            $.ajax({
                                                type: "POST",
                                                url: projectDirectory + '/' + 'companias_has_companiasasegurados/getCompaniasHasCompaniasasegurados',
                                                data: {token: $(<?php echo '".'.$id.'-input-hidden"';?>).val()}
                                            }).success(function (data) {
                                                data = JSON.parse(data);
                                                if (data.length == 1) {
                                                    $("#SiniestroLCOMPANIAASEGURADO").val(data[0]['Companiasasegurado']['D_COMPANIAASEGURADO']);
                                                    $("#modal-table-companiasasegurado-id").val(data[0]['Companiasasegurado']['PK_COMPANIAASEGURADO']);
                                                    $("#modal-table-compania-name").val(data[0]['Companiasasegurado']['D_COMPANIAASEGURADO']);
                                                } else {
                                                    $("#SiniestroLCOMPANIAASEGURADO").val("");
                                                    $("#modal-table-companiasasegurado-id").val("");
                                                }
                                                $("#SiniestroSEXPEDIENTECIA").attr('disabled', false);
                                            });

                                            <?php
                                             }
                                            }
                                            ?>
                                            $().modal('close');
                                        });
                                    </script>
                                </tr>
                            <?php endforeach; ?>
                            <tfoot>
                            <tr>
                                <?php foreach ($fields as $key => $field) { ?>
                                    <th rowspan="1" colspan="1">
                                        <?php echo h($fields_name[$key]); ?>
                                    </th>
                                <?php } ?>
                                <th rowspan="1" colspan="1">
                                    Acción
                                </th>
                            </tr>
                            </tfoot>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function showModalSelection1($title, $id, $input, $data_skel, $data)
    {
        $model = array_keys($data_skel);
        $model = $model[0];
        $size = sizeof(array_values($data_skel[$model]));
        $fields = array_keys($data_skel[$model]);
        $fields_name = array_values($data_skel[$model]);
        ?>
        <div class="modal fade" id="<?php echo $id; ?>" tabindex="-1" role="dialog" aria-hidden="false">
            <script>
                $(document).ready(function () {
                    window.tableData_<?=str_replace('-','_',$id)?> = $(<?php echo '"#'.$id.'-selection"';?>).DataTable();
                });
            </script>
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header modal-header-info">
                        <button type="button" class="close modal-close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span
                                class="sr-only">Close</span></button>
                        <h4 class="modal-title"><?php echo h($title); ?></h4>
                    </div>
                    <div class="modal-body">
                        <table id="<?php echo $id . '-selection'; ?>" class="table table-striped table-hover"
                               cellspacing="0"
                               role="grid">
                            <thead>
                            <tr role="row">
                                <?php foreach ($fields as $key => $field) { ?>
                                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">
                                        <?php echo h($fields_name[$key]); ?>
                                    </th>
                                <?php } ?>
                                <th class="sorting" tabindex="0" rowspan="1" colspan="1">
                                    Acción
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($data as $key => $value): ?>
                                <tr role="row" class="<?php echo 'value-' . $key; ?>"
                                    <?php if(!empty($input['cod'])){
                                        echo 'cod="'.$value[$model][$input['cod']].'"';
                                    } ?> >
                                    <?php for ($i = 0; $i < $size; $i++) { ?>
                                        <td><?php echo $this->Text->truncate(h($value[$model][$fields[$i]]), 50, array('ellipsis' => '...')); ?></td>
                                    <?php } ?>
                                    <td><a href="#" id="<?php echo $id . '-link-' . $key; ?>"
                                           class="dataTables_link label label-primary extra"
                                           data-dismiss="modal">Seleccionar</a>
                                    </td>
                                    <script>
                                        $(<?php echo '"#'.$id.'-link-'.$key.'"';?>).on("click", function (event) {
                                            event.preventDefault();
                                            $(<?php echo '".'.$id.'-input"';?>).val(<?php echo '"'.$value[$model][$input[1]].'"';?>);

                                            $(<?php echo '".'.$id.'-input-hidden"';?>).val(<?php echo '"'.$value[$model][$input[0]].'"';?>);
                                            $(<?php echo '".'.$id.'-input-hidden2"';?>).val(<?php echo '"'.$value[$model][$input[2]].'"';?>);

                                            $(<?php echo '".'.$id.'-input-hidden"';?>).change();

                                            <?php if(!empty($input[2])){?>
                                            $(<?php echo '".'.$id.'-input-hidden-extra"';?>).val(<?php echo '"'.$value[$model][$input[2]].'"';?>);
                                            <?php } ?>
                                            $().modal('close');
                                        });
                                    </script>
                                </tr>
                            <?php endforeach; ?>
                            <tfoot>
                            <tr>
                                <?php foreach ($fields as $key => $field) { ?>
                                    <th rowspan="1" colspan="1">
                                        <?php echo h($fields_name[$key]); ?>
                                    </th>
                                <?php } ?>
                                <th rowspan="1" colspan="1">
                                    Acción
                                </th>
                            </tr>
                            </tfoot>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
}

?>