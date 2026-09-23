<?php

class CurriculumSettingStudentMark extends BaseModel
{
    protected $tableName = 'curriculum_settings_student_marks';
    protected $primaryKey = '_id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_id'),
            array('columnName' => 'cursetting'),
            array('columnName' => 'registered'),
            array('columnName' => 'credits'),
            array('columnName' => 'testmarks'),
            array('columnName' => 'corsework'),
            array('columnName' => 'finalmark'),
            array('columnName' => 'totals'),
            array('columnName' => 'gradelabel'),
            array('columnName' => 'gradepoint'),
            array('columnName' => 'status', 'validations' => array('in_array' => array('Pending', 'Passed', 'Retake'))),
            array('columnName' => 'states', 'validations' => array('in_array' => array('Pending', 'Registered', 'Done'))),
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