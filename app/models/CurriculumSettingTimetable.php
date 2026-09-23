<?php

class CurriculumSettingTimetable extends BaseModel
{
    protected $tableName = 'curriculum_settings_timetable';
    protected $primaryKey = '_id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_id'),
            array('columnName' => 'currsetting'),
            array('columnName' => 'academterm'),
            array('columnName' => 'sessionid'),
            array('columnName' => 'clasroomid'),
            array('columnName' => 'testroomid'),
            array('columnName' => 'examroomid'),
            array('columnName' => 'timetable', 'validations' => array('in_array' => array('classes', 'exams'))),
            array('columnName' => 'weekdayid'),
            array('columnName' => 'classtime'),
            array('columnName' => 'testsdate'),
            array('columnName' => 'examsdate'),
            array('columnName' => 'active', 'validations' => array('in_array' => array('T', 'F'), 'required' => true)),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}