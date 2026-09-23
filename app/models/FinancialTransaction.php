<?php

class FinancialTransaction extends BaseModel
{
    protected $tableName = 'financial_transactions';
    protected $primaryKey = '_id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_id'),
            array('columnName' => 'usertype', 'validations' => array('in_array' => array('Student', 'Staff'))),
            array('columnName' => 'userid'),
            array('columnName' => 'tran_parent_ref'),
            array('columnName' => 'tran_internal_ref', 'validations' => array('required' => true)),
            array('columnName' => 'tran_external_ref'),
            array('columnName' => 'tran_amount'),
            array('columnName' => 'tran_type', 'validations' => array('in_array' => array('Credit', 'Debit', 'Charge'))),
            array('columnName' => 'tran_status', 'validations' => array('in_array' => array('Pending', 'Succeeded', 'Failed', 'Cancelled'))),
            array('columnName' => 'tran_mode', 'validations' => array('in_array' => array('Bank', 'Cash', 'Momo'))),
            array('columnName' => 'tran_non_bank_account'),
            array('columnName' => 'tran_date'),
            array('columnName' => 'tran_details'),
            array('columnName' => 'tran_status_reason'),
            array('columnName' => 'active', 'validations' => array('in_array' => array('T', 'F'), 'required' => true)),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}