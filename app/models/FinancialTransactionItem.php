<?php

class FinancialTransactionItem extends BaseModel
{
    protected $tableName = 'financial_transactions_items';
    protected $primaryKey = '_id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_id'),
            array('columnName' => 'tranidnum'),
            array('columnName' => 'tranamount'),
            array('columnName' => 'trandetails'),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }
}