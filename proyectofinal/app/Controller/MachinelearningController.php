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

class MachinelearningController extends AppController
{

    /**
     * This controller does not use a model
     *
     * @var array
     */

    public $uses = array(
        'Price', 'Ticker', 'Mlalgo', 'php-ml\Math\Statistic\Mean'
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

        $menuItems = $this->Mlalgo->find('all', array(
            'recursive' => -1,
        ));


        $menus = array(
            'items' => array(),
            'parents' => array()
        );

        foreach ($menuItems as $items=>$item){
            // Create current menus item id into array
            $menus['items'][$item['Mlalgo']['PK_ML']] = $item;
            // Creates list of all items with children
            $menus['parents'][$item['Mlalgo']['FK_ML']][] = $item['Mlalgo']['PK_ML'];
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

    public function leastsquares(){

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


    public function km3D(){

        /*require_once( APP .'Vendor'. '\php-ml' . '\Clustering' . '\KMeans'. '\Cluster.php');
        require_once( APP .'Vendor'. '\php-ml' . '\Clustering' . '\KMeans'. '\Point.php');
        require_once( APP .'Vendor'. '\php-ml' . '\Clustering' . '\KMeans'. '\Space.php');
        require_once( APP .'Vendor'. '\php-ml' . '\Clustering' . '\Clusterer.php');
        require_once( APP .'Vendor'. '\php-ml' . '\Clustering' . '\DBSCAN.php');
        require_once( APP .'Vendor'. '\php-ml' . '\Clustering' . '\FuzzyCMeans.php');
        require_once( APP .'Vendor'. '\php-ml' . '\Clustering' . '\KMeans.php');*/

        $opciones = array();

        $this->autoLayout = false;
        $this->autoRender = false;

        $pk = $this->request->data['pk'];
        $inicio = $this->request->data['inicio'];
        $fin = $this->request->data['fin'];
        $nclusters=$this->request->data['periodos'];

        $ticker= $this->Ticker->find('first', array(
            'recursive' => -1,
            'fields'=>array('Ticker.TIINGO_NAME'),
            'conditions'=>array('Ticker.PK_TICKER'=>$pk)
        ));

        $symbol=$ticker['Ticker']['TIINGO_NAME'];

        if (!empty($this->request->data)) {
            $opciones = array(
                'symbol' =>$symbol,
                'startDate' => $inicio ,
                'endDate' => $fin,
                'ohlc'=>true,
                'closeOnly'=>false,
                'apiKey'=> '&token=ec521aefb5ccb5202dbf55eb950b19bc1e7a248f'
            );
        }


        if ($this->request->is('ajax')) {
            $precios = $this->Price->find('all',array(
                'conditions' => $opciones,
            ));

            $ohlc= array();
            //print_r("precios:"+$precios['Price']);
            foreach ($precios['Price'] as $dato) {
                $x=$dato['close']/$dato['open'];
                $y=$dato['high']/$dato['open'];
                $z=$dato['low']/$dato['open'];
                array_push($ohlc,array($x,$y,$z));
            }
            //print_r("ohlc: "+ $ohlc);

            $resultado=$this->kmeans3D($ohlc,$nclusters[0]);

            $numIter=$resultado[2];
            //$kmeans=new \Phpml\Clustering\KMeans(5,Phpml\Clustering\KMeans::INIT_RANDOM);
            //$kmeans->cluster($ohlc);


            $resultadoFinal=array();
            $resDevolver=array();
            $i=0;
            foreach ($resultado[1] as $cluster){
                $resultadoFinal[$i]=array();
                $resDevolver[$i]=array();
                for($r=0;$r<sizeof($cluster);$r++){
                    array_push($resultadoFinal[$i],$ohlc[$cluster[$r]]);
                    array_push($resDevolver[$i],$precios['Price'][$cluster[$r]]);
                }
                $i++;
            }

            //print_r($resultadoFinal);
            $file = fopen('C:\xampp\htdocs\proyectofinal\app\webroot\kmeans.csv', 'w');

// save the column headers
            fputcsv($file, array('x1', 'y1', 'z1'));

            $tamGroups=array();
// save each row of the data
            foreach ($resultadoFinal as $group){
                array_push($tamGroups,sizeof($group));
                foreach ($group as $row)
                {
                    fputcsv($file, $row);
                }
            }

// Close the file
            fclose($file);

            return new CakeResponse(array('body' => json_encode(array('response' => true, 'success' => true,'numIter'=>$numIter,'tamGrupos' => $tamGroups,'resultado'=>$resDevolver)), 'status' => 200));
        }
    }

    public function kmeans3D($data, $k) {
        if($k <= 0)
        {
            echo "<div class=\"error span-15\">ERROR: K must be a positive integer greater than 0</div>";
            exit(0);
        }

        $oldCentroids = $this->randomCentroids($data, $k);
        $numIter=0;
        while (true)
        {
            $numIter++;
            $clusters = $this->assign_points($data, $oldCentroids, $k);
            $newCentroids = $this->calcCenter($clusters, $data);
            if ($oldCentroids === $newCentroids)
            {
                return(array ($newCentroids, $clusters,$numIter));
            }
            $oldCentroids = $newCentroids;
        }

    }

    /**
     * Creates random starting clusters between the max and min of the data values
     * @param $data array An array containing the
     * @param $k int The number of clusters
     */
    public function randomCentroids($data, $k) {
        $length=sizeof($data);
        /*$x=array();
        $y=array();
        $z=array();
        foreach ($data as $j)
        {
            array_push($x,$j[0]);
            array_push($y,$j[1]);
            array_push($z,$j[2]);
        }

        for($k; $k > 0; $k--)
        {
            $xin=rand(0, $length-1);
            $yin=rand(0, $length-1);
            $zin=rand(0, $length-1);
            $centroids[$k][0] = $x[$xin];
            $centroids[$k][1] = $y[$yin];
            $centroids[$k][2] = $z[$zin];
        }*/


        $valapar=array();
        $centroids=array();
        for($i=0; $i<$k; $i++)
        {
            do{
                $val=rand(0,$length-1);
            }while(in_array($val,$valapar));

            array_push($centroids,$data[$val]);

            array_push($valapar,$val);

        }

        return $centroids;

    }

    public function assign_points($data, $centroids, $k)
    {

        foreach ($data as $datum_index => $datum)
        {
            $i=0;
            foreach ($centroids as $centroid)
            {
                $distances[$datum_index][$i] = $this->dist($datum, $centroid);
                $i++;
            }
        }

        //print_r($distances);
       // $distances_from_clusters = array();
        foreach ($distances as $distance_index => $distance)
        {
            $which_cluster = $this->min_key($distance);

            //print_r($which_cluster);
            $tentative_clusters[$which_cluster][] = $distance_index;
            //array_push($distances_from_clusters,array("$distance_index" => $distance));
            $distances_from_clusters = array("$distance_index" => $distance);
        }

        //print_r($tentative_clusters);
        //print_r($distances_from_clusters);
        //in case there's not enough clusters, take the farthest element from any of the cluster's centres
        //and make it a cluster.
        /*if (count($tentative_clusters) < $k)
        {
            $point_as_cluster = $this->max_key($distances_from_clusters);
            //print_r($point_as_cluster);
            foreach ($tentative_clusters as $tentative_index => $tentative_cluster)
            {
                foreach ($tentative_cluster as $tentative_element)
                {
                    if ($tentative_element == $point_as_cluster)
                    {
                        $clusters[count($tentative_clusters)][] = $tentative_element;
                    }
                    else $clusters[$tentative_index][] = $tentative_element;
                }
            }
        }
        else
        {
            $clusters = $tentative_clusters;
        }*/
        $clusters = $tentative_clusters;
        //print_r($clusters);
        return $clusters;

    }

    function max_value($array){
        $x=0;
        $y=0;
        $z=0;
        foreach($array as $row)
        {
            if($row[0] > $x)
                $x = $row[0];
            if($row[1] > $y)
                $y = $row[1];
            if($row[2] > $z)
                $z = $row[2];
        }
        return array($x, $y ,$z);
    }

    public function calcCenter($clusters, $data)
    {
        foreach($clusters as $num_cluster => $cluster_elements)
        {
            foreach ($cluster_elements as $cluster_element)
            {
                $cluster_elements_coords[$num_cluster][] = $data[$cluster_element];
            }
        }
        //print_r($cluster_elements_coords);
        //print_r($data);
        foreach ($cluster_elements_coords as $cluster_element_coords)
        {
            $cluster_centers[] = $this->recenter($cluster_element_coords);
        }
       // print_r($cluster_centers);
       // print_r($cluster_centers);
        return $cluster_centers;
    }

    /**
     * Calculates the center coordinates of a set of points
     * @param array $coords An array of x and y points
     * @return array An array containing the x and y coordinates of the center point
     */
    public function recenter($coords)
    {
        $x=array();
        $y=array();
        $z=array();
        foreach ($coords as $k)
        {
            array_push($x,$k[0]);
            array_push($y,$k[1]);
            array_push($z,$k[2]);
        }

        $center[0] = round(array_sum($x) / count($coords),16);
        $center[1] = round(array_sum($y) / count($coords),16);
        $center[2] = round(array_sum($z) / count($coords),16);

        return $center;
    }

    /**
     * Calculates the distance between two points
     * @param array $v1 An integer array with x and y coordinate values
     * @param array $v2 An integer array with x and y coordinate values
     * @return double The distance between the two points
     */
    public function dist($v1, $v2)
    {
        $x = abs($v1[0] - $v2[0]);
        $y = abs($v1[1] - $v2[1]);
        $z = abs($v1[2] - $v2[2]);
        return round(sqrt(($x * $x) + ($y * $y) + ($z * $z)),16);
    }
    /**
     * Assigns points to one of the centroids
     * @param array $data the data points to cluster
     * @param array $centroids The array of centroids
     * @param int $k The number of clusters
     */

    /**
     * Gets the index of the min value in the array
     * @param $array array The array of values to get the max index from
     * @return int Index of the min value
     */
    public function min_key($array) {
        $épsilon = 0.00001;
        $min=1000;
        $valo=array();
        foreach ($array as $k => $val) {
           array_push($valo,$val);
        }

        for($j=0;$j<sizeof($valo);$j++) {
            for ($i = 0; $i < sizeof($valo); $i++) {
                //if ($valo[$i] == min($valo)) return $i;
                if (($valo[$i] - $valo[$j]) < $épsilon && $valo[$i]<$min) {
                    $min=$valo[$i];
                }
            }
        }

        for($i=0;$i<sizeof($valo);$i++){
            if (($valo[$i] - $min) ==0.000000){
                $min=$i;
            }
        }

        return $min;
    }

    /**
     * Gets the index of the max value in the array
     * @param $array array The array of values to get the max index from
     * @return int Index of the max value
     */
    public function max_key($array){
        foreach ($array as $k => $val) {
            if ($val == max($array)) return $k;
        }
    }


}

?>
