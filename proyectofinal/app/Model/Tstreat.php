<?php
App::uses('AppModel', 'Model');

/**
 * Mensaje Model
 *
 */
class Tstreat extends AppModel
{

    /**
     * Primary key field
     *
     * @var string
     */
    public $primaryKey = 'PK_TS';

    /**
     * Name of the model
     *
     * @var string
     */
    public $name = 'Tstreat';

    /**
     * Name of the database table
     *
     * @var string
     */
    public $useTable = 'tstreat';

    /**
     * Variable actsAs
     *
     * @var string
     */
    public $actsAs = array('Containable');


}