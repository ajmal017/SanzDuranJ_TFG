<?php
App::uses('AppModel', 'Model');

/**
 * Mensaje Model
 *
 */
class Datamining extends AppModel
{

    /**
     * Primary key field
     *
     * @var string
     */
    public $primaryKey = 'PK_DM';

    /**
     * Name of the model
     *
     * @var string
     */
    public $name = 'Datamining';

    /**
     * Name of the database table
     *
     * @var string
     */
    public $useTable = 'Datamining';

    /**
     * Variable actsAs
     *
     * @var string
     */
    public $actsAs = array('Containable');


}