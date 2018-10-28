<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<style>
    #dataTable tbody tr.selected {
        color:black;
        background-color: #66afe9;
    }

    .nothing{
        transition: box-shadow 0.1s linear 0.2s;
        box-shadow: 0 0 0 #0101DF;
    }

    .available{
        transition: box-shadow 0.1s linear 0.2s;
        box-shadow: 0 0 40px #0101DF;
    }
    .not_available{
        transition: box-shadow 0.1s linear 0.2s;
        box-shadow: 0 0 40px #FF0000;
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
                    case 'analisisSeriesTemporales':
                        document.getElementById('formularioMainIndexForm').action='/proyectofinal/timeseries/index';
                        document.getElementById('formularioMainIndexForm').submit();
                        break;
                    case 'analisisTécnico':
                        document.getElementById('formularioMainIndexForm').action='/proyectofinal/analisis/index';
                        document.getElementById('formularioMainIndexForm').submit();
                        break;
                    case 'dataminingMachineLearning':
                        document.getElementById('formularioMainIndexForm').action='/proyectofinal/machinelearning/index';
                        document.getElementById('formularioMainIndexForm').submit();
                        break;
                    case 'fractal':
                        document.getElementById('formularioMainIndexForm').action='/proyectofinal/fractal/index';
                        document.getElementById('formularioMainIndexForm').submit();
                        break;
                }

            }else{
                $("#secciones").find('img').removeClass('available');
                $("#secciones").find('img').removeClass('not_available');
                $(this).find('img').addClass('not_available');
            }
        })



        var table = $('#dataTable').DataTable();

        $('#dataTable tbody').on( 'click', 'tr', function () {
            if ( $(this).hasClass('selected') ) {
                $(this).removeClass('selected');
                $("#pkindiceStock").val('');
                $("#nombreindiceStock").val();
                $("#secciones").find('img').removeClass('not_available');
                $("#secciones").find('img').removeClass('available');
                $("#secciones").find('img').addClass('nothing');
            }
            else {
                table.$('tr.selected').removeClass('selected');
                var data = table.row( this ).data();
                $(this).addClass('selected');
                $("#pkindiceStock").val($(this).find('#pkticker').val());
                $("#nombreindiceStock").val(data[0]);
                $("#secciones").find('img').removeClass('not_available');
                $("#secciones").find('img').addClass('available');
            }

        } );


        $(".searchindex").on('click',function () {
            var pkindex=$(this).parent().parent().parent().find('#pk_ticker').val();
            var inicio=$(this).parent().parent().parent().find('#searchinicio').val();
            var fin=$(this).parent().parent().parent().find('#searchfin').val();
            var numerografica=$(this).parent().parent().parent().find('#numeromensaje').val();
            buscarIndex(pkindex,inicio,fin,numerografica);

        });

        $(".mostrarInfo").on('click',function(){
            var pkindex=$(this).parent().find('#pkticker').val();
            buscarInformacion(pkindex);
        })
    });

    function buscarIndex(pk,inicio,fin,numerografica) {

        $.ajax({
            type: "POST",
            url: projectDirectory + '/Main/searchStockPrice',
            data: {'inicio':inicio,'fin':fin,'pk':pk},
            async: true
        }).success(function (data) {
            data = JSON.parse(data);
            var jsonarray=data.precios;
            var array=data.precios.Price;

            console.log(numerografica);
            var result = [];

            for($i=0;$i<array.length;$i++){
                var momentDate = moment(array[$i]['date']);
                array[$i]['date']=momentDate.format("YYYY-MM-DD");
            }
            console.log(array)
            console.log(jsonarray);

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

                        console.log($("#"+numerografica).html());
                        // 8. Create a new line chart
                        var chart = new google.visualization.LineChart(document.getElementById(numerografica));

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

    function buscarInformacion(pkindex) {

        /*var principal=document.getElementById("principal");

        while(principal.hasChildNodes()){
            principal.removeChild(principal.firstChild);
        }*/


        var chartDiv='<div id="info_div"></div>';

        /*$("#principal").append(chartDiv);
        $("#principal").append(tablePrices);*/

        $.ajax({
            type: "POST",
            url: projectDirectory + '/Main/searchStockInfo',
            data: {'symbol':pkindex},
            async: true
        }).success(function (data) {
            data = JSON.parse(data);
            console.log(data);
            var jsonarray=data.info;
            var array=data.info;

            var result = [];

            var description=jsonarray.description;
            var startDate=jsonarray.startDate;
            var exchangeCode=jsonarray.exchangeCode;
            var name=jsonarray.name;
            var tickename=jsonarray.ticker;



            /*console.log(data.precios);
            console.log(typeof data.precios.Price);
            console.log(data.precios.Price);*/
            if (data.success == 1) {
                $("#mp-tickername").val(tickename);
                $("#mp-name").val(name);
                $("#mp-exchange").val(exchangeCode);
                $("#mp-startdate").val(startDate);
                $("#mp-descripcion").val(description);
                //$("#mp-title").html(tickename);



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
        <!--<div class="col-sm-3 col-md-2 sidebar">
            <h4>Proyecto fin de grado</h4>
            <hr>
            <ul class="nav nav-sidebar">
                <li><h5>Análisis series temporales</h5></li>
                <li><a href="">Descomposición</a></li>
                <li><a href="">Smoothing</a></li>
                <li><a href="">Autocorrelation</a></li>
            </ul>
            <ul class="nav nav-sidebar">
                <li><h5>Análisis técnico</h5></li>
                <li><a href="">Sma</a></li>
                <li><a href="">Ema</a></li>
                <li><a href="">Macd</a></li>
            </ul>
            <ul class="nav nav-sidebar">
                <li><h5>Machine Learning</h5></li>
                <li><a href="">Least Squares</a></li>
                <li><a href="">KMeans</a></li>
            </ul>
            <ul class="nav nav-sidebar">
                <li><h5>Fractal</h5></li>
                <li><a href="">Rodogram</a></li>
            </ul>
        </div>-->
        <div class="col-sm-12 main">
            <h1 class="page-header">Dashboard</h1>
            <?php echo $this->Form->create('formularioMain'); ?>
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

            <h2 class="sub-header">Available Stocks</h2>

            <?php
            echo $this->Form->hidden('PK_INDICE_STOCK',array(
                'id'=>'pkindiceStock',
            ));
            echo $this->Form->hidden('NOMBRE_INDICE_STOCK',array(
                'id'=>'nombreindiceStock',
            ));
            echo $this->Form->end();
            ?>

            <div id="principal" class="panel-body">
                <table id="dataTable" class="table  table-striped table-hover"
                       cellspacing="0" role="grid">
                    <thead>
                    <tr role="row">
                        <th class="sorting" tabindex="0" rowspan="1" colspan="1">Symbol</th>
                        <th class="sorting" tabindex="0" rowspan="1" colspan="1">Name</th>
                        <th class="sorting" tabindex="0" rowspan="1" colspan="1">Exchange</th>
                        <th class="actions">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i = 1;
                    foreach ($tickers as $ticker) {
                        echo "<tr>";
                        echo "<td>" . $ticker['Ticker']['SYMBOL'] . "</td>";
                        echo "<td>" . $ticker['Ticker']['NAME'] . "</td>";
                        echo "<td>" . $ticker['Ticker']['EXCHANGE'] . "</td>";
                        echo '<td class="actions">';
                        echo $this->Form->hidden('PK_TICKER',array(
                            'id' => 'pkticker',
                            'value'=>$ticker['Ticker']['PK_TICKER']
                        ));
                        /*echo $this->Html->tag('a', '<i class="fa fa-line-chart"></i>',
                            array('href' => '#mensaje-' . $i,
                                'class' => 'btn btn-xs btn-info btn-tooltip',
                                'escape' => false,
                                'data-toggle' => 'modal',
                                'data-placement' => 'top',
                                'title' => 'Parametros'));*/

                        echo $this->Html->tag('a','<i class="fa fa-info"></i>',
                            array('href' => '#info',
                                'class' => 'mostrarInfo btn btn-xs btn-info btn-tooltip',
                                'escape' => false,
                                'data-toggle' => 'modal',
                                'data-placement' => 'top',
                                'title' => 'Parametros'));
                        ?>
                        <?php
                        echo "</td>";
                        echo "</tr>";
                        ?>
                        <div id="mensaje-<?php echo $i ?>" class="modal fade" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header modal-header-info">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title"><?php echo $ticker['Ticker']['SYMBOL']; ?></h4>
                                    </div>
                                    <div class="modal-body" id="mdbody-<?php echo $i ?>">
                                        <div class="row">
                                            <?php
                                            echo $this->Form->hidden('numeromensaje',array(
                                                'id'=>'numeromensaje',
                                                'value'=>'chart_div-'.$i
                                            ));
                                            ?>
                                            <div class="col-md-4">
                                                <?php
                                                echo $this->Form->input('SYMBOL', array(
                                                    'label' => 'Symbol',
                                                    'id'=>'searchsymbol',
                                                    'class' => 'form-control input-sm',
                                                    'value' => $ticker['Ticker']['SYMBOL'],
                                                ));
                                                ?>
                                            </div>
                                            <?php
                                            echo $this->Form->hidden('PK_TICKER',array(
                                                'id'=>'pk_ticker',
                                                'value'=>$ticker['Ticker']['PK_TICKER']
                                            ));
                                            ?>
                                            <div class="col-md-4">
                                                <?php echo $this->Form->label('Inicio'); ?>
                                                <br>
                                                <?php
                                                echo $this->Form->date('INICIO', array(
                                                    'label' => 'Inicio',
                                                    'id'=>'searchinicio',
                                                    'class' => 'form-control input-sm',
                                                ));
                                                ?>
                                            </div>
                                            <div class="col-md-4">
                                                <?php echo $this->Form->label('Fin'); ?>
                                                <br>
                                                <?php
                                                echo $this->Form->date('FIN', array(
                                                    'label' => 'Fin',
                                                    'id'=>'searchfin',
                                                    'class' => 'form-control input-sm',
                                                ));
                                                ?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <?php echo $this->Form->end(); ?>
                                            </div>
                                            <div class="container" id=chart_div-<?php echo $i ?>"></div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="col-md-1">
                                            <?php echo $this->form->button('Visualizar',array(
                                                'type'=>'button',
                                                //'id'=>'searchindex',
                                                'class'=>'searchindex btn btn-success btn-sm'
                                            ))?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        $i++;
                    }
                    ?>
                    </tbody>
                </table>
                <div id="info" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header modal-header-info">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 id="mp-title' class="modal-title">Ticker info</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?php
                                        echo $this->Form->input('TICKERNAME', array('id' => 'mp-tickername', 'label' => 'Ticker name:', 'div' => array('class' => 'form-group'), 'type' => 'text', 'class' => 'form-control input-sm', 'disabled' => 'disabled'));
                                        ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <?php
                                        echo $this->Form->input('NAME', array('id' => 'mp-name', 'label' => 'Name:', 'div' => array('class' => 'form-group'), 'type' => 'text', 'class' => 'form-control input-sm','disabled' => 'disabled'));
                                        ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?php
                                        echo $this->Form->input('EXCHANGE', array('id' => 'mp-exchange', 'label' => 'Exchange code:', 'div' => array('class' => 'form-group'), 'type' => 'text', 'class' => 'form-control input-sm', 'disabled' => 'disabled'));
                                        ?>
                                    </div>
                                    <div class="col-sm-4">
                                        <?php
                                        echo $this->Form->input('STARTDATE', array('id' => 'mp-startdate', 'label' => 'Available from:', 'div' => array('class' => 'form-group'), 'type' => 'text', 'class' => 'form-control input-sm','disabled' => 'disabled'));
                                        ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <?php
                                        echo $this->Form->input('DESCRIPCION', array('id' => 'mp-descripcion', 'label' => 'Description:', 'div' => array('class' => 'form-group'), 'type' => 'textarea', 'class' => 'form-control input-sm','disabled' => 'disabled'));
                                        ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <?php echo $this->Form->end(); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>










