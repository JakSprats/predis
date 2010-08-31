<?php

require_once 'SharedConfigurations.php';
require_once 'Alsosql.php';

$database_connection['name'] = "backupdb";
$alsosql = new Palsosql_Client($database_connection, $single_server, NULL);
//$alsosql->echo_command       = 1;
//$alsosql->echo_response      = 1;
//$alsosql->mysql_echo_command = 1;

$keys = $alsosql->keys("*");
foreach($keys as $key){
    $type = $alsosql->type($key);
    if ($type != "index" && $type != "string") {
        $backup_table = "backup_$key";
        echo "BACKUP: $key TO ALSOSQL TABLE: $backup_table<br/>";
        try {$alsosql->dropTable($backup_table); } catch (Exception $e) { }
        $alsosql->createTableFromRedisObject($backup_table, $key);
        $mysql_backup_table = "redis_backup_" . $key .
                              "_" . gmdate("M_d_Y", time());
        echo "DUMP: $backup_table TO MYSQL TABLE: $mysql_backup_table<br/>";
        $alsosql->dumpToMysql($backup_table, $mysql_backup_table);
        echo "DROP ALSOSQL TABLE: $backup_table<br/>";
        $alsosql->dropTable($backup_table);
    }
}

?>
