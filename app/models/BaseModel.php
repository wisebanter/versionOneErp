<?php

class BaseModel extends AbstractDatabaseModel
{

    protected string $academicYearFormat = "CONCAT(name, '/', name + 1)";


    public function __construct()
    {
        parent::__construct(DATABASE_HOST, DATABASE_NAME, DATABASE_USER, DATABASE_PASS, DATABASE_PORT, TOKEN_SECRET, TOKEN_EXPIRY, TOKEN_CAN_EXPIRE, array(
            'tkloginid',
            'tkuseridno',
            'tkuserrole',
            'tkrolename',
            'tkusertype',
            'tkidentifier'
        ));
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getTablePrimaryKey(): string
    {
        return $this->primaryKey;
    }

    public function getTableColumns(): array
    {
        return $this->columns;
    }

    // public function runQuery(string $query, string $message = 'Query Okay'): array
    // {
    //     return $this->db->runQuery($query, $message);
    // }






    protected function randCode(int $limit = 6): int
    {
        return substr(number_format(time() * rand(), 0, '', ''), 0, $limit);
        // return str_pad(mt_rand(0, 999999), $limit, '0', STR_PAD_LEFT);
        // return random_int(100000, 999999);
    }


    protected function getCode(int $number, string $prefix = "CD", string $surfix = ""): string
    {
        $zeros = array(1 => "0000", 2 => "000", 3 => "00", 4 => "0");
        $strval = (string)$number;
        $tleng = strlen($strval);
        return $prefix . "" . (array_key_exists($tleng, $zeros) ? $zeros[$tleng] . $strval : $strval) . "" . $surfix;
    }

    protected function randomPasswordGenerator(int $limit = 6): string
    {

        $password = '';
        // $passwordSets = ['1234567890', '%^&@*#(){}', 'ABCDEFGHJKLMNPQRSTUVWXYZ', 'abcdefghjkmnpqrstuvwxyz'];
        $passwordSets = ['1234567890', '&@*#', 'ABCDEFGHJKLMNPQRSTUVWXYZ', 'abcdefghjkmnpqrstuvwxyz'];

        //Get random character from the array
        foreach ($passwordSets as $passwordSet) {
            $password .= $passwordSet[array_rand(str_split($passwordSet))];
        }

        // 6 is the length of password we want
        while (strlen($password) < $limit) {
            $randomSet = $passwordSets[array_rand($passwordSets)];
            $password .= $randomSet[array_rand(str_split($randomSet))];
        }
        return $password;
    }


    protected function str_reformat(string $str = "", string $mask = "*", int $start = 0, int $stops = 0): string
    {
        $result = $str;
        if (isset($result)) {
            // $result = substr_replace($str, $mask, $start, $stops);
            $rep = "";
            $breaks = substr($result, $start, $stops);
            for ($x = 1; $x <= strlen($breaks); $x++) {
                $rep .= $mask;
            }
            $result = substr_replace($str, $rep, $start, $stops);
            // $result = str_ireplace($breaks, $mask, $result);
        }

        return $result;
    }

    protected function convertNumberToWord(int $num): string
    {

        // $num = str_replace(array(',', ' '), '', trim($num));

        $words = array();
        $list1 = array(
            '',
            'one',
            'two',
            'three',
            'four',
            'five',
            'six',
            'seven',
            'eight',
            'nine',
            'ten',
            'eleven',
            'twelve',
            'thirteen',
            'fourteen',
            'fifteen',
            'sixteen',
            'seventeen',
            'eighteen',
            'nineteen'
        );
        $list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
        $list3 = array(
            '',
            'thousand',
            'million',
            'billion',
            'trillion',
            'quadrillion',
            'quintillion',
            'sextillion',
            'septillion',
            'octillion',
            'nonillion',
            'decillion',
            'undecillion',
            'duodecillion',
            'tredecillion',
            'quattuordecillion',
            'quindecillion',
            'sexdecillion',
            'septendecillion',
            'octodecillion',
            'novemdecillion',
            'vigintillion'
        );
        $num_length = strlen($num);
        $levels = (int) (($num_length + 2) / 3);
        $max_length = $levels * 3;
        $num = substr('00' . $num, -$max_length);
        $num_levels = str_split($num, 3);
        for ($i = 0; $i < count($num_levels); $i++) {
            $levels--;
            $hundreds = (int) ($num_levels[$i] / 100);
            $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ' ' : '');
            $tens = (int) ($num_levels[$i] % 100);
            $singles = '';
            if ($tens < 20) {
                $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '');
            } else {
                $tens = (int)($tens / 10);
                $tens = ' ' . $list2[$tens] . ' ';
                $singles = (int) ($num_levels[$i] % 10);
                $singles = ' ' . $list1[$singles] . ' ';
            }
            $words[] = $hundreds . $tens . $singles . (($levels && (int) ($num_levels[$i])) ? ' ' . $list3[$levels] . ' ' : '');
        } //end for loop
        $commas = count($words);
        if ($commas > 1) {
            $commas = $commas - 1;
        }
        return trim(implode(' ', $words));
    }

    protected function timeDifference(
        string $date1,
        string $format1,
        string $date2,
        string $format2,
        bool $absolute = false,
        string $outputFormat = 'default',
        bool $castToInt = false
    ): string|int {

        // 1. Parse dates using their explicit formats
        $dt1 = DateTime::createFromFormat($format1, $date1);
        $dt2 = DateTime::createFromFormat($format2, $date2);

        if (!$dt1 || !$dt2) {
            throw new InvalidArgumentException("Invalid date or format provided.");
        }

        // 2. Compute the interval
        // $absolute = false means $dt1 -> $dt2 (can yield negative periods if dt2 is earlier)
        $interval = $dt1->diff($dt2, $absolute);
        $isNegative = !$absolute && ($dt1 > $dt2);

        // 3. Handle shorthand unit calculations (seconds, hours, etc.)
        switch (strtolower($outputFormat)) {
            case 'seconds':
                $res = $dt2->getTimestamp() - $dt1->getTimestamp();
                $val = $absolute ? abs($res) : $res;
                return $castToInt ? $val : (string)$val;

            case 'minutes':
                $res = (int)(($dt2->getTimestamp() - $dt1->getTimestamp()) / 60);
                $val = $absolute ? abs($res) : $res;
                return $castToInt ? $val : (string)$val;

            case 'hours':
                $res = (int)(($dt2->getTimestamp() - $dt1->getTimestamp()) / 3600);
                $val = $absolute ? abs($res) : $res;
                return $castToInt ? $val : (string)$val;

            case 'default':
                // Custom descriptive format matching your first test case
                $parts = [];
                if ($interval->y) $parts[] = "{$interval->y} years";
                if ($interval->m) $parts[] = "{$interval->m} months";
                if ($interval->d) $parts[] = "{$interval->d} days";
                if ($interval->h) $parts[] = "{$interval->h} hours";
                if ($interval->i) $parts[] = "{$interval->i} minutes";
                if ($interval->s) $parts[] = "{$interval->s} seconds";

                $prefix = $isNegative ? '-' : '';
                return $prefix . (implode(' ', $parts) ?: '0 seconds');
        }

        // 4. Handle standard DateInterval mapping (%a, %h, %I, etc.)
        // If it's a negative interval, manually prepend the negative sign if not present
        $formatted = $interval->format($outputFormat);
        if ($isNegative && strpos($formatted, '-') !== 0) {
            $formatted = '-' . $formatted;
        }

        // If integer casting is requested on a formatted string, extract digits
        return $castToInt ? (int)$formatted : $formatted;
    }
}