<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link https://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
App::uses('HttpSocket', 'Network/Http');
class MainController extends AppController
{

    /**
     * This controller does not use a model
     *
     * @var array
     */
    public $uses = array(
        'Price','Ticker'
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
        $tickers = $this->Ticker->find('all', array(
            'recursive' => -1,
            'fields'=>array('Ticker.PK_TICKER','Ticker.SYMBOL','Ticker.NAME','Ticker.EXCHANGE')
        ));
        $this->set('tickers', $tickers);
        $this->layout='default';
    }

    public function searchStockInfo(){
        $this->autoLayout = false;
        $this->autoRender = false;

        $pk = $this->request->data['symbol'];

        $ticker= $this->Ticker->find('first', array(
            'recursive' => -1,
            'fields'=>array('Ticker.TIINGO_NAME'),
            'conditions'=>array('Ticker.PK_TICKER'=>$pk)
        ));

        $symbol=$ticker['Ticker']['TIINGO_NAME'];


        $HttpSocket = new HttpSocket();



        $url="https://api.tiingo.com/tiingo/daily/".$symbol."?token=ec521aefb5ccb5202dbf55eb950b19bc1e7a248f";

        if ($this->request->is('ajax')) {
            /*$json = $this->Http->get($url, [
                'headers' => [
                    'Content-type' =>  'application/json',
                    'Authorization'     => 'token=ec521aefb5ccb5202dbf55eb950b19bc1e7a248f'
                ]
            ]);*/

            $response = $HttpSocket->get($url);

            $res = json_decode($response, true);

            return new CakeResponse(array('body' => json_encode(array('response' => true, 'success' => true, 'info' => $res)), 'status' => 200));
        }
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
}