<?php

/**
 * Abstract Model Class
 *
 * Provides common CRUD operations for database tables.
 * All child classes must define the table name, primary key, columns, and required fields.
 *
 * Uses the provided Database class for query execution.
 */
abstract class AbstractDatabaseModel extends Database
{

    /**
     * @var string $tableName Name of the database table
     */
    protected $tableName = '';

    /**
     * @var string $primaryKey Primary key column name
     */
    protected $primaryKey = '';

    /**
     * @var string $primaryKeyAlias Primary key column name Alias
     */
    protected $primaryKeyAlias = 'idnum';

    /**
     * @var array $columns List of all column names in the table (excluding auto-increment primary key)
     */
    protected $columns = array();


    /**
     * @var array $error Holds the error got during the process
     */
    protected $error = array();


    /**
     * inputtypes
     *
     * @var array
     */
    private $inputtypes = array('number', 'array', 'assoc', 'email', 'phone', 'password', 'json');


    /**
     * Token TimeZone.
     *
     * @var string
     */
    private $timeZone = 'Africa/Kampala';

    /**
     * @var array $columns List of all column names in the table (excluding auto-increment primary key)
     */
    private $validator = array(
        'columnName' => 'string',
        'columnAlias' => 'string',
        'validations' => 'assoc',
    );


    private array $countries = array(
        '256' => array(
            'country' => 'UGANDA',
            'currency' => "UGX",
            'maxLengthWithoutCountryCode' => 10,
            'maxLengthWithCountryCode' => 12,
            'prefixLength' => 3,
            'prefixStartsAt' => 0,
            'operators' => array(
                'MTN' => array(
                    '076' => array('start' => 0, 'ends' => 4),
                    '077' => array('start' => 2, 'ends' => 9),
                    '078' => array('start' => 1, 'ends' => 9),
                    '079' => array('start' => 0, 'ends' => 6),
                ),
                'AIRTEL' => array(
                    '075' => array('start' => 0, 'ends' => 9),
                    '070' => array('start' => 0, 'ends' => 6),
                    '074' => array('start' => 0, 'ends' => 4),
                ),
            ),
        ),
    );


    /**
     * Token secret.
     *
     * @var string
     */
    private string $secret = '';


    /**
     * Token expiry.
     *
     * @var int
     */
    private $expirySeconds = 0;


    /**
     * Token Can Expire Keys.
     *
     * @var String
     */
    private $canExpire = array();


    /**
     * Token Payload Keys.
     *
     * @var array
     */
    private $payloadKeys = array();


    /**
     * Constructor.
     *
     */
    public function __construct(string $hostname, string $dbname, string $username, string $password, int $port, string $secret, int $expiryTime, string $canExpire, array $payloadKeys)
    {
        parent::__construct($hostname, $dbname, $username, $password, $port);

        $this->setPrimaryKey($this->primaryKey);
        $this->secret = $secret;
        $this->expirySeconds = $expiryTime;
        $this->payloadKeys = $payloadKeys;
        $this->canExpire = $canExpire;
        try {
            if (empty($this->tableName)) {
                throw new Exception(get_class($this) . ': Table name is not defined.', 422);
            } else if ($this->isNullOrEmpty($this->primaryKey)) {
                throw new Exception(get_class($this) . ': Table primary Key is not defined.', 422);
            } else if ($this->isNullOrEmpty($this->columns)) {
                throw new Exception(get_class($this) . ': Table Columns must no be empty.', 422);
            } else if (!is_array($this->columns)) {
                throw new Exception(get_class($this) . ': Table Columns must be an array.', 422);
            } else if ($this->isNullOrEmpty($secret) || $this->isNullOrEmpty($payloadKeys) || $this->isNullOrEmpty($canExpire) || $this->isNullOrEmpty($expiryTime)) {
                $err = array();
                if ($this->isNullOrEmpty($secret)) {
                    $err[] = 'Secret';
                }
                if ($this->isNullOrEmpty($payloadKeys)) {
                    $err[] = 'Payload Keys';
                }
                if ($this->isNullOrEmpty($expiryTime)) {
                    $err[] = 'Expiry Time';
                }
                if ($this->isNullOrEmpty($canExpire)) {
                    $err[] = 'can Expire';
                }

                throw new Exception(get_class($this) . ': Invalid: ' . implode(' and ', $err));
            } else if ($this->isAssociative($payloadKeys)) {
                throw new Exception(get_class($this) . ': Payload Keys MUST NOT BE an Associative array');
            } else if (!in_array(strtoupper($canExpire), array('T', 'F'))) {
                throw new Exception(get_class($this) . ': Can Expire MUST BE a : ' . implode(' OR ', array('T', 'F')));
            } else {
                $err = array();
                $mis = array();

                foreach ($this->columns as $cindex => $col) {
                    $cindex = $cindex + 1;
                    foreach ($this->validator as $index => $type) {

                        $key = strtolower($index);
                        $col = array_change_key_case($col, CASE_LOWER);

                        //   private $validator = array(
                        //     'columnName' => 'string',
                        //     'columnAlias' => 'string',
                        //     'validations' => 'assoc',
                        // );
                        if ($key === 'columnName' || ($key !== 'columnName' && isset($col[$key]))) {
                            if (!array_key_exists($key, $col)) {
                                $mis[] = "Item: {$cindex} => {$index}";
                            } else {
                                if ($type === 'string' && !is_string($col[$key])) {
                                    $err[] = "Item: {$cindex} => {$index} must be {$type}";
                                } else if ($type === 'assoc' && !$this->isAssociative($col[$key])) {
                                    $err[] = "Item: {$cindex} => {$index} must be {$type} Array";
                                }
                            }
                        }
                    }
                }

                if (count($mis) !== 0 || count($err) !== 0) {
                    $errs = array();
                    if (count($mis) !== 0) {
                        $errs[] = 'Missing keys: ' . implode(', ', $mis);
                    }
                    if (count($err) !== 0) {
                        $errs[] = implode(', ', $err);
                    }

                    throw new Exception(get_class($this) . " Table Columns: " . implode(' and ', $errs));
                }
            }
        } catch (Exception $e) {
            $this->setError($e);
        }
    }





    /**
     * Inserts a new record into the table.
     *
     * @param array $data Associative array of column => value
     * @return array Result from Database::runQuery()
     * @throws Exception if validation fails or query error occurs
     */
    public function insert(array $data, bool $includePrimaryKey = false, bool $requireToken = true): array
    {
        try {
            $action = 'INSERT';
            $validate = $this->ValidateParamsWithAction($action, false, $data, $requireToken);
            if (!$validate['status']) {
                return $validate;
            } else {
                $param = new Arguments($validate["provided"]);

                $columns = array(); 
                $colsMap = array();

                foreach ($this->columns as $col) {
                    $cname =  isset($col['columnName']) ? $col['columnName'] : '';
                    if ($cname !== '') {
                        $alias = isset($col['columnAlias']) ? $col['columnAlias'] : '';
                        $colsMap[$cname] = ($alias !== '') ? $alias : '';
                    }
                }


                foreach ($param->getArray() as $key => $value) {
                    if (array_key_exists($key, $colsMap)) {
                        $columns[$key] = $value;
                    }
                }

                return $this->RunQuery($this->tableName, $action, $columns);
            }
        } catch (Exception $e) {
            return $this->formatMessage(new Exception(get_class($this) . ' insert error: ' . $e->getMessage(), $e->getCode()));
        }
    }


    /**
     * Updates an existing record identified by primary key.
     *
     * @param mixed $id Primary key value
     * @param array $data Associative array of column => value to update
     * @return array Result from Database::runQuery()
     * @throws Exception if ID is empty, validation fails, or query error occurs
     */
    public function update(mixed $id, array $data, bool $includePrimaryKey = false, bool $requireToken = true)
    {
        try {

            $action = 'UPDATE';
            if ($this->isNullOrEmpty($id)) {
                throw new Exception($this->primaryKeyAlias . ' value is required for update.');
            } else if (!$this->isIntOrString($id)) {
                throw new Exception($this->primaryKeyAlias . ' value must be an integer or a string.');
            } else {

                // $data = array_merge(array($data, $this->primaryKeyAlias => $id));
                $validate = $this->ValidateParamsWithAction($action, false, $data, $requireToken);
                if (!$validate['status']) {
                    return $validate;
                } else {
                    $param = new Arguments($validate["provided"]);

                    $columns = array();
                    foreach ($this->columns as $col) {
                        $cname = isset($col['columnName']) ? $col['columnName'] : "";
                        $alias = isset($col['columnAlias']) ? $col['columnAlias'] : "";

                        // $valid = isset($col['validations']) ? $col['validations'] : array();
                        if (!$this->isNullOrEmpty($cname)) {
                            $name = !$this->isNullOrEmpty($alias) ? $alias : $cname;
                            if ($name !== $this->primaryKey || ($includePrimaryKey && $name === $this->primaryKey)) {
                                $columns[$cname] = $param->getValue($name);
                            }
                        }
                    }
                    return $this->RunQuery($this->tableName, $action, $columns, array($this->primaryKey => $param->getValue($this->primaryKeyAlias)));
                }
            }
        } catch (Exception $e) {
            return $this->formatMessage(new Exception(get_class($this) . ' update error: ' . $e->getMessage(), $e->getCode()));
        }
    }


    /**
     * Deletes a record identified by primary key.
     *
     * @param mixed $id Primary key value
     * @return array Result from Database::runQuery()
     * @throws Exception if ID is empty or query fails
     */
    public function delete(mixed $id, bool $requireToken = true)
    {
        try {
            $action = 'DELETE';
            if ($this->isNullOrEmpty($id)) {
                throw new Exception($this->primaryKeyAlias . ' value is required for delete.');
            } else if (!$this->isIntOrString($id)) {
                throw new Exception($this->primaryKeyAlias . ' value must be an integer or a string.');
            } else {
                $validate = $this->ValidateParamsWithAction($action, true, array($this->primaryKeyAlias => $id), $requireToken);
                if (!$validate['status']) {
                    return $validate;
                } else {
                    $param = new Arguments($validate["provided"]);

                    return $this->deleteWhere(array($this->primaryKey => $param->getValue($this->primaryKeyAlias)), $requireToken);
                }
            }
        } catch (Exception $e) {
            return $this->formatMessage(new Exception(get_class($this) . ' delete error: ' . $e->getMessage(), $e->getCode()));
        }
    }



    /**
     * Deletes a record identified by primary key.
     *
     * @param array $where Primary key value
     * @return array Result from Database::runQuery()
     * @throws Exception if ID is empty or query fails
     */
    public function deleteWhere(array $where = array(), bool $requireToken = true)
    {
        try {
            $action = 'DELETE';
            if (!$this->isAssociative($where)) {
                throw new Exception('Where value must be an Associative');
            } else {
                $validate = $this->ValidateParamsWithAction($action, false, $where, $requireToken);
                if (!$validate['status']) {
                    return $validate;
                } else {
                    $param = new Arguments($validate["provided"]);
                    $wheres = array();

                    foreach ($param->getArray() as $key => $value) {
                        if (!in_array($key, array_merge($this->payloadKeys, array("accesstoken")))) {
                            $wheres[$key] = $value;
                        }
                    }

                    // throw new Exception(json_encode($wheres));
                    return $this->RunQuery($this->tableName, $action, array(), $wheres);
                }
            }
        } catch (Exception $e) {
            return $this->formatMessage(new Exception(get_class($this) . ' delete error: ' . $e->getMessage(), $e->getCode()));
        }
    }

    /**
     * Finds a single record by primary key.
     *
     * @param mixed $id Primary key value
     * @return array Result from Database::runQuery() with 'results' containing the record
     * @throws Exception if ID is empty or query fails
     */
    public function find(mixed $id, array $columns = array(), bool $requireToken = true)
    {
        try {
            if ($this->isNullOrEmpty($id)) {
                throw new Exception($this->primaryKeyAlias . ' value is required for find.');
            } else if (!$this->isIntOrString($id)) {
                throw new Exception($this->primaryKeyAlias . ' value must be an integer or a string.');
            } else {
                $action = 'SELECT';
                $validate = $this->ValidateParamsWithAction($action, true, array($this->primaryKeyAlias => $id), $requireToken);
                if (!$validate['status']) {
                    return $validate;
                } else {
                    $param = new Arguments($validate["provided"]);
                    return $this->findAll($columns, array($this->primaryKey => $param->getValue($this->primaryKeyAlias)), $requireToken, false);
                }
            }
        } catch (Exception $e) {
            return $this->formatMessage(new Exception(get_class($this) . ' find error: ' . $e->getMessage(), $e->getCode()));
        }
    }


    /**
     * Retrieves all records, optionally with WHERE conditions.
     *
     * @param array $conditions Associative array of column => value for WHERE clause (AND logic)
     * @return array Result from Database::runQuery() with 'results' containing all matching records
     * @throws Exception if query fails
     */
    public function findAll(array $columns = array(), array $conditions = array(), bool $requireToken = true, bool $pagenation = true, $extension = '')
    {
        try {
            $action = 'SELECT';
            $validate = $this->ValidateParamsWithAction($action, false, $conditions, $requireToken);
            if (!$validate['status']) {
                return $validate;
            } else {
                $cond = new Arguments($validate["provided"]);
                $colmns = array();
                $wheres = array();
                $colsMap = array();


                foreach ($this->columns as $col) {
                    $cname =  isset($col['columnName']) ? $col['columnName'] : '';
                    if ($cname !== '') {
                        $alias = isset($col['columnAlias']) ? $col['columnAlias'] : '';
                        $colsMap[$cname] = ($alias !== '') ? $alias : '';
                    }
                }


                // foreach ($cond->getArray() as $key => $value) {
                //     if (array_key_exists($key, $colsMap)) {
                //         $wheres[$key] = $value;
                //     }
                // }


                foreach ($cond->getArray() as $key => $value) {
                    if (!in_array($key, array_merge($this->payloadKeys, array("accesstoken")))) {
                        $wheres[$key] = $value;
                    }
                }

                if (count($columns) === 0) {
                    foreach ($colsMap as $cname => $name) {
                        $colmns[$cname] = ($cname === $this->primaryKey) ? $this->primaryKeyAlias : $name;
                    }
                } else {
                    foreach ($columns as $key => $value) {
                        if (!$this->isNullOrEmpty($key)) {
                            $colmns[$key] = ($key === $this->primaryKey && $this->isNullOrEmpty($value)) ? $this->primaryKeyAlias : $value;
                        }
                    }
                }

                // throw new Exception(json_encode($cond->getArray()));
                // throw new Exception(json_encode($this->payloadKeys));
                // throw new Exception(json_encode($colmns));

                return $this->RunQuery($this->tableName, 'SELECT', $colmns, $wheres, array(
                    'extension' => $extension,
                    'usepagenation' => $pagenation,
                ));
            }
        } catch (Exception $e) {
            return $this->formatMessage(new Exception(get_class($this) . ' find error: ' . $e->getMessage(), $e->getCode()));
        }
    }



    /**
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     */

    /**
     * @param string $title
     * @param int $count
     * 
     * @return string
     */
    protected function EmptyOrMoreError($title = "", $count = 0): string
    {
        $counter = $this->isNullOrEmpty($count) ? 0 : (!is_numeric($count) ? 0 : $count);
        $message = $this->isNullOrEmpty($title) ? "record" : $title;
        $display = ($counter <= 0 ? "No" : "{$counter} matching ") . " {$message}" . ($counter <= 0 ? "" : "s") . " found." . ($counter !== 0 ? " Please choose the correct one to continue." : "");
        $display = trim(preg_replace('/\s+/', ' ', $display));
        return $display;
    }

    protected function isIntOrString(mixed $value): bool
    {
        return (is_int($value) || is_string($value));
    }

    protected function ValidateParamsWithAction(string $action, bool $pkRequired, array $source = array(), bool $requireToken = true, string $separator = ", "): array
    {
        $rules = array();
        if (!in_array(strtoupper($action), array('INSERT')) && $pkRequired) {
            $rules[$this->primaryKeyAlias] = array('required' => true, 'exists' => $this->tableName . "." . $this->primaryKey);
        }
        return $this->ValidateParams($source, $requireToken, $separator, $rules);
    }

    protected function ValidateParams(array $source = array(), bool $requireToken = true, string $separator = ", ", array $extraRules = array()): array
    {
        try {
            $items = array();
            foreach ($this->columns as $col) {
                $cname = isset($col['columnName']) ? $col['columnName'] : "";
                $alias = isset($col['columnAlias']) ? $col['columnAlias'] : "";
                $valid = isset($col['validations']) ? $col['validations'] : array();
                if (!$this->isNullOrEmpty($cname)) {
                    if (!$this->isNullOrEmpty($valid)) {
                        $name = !$this->isNullOrEmpty($alias) ? $alias : $cname;
                        if (array_key_exists($name, $source)) {
                            $items[$name] = $valid;
                        }
                    }
                }
            }
            $items = array_merge($items, $extraRules);

            if (count($this->error) !== 0) {
                return $this->error;
            } else {
                // $src = $this->database->change_case($source);
                if (!$requireToken) {
                    return $this->ValidateInputs($this->change_case($source), $this->change_case($items), $separator);
                } else {
                    $token = $this->getBearerToken();
                    $validToken = $this->ValidateToken($token);
                    if (!$validToken['status']) {
                        return $validToken;
                    } else {
                        $newSrc = array_merge($source, $this->change_case($validToken['payload']), array('accesstoken' => $token));
                        $newRul = array_merge($items, array("accesstoken" => array('required' => true)));
                        return $this->ValidateInputs($this->change_case($newSrc), $this->change_case($newRul), $separator);
                    }
                }
            }
        } catch (Exception $e) {
            return $this->formatMessage($e);
        }
    }


    /**
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     */


    /**
     * isExists
     *
     * @param  mixed $table
     * @param  string $column
     * @return array
     */
    // private function isExists($table = "", $where = array()): array
    private function isExists(string $table, string $column, mixed $value): array
    {
        try {
            if (!$this->isNullOrEmpty($table)) {
                // $value = trim($value);
                if (is_string($value)) {
                    $value = trim($value);
                }
                return $this->RunQuery($table, "SELECT", array(), array($column => $value), array('usePagenation' => false));
            } else {
                throw new Exception("Missing table name");
            }
        } catch (Exception $ex) {
            return $this->formatMessage($ex);
        }
    }

    /**
     * ValidateEmail
     *
     * @param  mixed $email
     * @return bool
     */
    private function ValidateEmail($email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== FALSE;
    }

    /*
	* This is used to Check if the String is a valid Json 
	*/
    private function isJson(string $string = "")
    {
        $string = html_entity_decode($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return ((is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string))))) ? true : false;
    }

    private function validateDate(string $date, string $format = 'Y-m-d H:i:s')
    {
        $d = new \DateTime($date, new \DateTimeZone($this->timeZone));
        // $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }



    /**
     * Convert array keys to lowercase
     */
    public function change_case($arr = array())
    {
        return array_change_key_case($arr, CASE_LOWER);
    }


    /**
     * ValidateInputs
     *
     * Master input validator for all API requests.
     *
     * @param array  $source     Incoming request values
     * @param array  $items      Validation rules
     * @param string $separator  Error message separator
     *
     * @return array
     */

    private function ValidateInputs(array $source = array(), array $items = array(), string $separator = ", "): array
    {
        try {
            $valErr = array();

            $items = $this->change_case($items);
            $source = $this->change_case($source);

            foreach ($items as $item => $rules) {
                $i = 0;
                foreach ($rules as $rule => $rule_value) {

                    $rule = strtolower(trim($rule));
                    // $value = isset($source[$item]) ? (!is_array($source[$item]) ? trim(preg_replace('/\s+/', ' ', $source[$item])) : $source[$item]) : "";
                    if (isset($source[$item])) {
                        $value = $source[$item];
                        if (is_string($value)) {
                            $value = trim(preg_replace('/\s+/', ' ', $value));
                        }
                    } else {
                        $value = "";
                    }

                    $display = in_array('display', array_keys($rules)) ? $rules['display'] : $item;
                    $display = ($display);

                    //echo "{$item} {$rule} Must be {$rule_value} <br/>";

                    if ($rule === 'required'  && $rule_value === true) {
                        if ($this->isNullOrEmpty($value)) {
                            $valErr[] = "{$display} is required";
                        }
                    } else if (!$this->isNullOrEmpty($value)) {
                        switch ($rule) {
                            case 'maxlength':
                                $limit = $this->isNullOrEmpty($rule_value) || !is_numeric($rule_value) ? 1 : $rule_value;
                                $val = $this->isNullOrEmpty($value) || !is_numeric($value) ? $value : strval($value);
                                if (strlen($val) > $limit) {
                                    $valErr[] = "{$display} MUST not exceed a Maximum of {$limit} characters";
                                }
                                break;
                            case 'min':
                                $limit = $this->isNullOrEmpty($rule_value) || !is_numeric($rule_value) ? 1 : $rule_value;
                                $limit = $limit <= 0 ? 1 : $limit;
                                if (strlen($value) < $limit) {
                                    $valErr[] = "{$display} must be a Minimum of {$limit} characters";
                                }
                                break;
                            case 'max':
                                $limit = $this->isNullOrEmpty($rule_value) || !is_numeric($rule_value) ? 1 : $rule_value;
                                $limit = $limit <= 0 ? 1 : $limit;
                                if (strlen($value) > $limit) {
                                    $valErr[] = "{$display} must be a Maximum of {$limit} characters";
                                }
                                break;
                            case 'matches':
                                if ($value !== trim($source[$rule_value])) {
                                    $parent = $items[$rule_value];
                                    $match = in_array('display', array_keys($parent)) ? $parent['display'] : $item;
                                    $valErr[] = "{$display} MUST Match {$match}";
                                }
                                break;
                            case 'in_array':
                                if (!$this->isNullOrEmpty($rule_value)) {
                                    if (is_array($rule_value)) {
                                        if (!$this->isAssociative($rule_value)) {
                                            if (!in_array($value, array_change_key_case($rule_value, CASE_LOWER))) {
                                                $valErr[] = "{$display} Must be among the following: " . implode(', ', $rule_value);
                                            }
                                        } else {
                                            $valErr[] = "{$display}.{$rule} Must not be an Associative Array ";
                                        }
                                    } else {
                                        $valErr[] = "{$display}.{$rule} Must be an Array";
                                    }
                                } else {
                                    $valErr[] = "{$display}.{$rule} Must not be Empty ";
                                }
                                break;
                            case 'unique':
                                if (!$this->isNullOrEmpty($rule_value)) {
                                    $exp = explode('.', $rule_value);
                                    if (count($exp) == 2) {

                                        // $res = $this->isExists($exp[0], array($exp[1] => $value));
                                        $res = $this->isExists($exp[0], $exp[1], $value);
                                        if ($res['status'] == true) {
                                            if ($res['resultsCount'] >= 1) {
                                                $valErr[] = "{$display} already exists";
                                            }
                                        } else {
                                            $valErr[] = "{$display}: {$res['message']}";
                                        }
                                    } else {
                                        $valErr[] = "{$display}.{$rule} Allows a Format of [table.column] ";
                                    }
                                } else {
                                    $valErr[] = "{$display}.{$rule} Must not be Empty ";
                                }
                                break;
                            case 'exists':
                                if (!$this->isNullOrEmpty($rule_value)) {
                                    $exp = explode('.',  $rule_value);
                                    if (count($exp) == 2) {
                                        // $res = $this->isExists($exp[0], array($exp[1] => $value));
                                        $res = $this->isExists($exp[0], $exp[1], $value);
                                        if ($res['status'] == true) {
                                            if ($res['resultsCount'] <= 0) {
                                                $valErr[] = "{$display} [{$value}] does not exists";
                                            }
                                        } else {
                                            $valErr[] = "{$display}: {$res['message']}";
                                        }
                                    } else {
                                        $valErr[] = "{$display}.{$rule} Allows a Format of [table.column] ";
                                    }
                                } else {
                                    $valErr[] = "{$display}.{$rule} Must not be Empty ";
                                }
                                break;
                            case 'inputtype':
                                $type = strtolower(trim($rule_value));
                                if (in_array($type, $this->inputtypes)) {
                                    switch ($type) {
                                        case $this->inputtypes[0]: //number
                                            if (!is_numeric($value)) {
                                                $valErr[] = "{$display} must be Numeric";
                                            }
                                            break;
                                        case $this->inputtypes[1]: //array
                                            if (!$this->isNullOrEmpty($value)) {
                                                if (!is_array($value)) {
                                                    $valErr[] = "{$display} must be an Array";
                                                }
                                            }
                                            break;
                                        case $this->inputtypes[2]: //assoc
                                            if (!$this->isNullOrEmpty($value)) {
                                                if (is_array($value)) {
                                                    if (!$this->isAssociative($value)) {
                                                        $valErr[] = "{$display} must be an Associative Array";
                                                    }
                                                } else {
                                                    $valErr[] = "{$display} must be an Array";
                                                }
                                            }
                                            break;
                                        case $this->inputtypes[3]: // email
                                            if (!$this->ValidateEmail($value)) {
                                                $valErr[] = "Invalid Email Addres: [{$value}]";
                                            }
                                            break;
                                            // case $this->inputtypes[4]: // phone
                                            // 	$phone = $this->validatePhone($value, $display);
                                            // 	if ($phone['status']) {
                                            // 		$source["formatedPhone"] = $phone['formatedPhone'];
                                            // 	} else {
                                            // 		$valErr[] = $phone['message'];
                                            // 	}

                                            break;
                                        case $this->inputtypes[5]: // password
                                            // /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\W])).+$/
                                            // ^ -> start of the String
                                            // .+ gobble up the entire string
                                            // $ end of the string
                                            $pass = $value;
                                            $ucl = preg_match('/[A-Z]/', $pass);  //UPPER CASE LETTER
                                            $lcl = preg_match('/[a-z]/', $pass);  //LOWER CASE LETTER
                                            $dig = preg_match('/\d/', $pass);  //NUMERIAL
                                            $spc = preg_match('/_|[\W]/', $pass);  //SPECIAL CHARACTERS

                                            if (!$ucl || !$lcl || !$dig || !$spc) {
                                                $err = array();
                                                if (!$ucl) {
                                                    $err[] = "Upper Case";
                                                }
                                                if (!$lcl) {
                                                    $err[] = "Lower Case";
                                                }
                                                if (!$dig) {
                                                    $err[] = "Number";
                                                }
                                                if (!$spc) {
                                                    $err[] = "Special Character";
                                                }

                                                $valErr[] = "{$display} MUST contain at least ONE (" . implode(", ", $err) . ")";
                                            }
                                            break;
                                        case $this->inputtypes[6]: // json
                                            if ($this->isJson($value) !== true) {
                                                $valErr[] = "{$display} must be a Json String";
                                            }
                                            break;

                                        default:
                                            # code...
                                            break;
                                    }
                                } else {
                                    $valErr[] = "Invalid input Type {$type} at {$display} allows ONLY: " . implode(", ", $this->inputtypes);
                                }

                                break;
                            case 'explode':
                                if (!is_array($rule_value)) {
                                    $valErr[] = "{$display} Explode Rule MUST be an Array";
                                } else if (count($rule_value) !== 2) {
                                    $valErr[] = "{$display} Explode Rule Array MUST contain Two Value: (seperator and length)";
                                } else {
                                    $glue = $rule_value[0];
                                    $leng = $rule_value[1];

                                    $isWhiteSpace = preg_match('/^\s+$/',  $glue);
                                    $glue = $isWhiteSpace ? $glue : trim($glue);
                                    $label = $isWhiteSpace ? "Space" : $glue;

                                    if (empty($glue) || !is_numeric($leng)) {
                                        $err = array();
                                        if (empty($glue)) {
                                            $err[] = "Value ONE MUST NOT be a empty";
                                        }
                                        if (!is_numeric($leng)) {
                                            $err[] = "Value TWO MUST be a numeric";
                                        }

                                        $valErr[] = "{$display} Explode Rule Array " . implode(" AND ", $err);
                                    } else {

                                        if (!str_contains($value, $glue)) {
                                            $valErr[] = "{$display} MUST be seperated by: {$label}";
                                        } else {
                                            if ($leng >= 1) {
                                                $exp = explode($glue,  $value);
                                                if (count($exp) < $leng) {
                                                    $valErr[] = "{$display} MUST contain at least {$leng} or more Words seperated by {$label}";
                                                }
                                            }
                                        }
                                    }
                                }
                                break;

                            case 'datevalue':
                                if (!$this->isNullOrEmpty($value)) {
                                    if ($this->isNullOrEmpty($rule_value)) {
                                        $valErr[] = "{$display} DATE FORMAT should be provided";
                                    } else {
                                        if (gettype($rule_value) != 'string') {
                                            $valErr[] = "{$display} Validation Value MUST BE a String";
                                        } else {
                                            $res = $this->validateDate($value, $rule_value);
                                            if (!$res) {
                                                $valErr[] = "{$display} DATE FORMAT MUST BE: " . $rule_value;
                                            }
                                        }
                                    }
                                }
                                break;


                            case 'phonecode':
                                if (!$this->isNullOrEmpty($value)) {
                                    if ($this->isNullOrEmpty($rule_value)) {
                                        $valErr[] = "{$display} Phone FORMAT should be provided";
                                    } else {
                                        if (!is_numeric($value) || !is_numeric($rule_value)) {
                                            $er = array();
                                            if (!is_numeric($value)) {
                                                $er[] = $display;
                                            }
                                            if (!is_numeric($rule_value)) {
                                                $er[] = $rule;
                                            }
                                            $valErr[] = implode(" and ", $er) . " must be Numeric";
                                        } else {
                                            $phone = $this->validatePhoneNumber($value, $rule_value);
                                            if ($phone['status']) {
                                                $source["formatedPhone"] = $phone['data']['formatedPhone'];
                                            } else {
                                                $valErr[] = $phone['message'];
                                            }
                                        }
                                    }
                                }
                                // $valErr[] = $rule_value;
                                break;

                            default:
                                # code...
                                break;
                        }
                    }

                    $i++;
                }
            }

            if (count($valErr) === 0) {
                return $this->formatMessage(new Exception('Validation OK', 200), 0, array('provided' => $source));
            } else {
                throw new Exception(implode($separator, $valErr), 422);
            }
        } catch (Error $ex) {
            return $this->formatMessage($ex);
        } catch (ErrorException $ex) {
            return $this->formatMessage($ex);
        } catch (Exception $ex) {
            return $this->formatMessage($ex);
        }
        // $this->passed = count($this->errors) <=0 ? true : false;
    }



    /**
     * Validate and normalize a phone number (MSISDN).
     *
     * This function:
     * 1. Cleans the phone number (removes invalid characters)
     * 2. Validates country code support
     * 3. Extracts prefix and checks if it belongs to a valid operator
     * 4. Ensures correct length based on country rules
     * 5. Returns formatted phone details if valid
     *
     * Supported formats:
     *  - 0701234567
     *  - 256701234567
     *  - +256701234567
     *  - 070-123-4567
     *
     * @param string  $msisdn       Phone number input (string or numeric)
     * @param string $countryCode  ISO numeric country calling code (default: 256 for Uganda)
     *
     * @return array
     * Example success response:
     * {
     *   "status" => 200,
     *   "message" => "OK",
     *   "data" => {
     *      "operator" => "MTN",
     *      "countryCode" => "256",
     *      "normalPhone" => "0701234567",
     *      "formatedPhone" => "256701234567"
     *   }
     * }
     *
     * Example error response:
     * {
     *   "status" => 403,
     *   "message" => "Invalid MSISDN length..."
     * }
     */
    private function validatePhoneNumber(string $msisdn, string $countryCode = '256'): array
    {
        try {

            // Check if the country code is supported
            if ($this->isNullOrEmpty($countryCode)) {
                throw new Exception("Country code cannot be null or empty.", 403);
            } else if (!array_key_exists($countryCode, $this->countries)) {
                throw new Exception("Unsupported country code: $countryCode, we only support: " . implode(', ', array_keys($this->countries)), 403);
            } else  if ($this->isNullOrEmpty($msisdn)) {
                throw new Exception("MSISDN cannot be null or empty.", 403);
            } else if (!is_string($msisdn) && !is_numeric($msisdn)) {
                throw new Exception("MSISDN must be a string or numeric value.", 403);
                // } else if (is_string($msisdn) && !ctype_digit($msisdn)) {
                //     throw new Exception("MSISDN string must contain only digits.", 403);
            } else {
                $countryData = $this->countries[$countryCode];
                $maxLengthWithoutCountryCode = $countryData['maxLengthWithoutCountryCode'];
                $maxLengthWithCountryCode = $countryData['maxLengthWithCountryCode'];
                $prifixLength = $countryData['prefixLength'];
                $prefixStartsAt = $countryData['prefixStartsAt'];
                $operators = $countryData['operators'];
                $currency = $countryData['currency'];


                // Remove any non-digit characters
                $cleanedNumber = preg_replace('/\D/', '', trim($msisdn));
                $numberLength = ((int)strlen($cleanedNumber));


                $isCountryCodePresent = $numberLength < $maxLengthWithCountryCode  ? false  : ($numberLength <= $maxLengthWithoutCountryCode ? false : true);
                $countryCodePresent = $isCountryCodePresent ? substr($cleanedNumber, 0, strlen($countryCode)) : $countryCode;
                $maxLength = ((int)$isCountryCodePresent ? $maxLengthWithCountryCode : $maxLengthWithoutCountryCode);
                $prefix = $isCountryCodePresent ? substr($cleanedNumber, strlen($countryCode), $prifixLength) : substr($cleanedNumber, 1, $prifixLength);
                $phoneNumber = $isCountryCodePresent ? $prefixStartsAt . substr($cleanedNumber, strlen($prefix)) : $cleanedNumber;



                if ($this->isNullOrEmpty($prefix)) {
                    throw new Exception("Invalid MSISDN: Missing country code or prefix.", 403);
                } else if ($isCountryCodePresent && (!array_key_exists($countryCodePresent, $this->countries) || $this->isNullOrEmpty($countryCodePresent))) {
                    throw new Exception("Unsupported country code: $countryCodePresent, we only support: " . implode(', ', array_keys($this->countries)), 403);
                } else if ($isCountryCodePresent && $countryCodePresent !== $countryCode) {
                    // throw new Exception("Invalid MSISDN: Country code $countryCodePresent is not supported. Expected country code: $countryCode.", 403);
                    throw new Exception("Invalid MSISDN: Expected country code: $countryCode, but found: $countryCodePresent", 403);
                } else if ($numberLength !== $maxLength) {
                    throw new Exception("Invalid MSISDN length: " . $numberLength . " digits, expected length is $maxLength digits for country code $countryCode.", 403);
                } else if (!is_numeric($phoneNumber)) {
                    throw new Exception("MSISDN must contain only digits after cleaning.", 403);
                } else if (strlen($prefix) !== $prifixLength) {
                    throw new Exception("Invalid prefix length: $prefix, expected length is $prifixLength digits for country code $countryCode.", 403);
                } else {


                    $errors = array();
                    $data = array();


                    // Get the prefix groups for the operators and check if the prefix exists in any of them
                    foreach ($operators as $operator => $prefixGroups) {
                        $prefix =  substr($phoneNumber, 0, $prifixLength);

                        // Check if the prefix exists in the current operator's prefix groups
                        $prefixGroup = array_key_exists($prefix, $prefixGroups) ? $prefixGroups[$prefix] : null;

                        if (!is_null($prefixGroup)) {

                            $start = $prefixGroup['start'];
                            $ends = $prefixGroup['ends'];



                            $suffixes = array_map(fn($i) => $prefix . $i, range($start, $ends));
                            $suffix = substr($phoneNumber, 0, strlen($prefix) + 1);

                            // $errors[] =     "Found $suffix in $operator with suffixes: " . implode(", ", $suffixes);

                            if (!in_array($suffix, $suffixes)) {
                                $errors[] = "Invalid MSISDN: Prefix number $suffix out of range for $operator. Expected Prefix: " . implode(", ", $suffixes);
                            } else {

                                $data['data'] = array(
                                    'operator' => $operator,
                                    'countryCode' => $countryCode,
                                    'countryCurrency' => $currency,
                                    'normalPhone' => $phoneNumber,
                                    'formatedPhone' => $countryCode . substr($phoneNumber,  1)
                                );
                            }
                        }
                    }

                    if (count($errors) !== 0) {
                        throw new Exception(implode(", ", $errors), 403);
                    } else {
                        if (count($data) === 0) {
                            throw new Exception("Invalid MSISDN: No valid operator found for $phoneNumber.", 403);
                        } else {
                            return $this->formatMessage(new Exception("OK", 200), $data);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            // Handle any unexpected errors gracefully
            return $this->formatMessage($e);
        }
    }





    /**
     * Base64Url Encode.
     *
     * @param string $data 
     * 
     * @return string
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64Url Decode.
     *
     * @param string $data 
     * 
     * @return string
     */
    private function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Get Authorization Header.
     * 
     * @return ?string|null
     */
    private function getAuthorizationHeader(): ?string
    {
        $headers = array();

        // 1. Try apache_request_headers() if available
        if (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();

            // Normalize header keys to lowercase for consistency
            foreach ($requestHeaders as $key => $value) {
                $headers[strtolower($key)] = $value;
            }

            if (isset($headers['authorization'])) {
                return trim($headers['authorization']);
            } else {
                return null;
            }
        }

        // 2. Fallback to $_SERVER variables (works on NGINX, FastCGI, etc.)
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            return trim($_SERVER['HTTP_AUTHORIZATION']);
        }

        // 3. PHP under CGI may store it in this alternative
        else if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            return trim($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
        }

        // No Authorization header found
        else {
            return null;
        }
    }


    /**
     * Validate Payload.
     *
     * @param array $provided 
     * 
     * @return array
     */
    private function validatePayload(array $provided): array
    {
        try {
            if ($this->isNullOrEmpty($this->payloadKeys) || $this->isNullOrEmpty($provided)) {
                $err = array();
                if ($this->isNullOrEmpty($this->payloadKeys)) {
                    $err[] = 'Payload Keys';
                }
                if ($this->isNullOrEmpty($provided)) {
                    $err[] = 'Payload Provided Keys';
                }
                throw new Exception('Missing: ' . implode('and', $err), 403);
            } else if ($this->isAssociative($this->payloadKeys) || !$this->isAssociative($provided)) {
                $err = array();
                if ($this->isAssociative($this->payloadKeys)) {
                    $err[] = 'Payload Keys MUST NOT BE an Associative array';
                }
                if (!$this->isAssociative($provided)) {
                    $err[] = 'Payload Provided Keys  MUST BE an Associative array';
                }
                throw new Exception(implode('and', $err), 403);
            } else {
                $err = array();
                foreach ($this->payloadKeys as $key) {
                    $k = strtolower($key);
                    if (!array_key_exists($k, array_change_key_case($provided, CASE_LOWER))) {
                        $err[] = $k;
                    }
                }
                if ($this->isNullOrEmpty($err)) {
                    throw new Exception("OK", 200);
                } else {
                    throw new Exception('Missing Payload keys: ' . implode(', ', $err), 403);
                }
            }
        } catch (Exception $e) {
            return $this->formatMessage($e);
        }
    }



    /**
     * Get Bearer Token.
     *
     * 
     * @return string
     */
    public function getBearerToken(): string
    {
        $authHeader = $this->getAuthorizationHeader();
        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $matches[1];
        }
        return "";
    }


    /**
     * Generate Token.
     *
     * @param array $parameters 
     * 
     * @return array
     */
    public function GenerateToken(array $parameters = array()): array
    {
        try {
            if (!$this->isNullOrEmpty($this->error)) {
                return $this->error;
            } else if ($this->isNullOrEmpty($parameters)) {
                throw new Exception("Missing Token Payload");
            } else {
                $valid = $this->validatePayload($parameters);
                if (!$valid['status']) {
                    return $valid;
                } else {
                    $header = array('alg' => 'HS256', 'typ' => 'JWT');
                    $now = new \DateTime("now", new \DateTimeZone($this->timeZone));

                    // Add expiration time to payload;
                    $payload = array(
                        'exp' => time() + $this->expirySeconds,
                        'iss' => $now->format("Y-m-d H:i:s"),
                        'val' => $parameters
                    );

                    $base64Header = $this->base64UrlEncode(json_encode($header));
                    $base64Payload = $this->base64UrlEncode(json_encode($payload));

                    $signature = hash_hmac('sha256', "$base64Header.$base64Payload", $this->secret, true);
                    $base64Signature = $this->base64UrlEncode($signature);
                    $token = "$base64Header.$base64Payload.$base64Signature";

                    return $this->formatMessage(new Exception("Token OK", 200), 0, array('token' => $token));
                }
            }
        } catch (Exception $e) {
            return $this->formatMessage($e);
        }
    }



    /**
     * Validate Token.
     *
     * @param string $token 
     * 
     * @return array
     */
    public  function ValidateToken(string $token): array
    {

        try {
            if (!$this->isNullOrEmpty($this->error)) {
                return $this->error;
            } else if ($this->isNullOrEmpty($token)) {
                // throw new Exception("missing token", 402);
                // throw new Exception(get_class($this) ." ". __FUNCTION__."  requires a Authorization Token to continue.", 401);
                throw new Exception("Call requires a Authorization Token to continue.", 401);
            } else {
                $parts = explode('.', $token);
                if (count($parts) !== 3) {
                    throw new Exception("Invalid token format provided. " . implode(' ', $parts), 402);
                } else {

                    [$base64Header, $base64Payload, $base64Signature] = $parts;

                    $header = json_decode($this->base64UrlDecode($base64Header), true);
                    $payload = json_decode($this->base64UrlDecode($base64Payload), true);
                    $signatureCheck = $this->base64UrlEncode(
                        hash_hmac('sha256', "$base64Header.$base64Payload", $this->secret, true)
                    );

                    // Check signature validity
                    if (hash_equals($base64Signature, $signatureCheck)) {

                        $valid = $this->validatePayload($payload['val']);
                        if (!$valid['status']) {
                            throw new Exception("Invalid token payload", 403);
                        } else {
                            // Check expiration
                            $expires = $this->canExpire == 'T';
                            if ((isset($payload['exp']) && $expires) ? time() > $payload['exp'] : false) {
                                throw new Exception("token already expired", 403);
                            } else {
                                return $this->formatMessage(new Exception("Token Okay", 200), 0, array('payload' => $payload['val']));
                            }
                        }
                    } else {
                        throw new Exception("Invalid token Signature", 403);
                    }
                }
            }
        } catch (Exception $e) {
            return $this->formatMessage($e);
        }
    }
}




// class Arguments
// {
//     private $_provided =  array();

//     public function __construct(array $provided = array(), bool $changeCase = true)
//     {
//         $this->_provided = $changeCase ? array_change_key_case($provided, CASE_LOWER) : $provided;
//     }

//     public function __toString(): string
//     {
//         return json_encode($this->_provided);
//     }

//     public function getValue(string $key, mixed $default = "")
//     {
//         $lower = strtolower($key);
//         // return isset($this->_provided[$lower]) ? preg_replace('/\s+/', ' ', (strtolower(gettype($this->_provided[$lower])) == "string" ? html_entity_decode(trim($this->_provided[$lower])) : $this->_provided[$lower])) : $default;
//         if (isset($this->_provided[$lower])) {
//             if (gettype($this->_provided[$lower]) === 'string') {
//                 return preg_replace('/\s+/', ' ', html_entity_decode(trim($this->_provided[$lower])));
//             } else {
//                 return $this->_provided[$lower];
//             }
//         } else {
//             return $default;
//         }
//     }

//     public function getArray()
//     {
//         return $this->_provided;
//     }

//     public function debug()
//     {
//         echo json_encode($this->_provided);
//     }
// }
