<?php

require_once 'Predisql.php';

class Zset_Cache {
    public function __construct($redisql,
                                $parameters = null,
                                $clientOptions = null) {
        if (empty($redisql)) {
            throw new Predis_ClientException("redisql missing in constructor");
        }
        $this->redisql = $redisql;
    }

    public function zrange($z_name, $user_id, $start, $finish, $more_args) {
        $z_obj = $z_name . ":" . $user_id;
        $most_recent = $this->get_zset_most_recent_archive_date();
        $case = $this->get_zset_query_case($start, $finish, $most_recent);
        $res_first;
        $res_second;
        if ($case == 1 || $case == 3) {
            if (empty($more_args)) {
                $res_first = $this->redisql->zrange($z_obj, $start, $finish);
            } else {
                $res_first = $this->redisql->zrange($z_obj, $start, $finish,
                                                    $more_args);
            }
            if ($case == 1) { return $res_first; }
        }
        if ($case == 2 || $case == 3) {
            $res_second = $this->m_zrange($z_name, $user_id,
                                          $start, $finish, $more_args);
            if ($case == 2) { return $res_second; }
        }
        return array_merge($res_second, $res_first);
    }

    public function archive($z_name, $user_id, $two_days_ago, $yesterday) {
        $i_yesterday         = intval($yesterday);
        $i_two_days_ago      = intval($two_days_ago);
        $z_obj               = $z_name . ":" . $user_id;
        $mysql_archive_table = $z_name . "_archive";
        $temp_mysql_table    = "user_" . gmdate("M_d_Y", time());

        // create Redisql table from redis command:
        //  "zrangebyscore ZSET yesterday two_days_ago WITHSCORES"
        $this->redisql->createTableAs($temp_mysql_table, $z_obj,
                                      "zrangebyscore",
                                      "$two_days_ago $yesterday WITHSCORES");

        // dump Redisql table to Mysql table-dump (of same name)
        $mysql_commands = $this->redisql->dumpToMysql($temp_mysql_table,
                                                      $temp_mysql_table);

        // drop Redisql table (no longer needed)
        $this->redisql->dropTable($temp_mysql_table);
        
        $meta_command = " INSERT INTO zset_archive_meta " .
                        "(name, most_recent, least_recent) VALUES " .
                        "(\"$z_name\", FROM_UNIXTIME($i_yesterday)," .
                        "          FROM_UNIXTIME($i_two_days_ago)) " .
                        "ON DUPLICATE KEY " .
                        "  UPDATE most_recent = FROM_UNIXTIME($i_yesterday);";
        $this->redisql->m_query($meta_command);
        
        // data can be converted from one type to another (e.g. text to date)
        $archive_command = "INSERT INTO $mysql_archive_table " . 
                           "  (user_id, score, $z_name) " .
                           "SELECT $user_id, FROM_UNIXTIME(a.value), b.value " .
                           "FROM $temp_mysql_table a, $temp_mysql_table b " .
                           "WHERE a.pk = b.pk +1 and a.pk % 2 = 0;";
        $this->redisql->m_query($archive_command);
        
        $drop_temp_table_command = "drop table $temp_mysql_table;";
        $this->redisql->m_query($drop_temp_table_command);
    }

    // PRIVATE PRIVATE PRIVATE PRIVATE PRIVATE PRIVATE PRIVATE PRIVATE
    // PRIVATE PRIVATE PRIVATE PRIVATE PRIVATE PRIVATE PRIVATE PRIVATE

    private $redisql;
    private function get_zset_most_recent_archive_date() {
        // do NOT get this from the DATABASE - performance hit
        return intval(gettimeofday(true) - 86400); // yesterday
    }

    private function get_zset_query_case($start, $finish, $most_recent) {
        if ($start < $most_recent) {
            if ($finish != -1 && $finish < $most_recent) {
                return 1;
            } else {
                return 3;
            }
        } else {
            return 2;
        }
    }

    private function m_zrange($z_name, $user_id, $start, $finish, $more_args) {
        $q = "SELECT $z_name FROM $z_name" . "_archive ";
        $where = "";
        if ($start != 0) {
          $where = "date >= FROM_UNIXTIME(FLOOR($start))";
        }
        if ($finish != -1 ) {
            if (!empty($where)) {
                $where .= " AND ";
            }
            $where .= " date <= FROM_UNIXTIME(FLOOR($finish));";
        }
        if (!empty($where)) {
            $q .= " WHERE " . $where;
        }
        $q .= " ORDER BY score";
        $ret = Array();
        $result = $this->redisql->m_query($q);
        while ($row = mysql_fetch_assoc($result)) {
            $ret[] = $row[$z_name];
        }
        return $ret;
    }
}

?>
