<?php
/**
 * Created by PhpStorm.
 * User: JOSESANZ
 * Date: 11/04/2018
 * Time: 13:40
 */

App::import('Vendor', 'Kachkaev\PHPR\RCore');
App::import('Vendor', 'Kachkaev\PHPR\Engine\CommandLineREngine');
class FractalController extends AppController
{
    var $temp_dir;
    var $R_path;
    var $R_path2;
    var $R_options_1;
    var $R_options_2;
    var $graphic;
    var $bannedCommandConfigFile;
    var $RCODE;
    var $Rerror;
    /**/
    /**
     * This controller does not use a model
     *
     * @var array
     */
    public $uses = array(
        'Price','Ticker','RClass',' Kachkaev\PHPR\RCore','Kachkaev\PHPR\Engine\CommandLineREngine'
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
            $this->set('tickers', $tickers);


            //echo $output;


            /*$urlPic= '/proyectofinal/app/Lib' . DS . 'output' . DS .'output.png';
            $urlDir= APP . DS . 'lib/output';

            echo "<img src= $urlPic>";

            $files = scandir(APP .'/lib/output/');

            sort($files);

            foreach($files as $file){
                echo'<a href="/app/lib/output/'.$file.'">'.$file.'</a>';
                echo '<br>';
            }*/
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

    public function rodogramPrediction(){

        $this->autoLayout = false;
        $this->autoRender = false;

        $symbol = $this->request->data['symbol'];
        $inicio = $this->request->data['inicio'];
        $principio = $this->request->data['principio'];


        $this -> temp_dir = APP . DS . 'tmp';
        $this -> R_path = "C:\Program Files\R\R-3.3.3\bin\R.exe";
        $this -> R_path2 = "C:\Program Files\R\R-3.3.3\bin\Rscript.exe";
        $this -> R_options_1 = "--quiet --no-restore --no-save  < ";
        $this -> R_options_2 = " > ";
        $this -> graphic = "png";
        $this -> bannedCommandConfigFile = APP . DS . 'Lib' . DS . 'security.txt';

        $files = glob('C:\xampp\htdocs\proyectofinal\app\webroot\img\output/*'); // glob() function searches for all the path names matching pattern

        foreach($files as $file){
            if(is_file($file))
                unlink($file); // delete
        }
        //require_once(APP . 'Vendor' . '\Kachkaev' . '\PHPR' . '\RCore');
        //require_once(APP . 'Vendor' . '\Kachkaev' . '\PHPR' . '\Engine'  . '\CommandLineREngine');

        $nbeds = 6; // number of beds / resources of the simulation
        $myrep = 5; // number of simulation runs
        $period = 7; // period of the simulation run
        $myIAT = 6; // Interarrival time
        $str='"Program Files"\R\R-3.3.3\bin\Rscript.exe xampp\htdocs\proyectofinal\app\Lib\fractalNuevo.R AMZN"';
        $str2='\"Program Files"\R\R-3.3.3\bin\Rscript.exe C:\xampp\htdocs\proyectofinal\app\Lib\fractalNuevo.R "'.$symbol.'" "'.$inicio.'" "'.$principio.'"';

        $output=exec($str2);

       
        //$output=exec('"C:\Program Files\R\R-3.3.3\bin\R.exe" CMD BATCH  "C:\xampp\htdocs\proyectofinal\app\Lib\fractalNuevo.R" $symbol');

        return new CakeResponse(array('body' => json_encode(array('response' => true, 'success' => true, 'fractal' =>$output )), 'status' => 200));
    }
}

/*"C:\Program Files\R\R-3.3.3\bin\>\Rscript 'C://xampp/htdocs/proyectofinal/app/Lib/myscript.R')"
C:\Program Files\R\R-3.3.3\bin\>\Rscript 'C://xampp/htdocs/proyectofinal/app/Lib/myscript.R')

    "C:\Program Files\R\R-3.3.3\bin\R.exe" CMD BATCH  "C:\xampp\htdocs\proyectofinal\app\Lib\myscript.R"*/