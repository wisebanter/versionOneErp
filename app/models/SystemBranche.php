<?php

class SystemBranche extends BaseModel
{
    protected $tableName = 'system_branches';
    protected $primaryKey = 'id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => 'id'),
            array('columnName' => 'title'),
            array('columnName' => 'phone'),
            array('columnName' => 'email'),
            array('columnName' => 'website'),
            array('columnName' => 'details'),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}