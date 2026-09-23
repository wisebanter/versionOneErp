<?php

class SettingProgrameExam extends BaseModel
{
    protected $tableName = 'setting_programes_exams';
    protected $primaryKey = '_id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_id'),
            array('columnName' => 'programid', 'validations' => array('required' => true)),
            array('columnName' => 'name'),
            array('columnName' => 'contributes'),
            array('columnName' => 'active', 'validations' => array('in_array' => array('T', 'F'), 'required' => true)),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}