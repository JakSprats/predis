<?php

require_once 'Predis.php';

class Predisql_Client extends Predis\Client {

    public function __construct($db_conn       = null,
                                $parameters    = null,
                                $clientOptions = null) {
        parent::__construct($parameters, $clientOptions);
        $this->mysql_db           = $db_conn;
        $this->echo_command       = 0;
        $this->echo_response      = 0;
        $this->mysql_echo_command = 0;
    }

    public function __destruct() {
        $this->m_close();
    }

    public $echo_command;  /* echo the Redisql commands to the server */
    public $echo_response; /* echo the Redisql responses from the server */

    // API API API API API API API API API API API API API API API API API
    // API API API API API API API API API API API API API API API API API
    public function createTable($table_name, $column_definitions) {
        if (!isset($table_name, $column_definitions)) {
            throw new Predis\ClientException("createTable(\"tablename\"," .
                                             "\"id INT, name TEXT, etc....\")");
        }
        $comma = strchr($column_definitions, ',');
        if (!$comma) {
            throw new Predis\ClientException(
                                   "SQL \"CREATE TABLE\" Column definitions " .
                                   "syntax error ($column_definitions)");
        }
        $cdefs_w_paren = "($column_definitions)";
        return $this->_cr8_tbl($table_name, $cdefs_w_paren);
    }

    public function createTableFromRedisObject($table_name, $redis_obj) {
        if (!isset($table_name, $redis_obj)) {
            throw new Predis\ClientException("createTableAsRedisObject(" .
                                             "\"tablename\",\"redis_obj\")");
        }
        $cdefs  = "AS DUMP $redis_obj";
        return $this->_cr8_tbl($table_name, $cdefs);
    }

    public function createTableAsRedisCommand($table_name,
                                              $redis_command) {
        if (!isset($table_name, $redis_command)) {
            throw new Predis\ClientException("createTableAsRedisCommand(" .
                                             "\"tablename\",\"redis_cmd\")");
        }
        $cdefs = "AS " .  "$redis_command";
        return $this->_cr8_tbl($table_name, $cdefs);
    }

    public function dropTable($table_name) {
        if (!isset($table_name)) {
            throw new Predis\ClientException("dropTable(\"tablename\")");
        }
        $redisql_cmd  = "DROP TABLE $table_name\r\n";
        return $this->localRawCommand($redisql_cmd);
    }

    public function createIndex($index_name, $table_name, $column) {
        if (!isset($index_name, $table_name, $column)) {
            throw new Predis\ClientException("createIndex(\"indexname\"," .
                                             "\"tablename\",\"columname\")");
        }
        $redisql_cmd  = "CREATE INDEX $index_name ON $table_name ($column)\r\n";
        return $this->localRawCommand($redisql_cmd);
    }

    public function dropIndex($table_name) {
        if (!isset($table_name)) {
            throw new Predis\ClientException("dropIndex(\"tablename\")");
        }
        $redisql_cmd  = "DROP INDEX $table_name\r\n";
        return $this->localRawCommand($redisql_cmd);
    }

    public function insert($table_name, $values_list) {
        $this->_insert($table_name, $values_list, 0);
    }

    public function insertAndReturnSize($table_name, $values_list) {
        $this->_insert($table_name, $values_list, 1);
    }


    public function delete($table_name, $where_clause) {
        if (!isset($table_name, $where_clause)) {
            throw new Predis\ClientException("delete(\"indexname\"," .
                                             "\"id = 27\")");
        }
        $cmd  = "*5\r\n";
        $cmd .= "$6\r\nDELETE\r\n";
        $cmd .= "$4\r\nFROM\r\n";
        $cmd .= "$" . strlen($table_name)   . "\r\n" . $table_name . "\r\n";
        $cmd .= "$5\r\nWHERE\r\n";
        $cmd .= "$" . strlen($where_clause) . "\r\n" . $where_clause . "\r\n";
        return $this->localRawCommand($cmd);

        $redisql_cmd = _add_wc("DELETE FROM $table_name", $where_clause);
        return $this->localRawCommand($redisql_cmd);
    }

    public function update($table_name, $update_list, $where_clause) {
        if (!isset($table_name, $update_list, $where_clause)) {
            throw new Predis\ClientException("update(\"tablename\"," .
                                             "\"col1=val1,col2=val2,etc...\"," .
                                             "\"id = 27\")");
        }
        $cmd  = "*6\r\n";
        $cmd .= "$6\r\nUPDATE\r\n";
        $cmd .= "$" . strlen($table_name)   . "\r\n" . $table_name . "\r\n";
        $cmd .= "$3\r\nSET\r\n";
        $cmd .= "$" . strlen($update_list)  . "\r\n" . $update_list . "\r\n";
        $cmd .= "$5\r\nWHERE\r\n";
        $cmd .= "$" . strlen($where_clause) . "\r\n" . $where_clause . "\r\n";
        return $this->localRawCommand($cmd);
    }

    public function scanSelect($column_list, $table_name, $where_clause) {
        if (!isset($column_list, $table_name, $where_clause)) {
            throw new Predis\ClientException("scanSelect(\"col1,col2,etc...\",".
                                             "\"tablename\",\"name = bill\")");
        }
        $cmd  = "*6";
        $cmd .= "$10\r\nSCANSELECT\r\n";
        $cmd .= "$" . strlen($column_list)  . "\r\n" . $column_list . "\r\n";
        $cmd .= "$4\r\nFROM\r\n";
        $cmd .= "$" . strlen($table_name)   . "\r\n" . $table_name . "\r\n";
        $cmd .= "$5\r\nWHERE\r\n";
        $cmd .= "$" . strlen($where_clause) . "\r\n" . $where_clause . "\r\n";
        return $this->localRawCommand($cmd);
    }

    public function select($column_list, $table_name, $where_clause) {
        return $this->_select($column_list, $table_name, $where_clause);
    }

    public function selectStore($column_list,
                                $table_name,
                                $where_clause,
                                $redis_command,
                                $redis_name) {
        $where_clause .= " STORE " . $redis_command . " " . $redis_name;
        return $this->_select($column_list, $table_name, $where_clause);
    }

    public function desc($table_name) {
        if (!isset($table_name)) {
            throw new Predis\ClientException("desc(\"tablename\")");
        }
        $redisql_cmd  = "DESC $table_name\r\n";
        return $this->localRawCommand($redisql_cmd);
    }

    public function dump($table_name) {
        if (!isset($table_name)) {
            throw new Predis\ClientException("dump(\"tablename\",0)");
        }
        $redisql_cmd = "DUMP $table_name\r\n";
        return $this->localRawCommand($redisql_cmd);
    }

    public function dumpToMysql($table_name, $mysql_table_name) {
        if (!isset($table_name, $mysql_table_name)) {
            throw new Predis\ClientException("dump(\"tablename\",0)");
        }
        $redisql_cmd  = "DUMP $table_name TO MYSQL $mysql_table_name\r\n";
        $mysql_commands = $this->localRawCommand($redisql_cmd);

        // put the Redisql results DIRECTLY into mysql
        foreach($mysql_commands as $command){
            $this->m_query($command);
        }
    }

    public function normalize($main_wildcard, $secondary_wildcard_list) {
        if (!isset($main_wildcard)) {
            throw new Predis\ClientException("normalize(\"tablename\"," .
                                             "\"user\",\"address,payment\")");
        }
        $redisql_cmd  = "NORM $main_wildcard $secondary_wildcard_list\r\n";
        return $this->localRawCommand($redisql_cmd);
    }

    public function denormalize($table_name, $main_wildcard) {
        if (!isset($table_name, $main_wildcard)) {
            throw new Predis\ClientException("denormalize(\"tablename\"," .
                                             "\"user:*:payment\")");
        }
        $redisql_cmd  = "DENORM $table_name $main_wildcard\r\n";
        return $this->localRawCommand($redisql_cmd);
    }

    public function lua($command) {
        if (!isset($command)) {
            throw new Predis\ClientException("lua(\"command\")");
        }
        $redisql_cmd  = "LUA $command\r\n";
        return $this->localRawCommand($redisql_cmd);
    }

    public function importFromMysql($table) {
        $col_select = "";
        $col_def    = "";
        $result = $this->m_query("SHOW COLUMNS FROM ".$table."");
        $tbl_has_date_col = 0;
        $i = 0;
        if ($this->m_num_rows($result) <= 0) return -1;
        while ($row = $this->m_fetch_assoc($result)) {
            if ($i) {
                $col_def    .= ", ";
                $col_select .= ", ";
            }
            // convert Mysql DATETIME & TIMESTAMP to Redisql INT
            if (!strcasecmp($row['Type'], "datetime") ||
                !strcasecmp($row['Type'], "timestamp")) {
                $tbl_has_date_col  = 1;
                $col_select       .= "unix_timestamp(" . $row['Field'] . ")";
                $col_def          .= $row['Field']." INT";
            } else {
                $col_select .= $row['Field'];
                $col_def .= $row['Field']." " . $row['Type'];
            }
            $i++;
        }
        $this->m_free_result($result);
        // TODO check "$col_select" for SQL KEYWORDS
        //       if so converting mysql dates to redisql ints results
        //       in errors during the "SELECT col,,,,," step
        // NOTE: technically Mysql should disallow keywords as column-names
        $this->createTable($table, $col_def);
        
        if ($tbl_has_date_col == 0) {
            /* this avoids errors when columns have SQL KEYWORD names */
            $col_select = "*";
        }
        try {
            $values = $this->m_query("SELECT " . $col_select .
                                     " FROM " . $table . "");
            while ($row = $this->m_fetch_row($values)) {
                $values_list = "";
                for ($j = 0; $j < $i; $j++) {
                    if ($j) {
                        $values_list .= ",";
                    }
                    /* need to escape strings for Redisql INSERTs */
                    $values_list .= str_replace(",", "\\,", $row[$j]);
                }
                try { /* individual inserts can fail for many reasons */
                    $this->insert($table, $values_list);
                } catch (Exception $e) { } /* current practice: ignore errors*/
            }
        } catch (Exception $e) {}

    }

    // MYSQL_INTEGRATION MYSQL_INTEGRATION MYSQL_INTEGRATION MYSQL_INTEGRATION
    // MYSQL_INTEGRATION MYSQL_INTEGRATION MYSQL_INTEGRATION MYSQL_INTEGRATION
    public $mysql_echo_command; /* echo mysql commands through this API */

    public function m_query($query) {
        if (!isset($this->mysql_link)) {
            $this->m_conn();
        }
        if ($this->mysql_echo_command == 1) echo "DEBUG: mysql: $query<br/>";
        $res = mysql_query($query);
        //if (!$res) { die(mysql_error()); }
        return $res;
    }

    public function m_fetch_assoc($result) {
        return mysql_fetch_assoc($result);
    }
    public function m_fetch_row($values) {
        return mysql_fetch_row($values);
    }
    public function m_num_rows($result) {
        return mysql_num_rows($result);
    }
    public function m_free_result($result) {
        return mysql_free_result($result);
    }

    // PRIVATE PRIVATE PRIVATE PRIVATE PRIVATE PRIVATE PRIVATE PRIVATE
    // PRIVATE PRIVATE PRIVATE PRIVATE PRIVATE PRIVATE PRIVATE PRIVATE
    private function _cr8_tbl($table_name, $cdefs) {
        $cmd    = "*4\r\n";
        $cmd   .= "$6\r\nCREATE\r\n";
        $cmd   .= "$5\r\nTABLE\r\n";
        $cmd   .= "$" . strlen($table_name) . "\r\n" . $table_name . "\r\n";
        $cmd   .= "$" . strlen($cdefs)      . "\r\n" . $cdefs . "\r\n";
        return $this->localRawCommand($cmd);
    }

    private function _insert($table_name, $values_list, $return_size) {
        if (!isset($table_name, $values_list)) {
            throw new Predis\ClientException("insertRow(\"indexname\"," .
                                             "\"1,bill,27,etc...\")");
        }
        $num_args = 5;
        if ($return_size == 1) {
            $num_args = 7;
        }
        $cmd  = "*" . $num_args . "\r\n";
        $cmd .= "$6\r\nINSERT\r\n";
        $cmd .= "$4\r\nINTO\r\n";
        $cmd .= "$" . strlen($table_name)  . "\r\n" . $table_name . "\r\n";
        $cmd .= "$6\r\nVALUES\r\n";
        $cmd .= "$" . strlen($values_list) . "\r\n" . $values_list . "\r\n";
        if ($return_size == 1) {
            $cmd .= "$6\r\nRETURN\r\n";
            $cmd .= "$4\r\nSIZE\r\n";
        }
        return $this->localRawCommand($cmd);
    }

    private function _select($column_list, $table_name, $where_clause) {
        if (!isset($column_list, $table_name, $where_clause)) {
            throw new Predis\ClientException("select(\"col1,col2,etc...\"," .
                                             "\"tablename\"," .
                                             "\"id = 27\")");
        }
        $cmd    = "*6\r\n";
        $cmd   .= "$6\r\nSELECT\r\n";
        $cmd   .= "$" . strlen($column_list)  . "\r\n" . $column_list . "\r\n";
        $cmd   .= "$4\r\nFROM\r\n";
        $cmd   .= "$" . strlen($table_name)   . "\r\n" . $table_name . "\r\n";
        $cmd   .= "$5\r\nWHERE\r\n";
        $cmd   .= "$" . strlen($where_clause) . "\r\n" . $where_clause . "\r\n";
        return $this->localRawCommand($cmd);
    }

    private function localRawCommand($redisql_cmd) {
        if ($this->echo_command) {
            echo "ALSOSQL COMMAND: " . $redisql_cmd . "</br>";
        }
        if ($this->echo_response) {
            $reply = $this->rawCommand($redisql_cmd);
            print_r($reply);
            echo "<br>";
            return $reply;
        } else {
            return $this->rawCommand($redisql_cmd);
        }
    }

    private $mysql_db;
    private $mysql_link;

    private function m_conn() {
        $this->mysql_link = mysql_connect($this->mysql_db['host'], 
                                          $this->mysql_db['user'],
                                          $this->mysql_db['password']) or
            die(mysql_error());
        mysql_select_db($this->mysql_db['name']) or die(mysql_error());
        return $this->mysql_link;
    }

    private function m_close() {
        if (isset($this->mysql_link)) {
            mysql_close($this->mysql_link);
        }
    }
}

/*
MIT License

Copyright (c) 2010 Russell Sullivan <jaksprats AT gmail DOT com>
ALL RIGHTS RESERVED 

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
?>
