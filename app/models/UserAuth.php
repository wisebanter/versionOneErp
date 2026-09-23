<?php

class UserAuth extends BaseModel
{
    protected $tableName = 'users_auth';
    protected $primaryKey = '_id';
    protected $columns = array();

    public function __construct()
    {
        $this->columns = array(
            array('columnName' => '_id'),
            array('columnName' => 'userrole'),
            array('columnName' => 'utype', 'validations' => array('in_array' => array('MAIN', 'ASST'))),
            array('columnName' => 'useridno'),
            array('columnName' => 'username'),
            array('columnName' => 'password'),
            array('columnName' => 'isactive', 'validations' => array('in_array' => array('T', 'F'))),
            array('columnName' => 'passchange', 'validations' => array('in_array' => array('T', 'F'))),
            array('columnName' => 'tempcode'),
        );

        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
    }




    public function signin(string $username, string $password): array
    {
        try {

            $fullnames = "CASE WHEN userrole NOT IN(6) THEN (SELECT fnames FROM users_staff WHERE _uid = useridno) ELSE (SELECT fnames FROM users_student WHERE _uid = useridno) END";
            $columns = array(
                '_id' => 'loginid',
                'userrole' => '',
                'utype' => 'usertype',
                'useridno' => '',
                'isactive' => '',
                'passchange' => '',
                'tempcode' => '',
                'username' => '',
                'password' => '',
                $fullnames => "fullname",
                '(SELECT _rname FROM users_role WHERE _rid = ' . $this->tableName . '.userrole)' => "rolename",
            );
            $where = array('username' => $username, 'password' => md5(SHA1($password)));

            // throw new Exception(json_encode($where));

            $check = $this->RunQuery($this->tableName, 'SELECT', $columns, $where, array('usepagenation' => false));
            if (!$check['status']) {
                return $check;
            } else if ($check['resultsCount'] != 1) {
                throw new Exception($this->EmptyOrMoreError("Authentication Detail", $check['resultsCount']), 401);
            } else {
                // return $check;
                $row = new Arguments($check['results'][0]);
                $loginid = (int) $row->getValue('loginid');
                $useridno = (int) $row->getValue('useridno');
                $userrole = (int) $row->getValue('userrole');
                $rolename = $row->getValue('rolename');
                $usertype = $row->getValue('usertype');
                $status = $row->getValue('isactive', 'F');
                $fullname = $row->getValue('fullname', '');
                $tempcode = $row->getValue('tempcode', '');




                if (!$this->isNullOrEmpty($tempcode)) {
                    return $this->formatMessage(new Exception("Dear, {$fullname}, please provide the OTP code that was sent to your phone number", 403), -10, array('lastId' => $loginid));
                } else if ($status != "T") {
                    // return $this->formatMessage(new Exception("Sorry!!, this account is currently de-activated, please contact the system admin on " . $this->info->toString(), 401), -13, array('lastId' => $loginid));
                    return $this->formatMessage(new Exception("Sorry!!, this account is currently de-activated, please contact the system administrator ", 401), -13, array('lastId' => $loginid));
                } else {
                    $token = $this->GenerateToken(array(
                        'tkloginid' => $loginid,
                        'tkuseridno' => $useridno,
                        'tkuserrole' => $userrole,
                        'tkrolename' => $rolename,
                        'tkusertype' => $usertype,
                        'tkidentifier' => $username,
                    ));

                    if (!$token['status']) {
                        return $token;
                    } else {
                        return $this->formatMessage(new Exception("Authorized Successfully", 200), 0, array(
                            'token' => $token['token']
                        ));
                    }
                }
            }
        } catch (Exception $e) {
            return $this->formatMessage($e);
        }
    }
}
