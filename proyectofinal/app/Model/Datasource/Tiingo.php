<?php
App::uses('HttpSocket', 'Network/Http','GuzzleHttp','Controller/Analisis');

class Tiingo extends DataSource {
    /**
     * An optional description of your datasource
     */
    public $description = 'Tiingo datasource';

    /**
     * Our default config options. These options will be customized in our
     * ``app/Config/database.php`` and will be merged in the ``__construct()``.
     */
    public $config = array(
        'apiKey' => 'ec521aefb5ccb5202dbf55eb950b19bc1e7a248f',
    );

    /**
     * If we want to create() or update() we need to specify the fields
     * available. We use the same array keys as we do with CakeSchema, eg.
     * fixtures and schema migrations.
     */
    protected $_schema = array(
        'date' => array(
            'type' => 'date',
            'null' => false,
            'key' => 'primary',
        ),
        'open' => array(
            'type' => 'float',
            'null' => true,
        ),
        'high' => array(
            'type' => 'float',
            'null' => true,
        ),
        'low' => array(
            'type' => 'float',
            'null' => true,
        ),
        'close' => array(
            'type' => 'float',
            'null' => true,
        ),
        'volume' => array(
            'type' => 'int',
            'null' => true,
        ),
        'adjClose' => array(
            'type' => 'float',
            'null' => true,
        ),
    );

    /**
     * Create our HttpSocket and handle any config tweaks.
     */
    public function __construct($config) {
        parent::__construct($config);
        $this->Http = new HttpSocket();
    }

    /**
     * Since datasources normally connect to a database there are a few things
     * we must change to get them to work without a database.
     */

    /**
     * listSources() is for caching. You'll likely want to implement caching in
     * your own way with a custom datasource. So just ``return null``.
     */
    public function listSources($data = null) {
        return null;
    }

    /**
     * describe() tells the model your schema for ``Model::save()``.
     *
     * You may want a different schema for each model but still use a single
     * datasource. If this is your case then set a ``schema`` property on your
     * models and simply return ``$model->schema`` here instead.
     */
    public function describe($model) {
        return $this->_schema;
    }

    /**
     * calculate() is for determining how we will count the records and is
     * required to get ``update()`` and ``delete()`` to work.
     *
     * We don't count the records here but return a string to be passed to
     * ``read()`` which will do the actual counting. The easiest way is to just
     * return the string 'COUNT' and check for it in ``read()`` where
     * ``$data['fields'] === 'COUNT'``.
     */
    public function calculate(Model $model, $func, $params = array()) {
        return 'COUNT';
    }

    /**
     * Implement the R in CRUD. Calls to ``Model::find()`` arrive here.
     */

    public function read(Model $model, $queryData = array(),
                         $recursive = null) {
        /**
         * Here we do the actual count as instructed by our calculate()
         * method above. We could either check the remote source or some
         * other way to get the record count. Here we'll simply return 1 so
         * ``update()`` and ``delete()`` will assume the record exists.
         */

        if ($queryData['fields'] === 'COUNT') {
            return array(array(array('count' => 1)));
        }

        $symbol=$queryData['conditions']['symbol'];
        $init=$queryData['conditions']['startDate'];
        $end=$queryData['conditions']['endDate'];
        $closeOnly=$queryData['conditions']['closeOnly'];


        if($closeOnly==true){
            $url="https://api.tiingo.com/tiingo/daily/".$symbol."/prices?columns=close&startDate=".$init."&endDate=".$end."&token=ec521aefb5ccb5202dbf55eb950b19bc1e7a248f";
        }else{
            if($queryData['conditions']['ohlc']==true){
                $url="https://api.tiingo.com/tiingo/daily/".$symbol."/prices?columns=open,high,low,close&startDate=".$init."&endDate=".$end."&token=ec521aefb5ccb5202dbf55eb950b19bc1e7a248f";
            }else{
                $url="https://api.tiingo.com/tiingo/daily/".$symbol."/prices?startDate=".$init."&endDate=".$end."&token=ec521aefb5ccb5202dbf55eb950b19bc1e7a248f";
            }
        }



        $json = $this->Http->get($url, [
            'headers' => [
                'Content-type' =>  'application/json',
                'Authorization'     => 'token=ec521aefb5ccb5202dbf55eb950b19bc1e7a248f'
            ]
        ]);
        $res = json_decode($json, true);
        if (is_null($res)) {
            $error = json_last_error();
            throw new CakeException($error);
        }
        return array($model->alias => $res);
    }


    /**
     * Implement the C in CRUD. Calls to ``Model::save()`` without $model->id
     * set arrive here.
     */

}


