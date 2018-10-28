<?php

/**
 * Created by PhpStorm.
 * User: JOSESANZ
 * Date: 11/04/2018
 * Time: 13:40
 */
//App::import(APP .DS.'Vendor'. DS . 'TS'.DS . 'ts.php');
App::import('Vendor', 'TS\ts.class');
App::import('Vendor', 'TS\ts_msg');

//App::import('Vendor', 'jpgraph\jpgraph');
//App::import('Vendor', 'jpgraph\jpgraph_line');

//App::import("C:\xampp\php\vendor\php-ai\php-ml");

App::import('Vendor', 'php-ml\Math\Statistic\Mean');
App::import('Vendor', 'php-ml\Math\Statistic\StandardDesviation');
App::import('Vendor', 'php-ml\Math\Statistic\Variance');
App::import('Vendor', 'php-ml\Math\Statistic\Covariance');
App::import('Vendor', 'php-ml\Math\Statistic\Correlation');
App::import('Vendor', 'php-ml\Math\Statistic\Gaussian');
App::import('Vendor', 'php-ml\Math\Statistic\ANOVA');

class TimeSeriesController extends AppController
{

    /**
     * This controller does not use a model
     *
     * @var array
     */

    public $uses = array(
        'Price', 'Ticker', 'Tstreat', 'php-ml\Math\Statistic\Mean'
    );


    /**
     * Displays a view
     *
     * @return CakeResponse|null
     * @throws ForbiddenException When a directory traversal attempt.
     * @throws NotFoundException When the view file could not be found
     *   or MissingViewException in debug mode.
     */
    public function index()
    {
        $pk=$this->request->data['formularioMain']['PK_INDICE_STOCK'];

        $tickers = $this->Ticker->find('first', array(
            'recursive' => -1,
            'fields'=>array('Ticker.PK_TICKER','Ticker.TIINGO_NAME','Ticker.SYMBOL','Ticker.NAME','Ticker.EXCHANGE'),
            'conditions'=>array('Ticker.PK_TICKER'=>$pk)
        ));

        $menuItems = $this->Tstreat->find('all', array(
            'recursive' => -1,
        ));


        $menus = array(
            'items' => array(),
            'parents' => array()
        );

        foreach ($menuItems as $items=>$item){
            // Create current menus item id into array
            $menus['items'][$item['Tstreat']['PK_TS']] = $item;
            // Creates list of all items with children
            $menus['parents'][$item['Tstreat']['FK_TS']][] = $item['Tstreat']['PK_TS'];
        }


        $this->set(array(
            'tickers' => $tickers,
            'menu'=>$menus

        ));

    }

    public function searchStockPrice()
    {
        require_once(APP . 'Vendor' . '\php-ml' . '\Math' . '\Statistic' . '\Mean.php');
        require_once(APP . 'Vendor' . '\php-ml' . '\Math' . '\Statistic' . '\StandardDeviation.php');
        require_once(APP . 'Vendor' . '\php-ml' . '\Math' . '\Statistic' . '\Variance.php');

        $opciones = array();

        $this->autoLayout = false;
        $this->autoRender = false;

        $pk = $this->request->data['pk'];
        $inicio = $this->request->data['inicio'];
        $fin = $this->request->data['fin'];

        $ticker = $this->Ticker->find('first', array(
            'recursive' => -1,
            'fields' => array('Ticker.TIINGO_NAME'),
            'conditions' => array('Ticker.PK_TICKER' => $pk)
        ));

        $symbol = $ticker['Ticker']['TIINGO_NAME'];

        if (!empty($this->request->data)) {
            $opciones = array(
                'symbol' => $symbol,
                'startDate' => $inicio,
                'endDate' => $fin,
                'ohlc' => false,
                'closeOnly' => false,
                'apiKey' => '&token=ec521aefb5ccb5202dbf55eb950b19bc1e7a248f'
            );
        }

        if ($this->request->is('ajax')) {
            $precios = $this->Price->find('all', array(
                'conditions' => $opciones,
            ));

            $valores = array();
            $labels = array();
            foreach ($precios['Price'] as $dato) {
                array_push($valores, $dato['close']);
                array_push($labels, date("Y-m-d", strtotime($dato['date'])));
            }


            $statistics=array();
            $media=\Phpml\Math\Statistic\Mean::arithmetic($valores);
            $mediana=\Phpml\Math\Statistic\Mean::median($valores);
            $scuad=\Phpml\Math\Statistic\StandardDeviation::sumOfSquares($valores);
            $pop=\Phpml\Math\Statistic\StandardDeviation::population($valores);
            $varianza=\Phpml\Math\Statistic\Variance::population($valores);

            array_push($statistics,array("Media"=>$media,"Mediana"=>$mediana,"DESumCuad"=>$scuad,"DEPop"=>$pop,"Varianza"=>$varianza));
            /*array_push($statistics,array("Mediana"=>\Phpml\Math\Statistic\Mean::median($valores)));
            array_push($statistics,array("DE Suma cuadrados"=>\Phpml\Math\Statistic\StandardDeviation::sumOfSquares($valores)));
            array_push($statistics,array("DE Population"=>\Phpml\Math\Statistic\StandardDeviation::population($valores)));
            array_push($statistics,array("Varianza"=>\Phpml\Math\Statistic\Variance::population($valores)));
*/
            /*echo "Media: " . \Phpml\Math\Statistic\Mean::arithmetic($valores);
            echo "<br>";
            echo "Mediana: " . \Phpml\Math\Statistic\Mean::median($valores);
            echo "<br>";
            //echo \Phpml\Math\Statistic\Mean::mode($valores);
            echo "Desviación estandar (Suma cuadrados): " . \Phpml\Math\Statistic\StandardDeviation::sumOfSquares($valores);
            echo "<br>";
            echo "Desviación estandar (Population): " . \Phpml\Math\Statistic\StandardDeviation::population($valores);
            echo "<br>";
            echo "Varianza (Population): " . \Phpml\Math\Statistic\Variance::population($valores);
            */

            return new CakeResponse(array('body' => json_encode(array('response' => true, 'success' => true, 'precios' => $precios,'statistics'=>$statistics)), 'status' => 200));
        }
    }

    public function acf(){
        $opciones = array();

        $this->autoLayout = false;
        $this->autoRender = false;

        $pk = $this->request->data['pk'];
        $inicio = $this->request->data['inicio'];
        $fin = $this->request->data['fin'];
        $periodos = $this->request->data['periodos'];


        $ticker= $this->Ticker->find('first', array(
            'recursive' => -1,
            'fields'=>array('Ticker.TIINGO_NAME'),
            'conditions'=>array('Ticker.PK_TICKER'=>$pk)
        ));

        $symbol=$ticker['Ticker']['TIINGO_NAME'];

        if (!empty($this->request->data)) {
            $opciones = array(
                'symbol' => $symbol,
                'startDate' => $inicio,
                'endDate' => $fin,
                'ohlc' => false,
                'closeOnly' => true,
                'apiKey' => '&token=ec521aefb5ccb5202dbf55eb950b19bc1e7a248f'
            );

        }

        if ($this->request->is('ajax')) {

            $precios = $this->Price->find('all', array(
                'conditions' => $opciones,
            ));

            $valores = array();
            $labels = array();
            foreach ($precios['Price'] as $dato) {
                array_push($valores, $dato['close']);
                array_push($labels, date("Y-m-d", strtotime($dato['date'])));
            }


            $diff=array();
            for($i=0;$i<sizeof($valores)-1;$i++){
                array_push($diff,$valores[$i+1]-$valores[$i]);
            }
            array_pop($labels);


            $time_series_1="";
            foreach ($diff as $p){
                $time_series_1 =$time_series_1 .",". strval($p);
            }
            $labels2="";
            foreach ($labels as $l){
                $labels2 =$labels2 ." ". strval($l);
            }

            require_once(APP . 'Vendor' . '\TS' . '\ts.class.php');
            //include('ts.class.php');
            $f = new TS("TimeSeriesOne",$time_series_1, $labels2); //build assigning directly the values and the labels

            /*$f->ts_name();
            $this->b();
            echo "<table border=1 cellpadding=10><tr><td>";
            $f->ts_print(true);
            echo "<td>";
            echo "Mean: ".$f->ts_mean();
            $this->b();
            echo "Variance: ".$f->ts_var();
            $this->b();
            echo "Sample Variance: ".$f->ts_var("nopop");
            $this->b();
            echo "StDev: ".$f->ts_stdev();
            echo "<td>";*/
            $grafica= $f->ts_plot(300,250,"Time Series");
            //echo "<td>";
            $acf2=$f->ts_acf($periodos[0]);
            $acf=$f->ts_acf_plot($periodos[0],300,250,"ACF");
            //echo "</table>";
            $resultadofinal=array();
            array_push($resultadofinal,$grafica);
            array_push($resultadofinal,$acf);

            /*echo "<form name=timeseries action=example2 method=POST>";
            echo "Time series values<br/><textarea name=ts rows=5 cols=50>";
            if(!isset($_REQUEST['ts'])) echo $time_series_1;
            else echo $_REQUEST['ts'];
            echo "</textarea><br/>";
            echo "<br/><br/>Labels<br/> <textarea name=label rows=5 cols=50>";
            if(!isset($_REQUEST['label'])) echo $labels2;
            else echo $_REQUEST['label'];
            echo "</textarea><br/>";

            echo "<input type=submit>";
            echo "</form>";

            if(!isset($_REQUEST['ts'])) exit;*/


            /*$time_series_1 = "100.0 200.0  250.0 400.0  150.0 350.0 200.0 300";  //some default values
            //$labels = "1990 1991 1992 1993 1994 1995 1996 1997";  //labels for the time series values


            $f = new \ts\ts("TimeSeriesOne",$valores, $labels); //build assigning directly the values and the labels

            $f->ts_name();
            $this->b();
            echo "<table border=1 cellpadding=10><tr><td>";
            $f->ts_plot(300,250,"Time Series");
            echo "<td>";
            $f->ts_acf_plot(10,300,250,"ACF");
            echo "</table>";*/

            $resultadofinal=array();
            for($i=0;$i<sizeof($acf2);$i++){
                array_push($resultadofinal, array('index' => $i, 'acf' => $acf2[$i],'target'=>0.15));
            }

            return new CakeResponse(array('body' => json_encode(array('response' => true, 'success' => true, 'acf' => $resultadofinal)), 'status' => 200));

        }
    }

    public function desestacionalizar(){

        $opciones = array();

        $this->autoLayout = false;
        $this->autoRender = false;

        $pk = $this->request->data['pk'];
        $inicio = $this->request->data['inicio'];
        $fin = $this->request->data['fin'];
        //$periodos = $this->request->data['periodos'];


        $ticker= $this->Ticker->find('first', array(
            'recursive' => -1,
            'fields'=>array('Ticker.TIINGO_NAME'),
            'conditions'=>array('Ticker.PK_TICKER'=>$pk)
        ));

        $symbol=$ticker['Ticker']['TIINGO_NAME'];

        if (!empty($this->request->data)) {
            $opciones = array(
                'symbol' => $symbol,
                'startDate' => $inicio,
                'endDate' => $fin,
                'ohlc' => false,
                'closeOnly' => true,
                'apiKey' => '&token=ec521aefb5ccb5202dbf55eb950b19bc1e7a248f'
            );

        }

        if ($this->request->is('ajax')) {

            $precios = $this->Price->find('all', array(
                'conditions' => $opciones,
            ));

            $valores = array();
            $labels = array();
            foreach ($precios['Price'] as $dato) {
                array_push($valores, $dato['close']);
                array_push($labels, array(idate('z', strtotime($dato['date']))));
            }



            require_once(APP . 'Vendor' . '\php-ml' . '\Helper' . '\Predictable.php');
            require_once(APP . 'Vendor' . '\php-ml' . '\Estimator.php');
            require_once(APP . 'Vendor' . '\php-ml' . '\Regression' . '\Regression.php');
            require_once(APP . 'Vendor' . '\php-ml' . '\Math' . '\Matrix.php');
            require_once(APP . 'Vendor' . '\php-ml' . '\Math' . '\LinearAlgebra' . '\LUDecomposition.php');
            require_once(APP . 'Vendor' . '\php-ml' . '\Regression' . '\LeastSquares.php');


            $lsq=new \Phpml\Regression\LeastSquares();
            $lsq->train($labels,$valores);
            $regression=$lsq->getCoefficients();
            $intercept=$lsq->getIntercept();

            $linea=array();

            foreach ($labels as $l=>$n){
                array_push($linea,$n[0]*$regression[0]+$intercept);
            }

            $resultadofinal=array();
            $i=0;
            foreach ($precios['Price'] as $dato) {
                array_push($resultadofinal, array('date'=>$dato['date'],'close'=>$dato['close'],'lsq'=>$linea[$i]));
                $i++;
            }

            return new CakeResponse(array('body' => json_encode(array('response' => true, 'success' => true, 'lsq' => $resultadofinal)), 'status' => 200));

        }
    }

    public function minimosCuadradosRegresion(){

        $opciones = array();

        $this->autoLayout = false;
        $this->autoRender = false;

        $pk = $this->request->data['pk'];
        $inicio = $this->request->data['inicio'];
        $fin = $this->request->data['fin'];
        //$periodos = $this->request->data['periodos'];


        $ticker= $this->Ticker->find('first', array(
            'recursive' => -1,
            'fields'=>array('Ticker.TIINGO_NAME'),
            'conditions'=>array('Ticker.PK_TICKER'=>$pk)
        ));

        $symbol=$ticker['Ticker']['TIINGO_NAME'];

        if (!empty($this->request->data)) {
            $opciones = array(
                'symbol' => $symbol,
                'startDate' => $inicio,
                'endDate' => $fin,
                'ohlc' => false,
                'closeOnly' => true,
                'apiKey' => '&token=ec521aefb5ccb5202dbf55eb950b19bc1e7a248f'
            );

        }

        if ($this->request->is('ajax')) {

            $precios = $this->Price->find('all', array(
                'conditions' => $opciones,
            ));

            $valores = array();
            $labels = array();
            foreach ($precios['Price'] as $dato) {
                array_push($valores, $dato['close']);
                array_push($labels, array(idate('z', strtotime($dato['date']))));
            }



            require_once(APP . 'Vendor' . '\php-ml' . '\Helper' . '\Predictable.php');
            require_once(APP . 'Vendor' . '\php-ml' . '\Estimator.php');
            require_once(APP . 'Vendor' . '\php-ml' . '\Regression' . '\Regression.php');
            require_once(APP . 'Vendor' . '\php-ml' . '\Math' . '\Matrix.php');
            require_once(APP . 'Vendor' . '\php-ml' . '\Math' . '\LinearAlgebra' . '\LUDecomposition.php');
            require_once(APP . 'Vendor' . '\php-ml' . '\Regression' . '\LeastSquares.php');


            $lsq=new \Phpml\Regression\LeastSquares();
            $lsq->train($labels,$valores);
            $regression=$lsq->getCoefficients();
            $intercept=$lsq->getIntercept();

            $linea=array();

            foreach ($labels as $l=>$n){
                array_push($linea,$n[0]*$regression[0]+$intercept);
            }

            $resultadofinal=array();
            $i=0;
            foreach ($precios['Price'] as $dato) {
                array_push($resultadofinal, array('date'=>$dato['date'],'close'=>$dato['close'],'lsq'=>$linea[$i]));
                $i++;
            }

            return new CakeResponse(array('body' => json_encode(array('response' => true, 'success' => true, 'lsq' => $resultadofinal)), 'status' => 200));

        }
    }

    public function lag(){
        $opciones = array();

        $this->autoLayout = false;
        $this->autoRender = false;

        $pk = $this->request->data['pk'];
        $inicio = $this->request->data['inicio'];
        $fin = $this->request->data['fin'];
        $periodos = $this->request->data['periodos'];


        $ticker= $this->Ticker->find('first', array(
            'recursive' => -1,
            'fields'=>array('Ticker.TIINGO_NAME'),
            'conditions'=>array('Ticker.PK_TICKER'=>$pk)
        ));

        $symbol=$ticker['Ticker']['TIINGO_NAME'];

        if (!empty($this->request->data)) {
            $opciones = array(
                'symbol' => $symbol,
                'startDate' => $inicio,
                'endDate' => $fin,
                'ohlc' => false,
                'closeOnly' => true,
                'apiKey' => '&token=ec521aefb5ccb5202dbf55eb950b19bc1e7a248f'
            );

        }

        if ($this->request->is('ajax')) {

            $precios = $this->Price->find('all', array(
                'conditions' => $opciones,
            ));

            $valores = array();
            $labels = array();
            foreach ($precios['Price'] as $dato) {
                array_push($valores, $dato['close']);
                array_push($labels, date("Y-m-d", strtotime($dato['date'])));
            }


            $time_series_1="";
            foreach ($valores as $p){
                $time_series_1 =$time_series_1 ." ". strval($p);
            }
            $labels2="";
            foreach ($labels as $l){
                $labels2 =$labels2 ." ". strval($l);
            }

            /*$numeroFlags=sizeof($periodos);
            $flags=array();
            for($i=0;$i<$numeroFlags;$i++){
                $emas[$i]=trader_ema($close,$periodos[$i]);
            }*/
            require_once(APP . 'Vendor' . '\TS' . '\ts.class.php');
            //include('ts.class.php');
            $f = new TS("TimeSeriesOne",$time_series_1, $labels2); //build assigning directly the values and the labels

            $laggedf=$f->ts_lag($periodos[0]);
            $lagg=$f->ts_lag_plot($periodos[0]);

            $fechas=array();
            for($i=$periodos[0];$i<sizeof($labels);$i++){
                array_push($fechas,$labels[$i]);
            }
            for($i=0;$i<$periodos[0];$i++){
                array_push($fechas,$labels[$i]);
                array_push($laggedf,$valores[$i]);
            }

            $resultadofinal=array();
            for($i=0;$i<sizeof($valores);$i++){
                array_push($resultadofinal, array('date' => $fechas[$i], 'lagged' => $laggedf[$i]));
            }

            return new CakeResponse(array('body' => json_encode(array('response' => true, 'success' => true, 'lag' => $resultadofinal)), 'status' => 200));

        }
    }

    public function smooth(){
        $opciones = array();

        $this->autoLayout = false;
        $this->autoRender = false;

        $pk = $this->request->data['pk'];
        $inicio = $this->request->data['inicio'];
        $fin = $this->request->data['fin'];
        $periodos = $this->request->data['periodos'];


        $ticker= $this->Ticker->find('first', array(
            'recursive' => -1,
            'fields'=>array('Ticker.TIINGO_NAME'),
            'conditions'=>array('Ticker.PK_TICKER'=>$pk)
        ));

        $symbol=$ticker['Ticker']['TIINGO_NAME'];

        if (!empty($this->request->data)) {
            $opciones = array(
                'symbol' => $symbol,
                'startDate' => $inicio,
                'endDate' => $fin,
                'ohlc' => false,
                'closeOnly' => true,
                'apiKey' => '&token=ec521aefb5ccb5202dbf55eb950b19bc1e7a248f'
            );

        }

        if ($this->request->is('ajax')) {

            $precios = $this->Price->find('all', array(
                'conditions' => $opciones,
            ));

            $valores = array();
            $labels = array();
            foreach ($precios['Price'] as $dato) {
                array_push($valores, $dato['close']);
                array_push($labels, date("Y-m-d", strtotime($dato['date'])));
            }


            $time_series_1="";
            foreach ($valores as $p){
                $time_series_1 =$time_series_1 ." ". strval($p);
            }
            $labels2="";
            foreach ($labels as $l){
                $labels2 =$labels2 ." ". strval($l);
            }

            /*$numeroFlags=sizeof($periodos);
            $flags=array();
            for($i=0;$i<$numeroFlags;$i++){
                $emas[$i]=trader_ema($close,$periodos[$i]);
            }*/
            require_once(APP . 'Vendor' . '\TS' . '\ts.class.php');
            //include('ts.class.php');
            $f = new TS("TimeSeriesOne",$time_series_1, $labels2); //build assigning directly the values and the labels

            $smooth=$f->ts_smoothing_simple(0.5,null,$periodos[0]);
            //$smooth=$f->ts_smoothing_trend(0.5,0.5,null,0.0,$periodos[0]);
            //$alfa=0.5, $beta=0.5, $Szero = null, $Tzero = null, $init_periods = 0


            $resultadofinal=array();
            for($i=0;$i<sizeof($valores);$i++){
                array_push($resultadofinal, array('date' => $labels[$i], 'smooth' => $smooth[$i]));
            }

            return new CakeResponse(array('body' => json_encode(array('response' => true, 'success' => true, 'smooth' => $resultadofinal)), 'status' => 200));

        }
    }

    public function diff(){
        $opciones = array();

        $this->autoLayout = false;
        $this->autoRender = false;

        $pk = $this->request->data['pk'];
        $inicio = $this->request->data['inicio'];
        $fin = $this->request->data['fin'];
        $periodos = $this->request->data['periodos'];


        $ticker= $this->Ticker->find('first', array(
            'recursive' => -1,
            'fields'=>array('Ticker.TIINGO_NAME'),
            'conditions'=>array('Ticker.PK_TICKER'=>$pk)
        ));

        $symbol=$ticker['Ticker']['TIINGO_NAME'];

        if (!empty($this->request->data)) {
            $opciones = array(
                'symbol' => $symbol,
                'startDate' => $inicio,
                'endDate' => $fin,
                'ohlc' => false,
                'closeOnly' => true,
                'apiKey' => '&token=ec521aefb5ccb5202dbf55eb950b19bc1e7a248f'
            );

        }

        if ($this->request->is('ajax')) {

            $precios = $this->Price->find('all', array(
                'conditions' => $opciones,
            ));

            $valores = array();
            $labels = array();
            foreach ($precios['Price'] as $dato) {
                array_push($valores, $dato['close']);
                array_push($labels, date("Y-m-d", strtotime($dato['date'])));
            }



            $diff=array();
                for($i=0;$i<sizeof($valores)-1;$i++){
                    array_push($diff,$valores[$i+1]-$valores[$i]);
                }


            $resultadofinal=array();
            for($i=0;$i<sizeof($diff);$i++){
                array_push($resultadofinal, array('index' => $i, 'diff' => $diff[$i]));
            }

            return new CakeResponse(array('body' => json_encode(array('response' => true, 'success' => true, 'diff' => $resultadofinal)), 'status' => 200));

        }
    }

    public function logaritmic(){
        $opciones = array();

        $this->autoLayout = false;
        $this->autoRender = false;

        $pk = $this->request->data['pk'];
        $inicio = $this->request->data['inicio'];
        $fin = $this->request->data['fin'];
        $periodos = $this->request->data['periodos'];


        $ticker= $this->Ticker->find('first', array(
            'recursive' => -1,
            'fields'=>array('Ticker.TIINGO_NAME'),
            'conditions'=>array('Ticker.PK_TICKER'=>$pk)
        ));

        $symbol=$ticker['Ticker']['TIINGO_NAME'];

        if (!empty($this->request->data)) {
            $opciones = array(
                'symbol' => $symbol,
                'startDate' => $inicio,
                'endDate' => $fin,
                'ohlc' => false,
                'closeOnly' => true,
                'apiKey' => '&token=ec521aefb5ccb5202dbf55eb950b19bc1e7a248f'
            );

        }

        if ($this->request->is('ajax')) {

            $precios = $this->Price->find('all', array(
                'conditions' => $opciones,
            ));

            $valores = array();
            $labels = array();
            foreach ($precios['Price'] as $dato) {
                array_push($valores, $dato['close']);
                array_push($labels, date("Y-m-d", strtotime($dato['date'])));
            }



            $ln=array();
            for($i=0;$i<sizeof($valores);$i++){
                array_push($ln,log($valores[$i]));
            }


            $resultadofinal=array();
            for($i=0;$i<sizeof($ln);$i++){
                array_push($resultadofinal, array('date' => $labels[$i], 'ln' => $ln[$i]));
            }


            return new CakeResponse(array('body' => json_encode(array('response' => true, 'success' => true, 'ln' => $resultadofinal)), 'status' => 200));

        }
    }

    public function difflog(){
        $opciones = array();

        $this->autoLayout = false;
        $this->autoRender = false;

        $pk = $this->request->data['pk'];
        $inicio = $this->request->data['inicio'];
        $fin = $this->request->data['fin'];
        $periodos = $this->request->data['periodos'];


        $ticker= $this->Ticker->find('first', array(
            'recursive' => -1,
            'fields'=>array('Ticker.TIINGO_NAME'),
            'conditions'=>array('Ticker.PK_TICKER'=>$pk)
        ));

        $symbol=$ticker['Ticker']['TIINGO_NAME'];

        if (!empty($this->request->data)) {
            $opciones = array(
                'symbol' => $symbol,
                'startDate' => $inicio,
                'endDate' => $fin,
                'ohlc' => false,
                'closeOnly' => true,
                'apiKey' => '&token=ec521aefb5ccb5202dbf55eb950b19bc1e7a248f'
            );

        }

        if ($this->request->is('ajax')) {

            $precios = $this->Price->find('all', array(
                'conditions' => $opciones,
            ));

            $valores = array();
            $labels = array();
            foreach ($precios['Price'] as $dato) {
                array_push($valores, $dato['close']);
                array_push($labels, date("Y-m-d", strtotime($dato['date'])));
            }



            $diff=array();
            for($i=0;$i<sizeof($valores)-1;$i++){
                array_push($diff,log($valores[$i+1])-log($valores[$i]));
            }

            $resultadofinal=array();
            for($i=0;$i<sizeof($diff);$i++){
                array_push($resultadofinal, array('index' => $i, 'diff' => $diff[$i]));
            }

            return new CakeResponse(array('body' => json_encode(array('response' => true, 'success' => true, 'diff' => $resultadofinal)), 'status' => 200));

        }
    }

    public function smoothtrend(){
        $opciones = array();

        $this->autoLayout = false;
        $this->autoRender = false;

        $pk = $this->request->data['pk'];
        $inicio = $this->request->data['inicio'];
        $fin = $this->request->data['fin'];
        $periodos = $this->request->data['periodos'];


        $ticker= $this->Ticker->find('first', array(
            'recursive' => -1,
            'fields'=>array('Ticker.TIINGO_NAME'),
            'conditions'=>array('Ticker.PK_TICKER'=>$pk)
        ));

        $symbol=$ticker['Ticker']['TIINGO_NAME'];

        if (!empty($this->request->data)) {
            $opciones = array(
                'symbol' => $symbol,
                'startDate' => $inicio,
                'endDate' => $fin,
                'ohlc' => false,
                'closeOnly' => true,
                'apiKey' => '&token=ec521aefb5ccb5202dbf55eb950b19bc1e7a248f'
            );

        }

        if ($this->request->is('ajax')) {

            $precios = $this->Price->find('all', array(
                'conditions' => $opciones,
            ));

            $valores = array();
            $labels = array();
            foreach ($precios['Price'] as $dato) {
                array_push($valores, $dato['close']);
                array_push($labels, date("Y-m-d", strtotime($dato['date'])));
            }


            $time_series_1="";
            foreach ($valores as $p){
                $time_series_1 =$time_series_1 ." ". strval($p);
            }
            $labels2="";
            foreach ($labels as $l){
                $labels2 =$labels2 ." ". strval($l);
            }

            /*$numeroFlags=sizeof($periodos);
            $flags=array();
            for($i=0;$i<$numeroFlags;$i++){
                $emas[$i]=trader_ema($close,$periodos[$i]);
            }*/
            require_once(APP . 'Vendor' . '\TS' . '\ts.class.php');
            //include('ts.class.php');
            $f = new TS("TimeSeriesOne",$time_series_1, $labels2); //build assigning directly the values and the labels

            //$smooth=$f->ts_smoothing_simple(0.5,null,$periodos[0]);
            $smooth=$f->ts_smoothing_trend(0.5,0.5,$valores[0],0.0,$periodos[0]);
            //$alfa=0.5, $beta=0.5, $Szero = null, $Tzero = null, $init_periods = 0


            $resultadofinal=array();
            for($i=0;$i<sizeof($valores);$i++){
                array_push($resultadofinal, array('date' => $labels[$i], 'smooth' => $smooth[0][$i],'trend' => $smooth[1][$i]));
            }

            return new CakeResponse(array('body' => json_encode(array('response' => true, 'success' => true, 'smoothtrend' => $resultadofinal)), 'status' => 200));

        }
    }

    public function regresion(){
        $opciones = array();

        $this->autoLayout = false;
        $this->autoRender = false;

        $pk = $this->request->data['pk'];
        $inicio = $this->request->data['inicio'];
        $fin = $this->request->data['fin'];
        //$periodos = $this->request->data['periodos'];


        $ticker= $this->Ticker->find('first', array(
            'recursive' => -1,
            'fields'=>array('Ticker.TIINGO_NAME'),
            'conditions'=>array('Ticker.PK_TICKER'=>$pk)
        ));

        $symbol=$ticker['Ticker']['TIINGO_NAME'];

        if (!empty($this->request->data)) {
            $opciones = array(
                'symbol' => $symbol,
                'startDate' => $inicio,
                'endDate' => $fin,
                'ohlc' => false,
                'closeOnly' => true,
                'apiKey' => '&token=ec521aefb5ccb5202dbf55eb950b19bc1e7a248f'
            );

        }

        if ($this->request->is('ajax')) {

            $precios = $this->Price->find('all', array(
                'conditions' => $opciones,
            ));

            $valores = array();
            $labels = array();
            foreach ($precios['Price'] as $dato) {
                array_push($valores, $dato['close']);
                array_push($labels, date("Y-m-d", strtotime($dato['date'])));
            }


            $time_series_1="";
            foreach ($valores as $p){
                $time_series_1 =$time_series_1 ." ". strval($p);
            }
            $labels2="";
            foreach ($labels as $l){
                $labels2 =$labels2 ." ". strval($l);
            }

            require_once(APP . 'Vendor' . '\TS' . '\ts.class.php');
            //include('ts.class.php');
            $f = new TS("TimeSeriesOne",$time_series_1, $labels2); //build assigning directly the values and the labels
            /*$f->ts_name();
            $this->b();
            echo "<table border=1 cellpadding=10><tr><td>";
            $f->ts_print(true);
            echo "<td>";
            echo "Mean: ".$f->ts_mean();
            $this->b();
            echo "Variance: ".$f->ts_var();
            $this->b();
            echo "Sample Variance: ".$f->ts_var("nopop");
            $this->b();
            echo "StDev: ".$f->ts_stdev();
            echo "<td>";*/
            $grafica= $f->ts_plot(300,250,"Time Series");
            //echo "<td>";
            $acf=$f->ts_acf_plot(10,300,250,"ACF");
            //echo "</table>";
            $resultadofinal=array();
            array_push($resultadofinal,$grafica);
            array_push($resultadofinal,$acf);

            /*echo "<form name=timeseries action=example2 method=POST>";
            echo "Time series values<br/><textarea name=ts rows=5 cols=50>";
            if(!isset($_REQUEST['ts'])) echo $time_series_1;
            else echo $_REQUEST['ts'];
            echo "</textarea><br/>";
            echo "<br/><br/>Labels<br/> <textarea name=label rows=5 cols=50>";
            if(!isset($_REQUEST['label'])) echo $labels2;
            else echo $_REQUEST['label'];
            echo "</textarea><br/>";

            echo "<input type=submit>";
            echo "</form>";

            if(!isset($_REQUEST['ts'])) exit;*/


            /*$time_series_1 = "100.0 200.0  250.0 400.0  150.0 350.0 200.0 300";  //some default values
            //$labels = "1990 1991 1992 1993 1994 1995 1996 1997";  //labels for the time series values


            $f = new \ts\ts("TimeSeriesOne",$valores, $labels); //build assigning directly the values and the labels

            $f->ts_name();
            $this->b();
            echo "<table border=1 cellpadding=10><tr><td>";
            $f->ts_plot(300,250,"Time Series");
            echo "<td>";
            $f->ts_acf_plot(10,300,250,"ACF");
            echo "</table>";*/

            return new CakeResponse(array('body' => json_encode(array('response' => true, 'success' => true, 'acf' => $resultadofinal)), 'status' => 200));

        }
    }

}

?>