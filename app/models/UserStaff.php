<?php

class UserStaff extends BaseModel
{
    protected $tableName = 'users_staff';
    protected $primaryKey = '_uid';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_uid'),
            array('columnName' => 'admited'),
            array('columnName' => 'faculty'),
            array('columnName' => 'registerno'),
            array('columnName' => 'fnames'),
            array('columnName' => 'email'),
            array('columnName' => 'phone'),
            array('columnName' => 'gender', 'validations' => array('in_array' => array('M', 'F'))),
            array('columnName' => 'religion'),
            array('columnName' => 'nationality'),
            array('columnName' => 'pactive', 'validations' => array('in_array' => array('T', 'F'))),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}