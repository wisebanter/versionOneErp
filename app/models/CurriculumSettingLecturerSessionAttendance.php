<?php

class CurriculumSettingLecturerSessionAttendance extends BaseModel
{
    protected $tableName = 'curriculum_settings_lecturer_session_attendance';
    protected $primaryKey = '_id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_id'),
            array('columnName' => 'lect_session'),
            array('columnName' => 'attendcode', 'validations' => array('required' => true)),
            array('columnName' => 'attenddate'),
            array('columnName' => 'createddate'),
            array('columnName' => 'active', 'validations' => array('in_array' => array('T', 'F'), 'required' => true)),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}