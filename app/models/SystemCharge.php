<?php

class SystemCharge extends BaseModel
{
    protected $tableName = 'system_charges';
    protected $primaryKey = '_hid';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_hid'),
            array('columnName' => '_hcode'),
            array('columnName' => '_hnames'),
            array('columnName' => '_hcharge'),
            array('columnName' => '_foradmin', 'validations' => array('in_array' => array('T', 'F'))),
            array('columnName' => '_hactive', 'validations' => array('in_array' => array('T', 'F'))),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}