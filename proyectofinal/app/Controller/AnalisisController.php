<?php
/**
 * Created by PhpStorm.
 * User: JOSESANZ
 * Date: 11/04/2018
 * Time: 13:40
 */
class AnalisisController extends AppController
{

    /**
     * This controller does not use a model
     *
     * @var array
     */
    public $uses = array(
        'Price','Ticker','Indicadore'
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

        $menuItems = $this->Indicadore->find('all', array(
            'recursive' => -1,
        ));


        $menus = array(
            'items' => array(),
            'parents' => array()
        );

        foreach ($menuItems as $items=>$item){
            // Create current menus item id into array
            $menus['items'][$item['Indicadore']['PK_INDICADORE']] = $item;
            // Creates list of all items with children
            $menus['parents'][$item['Indicadore']['FK_INDICADORE']][] = $item['Indicadore']['PK_INDICADORE'];
        }


        $this->set(array(
            'tickers' => $tickers,
            'menu'=>$menus

        ));
    }

    public function searchStockPrice(){
        $opciones = array();

        $this->autoLayout = false;
        $this->autoRender = false;

        $pk = $this->request->data['pk'];
        $inicio = $this->request->data['inicio'];
        $fin = $this->request->data['fin'];

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
                'ohlc'=>false,
                'closeOnly'=>false,
                'apiKey'=> '&token=ec521aefb5ccb5202dbf55eb950b19bc1e7a248f'
            );
        }

        if ($this->request->is('ajax')) {
            $precios = $this->Price->find('all',array(
                'conditions' => $opciones,
            ));

            return new CakeResponse(array('body' => json_encode(array('response' => true, 'success' => true, 'precios' => $precios)), 'status' => 200));
        }
    }



    public function macd(){
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
                'symbol' =>$symbol,
                'startDate' => $inicio ,
                'endDate' => $fin,
                'ohlc'=>false,
                'closeOnly'=>true,
                'apiKey'=> '&token=ec521aefb5ccb5202dbf55eb950b19bc1e7a248f'
            );
        }

        if ($this->request->is('ajax')) {
            $precios = $this->Price->find('all',array(
                'conditions' => $opciones,
            ));

            $close=array();
            foreach ($precios['Price'] as $key=>$p){
                array_push($close,$p['close']);
            }


                # Create the MACD signal and pass in the three parameters: fast period, slow period, and the signal.
                # we will want to tweak these periods later for now these are fine.
                #  data, fast period, slow period, signal period (2-100000)

                # array $real [, integer $fastPeriod [, integer $slowPeriod [, integer $signalPeriod ]]]
                $macd = trader_macd($close, $periodos[0], $periodos[1], $periodos[2]);
                $macd_raw = $macd[0];
                $signal   = $macd[1];
                $hist     = $macd[2];
                $macd[3]=array();
            foreach ($precios['Price'] as $key=>$p){
                array_push($macd[3],$p['date']);
            }

            $resultado=array();
            $macdini=$periodos[1]-1;
            $sigini=$macdini+($periodos[2]-1);
            $tamRes=sizeof($macd[3]);

            for($i=0;$i<$sigini;$i++){
               array_push($resultado,array('date'=>$macd[3][$i],'macd'=>0,'sig'=>0));
            }
            for($i=$sigini;$i<$tamRes;$i++){
                array_push($resultado,array('date'=>$macd[3][$i],'macd'=>$macd[0][$i],'sig'=>$macd[1][$i]));
            }


                //If not enough Elements for the Function to complete
                if(!$macd || !$macd_raw){
                    return 0;
                }

                /*#$macd = $macd_raw[count($macd_raw)-1] - $signal[count($signal)-1];
                $macd = (array_pop($macd_raw) - array_pop($signal));
                # Close position for the pair when the MACD signal is negative
                if ($macd < 0) {
                    return -1;
                    # Enter the position for the pair when the MACD signal is positive
                } elseif ($macd > 0) {
                    return 1;
                } else {
                    return 0;
                }*/

            return new CakeResponse(array('body' => json_encode(array('response' => true, 'success' => true, 'macd' => $resultado)), 'status' => 200));
        }
    }

    public function sma(){
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
                'symbol' =>$symbol,
                'startDate' => $inicio ,
                'endDate' => $fin,
                'ohlc'=>false,
                'closeOnly'=>true,
                'apiKey'=> '&token=ec521aefb5ccb5202dbf55eb950b19bc1e7a248f'
            );
        }

        if ($this->request->is('ajax')) {
            $precios = $this->Price->find('all',array(
                'conditions' => $opciones,
            ));

            $close=array();
            foreach ($precios['Price'] as $key=>$p){
                array_push($close,$p['close']);
            }

            $numeroMA=sizeof($periodos);


            $smas=array();
            for($i=0;$i<$numeroMA;$i++){
                $smas[$i]=trader_sma($close,$periodos[$i]);
            }


            $fechas=array();


            foreach ($precios['Price'] as $key=>$p){
                array_push($fechas,$p['date']);
            }

            $resultado=array();
            $tamRes=sizeof($fechas);

            /*for($j=0;$j<$numeroMA;$j++) {
                $r=array();
                for ($i = 0; $i < $periodos[$j]; $i++) {
                    array_push($r, array('date' => $fechas[$i], 'sma' => 0));
                }
                for ($i = $periodos[$j]; $i < $tamRes; $i++) {
                    array_push($r, array('date' => $fechas[$i], 'sma' => $smas[$j][$i]));
                }
                $resultado[$j]=$r;
            }*/

            $resultadofinal=array();
            $posicionMedias=array();
            $posiciones=array();


            for($i=0;$i<$periodos[0];$i++){
                array_push($posicionMedias,array(0,0));
                array_push($posiciones,array(false,"H"));
                array_push($resultadofinal, array('date' => $fechas[$i],'in'=>$posiciones[$i][0],'signal'=>$posiciones[$i][1],));
            }

            for ($i = $periodos[0]; $i < $periodos[1]; $i++) {
                array_push($posicionMedias,array(0,0));
                array_push($posiciones,array(false,"H"));
                array_push($resultadofinal, array('date' => $fechas[$i],'in'=>$posiciones[$i][0],'signal'=>$posiciones[$i][1], 'sma1' => $smas[0][$i]));
            }

            for ($i = $periodos[1]; $i < $periodos[2]; $i++) {

                if($smas[0][$i]<$smas[1][$i]){
                    array_push($posicionMedias,array(-1,0));
                }else{
                    array_push($posicionMedias,array(1,0));
                }

                $check=$posicionMedias[$i][0]-$posicionMedias[$i-1][0];

                switch ($check){
                    case 1:
                        array_push($posiciones,array(false,"H"));
                        break;
                    case -1:
                        array_push($posiciones,array(false,"H"));
                        break;
                    case 0: //seguimos arriba o abajo
                        $aux=$posiciones[$i-1];
                        if($aux[0]==true){
                            array_push($posiciones,array(true,"H"));
                        }else{
                            array_push($posiciones,array(false,"H"));
                        }
                        break;
                    case 2://cortamos hacia arriba
                        $aux=$posiciones[$i-1];
                        if($aux[0]==true){
                            array_push($posiciones,array(true,"H"));
                        }else{
                            array_push($posiciones,array(true,"B"));
                        }
                        break;
                    case -2://cortamos hacia abajo
                        $aux=$posiciones[$i-1];
                        if($aux[0]==false){
                            array_push($posiciones,array(false,"H"));
                        }else{
                            array_push($posiciones,array(false,"S"));
                        }
                        break;
                }

                array_push($resultadofinal, array('date' => $fechas[$i],'in'=>$posiciones[$i][0],'signal'=>$posiciones[$i][1], 'sma1' => $smas[0][$i],'sma2' => $smas[1][$i]));
            }

            for ($i = $periodos[2]; $i < $tamRes; $i++) {
                if($smas[0][$i]<$smas[2][$i]){
                    if($smas[0][$i]<$smas[1][$i]){
                        array_push($posicionMedias,array(-1,-1));
                    }else{
                        array_push($posicionMedias,array(1,-1));
                    }
                }else{
                    if($smas[0][$i]<$smas[1][$i]){
                        array_push($posicionMedias,array(-1,1));
                    }else{
                        array_push($posicionMedias,array(1,1));
                    }
                }

                $check=$posicionMedias[$i][1]-$posicionMedias[$i-1][1];

                switch ($check){
                    case 1:
                        $aux=$posiciones[$i-1];
                        if($aux[0]==true){
                            array_push($posiciones,array(true,"H"));
                        }else{
                            array_push($posiciones,array(false,"H"));
                        }
                        break;
                    case -1:
                        $aux=$posiciones[$i-1];
                        if($aux[0]==true){
                            array_push($posiciones,array(true,"H"));
                        }else{
                            array_push($posiciones,array(false,"H"));
                        }
                        break;
                    case 0: //seguimos arriba o abajo
                        $aux=$posiciones[$i-1];
                        if($aux[0]==true){
                            array_push($posiciones,array(true,"H"));
                        }else{
                            array_push($posiciones,array(false,"H"));
                        }
                        break;
                    case 2://cortamos hacia arriba
                        $aux=$posiciones[$i-1];
                        if($aux[0]==true){
                            array_push($posiciones,array(true,"H"));
                        }else{
                            array_push($posiciones,array(true,"BB"));
                        }
                        break;
                    case -2://cortamos hacia abajo
                        $aux=$posiciones[$i-1];
                        if($aux[0]==false){
                            array_push($posiciones,array(false,"H"));
                        }else{
                            array_push($posiciones,array(false,"SS"));
                        }
                        break;
                }

                array_push($resultadofinal, array('date' => $fechas[$i],'in'=>$posiciones[$i][0],'signal'=>$posiciones[$i][1], 'sma1' => $smas[0][$i],'sma2' => $smas[1][$i],'sma3' => $smas[2][$i]));
            }

            if(!$smas){
                return 0;
            }

            return new CakeResponse(array('body' => json_encode(array('response' => true, 'success' => true, 'sma' => $resultadofinal,'posiciones'=>$posiciones)), 'status' => 200));
        }
    }

    public function ema(){
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
                'symbol' =>$symbol,
                'startDate' => $inicio ,
                'endDate' => $fin,
                'ohlc'=>false,
                'closeOnly'=>true,
                'apiKey'=> '&token=ec521aefb5ccb5202dbf55eb950b19bc1e7a248f'
            );
        }

        if ($this->request->is('ajax')) {
            $precios = $this->Price->find('all',array(
                'conditions' => $opciones,
            ));

            $close=array();
            foreach ($precios['Price'] as $key=>$p){
                array_push($close,$p['close']);
            }

            $numeroMA=sizeof($periodos);


            $emas=array();
            for($i=0;$i<$numeroMA;$i++){
                $emas[$i]=trader_ema($close,$periodos[$i]);
            }


            $fechas=array();


            foreach ($precios['Price'] as $key=>$p){
                array_push($fechas,$p['date']);
            }

            $resultado=array();
            $tamRes=sizeof($fechas);

            for($j=0;$j<$numeroMA;$j++) {
                $r=array();
                for ($i = 0; $i < $periodos[$j]; $i++) {
                    array_push($r, array('date' => $fechas[$i], 'sma' => 0));
                }
                for ($i = $periodos[$j]; $i < $tamRes; $i++) {
                    array_push($r, array('date' => $fechas[$i], 'sma' => $emas[$j][$i]));
                }
                $resultado[$j]=$r;
            }

            $resultadofinal=array();
            /*for ($i = 0; $i < $periodos[0]; $i++) {
                array_push($resultadofinal, array('date' => $fechas[$i], 'sma1' => 0,'sma2' => 0,'sma3' => 0));
            }*/
            for ($i = $periodos[0]; $i < $periodos[1]; $i++) {
                array_push($resultadofinal, array('date' => $fechas[$i], 'ema1' => $emas[0][$i]));
            }
            for ($i = $periodos[1]; $i < $periodos[2]; $i++) {
                array_push($resultadofinal, array('date' => $fechas[$i], 'ema1' => $emas[0][$i],'ema2' => $emas[1][$i]));
            }
            for ($i = $periodos[2]; $i < $tamRes; $i++) {
                array_push($resultadofinal, array('date' => $fechas[$i], 'ema1' => $emas[0][$i],'ema2' => $emas[1][$i],'ema3' => $emas[2][$i]));
            }

            //If not enough Elements for the Function to complete
            if(!$emas){
                return 0;
            }



            return new CakeResponse(array('body' => json_encode(array('response' => true, 'success' => true, 'ema' => $resultadofinal)), 'status' => 200));
        }
    }

    public function soporte(){
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
                'symbol' =>$symbol,
                'startDate' => $inicio ,
                'endDate' => $fin,
                'ohlc'=>false,
                'closeOnly'=>true,
                'apiKey'=> '&token=ec521aefb5ccb5202dbf55eb950b19bc1e7a248f'
            );
        }

        if ($this->request->is('ajax')) {
            $precios = $this->Price->find('all',array(
                'conditions' => $opciones,
            ));

            $close=array();
            foreach ($precios['Price'] as $key=>$p){
                array_push($close,$p['close']);
            }

            $min=trader_min($close,sizeof($close));
            $max=trader_max($close,sizeof($close));

            foreach ($min as $key=>$p){
                $i=$p;
            }
            foreach ($max as $key=>$p){
                $k=$p;
            }
            print_r($min);

            print_r($max);

            $minn=$i;
            $maxx=$k;
            foreach ($precios['Price'] as $p){
                array_push($p,array('min'=>$minn));
                array_push($p,array('max'=>$maxx));

            }

            print_r(json_encode(array('response' => true, 'success' => true, 'precios' => $precios)));
            print_r($precios);
            print_r($o);
            die();
            die();
            die();
            return new CakeResponse(array('body' => json_encode(array('response' => true, 'success' => true, 'precios' => $precios)), 'status' => 200));

             }
    }

}