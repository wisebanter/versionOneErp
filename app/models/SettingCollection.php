<?php

class SettingCollection extends BaseModel
{
    protected $tableName = 'setting_collections';
    protected $primaryKey = '_id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_id'),
            array('columnName' => 'names'),
            array('columnName' => 'label', 'validations' => array('in_array' => array('Mon', 'day', 'Hall', 'Nash', 'Relgn'))),
            array('columnName' => 'active', 'validations' => array('in_array' => array('T', 'F'), 'required' => true)),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}