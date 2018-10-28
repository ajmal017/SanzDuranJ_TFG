<?php
//------------------------------------------------------------------------------

/*! \file ts.class.php
    \brief Main class file
    \author Roberto Pinto (aka Libra Veroen)
    \version 1.1
    \date September '06
    
    Class TS (ts.class.php)

    Performs basic operations on time series
    Created by: Roberto Pinto (aka Libra Veroen)
    Version: 1.1
    Status: beta
    Licence: GPL
    Release date: September '06
*/
//------------------------------------------------------------------------------

//-------CONFIGURATION SECTION (START)------------------------------------------
App::import('Vendor', 'TS\ts.class');
App::import('Vendor', 'TS\ts_msg');
define('TSDEBUG',0);    //set to 1 to show some debug messages
define('TSLANG',"it");  //set the language: it (italian) or en (english)�
define('USE_JGRAPH',"YES");  //set to YES if you want to use jgraph libraries
define('JGRAPH_PATH',APP .DS.'Vendor'. DS . 'jpgraph'.DS); //define the path to jgraph libraries
define('JGRAPH_IMAGE_PATH',APP.'webroot'. DS . 'img'. DS . 'ts' . DS); // set the path to the jgraph image repository
//-------CONFIGURATION SECTION (END)--------------------------------------------

//-------INCLUDING CLASSES AND LIBRARIES (START)--------------------------------
//include('app/Vendor/TS/ts_msg.php.inc');  // a really basic class to handle messages in different languages
require_once(APP . 'Vendor' . '\TS' . '\ts_msg.php.inc');
if(USE_JGRAPH == "YES") //includes the jgraph libraries
  {
  include_once(JGRAPH_PATH.'jpgraph.php');
  include_once(JGRAPH_PATH.'jpgraph_scatter.php');
  include_once(JGRAPH_PATH.'jpgraph_line.php');
  include_once(JGRAPH_PATH.'jpgraph_bar.php');
  }
//-------INCLUDING CLASSES AND LIBRARIES (END)----------------------------------

//-------MAIN CLASS (START)-----------------------------------------------------
/*! \class TS 
    \brief Class to performs basic operations on time series    
   
*/

class TS    //main class
  {

  var $name = null;  //name of the time series
  var $ts = array(); //the array containing the values of the time series 
  var $len; //the length (i.e. number of elements) of the time series
  var $labels = array(); //labels for the values
  var $messages = null;  //message handler
  

//------------------------------------------------------------------------------    
/*!
\brief Constructor
\param $name Name of the time series
\param $s String to be parsed for time series values
\param $l Array of labels 
*/
  function TS($name="", $s=null, $l=null)  //constructor
    {
    $this->name = $name;
    $this->messages = new TS_msg();
    
    if($s == null)
      {
      $this->ts = null;
      $this->len = 0;
      }
    else
      {
      $this->ts_parseline($s);  //parse the argument s which contains the values of the time series
      }
      
    if($l == null)
      $this->label = null;
    else
      $this->ts_setlabel($l);

        $files = glob(JGRAPH_IMAGE_PATH.'/*'); // glob() function searches for all the path names matching pattern

        foreach($files as $file){
            if(is_file($file))
                unlink($file); // delete
        }
      
    }

//------------------------------------------------------------------------------    
//! Parse a text line for time series values
/*!
      \param $s A string containing the time series values
      \param $exclude_zeroes if true, exclude the zeroes from the time series
      \return Set the class variable $ts
*/
  function ts_parseline($s, $exclude_zeroes = false)
    {
    $ts = str_word_count($s, 1, '0123456789.'); //parse the input string
    
    //force the cast to float values
    for($i = 0; $i < count($ts); $i++)
      $this->ts[$i] = (float) $ts[$i];
    
//count the number of values in the time series
//if $exclude_zeroes is true, the 0s are excluded from the count.
//by default, zeroes are included    
    ($exclude_zeroes) ? $this->len = $this->ts_count() : $this->len = count($this->ts);  
    if(TSDEBUG) $this->ts_print(true);
    }  

//------------------------------------------------------------------------------    
//! Set the label for the time series
/*!
      \param $label Labels fot the time series
      \return Set the class variable $labels
*/

  function ts_setlabel($label=array())  //set the labels for the values
    {
    $this->labels = str_word_count($label, 1, '0123456789.-_'); //parse the label string
    (count($this->labels) != $this->len) ? $this->messages->m(TSLANG,"TS_LABEL_VALUE_MISMATCH") : true ;
    }
//------------------------------------------------------------------------------
//! Count the number of elements in a time series, excluding zeroes
/*!
     \return Set the class variable $len
*/
  function ts_count()  
    {
     foreach($this->ts as $t=>$v)
      {
      if($v > 0)  
        {   
        $this->len++;
        }
      }
    }

//------------------------------------------------------------------------------    
//! Get the time series name
/*!
  \return The name of the time series
*/  
  function ts_name()
    {
    echo $this->name;
    }
//------------------------------------------------------------------------------
//! Print the time series values
/*!
  \param $with_label If true, print the labels
  \return The name of the time series
*/          
  function ts_print($with_label=true)  
    {
    if($with_label)
      {
      if($this->labels == null)
        {
        $this->messages->m(TSLANG,"TS_LABEL_NOT_DEFINED", false);
        
        for($i = 0; $i < $this->len; $i++)
          {
          echo $i." ".$this->ts[$i]."<br/>";
          }
        }
      else
        {
        for($i = 0; $i < $this->len; $i++)
          {
          echo $this->labels[$i]." - ".$this->ts[$i]."<br/>";
          }
        }
      }
      else
      {
      for($i = 0; $i < $this->len; $i++)
          {
          echo $this->ts[$i]."<br/>";
          }
      }
    }
    
//------------------------------------------------------------------------------    
//! Calculate the mean value of the time series
/*!
  \return The mean of the time series
*/  
  
  function ts_mean()  
    {
    $m = (float)0.0;
    
    foreach($this->ts as $t=>$v)
      {
      ($v > 0) ? $m += $v : true;
      }  
      $m /= $this->len;
    
    return (float)$m;
    
    }

//------------------------------------------------------------------------------    
//! Calculate the weighted mean of the time series
/*!
  \param $weights The weights to be used
  \return The weighted mean of the time series
*/  
  function ts_weighted_mean($weights=array()) //calculate the weighted mean value of the time series
    {
    $m = (float) 0.0;
    if(count($weights) != $this->len)
      {
      $this->messages->m(TSLANG,"TS_VALUES_WEIGHTS_MISMATCH", true);
      }
    
    for($i = 0; $i < $this->len; $i++)
      {
      $m += ($this->ts[$i] * $weights[$i]); 
      }  
      $m /= $this->len;    
     
     return (float)$m; 
    }

//------------------------------------------------------------------------------
//! Calculate the moving average
/*!
  \param $periods Number of periods to consider
  \return The moving average
  \todo   Return an array of the values 
*/  
  function ts_moving_average($periods=3) //calculate and print the weighted moving average
    {
    $m = (float) 0.0;
    ($periods <= 0) ? $this->messages->m(TSLANG,"TS_WRONG_PERIODS_VALUE", true) : true;
    
    for($i = $periods-1; $i < $this->len; $i++)
      {
      for($j = $periods-1; $j >= 0 ; $j--)
        {
        $m += $this->ts[$i-$j];
        }
        $m /= $periods;
        echo "<br>$m";
        $m = 0.0;
      }
    }

//------------------------------------------------------------------------------
//! Calculate the weighted moving average
/*!
  \param $periods Number of periods to consider
  \param $weights Weights to use
  \return The weighted moving average
  \todo   Return an array of the values 
*/  
  function ts_moving_weighted_average($periods=3, $weights=array()) //calculate and print the weighted moving average
    {
    if(count($weights) != $periods)
      {
      $this->messages->m(TSLANG,"TS_VALUES_WEIGHTS_MISMATCH", true);
      }
    
    
    $m = (float) 0.0;
    
    ($periods <= 0) ? $this->messages->m(TSLANG,"TS_WRONG_PERIODS_VALUE", true): true;
    
    for($i = $periods-1; $i < $this->len; $i++)
      {
      for($j = $periods-1; $j >= 0 ; $j--)
        {
        $m += ($this->ts[$i-$j]*$weights[$periods-$j-1]);
        }
        $m /= $periods;
        echo "<br>$m";
        $m = 0.0;
      }
    }

//------------------------------------------------------------------------------
//! Calculate the standard deviation of the time series
/*!
*/
  function ts_stdev()  //calculate the standard deviation
    {
    return sqrt($this->ts_var());
    }

//------------------------------------------------------------------------------
//! Calculate the variance of the time series
/*!
  \param $type (To BE DONE)
*/

  function ts_var($type="pop")  //calculate the variance
  //pop for the population variance
    {
    $mean = $this->ts_mean();
    $m = 0.0;
    for($i = 0; $i < $this->len; $i++)
      {
      $m += ($this->ts[$i]-$mean)*($this->ts[$i]-$mean);
      }
      if($type === "pop") return ($m /= (($this->len)-1));
      else return ($m /= (($this->len)));
    }
    
//------------------------------------------------------------------------------
//! Evaluate the autocorrelation of the time series
/*!
  \param $lags Number of lags
*/
  function ts_acf($lags=10)  
  //according to http://www.itl.nist.gov/div898/handbook/eda/section3/autocopl.htm
  //for more usefullness, do not use this function directly. Use ts_acf_plot instead
    {
    $var = $this->ts_var("sample");
    $mean = $this->ts_mean();
    $n = $this->len;
    $ch = array();
    
     for($i = 0; $i < $lags; $i++)
      {
      $ch[$i] = (float) 0.0;
      
      for($j = 0; $j < ($n - $i); $j++)
        {
        $ch[$i] += ($this->ts[$j] - $mean)*($this->ts[$j+$i] - $mean);
        }
        $ch[$i] /= $n;
        $ch[$i] /= $var;
      }    
    return $ch;
    }

//------------------------------------------------------------------------------
//! Plot the autocorrelation of the time series
/*!
  \param $lags Number of lags
  \param $x Width of the graph
  \param $y Height of the graph
  \param $title Title of the graph
*/
  function ts_acf_plot($lags, $x, $y, $title)  //plot the ACF
    {
    $v = $this->ts_acf($lags);
     
    $g = $this->ts_graph($x, $y, $title);
    $p = $this->ts_data($v, "BAR");
    $g->add($p);
    $g->Stroke(JGRAPH_IMAGE_PATH."data.png");
      return "<br/><img src=".JGRAPH_IMAGE_PATH."data.png>";
    }



//------------------------------------------------------------------------------    

  function ts_plot($x, $y, $title)    
    {
    $g = $this->ts_graph($x, $y, $title);
    $p = $this->ts_data($this->ts, "LINE");
    $g->add($p);
    $g->Stroke(JGRAPH_IMAGE_PATH."ts.png");
      return "<br/><img src=".JGRAPH_IMAGE_PATH."ts.png>";
    }
    
//------------------------------------------------------------------------------
//! Calculate the linear regression coefficients alfa and beta
/*!
  \param $descriptor Array of the independent variables Xt (see note)
  \note Yt = alfa * Xt + beta
  \return array($alfa, $beta)
*/
  function ts_simple_linear_regression($descriptor)  //calculate the linear regression interpolation
    {
    
    ($descriptor->len != $this->len) ? $this->messages->m(TSLANG,"TS_VALUES_DESCRIPTORS_MISMATCH", true) : true;
    
    $Sxx = 0.0;
    $Syy = 0.0;
    $Sxy = 0.0;
    $xmean = $descriptor->ts_mean();
    $ymean = $this->ts_mean();
    
    for($i = 0; $i < $this->len; $i++)
      {
      $Sxx += ($xmean - $descriptor->ts[$i])*($xmean - $descriptor->ts[$i]);
      $Syy += ($ymean - $this->ts[$i])*($ymean - $this->ts[$i]);
      $Sxy += ($xmean - $descriptor->ts[$i])*($ymean - $this->ts[$i]);     
      }
    $Sxx /= $this->len;
    $Syy /= $this->len;
    $Sxy /= $this->len;
    $alfa = $Sxy/$Sxx;
    $beta = $ymean - $alfa*$xmean;
    
    return array('alfa'=>$alfa, 'beta' => $beta);
    //TO DO: calculate other indicators for the regression
    }

//------------------------------------------------------------------------------

  function slr($descriptor)  //facility for simple regression
    {
    return $this->ts_simple_linear_regression($descriptor);
    }

//------------------------------------------------------------------------------
//! Return the lagged time series (i.e. ts[i] --> ts[i+periods])
/*!
  \param $periods Number of lag periods
  \return Lagged time series
*/
  function ts_lag($periods=1)  
    {
    $lagged = array();
    
    for($i = 0; $i < $this->len-$periods; $i++)
      $lagged[$i] = (float)$this->ts[$i+$periods];
      
    //for($i = $this->len-$periods; $i < $this->len; $i++)
    //  $lagged[$i] = (float)0.0;
      
    return $lagged;
    }
    
//------------------------------------------------------------------------------
//! Plot the lagged time series (i.e. ts[i] --> ts[i+periods])
/*!
  \param $periods Number of lag periods
  \todo  Modify the graphic routines
*/
  function ts_lag_plot($periods=1)   //plot the lag plot (jpgraph library required)
    {
    
    $ts_tmp = array();
    $lagged = $this->ts_lag($periods);
    $n = count($lagged);
    
    for($i = 0; $i < $n; $i++)
      $ts_tmp[$i] = $this->ts[$i];
      
    if(TSDEBUG)
      {
      print_r($lagged);
      print_r($ts_tmp);
      }
    //plot the lag diagram
    $graph = new Graph(300,300,"auto");
    $graph->SetScale("linlin");

    $graph->img->SetMargin(40,40,40,40);		
    $graph->SetShadow();

    $graph->title->Set("A simple scatter plot");
    $graph->title->SetFont(FF_FONT1,FS_BOLD);

    $sp1 = new ScatterPlot($lagged, $ts_tmp);

    $graph->Add($sp1);
    $graph->Stroke(JGRAPH_IMAGE_PATH."scatter.png");
    return "<br/><img src=".JGRAPH_IMAGE_PATH."scatter.png>";

      
    }

//------------------------------------------------------------------------------
//! Calculate the simple exponential smoothing forecast
/*!
  \param $alfa Smoothing parameter 
  \param $Szero Initializing value for S
  \param $init_periods  Number of periods to be used to initialize S
  \note The forecast for the period t+1 is calculated as\n
  Ft+1 = St \n
  St = alfa * At + (1-alfa)*St-1
*/
  function ts_smoothing_simple($alfa=0.5, $Szero = null, $init_periods = 0) 
  //returns an array of the smoothed exponential means
    {
    if($Szero == null && $init_periods == 0) $this->messages->m(TSLANG,"TS_WRONG_SMOOTHING_INIT", true);
     
    if($Szero == null && $init_periods != 0) //initialize with the average of the first $init_periods values
      {
      $Szero = 0.0;
      for($i = 0; $i < $init_periods; $i++)
        $Szero += $this->ts[$i];
        
        $Szero /= $init_periods;
      }  
     if($Szero != null) $init_periods = 0;  //if $Szero is assigned, then forget about $init_periods
     
     
     $S = array();  //array of the exponential means
     $S[0] = $Szero;
     
     for($i = $init_periods+1; $i < $this->len; $i++)
      {
      $S[$i] = $alfa*$this->ts[$i]+(1-$alfa)*$S[$i-1];
      }
        
    return $S;
    }

//------------------------------------------------------------------------------
//! Calculate the exponential smoothing forecast with trend
/*!
  \param $alfa Smoothing parameter 
  \param $beta Trend  parameter
  \param $Szero Initializing value for S
  \param $Tzero Initializing value for T
  \param $init_periods Number of periods to be used to initialize S
  \note The forecast for the period t+1 is calculated as\n
  Ft+1 = St +Tt\n
  St = alfa * At + (1-alfa)*(St-1 * Tt-1)\n
  Tt = beta * (St - St-1) + (1-beta)*Tt-1

*/

  function ts_smoothing_trend($alfa=0.5, $beta=0.5, $Szero = null, $Tzero = null, $init_periods = 0)
  //returns an array of the smoothed exponential means
    {
    if($Szero == null && $init_periods == 0 && $Tzero == null) $this->messages->m(TSLANG,"TS_WRONG_SMOOTHING_INIT", true);
    
    if($Szero == null && $init_periods != 0) //initialize with the average of the first $init_periods values
      {
      $Szero = 0.0;
      for($i = 0; $i < $init_periods; $i++)
        $Szero += $this->ts[$i];
        
        $Szero /= $init_periods;
      }  
      
     if($Szero != null) $init_periods = 0;  //if $Szero is assigned, then forget about $init_periods
    
     $S = array();  //array of the exponential means
     $S[0] = $Szero;
     $T = array();   //array of the exponential trend means
     $T[0] = $Tzero;
     
    for($i = $init_periods+1; $i < $this->len; $i++)
      {
      $S[$i] = $alfa*$this->ts[$i]+(1-$alfa)*($S[$i-1]+$T[$i-1]);
      $T[$i] = $beta*($S[$i] - $S[$i-1])+(1-$beta)*$T[$i-1];
      }
        
    return array($S,$T);

    }

//----------GRAPHIC FUNCTIONS (START)-------------------------------------------
//------------------------------------------------------------------------------
  function ts_graph($x = 300, $y = 200, $title="Graph")  //return a jgraph object representing a graph
    {
    if(USE_JGRAPH == "NO") return;  //return if graphic capabilities are not used
    
      $graph = new Graph($x, $y, "auto"); //all these options should be set on run time (TO BE DONE)
      $graph->title->Set($title);
      $graph->img->SetMargin(40,40,40,40);	
      $graph->img->SetAntiAliasing();
      $graph->SetScale("textlin");  //this is suitable for classic lineplot
      $graph->SetShadow();
      $graph->title->SetFont(FF_FONT1,FS_BOLD);
      return $graph;
    }
    
//------------------------------------------------------------------------------

  function ts_data($data, $type)    // return a jgraph object representing the data to be plotted
    {
      switch($type)
        {
        case "BAR":
          $p = new BarPlot($data);
          $p->SetWidth(0.1);
          return $p;
          break;
        case "LINE":
          $p = new LinePlot($data);  //this line should be set on run time (TO BE DONE)
          $p->mark->SetType(MARK_FILLEDCIRCLE);//all these options should be set on run time (TO BE DONE)
          $p->mark->SetFillColor("red");
          $p->mark->SetWidth(4);
          $p->SetColor("blue");
          $p->SetCenter();
          return $p;
          break;
        default:
          echo "Uknown data type";
          break;
        }
    }

//------------------------------------------------------------------------------
//----------GRAPHIC FUNCTIONS (END)-------------------------------------------    
   

} 

//-------MAIN CLASS (END)-----------------------------------------------------
//------------------------------------------------------------------------------

?>
