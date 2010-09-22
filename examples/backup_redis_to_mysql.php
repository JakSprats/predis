<?php

require_once 'SharedConfigurations.php';
require_once 'Predisql.php';

$database_connection['name'] = "backupdb";
$redisql = new Predisql_Client($database_connection, $single_server, NULL);
//$redisql->echo_command       = 1;
//$redisql->echo_response      = 1;
//$redisql->mysql_echo_command = 1;

$keys = $redisql->keys("*");
foreach($keys as $key){
    $type = $redisql->type($key);
    if ($type != "index" && $type != "string") {
        $backup_table = "backup_$key";
        echo "BACKUP: $key TO REDISQL TABLE: $backup_table<br/>";
        try {$redisql->dropTable($backup_table); } catch (Exception $e) { }
        $redisql->createTableFromRedisObject($backup_table, $key);
        $mysql_backup_table = "redis_backup_" . $key .
                              "_" . gmdate("M_d_Y", time());
        echo "DUMP: $backup_table TO MYSQL TABLE: $mysql_backup_table<br/>";
        $redisql->dumpToMysql($backup_table, $mysql_backup_table);
        echo "DROP REDISQL TABLE: $backup_table<br/>";
        $redisql->dropTable($backup_table);
    }
}

?>
