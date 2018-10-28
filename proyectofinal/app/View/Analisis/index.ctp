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
            if($('#mp-periodo1').val()==''){
                $('#mp-periodo1').focus();
            }else if($('#mp-periodo2').val()=='') {
                $('#mp-periodo2').focus();
            }
            else if($('#mp-periodo3').val()==''){
                $('#mp-periodo3').focus();
            }else{
            $("#modalParameter").modal('hide');
            var numeroIndicador = $('#mp-id-hidden').val();
            var p1=$('#mp-periodo1').val();
            var p2=$('#mp-periodo2').val();
            var p3=$('#mp-periodo3').val();
            var periodos=[];
            if(p1!=''){
                periodos.push(p1);
            }
            if(p2!=''){
                periodos.push(p2);
            }
            if(p3!=''){
                periodos.push(p3);
            }
            switch(numeroIndicador) {
                case '1':
                    macd(periodos);
                    break;
                case '2':
                    sma(periodos);
                    break;
                case '3':
                    ema(periodos);
                    break;
            }
            }
        })

    });

    function buscarIndex() {
        var symbol=$("#searchsymbol").val();
        var pk=$("#pk_ticker").val();
        var inicio=$("#searchinicio").val();
        var fin=$("#searchfin").val();
        var tipog=$("#tipoGrafica").val();

        $("#analisisTec").show('slow');

        $.ajax({
            type: "POST",
            url: projectDirectory + '/Analisis/searchStockPrice',
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
                            title: symbol + ' Close',
                            explorer: {
                                axis: 'horizontal',
                                keepInBounds: true,
                                maxZoomIn: 20.0,
                                maxZoomOut: 1
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

    function sma(periodos){
        var symbol=$("#searchsymbol").val();
        var pk=$("#pk_ticker").val();
        var inicio=$("#searchinicio").val();
        var fin=$("#searchfin").val();

        $.ajax({
            type: "POST",
            url: projectDirectory + '/Analisis/sma',
            data: {'symbol':symbol,'inicio':inicio,'fin':fin,'pk':pk,'periodos':periodos},
            async: true
        }).success(function (data) {
            console.log(data);
            data = JSON.parse(data);
            console.log(data);
            var resss=data.res;
            var array=data.sma;

            for($i=0;$i<array.length;$i++){
                var momentDate = moment(array[$i]['date']);
                array[$i]['date']=momentDate.format("YYYY-MM-DD");
            }
            console.log(array)
            console.log(resss);

            if (data.success == 1) {
                createNewPanel('sma',periodos);

                dt = $("#smamodalInformacionBuscada").DataTable({
                    data: data.sma,
                    columns: [
                        {title: "Fecha", data: 'date', defaultContent: '',width:"60%"},
                        {title: "In", data: 'in', defaultContent: ''},
                        {title: "Signal", data: 'signal', defaultContent: ''},
                        {title: "Sma1", data: 'sma1', defaultContent: ''},
                        {title: "Sma2", data: 'sma2', defaultContent: ''},
                        {title: "Sma3", data: 'sma3', defaultContent: ''},
                    ],
                    autowidth:true
                });

                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {
                    var data = new google.visualization.DataTable();

                    data.addColumn('datetime', 'Date');
                    data.addColumn('number',   'Sma1');
                    data.addColumn('number',   'Sma2');
                    data.addColumn('number',   'Sma3');



                    // 7. Cycle through the records, adding one row per record
                    array.forEach(function(packet) {
                        data.addRow([
                            (new Date(packet['date'])),
                            (packet['sma1']),
                            (packet['sma2']),
                            (packet['sma3']),

                        ]);
                    });

                    // 8. Create a new line chart
                    $('#smagraficas').append('<div class="container" id="chart_div2" style="width: 100%; height: 600px;"></div>');
                    var chart = new google.visualization.LineChart(document.getElementById('chart_div2'));

                    // 9. Render the chart, passing in our DataTable and any config data
                    chart.draw(data, {
                        title:  'Ambient Temperature (/test/temp)',
                        height: 150
                    });

                    var options = {
                        legend:'none',
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

            } else {
                $("#contenido-modal-error-asincrono").html("Ha ocurrido un error interno al obtener las novedades.");
                $("#modalErrorAsincrono").modal();
            }

        }).error(function () {
            $("#modalErrorAsincrono").modal();
        });
    }

    function ema(periodos){
        var symbol=$("#searchsymbol").val();
        var pk=$("#pk_ticker").val();
        var inicio=$("#searchinicio").val();
        var fin=$("#searchfin").val();

        $.ajax({
            type: "POST",
            url: projectDirectory + '/Analisis/ema',
            data: {'symbol':symbol,'inicio':inicio,'fin':fin,'pk':pk,'periodos':periodos},
            async: true
        }).success(function (data) {
            data = JSON.parse(data);
            var jsonarray=data.ema;
            var array=data.ema;
            var result = [];

            for($i=0;$i<array.length;$i++){
                var momentDate = moment(array[$i]['date']);
                array[$i]['date']=momentDate.format("YYYY-MM-DD");
            }
            console.log(array)
            console.log(jsonarray);

            if (data.success == 1) {
                createNewPanel('ema',periodos);
                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {
                    var data = new google.visualization.DataTable();

                    data.addColumn('datetime', 'Date');
                    data.addColumn('number',   'Ema1');
                    data.addColumn('number',   'Ema2');
                    data.addColumn('number',   'Ema3');

                    // 7. Cycle through the records, adding one row per record
                    array.forEach(function(packet) {
                        data.addRow([
                            (new Date(packet['date'])),
                            (packet['ema1']),
                            (packet['ema2']),
                            (packet['ema3']),
                        ]);
                    });

                    // 8. Create a new line chart
                    $('#emagraficas').append('<div class="container" id="chart_div3" style="width: 100%; height: 600px;"></div>');
                    var chart = new google.visualization.LineChart(document.getElementById('chart_div3'));

                    // 9. Render the chart, passing in our DataTable and any config data
                    chart.draw(data, {
                        title:  'Ambient Temperature (/test/temp)',
                        height: 150
                    });

                    var options = {
                        legend:'none',
                        chartArea:{
                          backgroundColor:{
                              stroke:'#4322c0',
                              strokeWidth:0.5
                          }
                        },
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

    function macd(periodos) {
        var symbol=$("#searchsymbol").val();
        var pk=$("#pk_ticker").val();
        var inicio=$("#searchinicio").val();
        var fin=$("#searchfin").val();

        $.ajax({
            type: "POST",
            url: projectDirectory + '/Analisis/macd',
            data: {'symbol':symbol,'inicio':inicio,'fin':fin,'pk':pk,'periodos':periodos},
            async: true
        }).success(function (data) {
            data = JSON.parse(data);
            var jsonarray=data.macd;
            var array=data.macd;

            var result = [];

            for($i=0;$i<array.length;$i++){
                var momentDate = moment(array[$i]['date']);
                array[$i]['date']=momentDate.format("YYYY-MM-DD");
            }
            console.log(array)
            console.log(jsonarray);

            if (data.success == 1) {
                    createNewPanel('macd',periodos);
                    google.charts.load('current', {'packages':['corechart']});
                    google.charts.setOnLoadCallback(drawChart);

                    function drawChart() {
                        var data = new google.visualization.DataTable();

                        data.addColumn('datetime', 'Date');
                        data.addColumn('number',   'Macd');
                        data.addColumn('number',   'Signal');

                        // 7. Cycle through the records, adding one row per record
                        array.forEach(function(packet) {
                            data.addRow([
                                (new Date(packet['date'])),
                                (packet['macd']),
                                (packet['sig']),
                            ]);
                        });

                        // 8. Create a new line chart
                        $('#macdgraficas').append('<div class="container" id="chart_div4" style="width: 100%; height: 600px;"></div>');
                        var chart = new google.visualization.LineChart(document.getElementById('chart_div4'));

                        // 9. Render the chart, passing in our DataTable and any config data
                        chart.draw(data, {
                            title:  'Ambient Temperature (/test/temp)',
                            height: 150
                        });

                        var options = {
                            legend:'none',
                            chartArea:{
                                backgroundColor:{
                                    stroke:'#4322c0',
                                    strokeWidth:0.5
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

            } else {
                $("#contenido-modal-error-asincrono").html("Ha ocurrido un error interno al obtener las novedades.");
                $("#modalErrorAsincrono").modal();
            }

        }).error(function () {
            $("#modalErrorAsincrono").modal();
        });
    }

    function soporte(){
        var symbol=$("#searchsymbol").val();
        var pk=$("#pk_ticker").val();
        var inicio=$("#searchinicio").val();
        var fin=$("#searchfin").val();

        $.ajax({
            type: "POST",
            url: projectDirectory + '/Analisis/soporte',
            data: {'symbol':symbol,'inicio':inicio,'fin':fin,'pk':pk},
            async: true
        }).success(function (data) {
            data = JSON.parse(data);
            var jsonarray=data.precios;
            console.log(jsonarray);
            var array=data.precios.Price;
            var soporte=data.precios.Soporte;
            var tamaño=array.length;

            var result = [];

            for($i=0;$i<array.length;$i++){
                var momentDate = moment(array[$i]['date']);
                array[$i]['date']=momentDate.format("YYYY-MM-DD");
            }
            console.log(array)
            console.log(jsonarray);

            if (data.success == 1) {

                    google.charts.load('current', {'packages':['corechart']});
                    google.charts.setOnLoadCallback(drawChart);

                    function drawChart() {
                        var data = new google.visualization.DataTable();

                        data.addColumn('datetime', 'Date');
                        data.addColumn('number',   'Close');
                        data.addColumn('number',   0);
                        data.addColumn('number',   1);

                        // 7. Cycle through the records, adding one row per record
                        array.forEach(function(packet) {
                            data.addRow([
                                (new Date(packet['date'])),
                                (packet['close']),
                                (packet[0]),
                                (packet[1]),
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

    function showHideData(s) {
        var dc=s+"divchart";
        var dt=s+"divtable";

        if($('#'+dc).css('display') == 'none'){
            $('#'+dc).show('slow');
            $('#'+dt).hide('slow');
        } else {
            $('#'+dc).hide('slow');
            $('#'+dt).show('slow');
        }
    }

    function createNewPanel(nameIndicator,periodos){

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
        for(var i=0;i<periodos.length;i++){
            nombreHeading+= ' - '+periodos[i];
        }

        console.log(nombreHeading);


        switch (nameIndicator){
            case "sma":
                $('#' + n).append('<div class="col-md-1"><h4>Chart</h4></div>\n' +
                    '                                    <div class="col-md-1"><label class="switch"><input class="TecSwitcher" id="'+nameIndicator+'TecSwitcher" onchange="showHideData('+"'sma'"+')" style="margin-top:20%;" type="checkbox"><span class="slider round"></span></label></div>\n' +
                    '                                    <div class="col-md-1"><h4>Data</h4></div><div class="col-md-1"></div><div class="col-md-3" style="text-align: center" "><h3>'+nombreHeading+'</h4></div>');
                break;
            case "ema":
                $('#' + n).append('<div class="col-md-1"><h4>Chart</h4></div>\n' +
                    '                                    <div class="col-md-1"><label class="switch"><input class="TecSwitcher" id="'+nameIndicator+'TecSwitcher" onchange="showHideData('+"'ema'"+')" style="margin-top:20%;" type="checkbox"><span class="slider round"></span></label></div>\n' +
                    '                                    <div class="col-md-1"><h4>Data</h4></div><div class="col-md-1"></div><div class="col-md-3" style="text-align: center" "><h3>'+nombreHeading+'</h4></div>');

                break;
            case "macd":
                $('#' + n).append('<div class="col-md-1"><h4>Chart</h4></div>\n' +
                    '                                    <div class="col-md-1"><label class="switch"><input class="TecSwitcher" id="'+nameIndicator+'TecSwitcher" onchange="showHideData('+"'macd'"+')" style="margin-top:20%;" type="checkbox"><span class="slider round"></span></label></div>\n' +
                    '                                    <div class="col-md-1"><h4>Data</h4></div><div class="col-md-1"></div><div class="col-md-3" style="text-align: center" "><h3>'+nombreHeading+'</h4></div>');

                break;
        }


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
        <div class="col-sm-12  main">
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


            <h2 class="sub-header">Análisis técnico</h2>

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
                                        <?php
                                        echo $this->Form->label('Indicadores'); ?>
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
                                                            <a class="IndicadorTecnico" href="#" data-id="<?= $itemId ?>">
                                                                <?= $menu['items'][$itemId]['Indicadore']['NAME'] ?>
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
                                        echo $this->Form->input('PK_INDICADOR', array('id' => 'mp-id', 'label' => 'Indicador:', 'div' => array('class' => 'form-group'), 'type' => 'text', 'class' => 'form-control input-sm','disabled'=>'disabled'));
                                        echo $this->Form->hidden('PK_INDICADOR',array('id'=>'mp-id-hidden',));?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4" id="periodo1">
                                            <?php
                                            echo $this->Form->input('PERIODO1', array('id' => 'mp-periodo1', 'label' => 'Periodo 1:', 'div' => array('class' => 'form-group'), 'type' => 'number', 'class' => 'form-control input-sm'));
                                            ?>
                                        </div>
                                        <div class="col-sm-4" id="periodo2">
                                            <?php
                                            echo $this->Form->input('PERIODO2', array('id' => 'mp-periodo2', 'label' => 'Periodo 2:', 'div' => array('class' => 'form-group'), 'type' => 'number', 'class' => 'form-control input-sm'));
                                            ?>
                                        </div>
                                        <div class="col-sm-4" id="periodo2">
                                            <?php
                                            echo $this->Form->input('PERIODO3', array('id' => 'mp-periodo3', 'label' => 'Periodo 3', 'div' => array('class' => 'form-group'), 'type' => 'number', 'class' => 'form-control input-sm'));
                                            ?>
                                        </div>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <?php echo $this->Form->button("Calcular", array('id'=>'calcularIndicador',
                                    'div' => false,
                                    'data-dismiss' => 'modal',
                                    'class' => 'btn btn-default btn-sm'
                                ));?>
                                <?php
                                echo $this->Form->button("Cancelar", array('div' => false,
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
