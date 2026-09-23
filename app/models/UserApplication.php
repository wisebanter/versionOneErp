<?php

class UserApplication extends BaseModel
{
    protected $tableName = 'users_application';
    protected $primaryKey = '_aid';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_aid'),
            array('columnName' => 'astates', 'validations' => array('in_array' => array('Pending', 'Paid', 'Admitted', 'Returned'))),
            array('columnName' => 'admissionno'),
            array('columnName' => 'receiptnumb'),
            array('columnName' => 'referee'),
            array('columnName' => 'fnames'),
            array('columnName' => 'phone'),
            array('columnName' => 'email'),
            array('columnName' => 'intake'),
            array('columnName' => 'religion'),
            array('columnName' => 'nationality'),
            array('columnName' => 'gender', 'validations' => array('in_array' => array('M', 'F'))),
            array('columnName' => 'applicationDate'),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}