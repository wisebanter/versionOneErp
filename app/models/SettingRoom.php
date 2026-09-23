<?php

class SettingRoom extends BaseModel
{
    protected $tableName = 'setting_rooms';
    protected $primaryKey = '_id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_id'),
            array('columnName' => 'roomname'),
            array('columnName' => 'roomtype', 'validations' => array('in_array' => array('Normal', 'Computer', 'Laboratory'))),
            array('columnName' => 'capacity'),
            array('columnName' => 'active', 'validations' => array('in_array' => array('T', 'F'), 'required' => true)),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}