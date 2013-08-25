<?php

class MySqlUtil {

    private $connection = NULL;

    function __construct() {
        $this->connection = mysql_connect(DB_ADDR, DB_USER, DB_PSWD);
        if (!$this->connection) {
            die('Could not connect: ' . mysql_error($this->connection));
        }
    }

    public function secureVar($var, $type) {
        if (empty($var) || empty($type) || (($type != 'int') && ($type != 'str'))) {
            return NULL;
        }

        if ($type == 'int') {
            $var = intval($var);
        } else {
            $var = str_replace('\\', '\\\\', $var);
            $var = mysql_real_escape_string($var, $this->connection);
        }

        return $var;
    }

    public function saveTrends($keywords) {
        if (empty($keywords)) {
            return 'Keywords not specified';
        }

        mysql_select_db(DB_NAME, $this->connection);

        $date = date('Y-m-d H:i:s');
        foreach ($keywords as $kwd) {
		$kwd=addslashes($kwd);
            $query = "insert into `keywords` (`keyword`, `date`, `total`) values"
                . " ('$kwd', '$date', 1)"
                . " on duplicate key update `total` = `total` + 1";
            mysql_query($query, $this->connection);
            if (mysql_error($this->connection)) {
                return mysql_error($this->connection);
            }
        }

        return 0;
    }

    public function searchTrends($keyword, $date, $total, $sort, $col) {
        mysql_select_db(DB_NAME, $this->connection);

        $query = "select `keyword`, `date`, `total` from `keywords` where 1";

        if (!empty($keyword)) {
            $keyword = $this->secureVar($keyword, 'str');
            $query .= " and (`keyword` LIKE '$keyword')";
        }

        if (!empty($date)) {
            $date = $this->secureVar($date, 'str');
            $query .= " and (`date` LIKE '$date')";
        }

        if (!empty($total)) {
            $total = $this->secureVar($total, 'int');
            $query .= " and (`total` = '$total')";
        }

        if (!empty($sort) && !empty($col)) {
            $sort = $this->secureVar($sort, 'str');
            $col = $this->secureVar($col, 'str');
            $query .= ' order by `' . $col . '` ' . $sort;
        }
        
        $res = mysql_query($query, $this->connection);

        if (mysql_error($this->connection)) {
            die('Error occurred: ' . mysql_error($this->connection));
        }

        for ($i = 0; $i < mysql_num_rows($res); $i++) {
            $set[$i] = mysql_fetch_array($res);
        }
        return $set;
    }
}
?>
