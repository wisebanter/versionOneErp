<?php

class CurriculumSettingLecturerSession extends BaseModel
{
    protected $tableName = 'curriculum_settings_lecturer_session';
    protected $primaryKey = '_id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_id'),
            array('columnName' => 'lecturer_assid'),
            array('columnName' => 'sessionidn'),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}