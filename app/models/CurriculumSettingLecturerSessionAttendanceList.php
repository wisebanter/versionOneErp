<?php

class CurriculumSettingLecturerSessionAttendanceList extends BaseModel
{
    protected $tableName = 'curriculum_settings_lecturer_session_attendance_list';
    protected $primaryKey = '_id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_id'),
            array('columnName' => 'attendidn'),
            array('columnName' => 'markidnum'),
            array('columnName' => 'attendstate', 'validations' => array('in_array' => array('Present', 'Absent', 'Reason'))),
            array('columnName' => 'attendreason'),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}