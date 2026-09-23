<?php

class CurriculumSettingLecturerSessionCourseworkSubmit extends BaseModel
{
    protected $tableName = 'curriculum_settings_lecturer_session_coursework_submit';
    protected $primaryKey = '_id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_id'),
            array('columnName' => 'courseworkid'),
            array('columnName' => 'markidnum'),
            array('columnName' => 'marksgot'),
            array('columnName' => 'submitfile'),
            array('columnName' => 'submitedon'),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}