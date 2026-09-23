<?php

class CurriculumSettingStudent extends BaseModel
{
    protected $tableName = 'curriculum_settings_student';
    protected $primaryKey = '_id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_id'),
            array('columnName' => 'studentidn'),
            array('columnName' => 'academterm'),
            array('columnName' => 'regstdyear', 'validations' => array('in_array' => array('1', '2', '3', '4', '5'))),
            array('columnName' => 'regdate'),
            array('columnName' => 'regstatus', 'validations' => array('in_array' => array('Pending', 'Complete'))),
            array('columnName' => 'acstatus', 'validations' => array('in_array' => array('Normal', 'Late'))),
            array('columnName' => 'active', 'validations' => array('in_array' => array('T', 'F'), 'required' => true)),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}