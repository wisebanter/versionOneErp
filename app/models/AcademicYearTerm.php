<?php

class AcademicYearTerm extends BaseModel
{
    protected $tableName = 'academic_years_terms';
    protected $primaryKey = '_id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_id'),
            array('columnName' => 'yearidn', 'validations' => array('required' => true)),
            array('columnName' => 'intakeid', 'validations' => array('required' => true)),
            array('columnName' => 'sectionid', 'validations' => array('required' => true)),
            array('columnName' => 'secnnumber'),
            array('columnName' => 'secnactive', 'validations' => array('in_array' => array('T', 'F'))),
            array('columnName' => 'regdeadline'),
            array('columnName' => 'startsdate'),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}