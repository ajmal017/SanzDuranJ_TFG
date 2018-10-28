<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
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

    .filamenulateral{
        display: flex;
        display: -webkit-flex;
        flex-wrap: wrap;

    }

    .menulateral{
        border-right: #337ab7;
        border-right-width: 1px;
        border-right-style: solid;
        border-bottom-right-radius: 4px;
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
            if(!Date.parse($("#searchinicio").val())||!Date.parse($("#searchfin").val())){

            }else{
                buscarIndex();
            }
        });

        $("#analisisTecSwitcher").on('change',function () {
            if($('#divchart').css('display') == 'none'){
                $('#divchart').show('slow');
                $("#divtable").hide('slow');
            } else {
                $("#divchart").hide('slow');
                $("#divtable").show('slow');
            }
        });

        $('.TecSwitcher').on('change',function () {
            console.log("hola");
            console.log($(this).html());
        });



        $('.ampl').click(function () {
            $(this).parent().children('ol.tree').toggle(200);

        });
        $(function(){
            $('.tree-toggle').parent().children('ol.tree').toggle(200);
        });

        $('.IndicadorTecnico').on('click',function () {
            var indicadorId=$(this).data('id');
            var nameIndicador;
            switch (indicadorId){
                case 1:
                    nameIndicador='MACD';
                    break;
                case 2:
                    nameIndicador='SMA';
                    break;
                case 3:
                    nameIndicador='EMA';
                    break;
                case 4:
                    nameIndicador='SOPORTE';
                    break;
            }
            $('#mp-id').val(nameIndicador);
            $('#mp-id-hidden').val(indicadorId);
            if($('#mp-id-hidden').val()==4){
                soporte();
            }else{
                $("#modalParameter").modal();
            }
        });


        $('#calcularIndicador').on('click',function () {
            var numeroIndicador = $('#mp-id-hidden').val();
            var indicador=$('#mp-id').val();
            var method=$('#mp-method').val();
            var from=$('#mp-rodfrom').val();

            rodogramPrediction(indicador);

        })
    });

    function buscarIndex() {
        var symbol=$("#searchsymbol").val();
        var pk=$("#pk_ticker").val();
        var inicio=$("#searchinicio").val();
        var fin=$("#searchfin").val();
        var tipog=$("#tipoGrafica").val();

        console.log(symbol);
        console.log(inicio);

        $('#mp-id').val(symbol);
        $('#mp-method').val("Rodogram");
        //$('#mp-rodfrom').val(inicio);

        $("#analisisTec").show('slow');

        $.ajax({
            type: "POST",
            url: projectDirectory + '/Fractal/searchStockPrice',
            data: {'symbol':symbol,'inicio':inicio,'fin':fin,'pk':pk},
            async: true
        }).success(function (data) {
            data = JSON.parse(data);
            var jsonarray=data.precios;
            var array=data.precios.Price;

            var result = [];

            for($i=0;$i<array.length;$i++){
                var momentDate = moment(array[$i]['date']);
                array[$i]['date']=momentDate.format("YYYY-MM-DD");
            }
            console.log(array)
            console.log(jsonarray);

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

                if(tipog==0){
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
                        /*chart.draw(data, {
                            title:  'Ambient Temperature (/test/temp)',
                            height: 150
                        });*/

                        var options = {
                            legend:'none',
                            title: symbol + ' Close',
                            chartArea: {
                                backgroundColor: {
                                    stroke: '#4322c0',
                                    strokeWidth: 0.5
                                }
                            },
                            explorer: {
                                axis: 'horizontal',
                                keepInBounds: true,
                                maxZoomIn: 20.0,
                                maxZoomOut: 1
                            },
                        };

                        chart.draw(data, options);
                    };
                }else if(tipog==1){
                    google.charts.load('current', {'packages':['corechart']});

                    function drawChart() {
                        var data = new google.visualization.DataTable();

                        data.addColumn('datetime', 'Date');
                        data.addColumn('number',   'Low');
                        data.addColumn('number',   'Open');
                        data.addColumn('number',   'Close');
                        data.addColumn('number',   'High');

                        // 7. Cycle through the records, adding one row per record
                        array.forEach(function(packet) {
                            data.addRow([
                                (new Date(packet['date'])),
                                (packet['low']),
                                (packet['open']),
                                (packet['close']),
                                (packet['high']),
                            ]);
                        });

                        // 8. Create a new line chart

                        // 9. Render the chart, passing in our DataTable and any config data


                        var options = {
                            legend:'none',
                            chartArea: {
                                backgroundColor: {
                                    stroke: '#4322c0',
                                    strokeWidth: 0.5
                                }
                            },
                            //bar: { groupWidth: '100%' },   // Remove space between bars.
                            candlestick: {
                                fallingColor: { strokeWidth: 0, fill: '#a52714' }, // red
                                risingColor: { strokeWidth: 0, fill: '#0b6c9d' }   // green
                            }
                        };

                        var chart = new google.visualization.CandlestickChart(document.getElementById('chart_div'));
                        chart.draw(data, options);

                        //var cSticks = document.querySelectorAll('rect[fill="#a52714"]');

                        /*for(var i=0;i<cSticks.length;i++){
                            cSticks[i].setAttribute('fill', '#0f9d58');
                            cSticks[i].setAttribute('stroke', '#0f9d58');
                        }*/
                    };
                    google.charts.setOnLoadCallback(drawChart);
                }



            } else {
                $("#contenido-modal-error-asincrono").html("Ha ocurrido un error interno al obtener las novedades.");
                $("#modalErrorAsincrono").modal();
            }

        }).error(function () {
            $("#modalErrorAsincrono").modal();
        });
    }

    function rodogramPrediction(indicador){
        var symbol=$("#searchsymbol").val();
        var pk=$("#pk_ticker").val();
        var ini=$("#searchinicio").val();
        var fin=$("#searchfin").val();
        var inicio=$("#mp-rodfrom").val();
        
        console.log(inicio);

        $.ajax({
            type: "POST",
            url: projectDirectory + '/Fractal/rodogramPrediction',
            data: {'symbol':symbol,'inicio':inicio,'principio':ini,'pk':pk},
            async: true
        }).success(function (data) {
            data = JSON.parse(data);
            var jsonarray=data.fractal;
            console.log(data);
            console.log(jsonarray);

            if (data.success == 1) {
             createNewPanel("fractal");
                $('#fractalgraficas').append(' <div id="myDiv2" style="width:100%;height:600px"><img src="/proyectofinal/app/webroot/img/output/outputrodogram.png" width="100%" height="500" class="center img"></div>');
                $('#fractalgraficas').append(' <div id="myDiv1" style="width:100%;height:600px"><img src="/proyectofinal/app/webroot/img/output/outputerror.png" width="100%" height="500" class="center img"></div>');

            } else {
                $("#contenido-modal-error-asincrono").html("Ha ocurrido un error interno al obtener las novedades.");
                $("#modalErrorAsincrono").modal();
            }

        }).error(function () {
            $("#modalErrorAsincrono").modal();
        });
    }

    function createNewPanel(nameIndicator){

        var panelprimary=document.createElement('div');
        panelprimary.setAttribute('class','panel panel-primary');

        var panelheading=document.createElement('div');
        panelheading.setAttribute('class','panel-heading');

        var rowdiv=document.createElement('div');
        rowdiv.setAttribute('class','row');
        var n='rowswitcher'+nameIndicator;
        rowdiv.setAttribute('id',n);

        panelprimary.appendChild(panelheading);
        panelheading.appendChild(rowdiv);



        $('#panelGraficaPrincipal').append(panelprimary);

        var nombreHeading=nameIndicator;
        nombreHeading=nombreHeading.toUpperCase();

        var panelbody=document.createElement('div');
        panelbody.setAttribute('class','panel-body');
        var nombrepanel=nameIndicator+'Body';
        panelbody.setAttribute('id',nombrepanel);
        panelprimary.appendChild(panelbody);

        $('#' + nombrepanel).append('<div class="row filamenulateral" id="'+nameIndicator+'divchart">\n' +
            '                                    <div class="col-sm-2 menulateral"></div><div class="col-sm-10" id="'+nameIndicator+'graficas">\n' +
            '                                    </div>\n' +
            '                                </div>\n' +
            '                                <div class="row" id="'+nameIndicator+'divtable" style="display: none;">\n' +
            '                                    <div id="'+nameIndicator+'tableDataStock" class="col-sm-12 modal-body">\n' +
            '                                        <table style="width: 100%;" id="'+nameIndicator+'modalInformacionBuscada" class="table table-striped table-hover tableFacturas"></table>\n' +
            '                                    </div>\n' +
            '                                </div>');


    }
</script>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12 main">
            <h1 class="page-header">Dashboard</h1>

            <div id="secciones" class="row placeholders">
                <div class="col-xs-6 col-sm-3 placeholder">
                    <i id="analisisSeriesTemporales"><img src="<?php $projectDirectory?>/proyectofinal/app/webroot/img/analisists.png" width="200" height="200" class="img" alt="Generic placeholder thumbnail"></i></a>
                    <h4>Análisis Series temporales</h4>
                    <span class="text-muted">Charts</span>
                </div>
                <div class="col-xs-6 col-sm-3 placeholder">
                    <i id="analisisTécnico"><img src="<?php $projectDirectory?>/proyectofinal/app/webroot/img/analisis_tecnico.png" width="200" height="200" class="img" alt="Generic placeholder thumbnail"></i>
                    <h4>Análisis Técnico</h4>
                    <span class="text-muted">Financial</span>
                </div>
                <div class="col-xs-6 col-sm-3 placeholder">
                    <i id="dataminingMachineLearning"><img src="<?php $projectDirectory?>/proyectofinal/app/webroot/img/data_mining.png" width="200" height="200" class="img" alt="Generic placeholder thumbnail"></i>
                    <h4>Machine Learning</h4>
                    <span class="text-muted">And Data Mining</span>
                </div>
                <div class="col-xs-6 col-sm-3 placeholder">
                    <i id="fractal"><img src="<?php $projectDirectory?>/proyectofinal/app/webroot/img/fractal.jpg" width="200" height="200" class="img" alt="Generic placeholder thumbnail"></i>
                    <h4>Fractal</h4>
                    <span class="text-muted">New techniques</span>
                </div>
            </div>


            <h2 class="sub-header">Fractal prediction</h2>

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
                                        'div' => array('class' => 'form-group col-md-2'),
                                        'value'=>$tickers['Ticker']['SYMBOL']));
                                    ?>
                                    <?php
                                    echo $this->Form->hidden('PK_TICKER',array(
                                        'id'=>'pk_ticker',
                                        'value'=>$tickers['Ticker']['PK_TICKER']
                                    ));
                                    ?>

                                    <div class='form-group col-md-2'>
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
                                    <div class='form-group col-md-2'>
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
                                    <div class="col-md-3">
                                        <?php echo $this->Form->label('Gráfica'); ?>
                                        <br>
                                        <?php
                                        echo $this->Form->hidden('TipoG',array(
                                            'id'=>'tipoGraf',
                                        ));
                                        echo $this->Form->select('TipoG', array(0=>'Line', 1=>'Candlestick'), array(
                                            'id'=>'tipoGrafica',
                                            'empty' => 'Elija tipo Gráfica',
                                            'label' => 'Tipo Gráfica',
                                            'class' => 'form-control input-sm'));
                                        ?>
                                    </div>
                                    <div class="col-md-1">
                                        <br>
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
                <div id="analisisTec" style="display: none" class="row">
                    <div id="panelGraficaPrincipal" class="col-md-12">
                        <div  class="panel panel-primary">
                            <div id="analisisTecHeading" class="panel-heading">
                                <div class="row">
                                    <div class="col-md-1"><h4>Chart</h4></div>
                                    <div class="col-md-1"><label class="switch"><input id="analisisTecSwitcher" onchange="showHideData()" style="margin-top:20%;" type="checkbox"><span class="slider round"></span></label></div>
                                    <div class="col-md-1"><h4>Data</h4></div>
                                </div>
                            </div>
                            <div id="analisisTecBody" class="panel-body">
                                <div class="row filamenulateral" id="divchart">
                                    <div class="col-sm-2 menulateral">
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <?php
                                                echo $this->Form->input('PK_INDICADOR', array('id' => 'mp-id', 'label' => 'Indicador:', 'div' => array('class' => 'form-group'), 'type' => 'text', 'class' => 'form-control input-sm','disabled'=>'disabled'));
                                                echo $this->Form->hidden('PK_INDICADOR',array('id'=>'mp-id-hidden',));?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-10" id="rodMethod">
                                                <?php
                                                echo $this->Form->input('METHOD', array('id' => 'mp-method', 'label' => 'Method:', 'div' => array('class' => 'form-group'), 'type' => 'text', 'class' => 'form-control input-sm','disabled'=>'disabled'));
                                                ?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-10" id="rodFrom">
                                                <?php
                                                echo $this->Form->label('Retrieve data since:'); ?>
                                                <br>
                                                <?php
                                                echo $this->Form->date('RODFROM', array(
                                                'label' => 'Retrieve data since:',
                                                'class' => 'form-control input-sm',
                                                'id'=>'mp-rodfrom',
                                                'div' => array('class' => 'form-control input-sm')));
                                                 ?>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-sm-8" id="rodFrom">
                                                <?php echo $this->Form->button("Calcular", array('id'=>'calcularIndicador',
                                                    'div' => false,
                                                    'data-dismiss' => 'modal',
                                                    'class' => 'btn btn-default btn-sm'
                                                ));?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-10" id="graficas">
                                        <div class="container" id="chart_div" style="width: 100%; height: 600px;"></div>
                                    </div>
                                </div>
                                <div class="row" id="divtable" style="display: none;">
                                    <div id="tableDataStock" class="col-sm-12 modal-body">
                                        <table style="width: 100%" id="modalInformacionBuscada" class="table table-striped table-hover tableFacturas"></table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
