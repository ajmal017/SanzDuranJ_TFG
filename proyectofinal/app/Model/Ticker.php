<?php
/**
 * Created by PhpStorm.
 * User: JOSESANZ
 * Date: 09/04/2018
 * Time: 20:13
 */
App::uses('AppModel','Model');
class Ticker extends AppModel {

    public $primaryKey="PK_TICKER";
    public $name="Ticker";
    public $useTable="Tickers";
    public $actAs="Containable";

}