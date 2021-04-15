<?php
if (!defined('IN_DS')) {
    die('Hacking attempt');
}

class Mysql
{
    var $host = '';
    var $database = '';
    var $user = '';
    var $password = '';
    var $port = 3306;
    var $record = array();
    var $isPConnect = false;
    var $linkID = null;
    var $queryID = null;

    function __construct($database, $host, $user, $password, $port)
    {
        $this->host = $host;
        $this->database = $database;
        $this->user = $user;
        $this->password = $password;
        $this->port = $port;
        $this->connect();
    }

    function connect()
    {

        if (is_null($this->linkID) || !is_resource($this->linkID) || strcasecmp(get_resource_type($this->linkID), 'mysql link') <> 0) {
            if (!$this->isPConnect) {
                $this->linkID = @mysqli_connect($this->host, $this->user, $this->password, $this->database, $this->port);
            } else {
                $this->linkID = @mysqli_connect($this->host, $this->user, $this->password, $this->database, $this->port);
            }
        }

        if (!$this->linkID) {
            die("连接错误: " . mysqli_connect_error());
        }

        if (defined('MYSQL_ENCODING')) {
            mysqli_set_charset($this->linkID, MYSQL_ENCODING);
        }
    }

    function query($sql)
    {
        $result = array();

        if ($sql == '')
            die('query string was empty');
        if ($this->queryID)
            $this->queryID = NULL;
        if (!@mysqli_ping($this->linkID)) {
            $this->close();
            $this->connect();
        }
        if (!mysqli_select_db($this->linkID, $this->database)) {
            die('can not use the database ' . $this->database . ', ' . mysqli_error($this->linkID) . ', ' . mysqli_error($this->linkID));
        }

        $result['sql'] = $sql;
        $result['start'] = microtime(true);
        $this->queryID = @mysqli_query($this->linkID, $sql);
        $result['end'] = microtime(true);
        $result['time'] = number_format($result["end"] - $result["start"], 3, '.', ' ');
        $result['parent'] = __FUNCTION__;

        if (!$this->queryID) {
            if (DEBUG_MODE) {
                debug_print_backtrace();
                //sendMail("Auto Catch Problem ".date("Y-m-d H:i:s"),$sql);
                die("query failed: {$sql}, " . mysqli_error($this->linkID) . ', ' . mysqli_error($this->linkID));
            }
        }

        return $this->queryID;
    }

    function getRow($queryID = '', $fetchType = MYSQLI_ASSOC)
    {
        $result = array();
        if (!$queryID)
            $queryID = $this->queryID;
        if (!is_resource($queryID)) {
            die('invalid query id, can not get the result from DB result');
        }

        $this->record = @mysqli_fetch_array($queryID, $fetchType);
        if (is_array($this->record))
            $result = $this->record;

        return $result;
    }

    function getNumRows($qryId = '')
    {
        if (is_resource($qryId))
            return @mysqli_num_rows($qryId);

        return @mysqli_num_rows($this->queryID);
    }

    function getAffectedRows()
    {
        return @mysqli_affected_rows($this->linkID);
    }

    function getLastInsertId()
    {
        return @mysqli_insert_id($this->linkID);
    }

    function freeResult($queryID = '')
    {
        if (!is_resource($queryID))
            @mysqli_free_result($this->queryID);
        return @mysqli_free_result($queryID);
    }

    function close()
    {
        if ($this->linkID) {
            @mysqli_close($this->linkID);
            $this->linkID = null;
        }
    }

    function getFirstRow(&$sql)
    {
        $rows = $this->getRows($sql);
        if (is_array($rows) && sizeof($rows) > 0)
            return current($rows);

        return array();
    }

    function getFirstRowColumn(&$sql, $keyname = '')
    {
        $first_row = $this->getFirstRow($sql);
        if (sizeof($first_row) == 0)
            return '';

        if ($keyname == '')
            return current($first_row);

        if (isset($first_row[$keyname]))
            return $first_row[$keyname];

        return '';
    }

    function getRows(&$sql, $key = '')
    {
        $arr_return = array();
        $qryId = $this->query($sql);
        if (!$qryId)
            return $arr_return;

        $i = 0;
        while ($row = mysqli_fetch_array($qryId, MYSQLI_ASSOC)) {
            if (!empty($key) && isset($row[$key])) {
                $key_value = $row[$key];
            } else {
                $key_value = $i++;
            }

            $arr_return[$key_value] = $row;
        }

        $this->freeResult($qryId);

        return $arr_return;
    }

}