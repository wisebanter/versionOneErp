<?php

class AcademicYearTermRelease extends BaseModel
{
    protected $tableName = 'academic_years_terms_release';
    protected $primaryKey = '_id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_id'),
            array('columnName' => 'termid'),
            array('columnName' => 'course'),
            array('columnName' => 'submitedby'),
            array('columnName' => 'releasedby'),
            array('columnName' => 'submiteddate'),
            array('columnName' => 'releaseddate'),
            array('columnName' => 'releasedstate', 'validations' => array('in_array' => array('Pending', 'Released'))),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}