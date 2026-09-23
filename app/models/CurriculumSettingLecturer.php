<?php

class CurriculumSettingLecturer extends BaseModel
{
    protected $tableName = 'curriculum_settings_lecturer';
    protected $primaryKey = '_id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_id'),
            array('columnName' => 'currsetting'),
            array('columnName' => 'academterm'),
            array('columnName' => 'lecturerid'),
            array('columnName' => 'testsubmited', 'validations' => array('in_array' => array('T', 'F'))),
            array('columnName' => 'coursesubmited', 'validations' => array('in_array' => array('T', 'F'))),
            array('columnName' => 'examsubmited', 'validations' => array('in_array' => array('T', 'F'))),
            array('columnName' => 'active', 'validations' => array('in_array' => array('T', 'F'), 'required' => true)),
            array('columnName' => 'qn_1'),
            array('columnName' => 'qn_2'),
            array('columnName' => 'qn_3'),
            array('columnName' => 'qn_4'),
            array('columnName' => 'qn_5'),
            array('columnName' => 'qn_6'),
            array('columnName' => 'qn_7'),
            array('columnName' => 'qn_8'),
            array('columnName' => 'qn_9'),
            array('columnName' => 'qn_10'),
            array('columnName' => 'qn_11'),
            array('columnName' => 'qn_12'),
            array('columnName' => 'qn_13'),
            array('columnName' => 'qn_14'),
            array('columnName' => 'qn_15'),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}