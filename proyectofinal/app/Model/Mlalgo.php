<?php
App::uses('AppModel', 'Model');

/**
 * Mensaje Model
 *
 */
class Mlalgo extends AppModel
{

    /**
     * Primary key field
     *
     * @var string
     */
    public $primaryKey = 'PK_ML';

    /**
     * Name of the model
     *
     * @var string
     */
    public $name = 'Mlalgo';

    /**
     * Name of the database table
     *
     * @var string
     */
    public $useTable = 'mlalgos';

    /**
     * Variable actsAs
     *
     * @var string
     */
    public $actsAs = array('Containable');


}