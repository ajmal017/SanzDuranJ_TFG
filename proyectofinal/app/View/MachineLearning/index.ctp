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
        border-bottom: #337ab7;
        border-bottom-width: 1px;
        border-bottom-style: solid;
        border-bottom-right-radius: 4px;

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
        })

        $('.ampl').click(function () {
            $(this).parent().children('ol.tree').toggle(200);

        });
        $(function(){
            $('.tree-toggle').parent().children('ol.tree').toggle(200);
        });

        $('.Mlalgo').on('click',function () {
            var mlId=$(this).data('id');
            var nameml;
            switch (mlId){
                case 1:
                    break;
                case 2:
                    break;
                case 3:
                    nameml='Kmeans';
                    break;
                case 4:
                    nameml="Least Squares"
                    break;
            }
            $('#mp-id').val(nameml);
            $('#mp-id-hidden').val(mlId);
            if($('#mp-id-hidden').val()==3){
                $("#modalParameter").modal();
            }else{
                aplicarMl();
            }

        });

        $('#calcularIndicador').on('click',function () {
            var numeroIndicador = $('#mp-id-hidden').val();
            var p1=$('#mp-nclusters').val();
            if($('#mp-nclusters').val()==''){
                $('#mp-nclusters').focus();
            }else if($('#mp-nclusters').val()<0){
                $('#mp-nclusters').focus();
                alert("Please provide a positive number");
            }
            else if($('#mp-nclusters').val()>5){
                $('#mp-nclusters').focus();
                alert("Please provide at least 5 clusters");
            }else{
                $("#modalParameter").modal('hide');
            var parametros=[];
            if(p1!=''){
                parametros.push(p1);
            }

            switch(numeroIndicador) {
                case '3':
                    kmeans3d(parametros);
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
            url: projectDirectory + '/Machinelearning/searchStockPrice',
            data: {'symbol':symbol,'inicio':inicio,'fin':fin,'pk':pk},
            async: true
        }).success(function (data) {
            data = JSON.parse(data);
            console.log(data);
            var jsonarray=data.precios;
            var array=data.precios.Price;
            var statistics=data.statistics;

            var result = [];

            for($i=0;$i<array.length;$i++){
                var momentDate = moment(array[$i]['date']);
                array[$i]['date']=momentDate.format("YYYY-MM-DD");
            }
            console.log(array)
            console.log(jsonarray);
            console.log(statistics);

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

                dt2 = $("#modalStatistics").DataTable({
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
                        chart.draw(data, {
                            title:  'Ambient Temperature (/test/temp)',
                            height: 150
                        });

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

    function aplicarMl() {
        var mlId = $('#mp-id-hidden').val();
        if($(this).attr('class')==''){
            return false;
        }
        $(this).removeAttr("href");
        $(this).removeClass('Mlalgo');
        switch(mlId) {
            case '4':
                leastSquares();
                break;
        }
    }

    function leastSquares(){
        var symbol=$("#searchsymbol").val();
        var pk=$("#pk_ticker").val();
        var inicio=$("#searchinicio").val();
        var fin=$("#searchfin").val();

        $.ajax({
            type: "POST",
            url: projectDirectory + '/MachineLearning/leastsquares',
            data: {'symbol':symbol,'inicio':inicio,'fin':fin,'pk':pk},
            async: true
        }).success(function (data) {
            console.log(data);
            data = JSON.parse(data);
            console.log(data);
            var jsonarray=data.lsq;
            var array=data.lsq;
            var result = [];

            console.log(array)
            console.log(jsonarray);

            if (data.success == 1) {
                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {
                    var data = new google.visualization.DataTable();

                    data.addColumn('datetime', 'Date');
                    data.addColumn('number',   'close');
                    data.addColumn('number',   'lsq');

                    // 7. Cycle through the records, adding one row per record
                    array.forEach(function(packet) {
                        data.addRow([
                            (new Date(packet['date'])),
                            (packet['close']),
                            (packet['lsq']),

                        ]);
                    });

                    // 8. Create a new line chart
                    $('#graficas').append('<div align="center"><h4 style="border-radius:4px;text-align: center; width:15%;background-color:#0b6c9d; color:white; border-width: 1px;border-style: solid; border:#4322c0 ">Least squares</h4></div><div class="container" id="chart_div2" style="width: 100%; height: 600px;"></div>');

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

    function kmeans3d(periodos) {

        var symbol=$("#searchsymbol").val();
        var pk=$("#pk_ticker").val();
        var inicio=$("#searchinicio").val();
        var fin=$("#searchfin").val();


        $("#minDatos").show('slow');


        $.ajax({
            type: "POST",
            url: projectDirectory + '/machinelearning/km3d',
            data: {'symbol':symbol,'inicio':inicio,'fin':fin,'pk':pk,'periodos':periodos},
            async: true
        }).success(function (data) {
            data = JSON.parse(data);
            var jsonarray=data.tamGrupos;
            var numIter=data.numIter;
            var array=data.resultado;

            var ngrupos=jsonarray.length;
            var nmuestras=0;
            for(var i=0;i<ngrupos;i++){
                nmuestras=nmuestras+array[i].length;
            }

            console.log(nmuestras);
            //var array=data.precios.Price;

            console.log(array);
            var result = [];

            /*
            for($i=0;$i<array.length;$i++){
                var momentDate = moment(array[$i]['date']);
                array[$i]['date']=momentDate.format("YYYY-MM-DD");
            }*/
            //console.log(array)
            console.log(jsonarray);
            console.log(numIter);


            /*console.log(data.precios);
            console.log(typeof data.precios.Price);
            console.log(data.precios.Price);*/
            if (data.success == 1) {
                createNewPanel('kmeans',numIter);

                $('#kmeansgraficas').append(' <div id="myDiv" style="width:100%;height:600px"></div>');

                //document.getElementById('myDiv').append('<h4>Numero de iteraciones:</h4>');
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
                    var cont=0;





                    var end=cont+jsonarray[0];
                    var filas=[];
                    for(i=cont;i<end;i++){
                        filas.push(rows[i]);
                        cont++;
                    }
                    console.log(filas);
                    console.log(cont);
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



                    var end=cont+jsonarray[1];
                    filas=[];
                    for(i=cont;i<end;i++){
                        filas.push(rows[i]);
                        cont++;
                    }
                    console.log(filas);
                    console.log(cont);
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



                    var end=cont+jsonarray[2];
                    filas=[];
                    for(i=cont;i<end;i++){
                        filas.push(rows[i]);
                        cont++;
                    }
                    console.log(filas);
                    console.log(cont);
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




                    var end=cont+jsonarray[3];
                    filas=[];
                    for(i=cont;i<end;i++){
                        filas.push(rows[i]);
                        cont++;
                    }
                    console.log(filas);
                    console.log(cont);
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





                    var end=cont+jsonarray[4];
                    filas=[];
                    for(i=cont;i<end;i++){
                        filas.push(rows[i]);
                        cont++;
                    }
                    console.log(filas);
                    console.log(cont);
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

                google.charts.load('current', {'packages':['corechart']});

                for($i=0;$i<ngrupos;$i++){
                    $('#kmeansgraficas').append(' <div id="myDiv2'+$i+'" style="width:100%;height:600px"></div>');
                    var arrayAux=array[$i];
                    console.log(arrayAux);
                    drawChart(arrayAux,$i,nmuestras);
                }
                function drawChart(array,$i,nmuestras) {
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

                        title: ' Group '+($i+1),
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

                    var chart = new google.visualization.CandlestickChart(document.getElementById('myDiv2'+$i));
                    chart.draw(data, options);

                    //var cSticks = document.querySelectorAll('rect[fill="#a52714"]');

                    /*for(var i=0;i<cSticks.length;i++){
                        cSticks[i].setAttribute('fill', '#0f9d58');
                        cSticks[i].setAttribute('stroke', '#0f9d58');
                    }*/
                };
                google.charts.setOnLoadCallback(drawChart);

            } else {
                $("#contenido-modal-error-asincrono").html("Ha ocurrido un error interno al obtener las novedades.");
                $("#modalErrorAsincrono").modal();
            }

        }).error(function () {
            $("#modalErrorAsincrono").modal();
        });
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

        $('#' + n).append('<div class="col-md-1"><h4>Chart</h4></div>\n' +
            '                                    <div class="col-md-1"><label class="switch"><input id="analisisTecSwitcher" style="margin-top:20%;" type="checkbox"><span class="slider round"></span></label></div>\n' +
            '                                    <div class="col-md-1"><h4>Data</h4></div><div class="col-md-1"></div><div class="col-md-3" style="text-align: center" "><h3>'+nombreHeading+'</h4></div>');



        var panelbody=document.createElement('div');
        panelbody.setAttribute('class','panel-body');
        var nombrepanel=nameIndicator+'Body';
        panelbody.setAttribute('id',nombrepanel);
        panelprimary.appendChild(panelbody);

        $('#' + nombrepanel).append('<div class="row filamenulateral" id="divchart">\n' +
            '                                    <div class="col-sm-2 menulateral"></div><div class="col-sm-10" id="'+nameIndicator+'graficas">\n' +
            '                                    <div align="center"><h4 style="border-radius:4px;text-align: center; width:15%;background-color:#0b6c9d; color:white; border-width: 1px;border-style: solid; border:#4322c0 ">Numero de iteraciones: '+periodos+'</h4></div></div></div>\n' +
            '                                </div>\n' +
            '                                <div class="row" id="divtable" style="display: none;">\n' +
            '                                    <div id="tableDataStock" class="col-sm-12 modal-body">\n' +
            '                                        <table style="width: 100%" " id="modalInformacionBuscada" class="table table-striped table-hover tableFacturas"></table>\n' +
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


            <h2 class="sub-header">Machine Learning</h2>

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
                        <div class="panel panel-primary">
                            <div id="analisisTecHeading" class="panel-heading">
                                <div class="row">
                                    <div class="col-md-1"><h4>Chart</h4></div>
                                    <div class="col-md-1"><label class="switch"><input id="analisisTecSwitcher" style="margin-top:20%;" type="checkbox"><span class="slider round"></span></label></div>
                                    <div class="col-md-1"><h4>Data</h4></div>
                                </div>
                            </div>
                            <div id="analisisTecBody" class="panel-body">
                                <div class="row filamenulateral" id="divchart">
                                    <div class="col-sm-2 menulateral">
                                        <?php
                                        echo $this->Form->label('Machine Learning'); ?>
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
                                                            <a class="Mlalgo" href="#" data-id="<?= $itemId ?>">
                                                                <?= $menu['items'][$itemId]['Mlalgo']['NAME'] ?>
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
                                <div class="row" id="statisticsTable">
                                    <div id="tableStatistics" class="modal-body">
                                        <table id="modalStatistics" class="table table-striped table-hover tableFacturas"></table>
                                    </div>
                                </div>
                                <div class="row" id="divtable" style="display: none;">
                                    <div id="tableDataStock" class="col-sm-12 modal-body">
                                        <table width="100%" id="modalInformacionBuscada" class="table table-striped table-hover tableFacturas"></table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modalParameter" tabindex="-2" role="dialog" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header modal-header-info">
                                    <button type="button" class="close modal-close"><span
                                            aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                                    <h4 class="modal-title">Parametros</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <?php
                                            echo $this->Form->input('PK_INDICADOR', array('id' => 'mp-id', 'label' => 'Algoritmo:', 'div' => array('class' => 'form-group'), 'type' => 'text', 'class' => 'form-control input-sm','disabled'=>'disabled'));
                                            echo $this->Form->hidden('PK_INDICADOR',array(
                                                'id'=>'mp-id-hidden',
                                            ));
                                            ?>
                                        </div>
                                        <div class="col-sm-4" id="nclusters">
                                            <?php
                                            echo $this->Form->input('NClusters', array('id' => 'mp-nclusters', 'label' => 'Numero de clusters:', 'div' => array('class' => 'form-group'), 'type' => 'number', 'class' => 'form-control input-sm'));
                                            ?>
                                        </div>
                                        <!--<div class="col-sm-4" id="nIteraciones">
                                            <?php
                                            //echo $this->Form->input('PERIODO1', array('id' => 'mp-niteraciones', 'label' => 'Numero iteraciones:', 'div' => array('class' => 'form-group'), 'type' => 'number', 'class' => 'form-control input-sm'));
                                            ?>
                                        </div>-->
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <?php echo $this->Form->button("Calcular", array(
                                        'id'=>'calcularIndicador',
                                        'div' => false,
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
