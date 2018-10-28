<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
<style>
    #dataTable tbody tr.selected {
        color:black;
        background-color: #66afe9;
    }

    .available{
        transition: box-shadow 0.1s linear 0.2s;
        box-shadow: 0 0 40px #0101DF;
    }
    .not_available{
        transition: box-shadow 0.1s linear 0.2s;
        box-shadow: 0 0 40px #FF0000;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    /* Hide default HTML checkbox */
    .switch input {display:none;}

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #2196F3;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>

<?php header('Access-Control-Allow-Origin: *'); ?>

<script type="text/javascript">
    var projectDirectory = '<?= Configure::read("PROJECT_DIRECTORY") ?>';
    $(document).ready(function () {

        $("#secciones .placeholder i").on('click',function(){
            var indice=document.getElementById('pkindiceStock').value;
            if(indice.length>0){
                switch($(this).attr('id')){
                    case 'analisisTécnico':
                        window.location = projectDirectory+"Analisis/index";
                        break;
                }

            }else{
                $("#secciones").find('img').removeClass('available');
                $("#secciones").find('img').removeClass('not_available');
                $(this).find('img').addClass('not_available');
            }
        })

        var table = $('#dataTable').DataTable();


        $("#searchindex").on('click',function () {
            buscarIndex();
        });

        $("#minDatosSwitcher").on('change',function () {
            if($('#chart_div').css('display') == 'none'){
                $('#chart_div').show('slow');
                $("#tableDataStock").hide('slow');
            } else {
                $("#chart_div").hide('slow');
                $("#tableDataStock").show('slow');
            }

        })

        $('.ampl').click(function () {
            $(this).parent().children('ol.tree').toggle(200);

        });
        $(function(){
            $('.tree-toggle').parent().children('ol.tree').toggle(200);
            /*if($(this).next().is(':visible')){
                $(this).attr('class', 'ampl fa fa-minus-square');
            }else{
                $(this).attr('class', 'ampl fa fa-plus-square');
            }*/
        });

        $('.DMalgo').on('click',function () {
            var dmId=$(this).data('id');
            var namedm;
            switch (dmId){
                case 1:
                    namedm='KMEANS3D';
                    break;
            }
            $('#mp-id').val(namedm);
            $('#mp-id-hidden').val(dmId);
            if($('#mp-id-hidden').val()==1){
                $("#modalParameter").modal();
            }else{
                aplicarDM();
            }

            //$("#modalParameter").modal();
        });

        /*$('#numeroMedias').on('change',function () {
            var nMedias = $(this).children(":selected").attr('value');
            var indicador=$('#mp-id').val();
            console.log(nMedias);
            console.log(indicador);

            if(indicador=='MACD'){
                $('#periodo1').show();
                $('#periodo2').show();
                $('#periodo3').show();
            }else{
                switch (nMedias){
                    case 1:
                        $('#periodo1').show();
                        break;
                    case 2:
                        $('#periodo1').show();
                        $('#periodo2').show();
                        break;
                    case 3:
                        $('#periodo1').show();
                        $('#periodo2').show();
                        $('#periodo3').show();
                        break;
                }
            }
        });*/

        $('#calcularIndicador').on('click',function () {
            var numeroIndicador = $('#mp-id-hidden').val();
            var p1=$('#mp-nclusters').val();

            var periodos=[];
            if(p1!=''){
                periodos.push(p1);
            }

            switch(numeroIndicador) {
                case '1':
                    kmeans3d(periodos);
                    break;
                case '2':
                    sma(periodos);
                    break;
                case '3':
                    ema(periodos);
                    break;
                case '4':
                    lag(periodos);
                    break;
                case '5':
                    smooth(periodos);
                    break;
                case '6':
                    smoothtrend(periodos);
            }
        })

    });



    function buscarIndex() {
        var symbol=$("#searchsymbol").val();
        var pk=$("#pk_ticker").val();
        var inicio=$("#searchinicio").val();
        var fin=$("#searchfin").val();
        var tipog=$("#tipoGrafica").val();

        $("#minDatos").show('slow');

        $.ajax({
            type: "POST",
            url: projectDirectory + '/Datamining/searchStockPrice',
            data: {'symbol':symbol,'inicio':inicio,'fin':fin,'pk':pk},
            async: true
        }).success(function (data) {
            data = JSON.parse(data);
            console.log(data);
            var jsonarray=data.precios;
            var array=data.precios.Price;
            //var statistics=data.statistics;

            var result = [];

            for($i=0;$i<array.length;$i++){
                var momentDate = moment(array[$i]['date']);
                array[$i]['date']=momentDate.format("YYYY-MM-DD");
            }
            console.log(array)
            console.log(jsonarray);
            //console.log(statistics);

            if (data.success == 1) {

                dt = $("#modalInformacionBuscada").DataTable({
                    data: data.precios.Price,
                    columns: [
                        {title: "Fecha", data: 'date', defaultContent: '',width:"60%"},
                        {title: "Close", data: 'close', defaultContent: ''},
                        {title: "High", data: 'high', defaultContent: ''},
                        {title: "Low", data: 'low', defaultContent: ''},
                        {title: "Open", data: 'open', defaultContent: ''},
                        {title: "SplitFactor", data: 'splitFactor', defaultContent: ''},
                        {title: "Volume", data: 'volume', defaultContent: ''},
                        {title: "AdjClose", data: 'adjClose', defaultContent: ''},
                        {title: "adjHigh", data: 'adjHigh', defaultContent: ''},
                        {title: "AdjLow", data: 'adjLow', defaultContent: ''},
                        {title: "AdjOpen", data: 'adjOpen', defaultContent: ''},
                        {title: "AdjVolume", data: 'adjVolume', defaultContent: ''},
                        {title: "DivCash", data: 'divCash', defaultContent: ''},
                    ]
                });

                /*dt2 = $("#modalStatistics").DataTable({
                    "paging":   false,
                    "ordering": false,
                    "info":     false,
                    searching: false,
                    data: data.statistics,
                    columns: [
                        {title: "Media", data: 'Media', defaultContent: ''},
                        {title: "Mediana", data: 'Mediana', defaultContent: ''},
                        {title: "Desv. Suma cuadrados", data: 'DESumCuad', defaultContent: ''},
                        {title: "Desv Population", data: 'DEPop', defaultContent: ''},
                        {title: "Varianza", data: 'Varianza', defaultContent: ''},
                    ]
                });*/

                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {
                    var data = new google.visualization.DataTable();

                    data.addColumn('datetime', 'Date');
                    data.addColumn('number',   'Close');

                    // 7. Cycle through the records, adding one row per record
                    array.forEach(function(packet) {
                        data.addRow([
                            (new Date(packet['date'])),
                            (packet['close']),
                        ]);
                    });

                    // 8. Create a new line chart
                    var chart = new google.visualization.LineChart(document.getElementById('chart_div'));

                    // 9. Render the chart, passing in our DataTable and any config data
                    chart.draw(data, {
                        title:  'Ambient Temperature (/test/temp)',
                        height: 150
                    });

                    var options = {
                        legend:'none'
                    };

                    chart.draw(data, options);
                };
            } else {
                $("#contenido-modal-error-asincrono").html("Ha ocurrido un error interno al obtener las novedades.");
                $("#modalErrorAsincrono").modal();
            }

        }).error(function () {
            $("#modalErrorAsincrono").modal();
        });
    }

    function aplicarDM() {
        var mlId = $('#mp-id-hidden').val();
        /*var p1=$('#mp-periodo1').val();
        var p2=$('#mp-periodo2').val();
        var p3=$('#mp-periodo3').val();*/


        /*var periodos=[];
        if(p1!=''){
            periodos.push(p1);
        }
        if(p2!=''){
            periodos.push(p2);
        }
        if(p3!=''){
            periodos.push(p3);
        }*/
        switch(mlId) {
            case '1':
                kmeans3d();
                break;
            case '2':
                sma(periodos);
                break;
            case '3':
                ema(periodos);
                break;
        }
    }

    function kmeans3d(periodos) {

        var symbol=$("#searchsymbol").val();
        var pk=$("#pk_ticker").val();
        var inicio=$("#searchinicio").val();
        var fin=$("#searchfin").val();


        $("#minDatos").show('slow');


        $.ajax({
            type: "POST",
            url: projectDirectory + '/datamining/km3d',
            data: {'symbol':symbol,'inicio':inicio,'fin':fin,'pk':pk,'periodos':periodos},
            async: true
        }).success(function (data) {
            data = JSON.parse(data);
            var jsonarray=data.tamGrupos;
            //var array=data.precios.Price;

            var result = [];

            /*
            for($i=0;$i<array.length;$i++){
                var momentDate = moment(array[$i]['date']);
                array[$i]['date']=momentDate.format("YYYY-MM-DD");
            }*/
            //console.log(array)
            console.log(jsonarray);


            /*console.log(data.precios);
            console.log(typeof data.precios.Price);
            console.log(data.precios.Price);*/
            if (data.success == 1) {

                /*dt = $("#modalInformacionBuscada").DataTable({
                    data: data.precios.Price,
                    columns: [
                        {title: "Fecha", data: 'date', defaultContent: '',width:"60%"},
                        {title: "Close", data: 'close', defaultContent: ''},
                        {title: "High", data: 'high', defaultContent: ''},
                        {title: "Low", data: 'low', defaultContent: ''},
                        {title: "Open", data: 'open', defaultContent: ''},
                        {title: "SplitFactor", data: 'splitFactor', defaultContent: ''},
                        {title: "Volume", data: 'volume', defaultContent: ''},
                        {title: "AdjClose", data: 'adjClose', defaultContent: ''},
                        {title: "adjHigh", data: 'adjHigh', defaultContent: ''},
                        {title: "AdjLow", data: 'adjLow', defaultContent: ''},
                        {title: "AdjOpen", data: 'adjOpen', defaultContent: ''},
                        {title: "AdjVolume", data: 'adjVolume', defaultContent: ''},
                        {title: "DivCash", data: 'divCash', defaultContent: ''},
                    ]
                });*/

                Plotly.d3.csv('/proyectofinal/app/webroot/kmeans.csv', function(err, rows){
                    function unpack(rows, key) {
                        return rows.map(function(row)
                        { return row[key]; });
                    }
                    console.log(rows);
                    var i=0;
                    var filas=[];
                    for(i=0;i<jsonarray[0];i++){
                        filas.push(rows[i]);
                    }
                    console.log(filas);
                    var trace1 = {
                        x:unpack(filas, 'x1'),  y: unpack(filas, 'y1'), z: unpack(filas, 'z1'),
                        mode: 'markers',
                        marker: {
                            size: 5,
                            line: {
                                color: 'rgba(255, 0, 0, 0.14)',
                                width: 0.5
                            },
                            opacity: 0.8
                        },
                        type: 'scatter3d'
                    };
                    filas=[];
                    for(i=jsonarray[0];i<jsonarray[0]+jsonarray[1];i++){
                        filas.push(rows[i]);
                    }
                    console.log(filas);
                    var trace2 = {
                        x:unpack(filas, 'x1'),  y: unpack(filas, 'y1'), z: unpack(filas, 'z1'),
                        mode: 'markers',
                        marker: {
                            size: 5,
                            line: {
                                color: 'rgba(255, 229, 0, 0.14)',
                                width: 0.5
                            },
                            opacity: 0.8
                        },
                        type: 'scatter3d'
                    };
                    filas=[];
                    for(i=jsonarray[1];i<jsonarray[1]+jsonarray[2];i++){
                        filas.push(rows[i]);
                    }
                    console.log(filas);
                    var trace3 = {
                        x:unpack(filas, 'x1'),  y: unpack(filas, 'y1'), z: unpack(filas, 'z1'),
                        mode: 'markers',
                        marker: {
                            size: 5,
                            line: {
                                color: 'rgba(0, 0, 250, 0.14)',
                                width: 0.5
                            },
                            opacity: 0.8
                        },
                        type: 'scatter3d'
                    };
                    filas=[];
                    for(i=jsonarray[2];i<jsonarray[2]+jsonarray[3];i++){
                        filas.push(rows[i]);
                    }
                    console.log(filas);
                    var trace4 = {
                        x:unpack(filas, 'x1'),  y: unpack(filas, 'y1'), z: unpack(filas, 'z1'),
                        mode: 'markers',
                        marker: {
                            size: 5,
                            line: {
                                color: 'rgba(255, 167, 178, 0.14)',
                                width: 0.5
                            },
                            opacity: 0.8
                        },
                        type: 'scatter3d'
                    };
                    filas=[];
                    for(i=jsonarray[3];i<jsonarray[3]+jsonarray[4];i++){
                        filas.push(rows[i]);
                    }
                    console.log(filas);
                    var trace5 = {
                        x:unpack(filas, 'x1'),  y: unpack(filas, 'y1'), z: unpack(filas, 'z1'),
                        mode: 'markers',
                        marker: {
                            size: 5,
                            line: {
                                color: 'rgba(217, 217, 217, 0.14)',
                                width: 0.5
                            },
                            opacity: 0.8
                        },
                        type: 'scatter3d'
                    };
                    var data = [trace1,trace2,trace3,trace4,trace5];
                    var layout = {
                        dragmode: false,
                        margin: {
                            l: 0,
                            r: 0,
                            b: 0,
                            t: 0
                        }};
                    Plotly.newPlot('myDiv', data, layout);
                });

                /*google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {
                    var data = new google.visualization.DataTable();

                    data.addColumn('datetime', 'Date');
                    data.addColumn('number',   'Close');

                    // 7. Cycle through the records, adding one row per record
                    array.forEach(function(packet) {
                        data.addRow([
                            (new Date(packet['date'])),
                            (packet['close']),
                        ]);
                    });

                    // 8. Create a new line chart
                    var chart = new google.visualization.LineChart(document.getElementById('chart_div'));

                    // 9. Render the chart, passing in our DataTable and any config data
                    chart.draw(data, {
                        title:  'Ambient Temperature (/test/temp)',
                        height: 150
                    });

                    var options = {
                        legend:'none'
                    };

                    chart.draw(data, options);
                };*/



            } else {
                $("#contenido-modal-error-asincrono").html("Ha ocurrido un error interno al obtener las novedades.");
                $("#modalErrorAsincrono").modal();
            }

        }).error(function () {
            $("#modalErrorAsincrono").modal();
        });
    }
</script>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12 main">
            <h1 class="page-header">Dashboard</h1>
            <div id="secciones" class="row placeholders">
                <div class="col-xs-6 col-sm-3 placeholder">
                    <i id="analisisTécnico"><img src="<?php $projectDirectory?>/proyectofinal/app/webroot/img/analisis_tecnico.png" width="200" height="200" class="img" alt="Generic placeholder thumbnail"></i>
                    <h4>Análisis Técnico</h4>
                    <span class="text-muted">Charts</span>
                </div>
                <div class="col-xs-6 col-sm-3 placeholder">
                    <i id="mineriaDatos"><img src="<?php $projectDirectory?>/proyectofinal/app/webroot/img/data_mining.png" width="200" height="200" class="img" alt="Generic placeholder thumbnail"></i>
                    <h4>Data Mining</h4>
                    <span class="text-muted">Preprocess</span>
                </div>
                <div class="col-xs-6 col-sm-3 placeholder">
                    <i id="machineLearning"><img src="<?php $projectDirectory?>/proyectofinal/app/webroot/img/machine_learning.jpg" width="200" height="200" class="img" alt="Generic placeholder thumbnail"></i>
                    <h4>Machine Learning</h4>
                    <span class="text-muted">Forecasting</span>
                </div>
                <div class="col-xs-6 col-sm-3 placeholder">
                    <i id="fractal"><img src="<?php $projectDirectory?>/proyectofinal/app/webroot/img/fractal.jpg" width="200" height="200" class="img" alt="Generic placeholder thumbnail"></i>
                    <h4>Fractal</h4>
                    <span class="text-muted">Forecasting</span>
                </div>
            </div>


            <h2 class="sub-header">Data Mining</h2>

            <?php
            echo $this->Form->hidden('PK_INDICE_STOCK',array(
                'id'=>'pkindiceStock',
            ));
            echo $this->Form->hidden('NOMBRE_INDICE_STOCK',array(
                'id'=>'nombreindiceStock',
            ));
            ?>

            <div id="principal" class="panel-body">
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <strong><?php $tickers['Ticker']['SYMBOL'] ?></strong>
                            </div>
                            <div class="panel-body">
                                <?php echo $this->Form->create('Ticker', array(
                                    'type' => 'file',
                                    'name'=>'Ticker',
                                    'id'=>'Ticker'
                                )); ?>
                                <div class="row">
                                    <?php
                                    echo $this->Form->input('SYMBOL', array(
                                        'label' => 'Symbol',
                                        'id'=>'searchsymbol',
                                        'class' => 'form-control input-sm',
                                        'div' => array('class' => 'form-group col-md-3'),
                                        'value'=>$tickers['Ticker']['SYMBOL']));
                                    ?>
                                    <?php
                                    echo $this->Form->hidden('PK_TICKER',array(
                                        'id'=>'pk_ticker',
                                        'value'=>$tickers['Ticker']['PK_TICKER']
                                    ));
                                    ?>

                                    <div class='form-group col-md-3'>
                                        <?php
                                        echo $this->Form->label('Inicio'); ?>
                                        <br>
                                        <?php
                                        echo $this->Form->date('Inicio', array(
                                            'label' => 'Inicio',
                                            'class' => 'form-control input-sm',
                                            'id'=>'searchinicio',
                                            'div' => array('class' => 'form-group col-md-3')));
                                        ?>
                                    </div>
                                    <div class='form-group col-md-3'>
                                        <?php
                                        echo $this->Form->label('Fin'); ?>
                                        <br>
                                        <?php
                                        echo $this->Form->date('Fin', array(
                                            'label' => 'Fin',
                                            'class' => 'form-control input-sm',
                                            'id'=>'searchfin',
                                            'div' => array('class' => 'form-group col-md-3')));
                                        ?>
                                    </div>
                                    <div class="col-md-1">
                                        <br>
                                        <?php //echo $this->Html->link('<i class="fa fa-plus-square fa-margin-right"></i> Nuevo Mensaje', array('action' => 'add'), array('escape' => false)); ?>
                                        <?php echo $this->form->button('Representar',array(
                                            'type'=>'button',
                                            'id'=>'searchindex',
                                            'class'=>'btn btn-info btn-sm',
                                            'style' => array(
                                                'padding:5px;margin-top:5px;')
                                        ))?>
                                    </div>
                                    <div class="col-md-2">
                                        <?php echo $this->Form->end(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="minDatos" style="display: none" class="row">
                    <div id="panelGraficaPrincipal" class="col-md-12">
                        <div class="panel panel-primary">
                            <div id="minDatosHeading" class="panel-heading">
                                <div class="row">
                                    <div class="col-md-1"><h4>Chart</h4></div>
                                    <div class="col-md-1"><label class="switch"><input id="minDatosSwitcher" style="margin-top:20%;" type="checkbox"><span class="slider round"></span></label></div>
                                    <div class="col-md-1"><h4>Data</h4></div>
                                </div>
                            </div>
                            <div id="minDatosBody" class="panel-body">
                                <div class="row" id="divchart">
                                    <div class="col-sm-1">
                                        <?php
                                        echo $this->Form->label('DataMining'); ?>
                                        <br>
                                        <?php
                                        createTreeView(0,$menu);
                                        function createTreeView($parent, $menu) {
                                            if (isset($menu['parents'][$parent])) { ?>
                                                <ol class="nav-list tree"><?php
                                                foreach ($menu['parents'][$parent] as $itemId) {
                                                    $clase = isset($menu['parents'][$itemId]) ? 'tree-toggle nav-header"' : ''; ?>
                                                    <li>
                                                        <label class="<?= $clase ?>">
                                                            <a class="DMalgo" href="#" data-id="<?= $itemId ?>">
                                                                <?= $menu['items'][$itemId]['Datamining']['NAME'] ?>
                                                            </a>
                                                        </label>
                                                        <?php if (!empty($clase)) { ?>
                                                            <i class="ampl fa fa-plus-square"></i>
                                                            <?= createTreeView($itemId, $menu) ?>
                                                        <?php } ?>
                                                    </li>
                                                <?php } ?>
                                                </ol><?php
                                            }
                                        }  ?>
                                    </div>
                                    <div class="col-sm-11" id="graficas">
                                        <div class="container" id="chart_div" style="width: 900px; height: 500px;"></div>
                                        <div id="myDiv" style="width:100%;height:100%"></div>
                                    </div>
                                </div>
                                <div class="row" id="divtable" style="display: none;">
                                    <div id="tableDataStock" class="modal-body">
                                        <table id="modalInformacionBuscada" class="table table-striped table-hover tableFacturas"></table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modalParameter" tabindex="-2" role="dialog" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header modal-header-info">
                                    <button type="button" class="close modal-close" data-dismiss="modal"><span
                                                aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                                    <h4 class="modal-title">Parametros</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <?php
                                            echo $this->Form->input('PK_INDICADOR', array('id' => 'mp-id', 'label' => 'LAG:', 'div' => array('class' => 'form-group'), 'type' => 'text', 'class' => 'form-control input-sm','disabled'=>'disabled'));
                                            echo $this->Form->hidden('PK_INDICADOR',array(
                                                'id'=>'mp-id-hidden',
                                            ));
                                            ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4" id="periodo1">
                                            <?php
                                            echo $this->Form->input('NClusters', array('id' => 'mp-nclusters', 'label' => 'Numero de clusters:', 'div' => array('class' => 'form-group'), 'type' => 'number', 'class' => 'form-control input-sm'));
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <?php echo $this->Form->button("Calcular", array(
                                        'id'=>'calcularIndicador',
                                        'div' => false,
                                        'data-dismiss' => 'modal',
                                        'class' => 'btn btn-default btn-sm'
                                    ));?>
                                    <?php
                                    echo $this->Form->button("Cancelar", array(
                                        'div' => false,
                                        'data-dismiss' => 'modal',
                                        'class' => 'btn btn-default btn-sm'
                                    ));?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
