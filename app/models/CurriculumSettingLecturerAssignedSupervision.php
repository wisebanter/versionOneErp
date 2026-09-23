<?php

class CurriculumSettingLecturerAssignedSupervision extends BaseModel
{
    protected $tableName = 'curriculum_settings_lecturer_assigned_supervision';
    protected $primaryKey = '_id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_id'),
            array('columnName' => 'lecturer_assid'),
            array('columnName' => 'student_markid'),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}