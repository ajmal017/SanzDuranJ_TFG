<?php
//------------------------------------------------------------------------------

//      Example file (example2.php)
//      
//      Created by: Roberto Pinto (aka Libra Veroen)
//      Version: 1.0
//      Status: beta
//      Licence: GPL
//      Release date: September '06

//------------------------------------------------------------------------------

include('ts.class.php');


$time_series_1 = "100.0 200.0  250.0 400.0  150.0 350.0 200.0 300";  //some default values
$labels = "1990 1991 1992 1993 1994 1995 1996 1997";  //labels for the time series values

echo "<form name=timeseries action=example2.php method=POST>";
echo "Time series values<br/><textarea name=ts rows=5 cols=50>";
  if(!isset($_REQUEST['ts'])) echo $time_series_1;
  else echo $_REQUEST['ts'];
echo "</textarea><br/>";
echo "<br/><br/>Labels<br/> <textarea name=label rows=5 cols=50>";
  if(!isset($_REQUEST['label'])) echo $labels;
  else echo $_REQUEST['label'];
echo "</textarea><br/>";

echo "<input type=submit>";
echo "</form>";
  
  if(!isset($_REQUEST['ts'])) exit; 

$f = new TS("TimeSeriesOne",$_REQUEST['ts'], $_REQUEST['label']); //build assigning directly the values and the labels
$f->ts_name();
b();
echo "<table border=1 cellpadding=10><tr><td>";
$f->ts_print(true);
echo "<td>";
echo "Mean: ".$f->ts_mean();
b();
echo "Variance: ".$f->ts_var();
b();
echo "Sample Variance: ".$f->ts_var("nopop");
b();
echo "StDev: ".$f->ts_stdev();
echo "<td>";
$f->ts_plot(300,250,"Time Series");
echo "<td>";
$f->ts_acf_plot(10,300,250,"ACF");
echo "</table>";


function b($n=1)  
  {
  for($i=0; $i < $n; $i++)
    echo ("<br/>");
  }
?>
