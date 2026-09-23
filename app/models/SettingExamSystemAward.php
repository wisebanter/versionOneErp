<?php

class SettingExamSystemAward extends BaseModel
{
    protected $tableName = 'setting_exams_system_award';
    protected $primaryKey = 'id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => 'id'),
            array('columnName' => 'systemid', 'validations' => array('required' => true)),
            array('columnName' => 'programid', 'validations' => array('required' => true)),
            array('columnName' => 'name'),
            array('columnName' => 'cls_min'),
            array('columnName' => 'cls_max'),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}