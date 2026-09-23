<?php

class SettingExamSystemGrading extends BaseModel
{
    protected $tableName = 'setting_exams_system_grading';
    protected $primaryKey = 'id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => 'id'),
            array('columnName' => 'systemid', 'validations' => array('required' => true)),
            array('columnName' => 'grade'),
            array('columnName' => 'grd_max'),
            array('columnName' => 'grd_min'),
            array('columnName' => 'points'),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}