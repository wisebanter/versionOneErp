<?php

class CurriculumSettingLecturerSessionCoursework extends BaseModel
{
    protected $tableName = 'curriculum_settings_lecturer_session_coursework';
    protected $primaryKey = '_id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_id'),
            array('columnName' => 'lect_session'),
            array('columnName' => 'markedoutof'),
            array('columnName' => 'contributes'),
            array('columnName' => 'title'),
            array('columnName' => 'instructions'),
            array('columnName' => 'foruploading', 'validations' => array('in_array' => array('T', 'F'))),
            array('columnName' => 'submitdeadline'),
            array('columnName' => 'createddate'),
            array('columnName' => 'active', 'validations' => array('in_array' => array('T', 'F'), 'required' => true)),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}