<?php


class Database
{

    /**
     * Column Prefix.
     *
     * @var string
     */
    public $colPrefix = "p_";

    /**
     * General Error.
     *
     * @var array
     */
    protected $error = array();

    /**
     * This will stores our MYSQL Database Connection
     * 
     * @var PDO|null
     */
    private $conn = null;

    /**
     * Database name.
     *
     * @var string
     */
    private $dbname = "";


    /**
     * @var string $primaryKey Primary key column name
     */
    protected $primaryKey = '';



    /**
     * Allow Multi-row insertion.
     *
     * @var string
     */
    public $multrowKey = "multiple-rows";

    /**
     * Construct.
     *
     * @param string $hostname database hostname.
     * @param string $database database name.
     * @param string $username database username.
     * @param string $password database password.
     * @param int $port database port.
     */
    public function __construct(string $hostname, string $database, string $username, string $password = '', int $port = 0)
    {
        try {
            if (version_compare(PHP_VERSION, '7.0.0', "<")) {
                throw new Exception("The System requires PHP Version above 7.0.0 but you have: " . PHP_VERSION);
            } else if ($this->isNullOrEmpty($hostname) || $this->isNullOrEmpty($database) || $this->isNullOrEmpty($username) || (!$this->isNullOrEmpty($port) && !is_numeric($port))) {
                $gen = array();
                $err = array();
                if ($this->isNullOrEmpty($hostname)) {
                    $err[] = "hostname";
                }
                if ($this->isNullOrEmpty($database)) {
                    $err[] = "database name";
                }
                if ($this->isNullOrEmpty($username)) {
                    $err[] = "username";
                }

                if (count($err) >= 1) {
                    $gen[] = "Missing Database: " . implode(", ", $err);
                }
                if (!$this->isNullOrEmpty($port) && !is_numeric($port)) {
                    $gen[] = "Port MUST be numeric";
                }

                throw new Exception(implode(" and ", $gen));
            } else {
                $this->dbname = $database;


                $encode = "utf8";
                $dsn = "mysql:host=" . $hostname . ";dbname=" . $database;
                if (!$this->isNullOrEmpty($port) && $port > 0) {
                    $dsn .= ";port=" . $port;
                }
                // $opt = array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION );

                $opt = array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    // PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES ".$encode,
                    PDO::ATTR_EMULATE_PREPARES =>  false,
                    PDO::ATTR_STRINGIFY_FETCHES =>  false,
                    // PDO::SQLSRV_ATTR_FORMAT_DECIMALS => true,
                );

                if (version_compare(PHP_VERSION, "5.3.6", '<')) {
                    if (defined("PDO::MYSQL_ATTR_INIT_COMMAND")) {
                        $opt[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $encode;
                    }
                } else {
                    $dsn .= ";charset=" . $encode;
                }
                $this->conn = @new PDO($dsn, $username, $password, $opt);

                if (version_compare(PHP_VERSION, "5.3.6", '<') && !defined("PDO::MYSQL_ATTR_INIT_COMMAND")) {
                    $this->conn->exec('SET NAMES ' . $encode);
                }
            }
        } catch (PDOException $e) {

            if (!in_array($e->getCode(), array('HY093'))) {
                $this->error = $this->formatMessage(new Exception(str_ireplace($this->dbname . ".", "", $e->errorInfo[2]), 403), $e->errorInfo[1]);
            } else {
                $code = preg_replace('/[^0-9]/', '', $e->getCode());
                $this->error = $this->formatMessage(new Exception(str_ireplace($this->dbname . ".", "", $e->getMessage()), 403), ((int)$code));
            }
        } catch (Error $e) {
            $this->error = $this->formatMessage(new Exception($e->getMessage(), $e->getCode()));
        } catch (Exception $e) {
            $this->error = $this->formatMessage($e);
        }
    }

    /**
     * Function to destroy database Connection After use
     * 
     */

    public function __destruct()
    {
        if ($this->isNullOrEmpty($this->error)) {
            $this->conn = null;
        }
    }



    public function setError(Exception $ex)
    {
        $this->error = $this->formatMessage($ex);
    }


    public function setPrimaryKey(string $primaryKey)
    {
        $this->primaryKey = $primaryKey;
    }


    /**
     * Format Error.
     *
     * @param Throwable $ex
     * @param mixed $messageCode 
     * 
     * @return array
     */
    public function formatMessage(Throwable $ex, mixed $messageCode = 0, array $extraParams = array()): array
    {
        try {
            return array_merge(
                array(
                    'status' => in_array($ex->getCode(), array(200, 201, 202, 203)) ? true : false,
                    'statusCode' => $ex->getCode() == 0 ? 500 : $ex->getCode(),
                    'messageCode' => $messageCode,
                    'message' => $ex->getMessage(),
                    // 'file' => $ex->getFile(),
                    // 'line' => $ex->getLine(),
                    // 'message' => !$this->isNullOrEmpty($ex->getMessage()) ? $ex->getMessage() : "Operation has been Successfully Done",
                ),
                $extraParams
            );
        } catch (Error $e) {
            return array('status' => $e->getCode() == 200, 'statusCode' => $e->getCode(), 'messageCode' => 'TypeError: ' . $e->getCode(), 'message' => $e->getMessage());
        } catch (TypeError $e) {
            return array('status' => $e->getCode() == 200, 'statusCode' => $e->getCode(), 'messageCode' => 'TypeError: ' . $e->getCode(), 'message' => $e->getMessage());
        } catch (Exception $e) {
            return array('status' => $e->getCode() == 200, 'statusCode' => $e->getCode(), 'messageCode' => 'Exception: ' . $e->getCode(), 'message' => $e->getMessage());
        }
    }



    /**
     * isAssociative
     * This is used to Check if an array is an Associative 
     * 
     * @param array $array 
     * 
     * @return bool
     */
    public function isAssociative(mixed $array): bool
    {
        if (gettype($array) == 'array') {
            if (is_array($array)) {
                $array = array_keys($array);
                $values = array_values($array);
                return (count($array) == count($values)) ? ($array !== array_keys($array)) : false;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }



    /**
     * @param mixed $var
     * 
     * @return bool
     */
    public function isNullOrEmpty(mixed $var = null, bool $simple = false): bool
    {
        if ($simple) {
            return $var === null || $var === '' || $var === array() || $var === 0 || $var === '0' || $var === false;
        } else {

            if ($var === null) { // Check for null first (fastest check)
                return true;
            } else if (is_string($var)) {  // Check for empty strings (including '0' which $this->isNullOrEmpty() considers non-empty)
                return $var === '';
            } else if (is_array($var)) { // Check for empty arrays
                return $var === array();
            } else if ($var instanceof \Countable) { // Check for empty Countable objects
                return count($var) === 0;
            } else if (is_numeric($var)) { // Check for numeric zero values
                return ($var == 0 || $var == 0.0); // Use loose comparison for numeric types
            } else if (is_bool($var)) { // Check for boolean false
                return $var === false;
            } else if (is_object($var)) { // Check for empty objects
                // For objects with __toString method
                if (method_exists($var, '__toString')) {
                    return (string)$var === '';
                } else if ($var instanceof \stdClass) { // For empty stdClass objects
                    return (array)$var ===  array();
                } else {       // For other objects, consider them non-empty by default
                    return false;
                }
            } else if (is_resource($var)) { // Check for resources (resources are never empty)
                return false;
            } else { // Fallback: use $this->isNullOrEmpty() but handle '0' string case
                return $this->isNullOrEmpty($var) && $var !== '0' && $var !== 0;
            }
        }
    }




    /**
     * Universal PDO Query Builder
     * Fully self-contained: supports CRUD, aggregates, nested AND/OR WHERE, multi-row inserts
     *
     * @param string $table      Table name
     * @param string $action     SQL action: SELECT, INSERT, UPDATE, DELETE
     * @param array  $columns    Columns array (supports aggregates "COUNT(id)" => "total_orders")
     * @param array  $where      Nested where conditions (supports AND/OR)
    //  * @param string $extension  Additional SQL (ORDER BY, GROUP BY, LIMIT)
    //  * @param string $message    Success message for non-SELECT actions
     * @return array $extras    array('extension' => 'ORDER BY', 'message' => 'Query OK', 'showsql' => false, 'usePagenation' => true)
     * @return array             Standardized response array
     */
    // public function RunQuery(string $table = "", string $action = "", array $columns = array(), array $where = array(), string $extension = "", string $message = "Operation has been Successfully Done", $showSql = false, bool $applyPagination = true): array
    public function RunQuery(string $table = "", string $action = "", array $columns = array(), array $where = array(), array $extras = array()): array
    {
        try {

            $extras = array_change_key_case($extras, CASE_LOWER);
            $extension = isset($extras['extension']) ? $extras['extension'] : '';
            $message = isset($extras['message']) ? $extras['message'] : 'Operation has been Successfully Done';
            $showSql = ((bool)isset($extras['showsql']) ? $extras['showsql'] : false);

            $usePagenation = ((bool)isset($extras['usepagenation']) ? $extras['usepagenation'] : true);
            $resultsPerPage = ((bool)isset($extras['resultsperpage']) ? $extras['resultsperpage'] : 30);
            $currentPage = ((bool)isset($extras['currentpage']) ? $extras['currentpage'] : 1);


            $table = trim(preg_replace('/\s+/', ' ', $table));
            $action = trim(preg_replace('/\s+/', '', $action));
            $extension = trim(preg_replace('/\s+/', ' ', $extension));
            $message = trim(preg_replace('/\s+/', ' ', $message));


            if (!$this->isNullOrEmpty($this->error)) {
                return $this->error;
            } else if (is_null($this->conn)) {
                throw new Exception("Connection Variable is Null");
            } else {
                if ($this->isNullOrEmpty($table) || $this->isNullOrEmpty($action)) {
                    $err = array();
                    if ($this->isNullOrEmpty($table)) {
                        $err[] = "Table name";
                    }
                    if ($this->isNullOrEmpty($action)) {
                        $err[] = "Action";
                    }
                    throw new Exception("Missing SQL: " . implode(', ', $err));
                } else if (
                    (!$this->isNullOrEmpty($columns) && !$this->isAssociative($columns)) ||
                    (!$this->isNullOrEmpty($where) && !$this->isAssociative($where))
                ) {
                    $err = array();
                    if (!$this->isNullOrEmpty($columns) && !$this->isAssociative($columns)) {
                        $err[] = "Column";
                    }
                    if (!$this->isNullOrEmpty($where) && !$this->isAssociative($where)) {
                        $err[] = "Where";
                    }
                    throw new Exception("Parameter(s): [" . implode(', ', $err) . "] MUST BE of Associative Array type");
                } else {
                    $allowed = array("INSERT", "DELETE", "UPDATE", "SELECT");
                    $action = strtoupper(trim($action, " \n\r\t\v\0"));

                    if (!in_array($action, $allowed)) {
                        throw new Exception("Action: {$action} MUST BE in " . implode(', ', $allowed) . "");
                    } else 
						if (!in_array($action,  array($allowed[1], $allowed[3])) && $this->isNullOrEmpty($columns)) {
                        throw new Exception("Column Parameter MUST NOT be empty");
                    } else {


                        // ---------------------- Initialize ----------------------
                        $isInsert = $action == $allowed[0];
                        $isDelete = $action == $allowed[1];
                        $isUpdate = $action == $allowed[2];
                        $isSelect = $action == $allowed[3];
                        $params = array(); // Bound parameters
                        $errors = array(); // Bound parameters
                        $sql = "";


                        // ---------------------- Build SQL ----------------------
                        if ($isInsert) {
                            // Multi-row INSERT

                            $baseColumns = array();
                            if (isset($columns[$this->multrowKey]) && is_array($columns[$this->multrowKey])) {
                                $rows = $columns[$this->multrowKey];
                                if (empty($rows)) {
                                    // throw new Exception("Multi-row insert cannot be empty");
                                    $errors[] = "Multi-row insert cannot be empty";
                                } else {
                                    $cols = array_keys($rows[0]);
                                    $baseColumns = $cols;

                                    $placeholders = array();
                                    foreach ($rows as $i => $row) {
                                        $ph = array();
                                        foreach ($cols as $c) {
                                            $param = ":" . $c . "_" . $i;
                                            $ph[] = $param;
                                            $params[$param] = $row[$c];
                                        }
                                        $placeholders[] = "(" . implode(",", $ph) . ")";
                                    }
                                    $sql = "INSERT INTO " . $table . " (" . implode(",", $cols) . ") VALUES " . implode(",", $placeholders);
                                }
                            } else {
                                // Single-row INSERT
                                if (empty($columns)) {
                                    // throw new Exception("INSERT columns cannot be empty");
                                    $errors[] = "INSERT columns cannot be empty";
                                } else {
                                    $cols = array_keys($columns);
                                    $baseColumns = $cols;
                                    $phs = array();
                                    foreach ($columns as $k => $v) {
                                        $ph = ":" . $k;
                                        $phs[] = $ph;
                                        $params[$ph] = $v;
                                    }
                                    $sql = "INSERT INTO " . $table . " (" . implode(",", $cols) . ") VALUES (" . implode(",", $phs) . ")";
                                }
                            }

                            // Construct the UPDATE assignments dynamically
                            // Note: MySQL 8.0+ prefers "AS new_rows ON DUPLICATE KEY UPDATE username = new_rows.username"
                            // This approach uses the universally compatible VALUES(column) syntax:
                            $updatePart = [];
                            foreach ($baseColumns as $col) {
                                // Avoid updating primary/unique keys if they trigger the conflict
                                if (!in_array($col, array('id', '_id', $this->primaryKey))) {
                                    $updatePart[] = "`$col` = VALUES(`$col`)";
                                }
                            }
                            $updateString = implode(', ', $updatePart);

                            $sql .= count($updatePart) >= 1 ? " ON DUPLICATE KEY UPDATE " . $updateString : "";
                        } else if ($isUpdate) {
                            if (empty($columns)) {
                                $errors[] = "UPDATE columns cannot be empty";
                            } else {
                                $set = array();

                                // Check if a key-based batch CASE structure is provided
                                $casePayload = isset($columns['case']) ? $columns['case'] : (isset($columns['CASE']) ? $columns['CASE'] : null);

                                if (is_array($casePayload) && isset($casePayload['key']) && (isset($casePayload['data']) || isset($casePayload['columns']))) {
                                    // -------------------------------------------------------------
                                    // 1. Batch UPDATE CASE by Key Column (e.g., WHEN id = 1 THEN ...)
                                    // -------------------------------------------------------------
                                    $caseKey  = trim($casePayload['key'], '`');
                                    $caseData = isset($casePayload['data']) ? $casePayload['data'] : $casePayload['columns'];
                                    $useElse  = isset($casePayload['else']) ? $casePayload['else'] : true; // Default: ELSE `col_name`

                                    foreach ($caseData as $col => $whenThenMap) {
                                        if (is_array($whenThenMap)) {
                                            $cleanCol = trim($col, '`');
                                            $caseSql  = "`" . $cleanCol . "` = CASE";
                                            $cIdx     = 0;

                                            foreach ($whenThenMap as $whenVal => $thenVal) {
                                                $paramWhen = ":cw_" . preg_replace('/[^a-zA-Z0-9_]/', '', $cleanCol) . "_" . $cIdx;
                                                $paramThen = ":ct_" . preg_replace('/[^a-zA-Z0-9_]/', '', $cleanCol) . "_" . $cIdx;

                                                $caseSql .= " WHEN `" . $caseKey . "` = " . $paramWhen . " THEN " . $paramThen;

                                                $params[$paramWhen] = $whenVal;
                                                $params[$paramThen] = $thenVal;
                                                $cIdx++;
                                            }

                                            if ($useElse === true || $useElse === $cleanCol || $useElse === "`" . $cleanCol . "`") {
                                                $caseSql .= " ELSE `" . $cleanCol . "`";
                                            } else if ($useElse !== false && $useElse !== null) {
                                                $paramElse = ":ce_" . preg_replace('/[^a-zA-Z0-9_]/', '', $cleanCol);
                                                $caseSql .= " ELSE " . $paramElse;
                                                $params[$paramElse] = $useElse;
                                            }

                                            $caseSql .= " END";
                                            $set[]    = $caseSql;
                                        }
                                    }
                                } else {
                                    // -------------------------------------------------------------
                                    // 2. Standard SET or Per-Column Explicit CASE UPDATE
                                    // -------------------------------------------------------------
                                    foreach ($columns as $k => $v) {
                                        $cleanCol  = trim($k, '`');
                                        $isColCase = is_array($v) && (isset($v['case']) || isset($v['CASE']));

                                        if ($isColCase) {
                                            $caseData = isset($v['case']) ? $v['case'] : $v['CASE'];
                                            $elseVal  = isset($v['else']) ? $v['else'] : (isset($v['ELSE']) ? $v['ELSE'] : true);

                                            $caseSql = "`" . $cleanCol . "` = CASE";
                                            foreach ($caseData as $i => $c) {
                                                $whenCond = isset($c['when']) ? $c['when'] : (isset($c['WHEN']) ? $c['WHEN'] : null);
                                                $thenVal  = isset($c['then']) ? $c['then'] : (isset($c['THEN']) ? $c['THEN'] : null);

                                                if ($whenCond !== null && $thenVal !== null) {
                                                    if (is_array($whenCond)) {
                                                        $wClauses = array();
                                                        foreach ($whenCond as $wCol => $wVal) {
                                                            $pWhen = ":cw_" . preg_replace('/[^a-zA-Z0-9_]/', '', $cleanCol) . "_" . $i . "_" . preg_replace('/[^a-zA-Z0-9_]/', '', $wCol);
                                                            $wClauses[] = "`" . trim($wCol, '`') . "` = " . $pWhen;
                                                            $params[$pWhen] = $wVal;
                                                        }
                                                        $whenStr = implode(" AND ", $wClauses);
                                                    } else {
                                                        if (preg_match('/[=><]/', (string)$whenCond)) {
                                                            $whenStr = $whenCond;
                                                        } else {
                                                            $pWhen = ":cw_" . preg_replace('/[^a-zA-Z0-9_]/', '', $cleanCol) . "_" . $i;
                                                            $whenStr = "`id` = " . $pWhen;
                                                            $params[$pWhen] = $whenCond;
                                                        }
                                                    }

                                                    $pThen = ":ct_" . preg_replace('/[^a-zA-Z0-9_]/', '', $cleanCol) . "_" . $i;
                                                    $caseSql .= " WHEN " . $whenStr . " THEN " . $pThen;
                                                    $params[$pThen] = $thenVal;
                                                }
                                            }

                                            if ($elseVal === true || $elseVal === $cleanCol || $elseVal === "`" . $cleanCol . "`") {
                                                $caseSql .= " ELSE `" . $cleanCol . "`";
                                            } else if ($elseVal !== false && $elseVal !== null) {
                                                $pElse = ":ce_" . preg_replace('/[^a-zA-Z0-9_]/', '', $cleanCol);
                                                $caseSql .= " ELSE " . $pElse;
                                                $params[$pElse] = $elseVal;
                                            }

                                            $caseSql .= " END";
                                            $set[]    = $caseSql;
                                        } else {
                                            // Standard UPDATE assignment
                                            $param          = ":" . preg_replace('/[^a-zA-Z0-9_]/', '', $cleanCol);
                                            $set[]          = "`" . $cleanCol . "` = " . $param;
                                            $params[$param] = $v;
                                        }
                                    }
                                }

                                $sql = "UPDATE " . $table . " SET " . implode(", ", $set);
                            }
                        } else if ($isDelete) {
                            $sql = "DELETE FROM " . $table;
                        } else if ($isSelect) {
                            if (empty($columns)) {
                                $cols = "*";
                            } else {
                                $selectParts = array();
                                foreach ($columns as $k => $v) {
                                    if (!empty($v)) {
                                        $selectParts[] = $k . " AS `" . $v . "`";
                                    } else {
                                        $selectParts[] = $k;
                                    }
                                }
                                $cols = implode(",", $selectParts);
                            }
                            $sql = "SELECT " . $cols . " FROM " . $table;
                        }



                        // throw new Exception($sql);


                        // ---------------------- Build WHERE clause (nested AND/OR inside function) ----------------------
                        $buildWhere = function ($whereArray, $prefix = 'w_') use (&$params, &$buildWhere, &$errors) {
                            $clauses = array();
                            foreach ($whereArray as $key => $val) {
                                $upperKey = strtoupper($key);

                                if (in_array($upperKey, array('AND', 'OR'))) {

                                    $isAnd = $upperKey == 'AND';
                                    $isAssoc = (empty($val) || (!empty($val) && !is_array($val)))
                                        ? false
                                        : array_keys($val) !== range(0, count($val) - 1);

                                    if (!$isAssoc) {
                                        $errors[] = "KEY: {$upperKey} MUST BE an array";
                                    } else {
                                        $individual = array();
                                        foreach ($val as $k => $v) {

                                            $k = trim(preg_replace('/\s+/', ' ', $k));
                                            $param = ":" . str_ireplace(":", "", $prefix) . preg_replace('/[^a-zA-Z0-9_]/', '', $k);

                                            $counts = array_count_values(array_keys($params));
                                            $param = isset($params[$param]) && ($counts[$param] >= 1) ? $param . "_" . $counts[$param] + 1 : $param;


                                            if (is_numeric($k) && is_array($v)) {
                                                if (count($v) >= 1) {
                                                    $individual[] = $buildWhere($v, $prefix);
                                                }
                                            } else {
                                                if (is_array($v)) {
                                                    // $individual[] = $buildWhere($v, $prefix);
                                                    $individual[] = $buildWhere(array($k => $v), $prefix);
                                                } else {
                                                    $exp = explode(" ", $k);
                                                    if (count($exp) >= 2) {
                                                        $individual[] = $buildWhere(array($exp[0] => array("operator" => $exp[1], "value" => $v)), $prefix);
                                                    } else {
                                                        $individual[] = $k . " = " . $param;
                                                        $params[$param] = $v;
                                                    }
                                                }
                                            }
                                        }
                                        if (count($individual) >= 1) {
                                            $open = !$isAnd ? "(" : "";
                                            $close = !$isAnd ? ")" : "";
                                            $clauses[] = $open . implode(" {$upperKey} ", $individual) . $close;
                                        }
                                    }
                                } else {
                                    // $clauses[] = $key;
                                    if (is_numeric($key)) {
                                        if (!is_array($val)) {
                                            $errors[] = "Invalid Key: {$key}";
                                        } else {
                                            $individual = array();
                                            foreach ($val as $k => $v) {
                                                $individual[] = $buildWhere(array($k => $v), $prefix);
                                            }
                                            if (count($individual) >= 1) {
                                                $clauses[] =  implode(" AND ", $individual);
                                            }
                                        }
                                    } else {
                                        $key = preg_replace('/\s+/', ' ', trim($key));
                                        $param = ":" . str_ireplace(":", "", $prefix) . preg_replace('/[^a-zA-Z0-9_]/', '', $key);

                                        $counts = array_count_values(array_keys($params));
                                        $param = isset($params[$param]) && ($counts[$param] >= 1) ? $param . "_" . $counts[$param] + 1 : $param;


                                        if (!is_array($val)) {
                                            $exp = explode(" ", $key);
                                            $individual = array();
                                            if (count($exp) >= 2) {
                                                $individual[] = $buildWhere(array($exp[0] => array("operator" => $exp[1], "value" => $val)), $prefix);
                                            } else {
                                                $individual[] = $key . " = " . $param;
                                                $params[$param] = $val;
                                            }
                                            if (count($individual) >= 1) {
                                                $clauses[] =  implode(" AND ", $individual);
                                            }
                                        } else {

                                            $val = array_change_key_case($val, CASE_LOWER);
                                            $operator = strtoupper(isset($val['operator']) ? $val['operator'] : '=');
                                            $value = isset($val['value']) ? $val['value'] : null;



                                            if (in_array($operator, array('NOT IN', 'IN'))) {
                                                if (is_string($value) && stripos(trim($value), 'SELECT') === 0) {
                                                    $clauses[] = $key . " " . $operator . " (" . $value . ")";
                                                } else if (is_array($value)) {
                                                    $phs = array();
                                                    foreach ($value as $i => $v) {
                                                        $p = $param . "_in_" . $i;
                                                        $phs[] = $p;
                                                        $params[$p] = $v;
                                                    }
                                                    $clauses[] = $key . " " . $operator . " (" . implode(",", $phs) . ")";
                                                } else {
                                                    $val = str_ireplace(array("(", ")", ""), "", $value);
                                                    $exp = explode(",", $val);
                                                    $phs = array();
                                                    foreach ($exp as $i => $v) {
                                                        $p = $param . "_in_" . $i;
                                                        $phs[] = $p;
                                                        $params[$p] = $v;
                                                    }
                                                    $clauses[] = $key . " " . $operator . " (" . implode(",", $phs) . ")";
                                                }
                                            } else if ($operator === 'BETWEEN' && is_array($value) && count($value) == 2) {
                                                // $params[$param . "_btn_1"] = $value[0];
                                                // $params[$param . "_btn_2"] = $value[1];
                                                // $clauses[] = $key . " BETWEEN " . $param . "_1 AND " . $param . "_2";

                                                $params[$param . "_btn_1"] = $value[0];
                                                $params[$param . "_btn_2"] = $value[1];
                                                $clauses[] = $key . " BETWEEN " . $param . "_btn_1 AND " . $param . "_btn_2";
                                            } else if ($operator === 'IS NULL' || $operator === 'IS NOT NULL') {
                                                $clauses[] = $key . " " . $operator;

                                                // if (is_null($value) || empty($value)) {
                                                //     $clauses[] = $key . " " . $operator;
                                                // }else{

                                                // }

                                            } else if ($operator === 'LIKE') {
                                                $clauses[] = $key . " LIKE " . $param;
                                                $params[$param] = $value;
                                            } else if ($operator === 'HAVING') {
                                                $clauses[] = $key . " HAVING (" . $param . ")";
                                                $params[$param] = $value;
                                            } else {
                                                $clauses[] = $key . " " . $operator . " " . $param;
                                                $params[$param] = $value;
                                            }
                                        }
                                    }
                                }
                            }
                            return implode(" AND ", $clauses);
                        };


                        $whereClause = '';
                        if (!empty($where)) {
                            if (!$isInsert) {
                                $whereClause = $buildWhere($where, $this->colPrefix);
                                $sql .= " WHERE " . $whereClause;
                            }
                        }

                        // ---------------------- Append SQL extensions ----------------------
                        if (!empty(trim($extension))) {
                            if (!$isInsert) {
                                $sql .= " " . trim($extension);
                            }
                        }
// throw new Exception($sql);


                        if (count($errors) >= 1) {
                            throw new Exception(implode(", ", $errors));
                        } else {
                            // return $this->formatMessage(new Exception($sql), 0, array('params' => $params));
                            // return $this->formatMessage(new Exception(json_encode($params)));

                            // ---------------------- Execute ----------------------


                            $total_pages = 0;
                            $total_results = 0;
                            $offset = 0;
                            $prevPage = null;
                            $nextPage = null;

                            if ($isSelect && $usePagenation) {
                                // Count query
                                $countSql = "SELECT COUNT(*) FROM " . $table;
                                if (!empty($whereClause)) {
                                    $countSql .= " WHERE " . $whereClause;
                                }

                                $stmt_count = $this->conn->prepare($countSql);
                                foreach ($params as $k => $v) {
                                    $stmt_count->bindValue($k, $v);
                                }



                                $stmt_count->execute();
                                $total_results = (int)$stmt_count->fetchColumn();
                                $total_pages = ceil($total_results / $resultsPerPage);

                                $offset = ($currentPage - 1) * $resultsPerPage;
                                $prevPage = ($currentPage > 1) ? $currentPage - 1 : null;
                                $nextPage = ($currentPage < $total_pages) ? $currentPage + 1 : null;

                                $sql .= " LIMIT :offset, :per_page";
                                $params[':offset'] = $offset;
                                $params[':per_page'] = $resultsPerPage;
                            }

                            // throw new Exception($sql);
                            // throw new Exception(json_encode(array('sql' => $sql, 'params' => $params)));



                            // $this->conn->beginTransaction();
                            $stmt = $this->conn->prepare($sql);
                            foreach ($params as $k => $v) {
                                if ($k === ':offset' || $k === ':per_page') {
                                    $stmt->bindValue($k, $v, PDO::PARAM_INT);
                                } else {
                                    $stmt->bindValue($k, $v);
                                }
                            }
                            $stmt->execute();
                            $lastId = $isInsert ? ((int)$this->conn->lastInsertId()) : 0;
                            // $this->conn->commit();


                            // if ($stmt->rowCount() > 0) {
                            //     echo "Row updated";
                            // } else {
                            //     echo "No row found OR data was identical";
                            // }

                            $sqlDetails = $showSql ? array('sql' => $sql, 'params' => $params) : array();
                            if (!$isSelect) {
                                $rowsAffected = $stmt->rowCount();
                                $stsCode = 200;
                                if ($isUpdate || $isDelete) {
                                    // $stsCode = $rowsAffected > 0 ? 200 : ($isUpdate ? 204 : 202);
                                    $stsCode = $rowsAffected > 0 ? 200 : ($isUpdate ? 203 : 202);
                                } else if ($isInsert) {
                                    $stsCode = $lastId > 0 ? 201 : 202;
                                }
                                return $this->formatMessage(
                                    new Exception($message, $stsCode),
                                    0,
                                    array_merge(
                                        array('resultsCount' => $rowsAffected, 'lastInserted' => $lastId),
                                        $sqlDetails
                                    )
                                );
                            } else {
                                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                $resParams = array_merge(($usePagenation ? array('pagination' => array(
                                    'more' => $nextPage !== null,
                                    'currentPage' => $currentPage,
                                    'totalRecords' => $total_results,
                                    'totalPerPage' => $resultsPerPage,
                                    'totalPages' => $total_pages,
                                    'nextPage' => $nextPage,
                                    'prevPage' => $prevPage,
                                )) : array()),  array(
                                    'resultsCount' => count($results),
                                    'results' => $results,
                                ));
                                return $this->formatMessage(new Exception($message, 200), 0, array_merge($resParams, $sqlDetails));
                            }
                        }
                    }
                }
            }
        } catch (PDOException $e) {
            if (!in_array($e->getCode(), array('HY093'))) {
                return $this->formatMessage(new Exception(str_ireplace($this->dbname . ".", "", $e->errorInfo[2]), 403), $e->errorInfo[1]);
            } else {
                $code = preg_replace('/[^0-9]/', '', $e->getCode());
                return $this->formatMessage(new Exception(str_ireplace($this->dbname . ".", "", $e->getMessage()), 403), ((int)$code));
            }
        } catch (Error $e) {
            return $this->formatMessage(new Exception($e->getMessage(), $e->getCode()));
        } catch (Exception $e) {
            return $this->formatMessage($e);
        }
    }


    /**
     * Universal PDO Query Builder
     * Fully self-contained: supports CRUD, aggregates, nested
     *
     * @param string $sql     SQL Query: SELECT, INSERT, UPDATE, DELETE
     * @param array $extras   array('extension' => 'ORDER BY', 'message' => 'Query OK', 'showsql' => false, 'applypagination' => true)
     * @return array          Standardized response array
     */
    public function RunExecute(string $sql = "", array $extras = array()): array
    {
        try {
            $allowed = array("INSERT", "DELETE", "UPDATE", "SELECT");

            if (!$this->isNullOrEmpty($this->error)) {
                return $this->error;
            } else if (is_null($this->conn)) {
                throw new Exception("Connection Variable is Null");
            } else {

                if ($this->isNullOrEmpty($sql)) {
                    throw new Exception("Missing SQL Query String");
                } else {
                    $explode = explode(" ", trim(preg_replace('!\s+!', ' ', $sql), " \n\r\t\v\0"));
                    if (count($explode) >= 2) {
                        $action = strtoupper(trim($explode[0], " \n\r\t\v\0"));
                        // $table = trim($explode[], " \n\r\t\v\0");

                        if (in_array($action, $allowed)) {

                            $params = $this->parseSqlToRunQueryParameters($sql);

                            $extras['extension'] = $params['extension'];
                            return $this->RunQuery($params['table'], $params['action'], $params['columns'], $params['where'],  $extras);
                        } else {
                            throw new Exception("Invalid SQL Action: {$action}");
                        }
                    } else {
                        throw new Exception("Invalid SQL Statement: {$sql}");
                    }
                }
            }
        } catch (PDOException $e) {
            if (!in_array($e->getCode(), array('HY093'))) {
                return $this->formatMessage(new Exception(str_ireplace($this->dbname . ".", "", $e->errorInfo[2]), 403), $e->errorInfo[1]);
            } else {
                $code = preg_replace('/[^0-9]/', '', $e->getCode());
                return $this->formatMessage(new Exception(str_ireplace($this->dbname . ".", "", $e->getMessage()), 403), ((int)$code));
            }
        } catch (Error $e) {
            return $this->formatMessage(new Exception($e->getMessage(), $e->getCode()));
        } catch (Exception $e) {
            return $this->formatMessage($e);
        }
    }

    /**
     * Universal PDO Multiple Query Builder
     *
     * @param array $queries     SQL Query: SELECT, INSERT, UPDATE, DELETE
     * @return array             Standardized response array
     */

    public function execute(array $queries): array
    {
        try {
            if (!$this->isNullOrEmpty($this->error)) {
                return $this->error;
            } else if (is_null($this->conn)) {
                throw new Exception("Connection Variable is Null");
            } else {


                // Notice the & before $value - this allows us to modify the item directly
                foreach ($queries as &$value) {
                    $value = trim($value); // Clean up trailing spaces first
                    if (!str_ends_with($value, ';')) {
                        $value = $value . ';';
                    }
                }
                unset($value);

                $this->conn->exec(implode('', $queries));

                throw new Exception("All commands executed successfully.", 200);
            }
        } catch (Error $e) {
            return $this->formatMessage(new Exception($e->getMessage(), $e->getCode()));
        } catch (Exception $e) {
            return $this->formatMessage($e);
        }
    }


    /**
     * Parses a raw SQL string into structured parameters compatible with RunQueryLog.
     *
     * @param string $sql The raw SQL statement to parse.
     * @return array{table: string, action: string, columns: array, where: array, extension: string}
     */


    /*
*     * 1. The input string you provided
        * $sqlStatement = "SELECT role_id as `idnum`, fullnames as `names`, phononumber FROM users WHERE id = '24' and status = 'T' ORDER BY id DESC";

        * * 2. Parse the string
        * $params = parseSqlToRunQueryParameters($sqlStatement);

        * * Print the output structural payload
        * print_r($params);

        * Array
        * (
        *     [table] => users
        *     [action] => SELECT
        *     [columns] => Array
        *         (
        *             [role_id] => idnum
        *             [fullnames] => names
        *             [phononumber] => 
        *         )

        *     [where] => Array
        *         (
        *             [id] => 24
        *             [status] => T
        *         )

        *     [extension] => ORDER BY id DESC
        * )

        * * Automatically maps array keys to your function arguments sequentially
        * $this->RunQuery(
        *     $params['table'],
        *     $params['action'],
        *     $params['columns'],
        *     $params['where'],
        *     $params['extension']
        * );

    **/


    private function parseSqlToRunQueryParameters(string $sql): array
    {
        // Standardize spacing and line breaks
        $sql = trim(preg_replace('/\s+/', ' ', $sql));

        // Detect SQL Action
        if (!preg_match('/^(SELECT|INSERT|UPDATE|DELETE)\b/i', $sql, $actionMatch)) {
            throw new InvalidArgumentException("Unsupported or invalid SQL statement action.");
        }
        $action = strtoupper($actionMatch[1]);

        // Initialize default components
        $table = '';
        $columns = [];
        $where = [];
        $extension = '';

        switch ($action) {
            case 'SELECT':
                // Regex to split SELECT elements, FROM table, WHERE clause, and trailing extensions
                $pattern = '/^SELECT\s+(.+?)\s+FROM\s+([a-zA-Z0-9_`]+)(?:\s+WHERE\s+(.+?))?(?:\s+(ORDER BY|GROUP BY|LIMIT)\s+(.+))?$/i';
                if (preg_match($pattern, $sql, $matches)) {
                    $rawColumns = $matches[1];
                    $table = str_replace(['`', '"'], '', $matches[2]);
                    $rawWhere = $matches[3] ?? '';

                    // Reconstruct extension if present
                    if (!empty($matches[4]) && !empty($matches[5])) {
                        $extension = trim($matches[4] . ' ' . $matches[5]);
                    }

                    // Parse Columns with "AS" mapping logic
                    $colParts = explode(',', $rawColumns);
                    foreach ($colParts as $part) {
                        $part = trim($part);
                        // Match pattern: column_name AS alias OR column_name alias
                        if (preg_match('/^(.+?)\s+AS\s+[`"\'\s]?([^`"\'\s]+)[`"\'\s]?$/i', $part, $colMatch)) {
                            $actualColumn = trim($colMatch[1], '`" ');
                            $alias = trim($colMatch[2], '`" ');
                            $columns[$actualColumn] = $alias;
                        } else {
                            // Plain column with no alias. Map column name to empty string per your example
                            $cleanCol = trim($part, '`" ');
                            $columns[$cleanCol] = '';
                        }
                    }

                    // Parse WHERE conditions
                    if (!empty($rawWhere)) {
                        $where = $this->parseWhereClauseString($rawWhere);
                    }
                }
                break;

            case 'INSERT':
                // Pattern for INSERT INTO table (cols) VALUES (vals)
                if (preg_match('/^INSERT\s+INTO\s+([a-zA-Z0-9_`]+)\s*\((.+?)\)\s*VALUES\s*\((.+?)\)/i', $sql, $matches)) {
                    $table = str_replace(['`', '"'], '', $matches[1]);
                    $fields = array_map(fn($f) => trim($f, '`" '), explode(',', $matches[2]));

                    // Extremely basic value extractor (handles comma-separated quoted/unquoted values)
                    str_getcsv($matches[3], ',', "'");
                    $vals = array_map(fn($v) => trim($v, "'\" "), explode(',', $matches[3]));

                    if (count($fields) === count($vals)) {
                        $columns = array_combine($fields, $vals);
                    }
                }
                break;

            case 'UPDATE':
                // Pattern for UPDATE table SET col=val WHERE cond
                if (preg_match('/^UPDATE\s+([a-zA-Z0-9_`]+)\s+SET\s+(.+?)(?:\s+WHERE\s+(.+?))?$/i', $sql, $matches)) {
                    $table = str_replace(['`', '"'], '', $matches[1]);
                    $rawSet = $matches[2];
                    $rawWhere = $matches[3] ?? '';

                    // Parse SET assignments into the column array
                    foreach (explode(',', $rawSet) as $assignment) {
                        if (str_contains($assignment, '=')) {
                            [$col, $val] = explode('=', $assignment, 2);
                            $columns[trim($col, '`" ')] = trim($val, "'\" ");
                        }
                    }

                    if (!empty($rawWhere)) {
                        $where = $this->parseWhereClauseString($rawWhere);
                    }
                }
                break;

            case 'DELETE':
                // Pattern for DELETE FROM table WHERE cond
                if (preg_match('/^DELETE\s+FROM\s+([a-zA-Z0-9_`]+)(?:\s+WHERE\s+(.+?))?$/i', $sql, $matches)) {
                    $table = str_replace(['`', '"'], '', $matches[1]);
                    $rawWhere = $matches[2] ?? '';
                    if (!empty($rawWhere)) {
                        $where = $this->parseWhereClauseString($rawWhere);
                    }
                }
                break;
        }

        return [
            'table'     => $table,
            'action'    => $action,
            'columns'   => $columns,
            'where'     => $where,
            'extension' => $extension
        ];
    }

    /**
     * Helper to parse a standard SQL WHERE clause into an associative array.
     */
    private  function parseWhereClauseString(string $whereString): array
    {
        $conditions = [];

        // Split by basic " AND " token case-insensitively
        $parts = preg_split('/\s+AND\s+/i', $whereString);
        if ($parts === false) {
            return [];
        }

        foreach ($parts as $part) {
            $part = trim($part);

            // Supports normal equality checks: field = 'value' or field = 24
            if (preg_match('/^([a-zA-Z0-9_`.]+)\s*=\s*(.+)$/', $part, $matches)) {
                $column = trim($matches[1], '`" ');
                $value = trim($matches[2], "'\" ");
                $conditions[$column] = $value;
            }
        }

        return $conditions;
    }
}
