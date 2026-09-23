<?php

class SettingSection extends BaseModel
{
    protected $tableName = 'setting_sections';
    protected $primaryKey = '_id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_id'),
            array('columnName' => 'name'),
            array('columnName' => 'duration'),
            array('columnName' => 'active', 'validations' => array('in_array' => array('T', 'F'), 'required' => true)),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}