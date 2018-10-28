<?php
App::uses('AppModel', 'Model');

/**
 * Mensaje Model
 *
 */
class Indicadore extends AppModel
{

    /**
     * Primary key field
     *
     * @var string
     */
    public $primaryKey = 'PK_INDICADORE';

    /**
     * Name of the model
     *
     * @var string
     */
    public $name = 'Indicadore';

    /**
     * Name of the database table
     *
     * @var string
     */
    public $useTable = 'indicadores';

    /**
     * Variable actsAs
     *
     * @var string
     */
    public $actsAs = array('Containable');


}