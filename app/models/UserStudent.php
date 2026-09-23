<?php

class UserStudent extends BaseModel
{
    protected $tableName = 'users_student';
    protected $primaryKey = '_uid';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_uid'),
            array('columnName' => 'reffNumb'),
            array('columnName' => 'registerno'),
            array('columnName' => 'branchid', 'validations' => array('required' => true)),
            array('columnName' => 'admited'),
            array('columnName' => 'curriculum', 'validations' => array('required' => true)),
            array('columnName' => 'course'),
            array('columnName' => 'program'),
            array('columnName' => 'section'),
            array('columnName' => 'session'),
            array('columnName' => 'intake'),
            array('columnName' => 'stdyear', 'validations' => array('in_array' => array('1', '2', '3', '4', '5'))),
            array('columnName' => 'religion'),
            array('columnName' => 'uhall'),
            array('columnName' => 'nationality'),
            array('columnName' => 'fnames'),
            array('columnName' => 'phone'),
            array('columnName' => 'email'),
            array('columnName' => 'birthDate'),
            array('columnName' => 'gender', 'validations' => array('in_array' => array('M', 'F'))),
            array('columnName' => 'inhostel', 'validations' => array('in_array' => array('T', 'F'))),
            array('columnName' => 'ishalted', 'validations' => array('in_array' => array('T', 'F'))),
            array('columnName' => 'deadyear', 'validations' => array('in_array' => array('T', 'F'))),
            array('columnName' => 'graduated', 'validations' => array('in_array' => array('T', 'F'))),
            array('columnName' => 'pactive', 'validations' => array('in_array' => array('T', 'F'))),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}