<?php
//------------------------------------------------------------------------------
/*! \file ts_msg.php.inc
    \brief Messages class file
    \author Roberto Pinto (aka Libra Veroen)
    \version 1.1
    \date September '06

//      Class TS_msg (ts_msg.php.inc)
//      Basic messages handler
//      Created by: Roberto Pinto (aka Libra Veroen)
//      Version: 1.1
//      Status: beta
//      Licence: GPL
//      Release date: September '06
*/
//------------------------------------------------------------------------------
/*! \class TS_msg 
    \brief Class for multi-languages message handling
*/

class TS_msg
{
  var $msg = array();

//!\brief Constructor

function TS_msg()
  {  
  $this->msg['it']['TS_LABEL_VALUE_MISMATCH'] = "Errore: il numero di etichette non coincide con il numero di valori della serie storica<br/>\n";
  $this->msg['it']['TS_LABEL_NOT_DEFINED'] = "Attenzione: etichette non definite<br/>\n";
  $this->msg['it']['TS_VALUES_WEIGHTS_MISMATCH'] = "Errore: il numero di pesi non coincide con numero di valori<br/>\n";
  $this->msg['it']['TS_WRONG_PERIODS_VALUE'] = "Errore: necessario utilizzare un valore maggiore di 0<br/>\n";
  $this->msg['it']['TS_VALUES_DESCRIPTORS_MISMATCH'] = "Errore: il numero di descrittori e' diverso dal numero di valori della serie storica<br/>\n"; 
  $this->msg['it']['TS_WRONG_SMOOTHING_INIT'] = "Errore: almeno un valore tra S0 e init_periods deve essere assegnato<br/>\n";
    
   
  $this->msg['en']['TS_LABEL_VALUE_MISMATCH'] = "Error: the number of labels is not equal to the number of values<br/>\n";
  $this->msg['en']['TS_LABEL_NOT_DEFINED'] = "Warning: labels not defined<br/>\n";
  $this->msg['en']['TS_VALUES_WEIGHTS_MISMATCH'] = "Error: the number of weights is not equal to the number of values<br/>\n";
  $this->msg['en']['TS_WRONG_PERIODS_VALUE'] = "Error: value grater than zero is required<br/>\n";
  $this->msg['en']['TS_VALUES_DESCRIPTORS_MISMATCH'] = "Error: the number of descriptors is not equal to the number os values<br/>\n";
  $this->msg['en']['TS_WRONG_SMOOTHING_INIT'] = "Error: at least one between S0 and init_periods should be initialized<br/>\n";  
    
  }
//! Print a message
/*! 
  \param  $lang Language (it or en)
  \param  $msg Identifier of the message
  \param  $exit If true, terminate the program
*/
function m($lang, $msg, $exit=false)
  {
  
  echo $this->msg[$lang][$msg];
  
  if($exit==true) exit;
  
  }
}
?>
