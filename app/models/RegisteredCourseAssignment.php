<?php

class RegisteredCourseAssignment extends BaseModel
{
    protected $tableName = 'registered_course_assignments';
    protected $primaryKey = '_id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_id'),
            array('columnName' => 'course'),
            array('columnName' => 'nametag', 'validations' => array('in_array' => array('Prog', 'Secn', 'Sess', 'Intk'))),
            array('columnName' => 'assignid'),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}