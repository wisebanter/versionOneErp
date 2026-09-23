<?php

class CurriculumSettingLecturerContent extends BaseModel
{
    protected $tableName = 'curriculum_settings_lecturer_content';
    protected $primaryKey = '_id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_id'),
            array('columnName' => 'lecturer_assid'),
            array('columnName' => 'content_title'),
            array('columnName' => 'content_filename'),
            array('columnName' => 'content_details'),
            array('columnName' => 'uploaded_date'),
            array('columnName' => 'active', 'validations' => array('in_array' => array('T', 'F'), 'required' => true)),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}