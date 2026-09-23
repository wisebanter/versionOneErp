<?php

class SettingExamSystem extends BaseModel
{
    protected $tableName = 'setting_exams_system';
    protected $primaryKey = 'sysid';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => 'sysid'),
            array('columnName' => 'sysname'),
            array('columnName' => 'sysactive', 'validations' => array('in_array' => array('T', 'F'))),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}