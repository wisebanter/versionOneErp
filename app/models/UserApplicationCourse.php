<?php

class UserApplicationCourse extends BaseModel
{
    protected $tableName = 'users_application_courses';
    protected $primaryKey = '_acid';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_acid'),
            array('columnName' => 'applied'),
            array('columnName' => 'course'),
            array('columnName' => 'program'),
            array('columnName' => 'section'),
            array('columnName' => 'session'),
            array('columnName' => 'priotized', 'validations' => array('in_array' => array('T', 'F'))),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}