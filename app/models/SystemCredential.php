<?php

class SystemCredential extends BaseModel
{
    protected $tableName = 'system_credentials';
    protected $primaryKey = '_cid';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_cid'),
            array('columnName' => 'reff_number'),
            array('columnName' => 'descriptins'),
            array('columnName' => 'credentials'),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}