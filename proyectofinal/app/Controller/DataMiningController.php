<?php
/**
 * Created by PhpStorm.
 * User: JOSESANZ
 * Date: 11/04/2018
 * Time: 13:40
 */
/*App::import('Vendor', 'php-ml\Clustering\KMeans\Cluster');
App::import('Vendor', 'php-ml\Clustering\KMeans\Point');
App::import('Vendor', 'php-ml\Clustering\KMeans\Space');
App::import('Vendor', 'php-ml\Clustering\Clusterer');
App::import('Vendor', 'php-ml\Clustering\DBSCAN');
App::import('Vendor', 'php-ml\Clustering\FuzzyCMeans');
App::import('Vendor', 'php-ml\Clustering\KMeans');*/
class DataMiningController extends AppController
{

    /**
     * This controller does not use a model
     *
     * @var array
     */
    public $uses = array(
        'Price','Ticker', 'Datamining'
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
        $menuItems = $this->Datamining->find('all', array(
            'recursive' => -1,
        ));


        $menus = array(
            'items' => array(),
            'parents' => array()
        );

        foreach ($menuItems as $items=>$item){
            // Create current menus item id into array
            $menus['items'][$item['Datamining']['PK_DM']] = $item;
            // Creates list of all items with children
            $menus['parents'][$item['Datamining']['FK_DM']][] = $item['Datamining']['PK_DM'];
        }


        $this->set(array(
            'tickers' => $tickers,
            'menu'=>$menus

        ));


        /*$opciones = array();

        $inicio = "2017-1-1";
        $fin = "2018-1-1";

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

        $precios = $this->Price->find('all',array(
            'conditions' => $opciones,
        ));

        $ohlc= array();
        $i=0;

        foreach ($precios['Price'] as $dato) {
            $ohlc[$i]=[$dato['open'],$dato['close']];
            $i++;
        }

        $resultado=$this->kmeans($ohlc,5);

        $resplot=$this->graph($ohlc,$resultado,50);

        $this->set('resplot',$resplot);*/
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
            $i=0;

            foreach ($precios['Price'] as $dato) {
                $x=$dato['close']/$dato['open'];
                $y=$dato['high']/$dato['open'];
                $z=$dato['low']/$dato['open'];
                $ohlc[$i]=[$x,$y,$z];
                $i++;
            }

            $resultado=$this->kmeans3D($ohlc,$nclusters[0]);


            //$kmeans=new \Phpml\Clustering\KMeans(5,Phpml\Clustering\KMeans::INIT_RANDOM);
            //$kmeans->cluster($ohlc);


            $resultadoFinal=array();
            $i=0;
            foreach ($resultado[1] as $cluster){
                $resultadoFinal[$i]=array();
                for($r=0;$r<sizeof($cluster);$r++){
                    array_push($resultadoFinal[$i],$ohlc[$cluster[$r]]);
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




            return new CakeResponse(array('body' => json_encode(array('response' => true, 'success' => true, 'tamGrupos' => $tamGroups)), 'status' => 200));
        }
    }

    public function kmeans3D($data, $k) {
        if($k <= 0)
        {
            echo "<div class=\"error span-15\">ERROR: K must be a positive integer greater than 0</div>";
            exit(0);
        }

        $oldCentroids = $this->randomCentroids($data, $k);
        while (true)
        {
            $clusters = $this->assign_points($data, $oldCentroids, $k);
            $newCentroids = $this->calcCenter($clusters, $data);
            if ($oldCentroids === $newCentroids)
            {
                return(array ($newCentroids, $clusters));
            }
            $oldCentroids = $newCentroids;
        }

    }


    function graph($data, $results, $jitter)
    {
        $x_coords=0;
        $y_coords=0;
        $xmin=0;
        $xmax=0;

        if($jitter)
            $data = $this->addjitter($data);

        $clusters = array();

        foreach($results[1] as $v)
        {

            $count = 0;		//Build the chd data to append
            foreach($v as $k)
            {
                $x_coords = $x_coords . "," . $data[$k][0];
                $y_coords = $y_coords . "," . $data[$k][1];
                $count++;
            }
            array_push($clusters, $count);
        }

        $x_coords = trim($x_coords, ",");
        $y_coords = trim($y_coords, ",");

        $resplot= "<img src=\"https://chart.googleapis.com/chart?cht=s&chd=t:" .
            $x_coords . "|" . $y_coords .
            $this->printCHM($clusters) .
            "&chxt=x,y&chs=620x460\"><br>";

        return $resplot;
    }
    /**
     * Adds jitter for visualization
     * @param array $data The array of data to add jitter to
     * @return array An array of data with jitter added to its points
     */
    function addJitter($data)
    {
        $max = $this->max_value($data);
        $i = 0;
        foreach($data as $k)
        {
            if(rand(0,1))
                $data[$i][0] = $k[0] + ($max[0] * (rand(0,15)/1000));
            else
                $data[$i][0] = $k[0] - ($max[0] * (rand(0,15)/1000));


            if(rand(0,1))
                $data[$i][1] = $k[1] + ($max[1] * (rand(0,15)/1000));
            else
                $data[$i][1] = $k[1] - ($max[1] * (rand(0,15)/1000));

            $i++;
        }
        return $data;
    }


    /**
     * Assigns a color and shape to each cluster
     * @param array $clusters The clusters array obtained from the kmeans results array
     * @retrurn string The chart marker string to be appended to the Google Charts URL
     */
    function printCHM($clusters)
    {
        $colors = array("FF0000", "00FF00", "0000FF", "FFFF00", "00FFFF", "FF00FF");
        $shapes = array("o", "s", "d", "c", "x");

        $count = -1; $str = "&chm=o,FFFFFF,0,0,0|";
        for($i = 0; $i < count($clusters); $i++)
        {
            $str .= "x" . "," . $colors[$i%5] . ",0,";
            $str .= $count + 1;
            $count = $count + $clusters[$i];
            $str .= ":" . $count . ",5|";
        }
        return trim($str, "|");
    }

    /**
     * Prints out text results of the kmeans clustering
     * @param array $data The initial data points from loadData
     * @param array $resutls The results array from the kmeans function
     */
    function printResults($data, $results)
    {
        $instances = array( );
        $count = 0;
        $string="";

        foreach($results[0] as $centroid)
        {
            echo "<b>Cluster" . ++$count . "</b><br>\r\n";

            echo nbsp(6) . "Centroid: (" .$centroid[0] . ", " . $centroid[1] . ")<br>\r\n";

            echo nbsp(6) . "Contains Points: ";
            foreach($results[1][$count-1] as $item)
            {
                $string .= ", " . $item;
            }
            echo trim($string, ",") . "<br><br>\r\n";
            $string = "";
            array_push($instances, count($results[1][$count-1]));

        }

        echo "<b>Clustered Instances:</b><br>\r\n";
        for($i = 0; $i < count($instances); $i++)
        {
            echo nbsp(6) . ($i+1) . nbsp(3) . $instances[$i] . nbsp(3) . $instances[$i]/array_sum($instances) . "<br>\r\n";
        }

    }

    /**
     * Gets the max values of a two dimentional array
     */
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


    public function kmeans($data, $k) {
        if($k <= 0)
        {
            echo "<div class=\"error span-15\">ERROR: K must be a positive integer greater than 0</div>";
            exit(0);
        }

        $oldCentroids = $this->randomCentroids($data, $k);
        while (true)
        {
            $clusters = $this->assign_points($data, $oldCentroids, $k);
            $newCentroids = $this->calcCenter($clusters, $data);
            if ($oldCentroids === $newCentroids)
            {
                return(array ($newCentroids, $clusters));
            }
            $oldCentroids = $newCentroids;
        }

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
        foreach ($cluster_elements_coords as $cluster_element_coords)
        {
            $cluster_centers[] = $this->recenter($cluster_element_coords);
        }
        return $cluster_centers;
    }

    /**
     * Calculates the center coordinates of a set of points
     * @param array $coords An array of x and y points
     * @return array An array containing the x and y coordinates of the center point
     */
    public function recenter($coords)
    {   $x=0;
        $y=0;
        $z=0;
        foreach ($coords as $k)
        {
            $x = $x + $k[0];
            $y = $y + $k[1];
            $z = $z + $k[2];
        }
        $center[0] = round($x / count($coords),8);
        $center[1] = round($y / count($coords),8);
        $center[2] = round($z / count($coords),8);
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
        return round(sqrt(($x * $x) + ($y * $y) + ($z*$z)),8);
    }
    /**
     * Assigns points to one of the centroids
     * @param array $data the data points to cluster
     * @param array $centroids The array of centroids
     * @param int $k The number of clusters
     */
    public function assign_points($data, $centroids, $k)
    {
        foreach ($data as $datum_index => $datum)
        {
            foreach ($centroids as $centroid)
            {
                $distances[$datum_index][] = $this->dist($datum, $centroid);
            }
        }
        foreach ($distances as $distance_index => $distance)
        {
            $which_cluster = $this->min_key($distance);
            $tentative_clusters[$which_cluster][] = $distance_index;
            $distances_from_clusters = array("$distance_index" => $distance);
        }
        //in case there's not enough clusters, take the farthest element from any of the cluster's centres
        //and make it a cluster.
        if (count($tentative_clusters) < $k)
        {
            $point_as_cluster = $this->max_key($distances_from_clusters);
            foreach ($tentative_clusters as $tentative_index => $tentative_cluster)
            {
                foreach ($tentative_cluster as $tentative_element)
                {
                    if ($tentative_element == $point_as_cluster)
                    {
                        $clusters[$k+1][] = $tentative_element;
                    }
                    else $clusters[$tentative_index][] = $tentative_element;
                }
            }
        }
        else
        {
            $clusters = $tentative_clusters;
        }
        return $clusters;
    }

    /**
     * Creates random starting clusters between the max and min of the data values
     * @param $data array An array containing the
     * @param $k int The number of clusters
     */
    public function randomCentroids($data, $k) {
        $length=sizeof($data);
        foreach ($data as $j)
        {
            $x[] = $j[0];
            $y[] = $j[1];
            $z[] = $j[2];
        }

        for($k; $k > 0; $k--)
        {
            $xin=rand(0, $length-1);
            $yin=rand(0, $length-1);
            $zin=rand(0, $length-1);
            $centroids[$k][0] = $x[$xin];
            $centroids[$k][1] = $y[$yin];
            $centroids[$k][2] = $z[$zin];
        }
        return $centroids;

    }

    /**
     * Gets the index of the min value in the array
     * @param $array array The array of values to get the max index from
     * @return int Index of the min value
     */
    public function min_key($array) {
        foreach ($array as $k => $val) {
            if ($val == min($array)) return $k;
        }
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


    /**
     * Loads data from MySQL into an array
     * @param $column1 string The name of the first MySQL field to use
     * @param $column2 string The name of the second MySQL field to use
     * @return array The data loaded into two dimensional array, col1 is the first value, followed by col2
     */
    public function loadData($column1, $column2)
    {
        if(0 == strcmp($column1, $column2))
            $query = "SELECT $column1 from " . TABLE  ." LIMIT 100";
        else
            $query = "SELECT $column1, $column2 from " . TABLE . " LIMIT 100";

        $result = mysql_query($query);
        while($row = mysql_fetch_assoc($result))
        {
            $data[$i] = array($row[$column1], $row[$column2]);
            $i++;
        }

        return $data;
    }





}