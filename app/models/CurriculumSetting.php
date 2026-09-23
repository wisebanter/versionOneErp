<?php

class CurriculumSetting extends BaseModel
{
    protected $tableName = 'curriculum_settings';
    protected $primaryKey = '_id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_id'),
            array('columnName' => 'code'),
            array('columnName' => 'curriculum'),
            array('columnName' => 'programid'),
            array('columnName' => 'courseid'),
            array('columnName' => 'cosunitid'),
            array('columnName' => 'sectionid'),
            array('columnName' => 'studyyear'),
            array('columnName' => 'studyterm'),
            array('columnName' => 'creditunits'),
            array('columnName' => 'duration'),
            array('columnName' => 'iscore', 'validations' => array('in_array' => array('T', 'F'))),
            array('columnName' => 'issupervised', 'validations' => array('in_array' => array('T', 'F'))),
            array('columnName' => 'payamount'),
            array('columnName' => 'active', 'validations' => array('in_array' => array('T', 'F'), 'required' => true)),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}