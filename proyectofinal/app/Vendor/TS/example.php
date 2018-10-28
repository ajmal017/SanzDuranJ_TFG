<?php
//------------------------------------------------------------------------------

//      Example file (example.php)
//      Basic messages handler
//      Created by: Roberto Pinto (aka Libra Veroen)
//      Version: 1.0
//      Status: beta
//      Licence: GPL
//      Release date: August '06

//------------------------------------------------------------------------------

include('ts.class.php');

$time_series_1 = "100.0 200.0  250.0 400.0  150.0 350.0";  //some values
$labels = "1990 1991 1992 1993 1994 1995";  //labels for the time series values
$months = "gen feb mar apr may jun";

$f = new TS("TimeSeriesOne",$time_series_1, $labels); //build assigning directly the values and the labels

//print out the values along with the labels
$f->ts_name();
b();
$f->ts_print(true);
b();


$time_series_2 = "1 2 3 4 5 6"; //another time series
$s = new TS("TimeSeriesTwo");
$s->ts_parseline($time_series_2);  //values for the second time series 

//print out the values along with the labels
$s->ts_name();
b();
$s->ts_print(true); //this should generate a non-blocking message, because we have not set the label for the second time series
b();
echo "Assigning the label:";
$s->ts_setlabel($months);
b();
$s->ts_print(true); //now it should work fine
b();
echo "We can even print the time series alone:<br/>";
$f->ts_print(false);
b();
$s->ts_print(false);
b();
echo "Calculate some values for the first time series: <br/>";
echo "Mean: ".$f->ts_mean();
b();
echo "Variance: ".$f->ts_var();
b();
echo "Sample Variance: ".$f->ts_var("nopop");
b();
echo "StDev: ".$f->ts_stdev();
b();
echo "Weighted average: ".$f->ts_weighted_mean(array(1.0,2.0,1.0,1.0,1.0,3.0));
b(2);
echo "Moving average with 3 periods: ";
$f->ts_moving_average(3);
b(2);
echo "Moving weighted average with 3 periods: ";
$f->ts_moving_weighted_average(3,array(1,2,3));
b(2);
echo "Simple linear regression: TimeSerieOne = alfa * TimesSeriesTwo + Beta: <br/>";
$regression_results = $f->ts_simple_linear_regression($s);
print_r($regression_results);
b();
echo "The same as above, but using the facility slr()<br/>";
$regression_results = $f->slr($s);
print_r($regression_results);
b(2);
echo "Simple Exponential Smoothing <br/>"; 
$f->ts_smoothing_simple(0.5, null, 2);
b();
b();
echo "Simple Exponential Smoothing with a different init values<br/>";
$f->ts_smoothing_simple(0.5, 10, 2); //if both $Szero and $init_Periods are initialised, the former has the precedence
b(2);
echo "Lagged time series: ";
print_r($f->ts_lag());
b(2);
echo "ACF: ";
print_r($f->ts_acf()); //TODO: this would be far better if plotted

function b($n=1)  
  {
  for($i=0; $i < $n; $i++)
    echo ("<br/>");
  }
?>