<?php
/**
 * Created by PhpStorm.
 * User: JOSESANZ
 * Date: 09/04/2018
 * Time: 20:18
 */
App::uses('AppController','Controller');
class TickersController extends AppController{
    public $components = array('Paginator');
    public $uses = array('Countries');


    public function index() {
        //Buscamos todos los baremos
        $tickers = $this->Ticker->find('all', array(
            'recursive' => -1,
        ));
        //Encriptamos las PK de baremos
        for ($x = 0; $x < count($tickers); $x++) {
            $baremos[$x]['Ticker']['PK_TICKER'] = $this->encrypt($tickers[$x]['Ticker']['PK_TICKER']);
        }

        //Enviamos a la vista la lista de baremos
        $this->set('tickers', $tickers);
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        //Desencriptamos la pk
        if ($id != null) {
            $id = $this->decrypt($id);
            $id = $id[0];
        }

        //Si no existe ningun baremo con ese id volvemos a la ventana principal del modulo
        if (!$this->Ticker->exists($id)) {
            $this->showModal('4', 'GCS');
            $this->redirect(array('controller' => 'tickers', 'action' => 'index'));
        }

        //Buscamos la informacion del Baremo
        $options = array('recursive' => 0, 'conditions' => array('Ticker.' . $this->Ticker->primaryKey => $id));
        $ticker = $this->Ticcker->find('first', $options);

        //Buscamos los codigosbaremo asociados
        $options = array('recursive' => 0, 'conditions' => array('Codigosbaremo.FK_BAREMO' => $id));
        $codigosbaremo = $this->Codigosbaremo->find('all', $options);

        //Encriptamos la PK del Baremo
        $baremo['Baremo']['PK_BAREMO'] = $this->encrypt($baremo['Baremo']['PK_BAREMO']);

        //Encriptamos las PK de codigosbaremo
        for ($x = 0; $x < count($codigosbaremo); $x++) {
            $codigosbaremo[$x]['Codigosbaremo']['PK_CODIGOBAREMO'] = $this->encrypt($codigosbaremo[$x]['Codigosbaremo']['PK_CODIGOBAREMO']);
        }

        //Enviamos a la vista la informacion necesaria
        $this->set('baremo', $baremo);
        $this->set('codigosbaremo', $codigosbaremo);
    }

    public function add() {
        //Si recivimos informacion del formulario
        if ($this->request->is('post')) {
            //Guardamoe el Baremo
            $this->Baremo->create();
            if ($this->Baremo->save($this->request->data)) {
                $this->showModal('1', 'GCS');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->showModal('3', 'GCS');
            }
        }
    }

    public function edit($id = null) {
        //Desencriptamos la pk
        if ($id != null) {
            $id = $this->decrypt($id);
            $id = $id[0];
        }

        //Si no existe ningun baremo con ese id volvemos a la ventana principal del modulo
        if (!$this->Baremo->exists($id)) {
            $this->showModal('4', 'GCS');
            $this->redirect(array('controller' => 'baremos', 'action' => 'index'));
        }

        //Si recivimos informacion del formulario
        if ($this->request->is(array('post', 'put'))) {
            //Desencriptamos el ID recibido en el request->data
            $this->request->data['Baremo']['PK_BAREMO'] = $this->decrypt($this->request->data['Baremo']['PK_BAREMO']);
            $this->request->data['Baremo']['PK_BAREMO'] = $this->request->data['Baremo']['PK_BAREMO'][0];

            //Guardamoe el Baremo
            if ($this->Baremo->save($this->request->data)) {
                $this->showModal('1', 'GCS');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->showModal('3', 'GCS');
            }
        }
        //Buscamos la informacion del Baremo
        $options = array('recursive' => 0, 'conditions' => array('Baremo.' . $this->Baremo->primaryKey => $id));
        $baremo = $this->Baremo->find('first', $options);

        //Buscamos los codigosbaremo asociados
        $options = array('recursive' => 0, 'conditions' => array('Codigosbaremo.FK_BAREMO' => $id));
        $codigosbaremo = $this->Codigosbaremo->find('all', $options);

        //Encriptamos la PK del Baremo
        $baremo['Baremo']['PK_BAREMO'] = $this->encrypt($baremo['Baremo']['PK_BAREMO']);

        //Encriptamos las PK de codigosbaremo
        for ($x = 0; $x < count($codigosbaremo); $x++) {
            $codigosbaremo[$x]['Codigosbaremo']['PK_CODIGOBAREMO'] = $this->encrypt($codigosbaremo[$x]['Codigosbaremo']['PK_CODIGOBAREMO']);
        }

        //Enviamos a la vista la informacion necesaria
        $this->request->data = $baremo;
        $this->set('codigosbaremo', $codigosbaremo);
    }

    public function delete($id = null) {
        //Desencriptamos la pk
        if ($id != null) {
            $id = $this->decrypt($id);
            $id = $id[0];
        }

        $this->Baremo->id = $id;
        //Si no existe ningun baremo con ese id volvemos a la ventana principal del modulo
        if (!$this->Baremo->exists()) {
            $this->showModal('4', 'GCS');
            $this->redirect(array('controller' => 'baremos', 'action' => 'index'));
        }

        //Buscamos la informacion del Baremo
        $baremo = $this->Baremo->find('first', array('recursive' => 1, 'conditions' => array('PK_BAREMO' => $id)));

        //Borramos el Baremo
        if (count($baremo['Companiasasegurado']) == 0 && count($baremo['Codigosbaremo']) == 0) {
            try {
                $this->Baremo->delete();
                $this->showModal('1', 'GCS');
            } catch (Exception $e) {
                $this->showModal('034', 'GCS');
            }
        } else {
            $this->showModal('034', 'GCS');
        }

        $this->redirect(array('action' => 'index'));
    }

    public function existeBaremo() {
        $this->autoLayout = false;
        $this->autoRender = false;
        if ($this->request->is('ajax')) {
            $baremo = $this->Baremo->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'PK_BAREMO' => $this->request->data['PK_BAREMO']
                )
            ));
            return new CakeResponse(array('body' => json_encode(array('response' => !empty($baremo), 'success' => true)), 'status' => 200));
        }
    }
}