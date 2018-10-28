<?php
App::uses('Controller', 'Controller');
class AppController extends Controller{
    public $uses = array(
        'Price'
    );
    public $url;
    public $php_eol;
    public $base;

    public function root() {
        $this->ultracore();
        return $this->base;
    }
    public function beforeFilter() {
        parent::beforeFilter();

        if ($this->request->is('ajax')) {
            return;
        }

        // Servidor en el que nos encontramos (UltraCore)
        $entorno = Configure::read("ENVIROMENT");
        $entorno_local = explode('/', $_SERVER['PHP_SELF']);
        $this->url = "http://localhost/{$entorno_local[1]}/";
        $this->php_eol = "\n";
        $this->base = ROOT;
        $this->set('entorno', $entorno);
        $this->set('urlPHP', $this->url);
        $this->set('urlJS', $this->url);
    }
}
