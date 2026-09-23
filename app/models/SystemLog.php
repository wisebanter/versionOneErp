<?php

class SystemLog extends BaseModel
{
    protected $tableName = 'system_logs';
    protected $primaryKey = '_id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_id'),
            array('columnName' => 'loginid'),
            array('columnName' => 'uroleid'),
            array('columnName' => 'userpid'),
            array('columnName' => 'details'),
            array('columnName' => 'tbaction', 'validations' => array('in_array' => array('SELECT', 'INSERT', 'UPDATE', 'DELETE'))),
            array('columnName' => 'tblname'),
            array('columnName' => 'tblwhere'),
            array('columnName' => 'act_date'),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}