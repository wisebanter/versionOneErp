<?php

class UserRole extends BaseModel
{
    protected $tableName = 'users_role';
    protected $primaryKey = '_rid';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_rid'),
            array('columnName' => '_rname'),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}