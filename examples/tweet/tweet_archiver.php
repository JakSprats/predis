<?php

/*
  REQUIREMENTS: to run this script
         1.) the database "tweet_archive" must exist
         2.) the tables defined in "table_definitions.sql" need to exist
*/
require_once '../SharedConfigurations.php';
require_once 'Alsosql.php';
require_once '../ZsetCache.php';

require_once 'tweet_populate.php';
require_once 'tweet_helper.php';

$database_connection['name'] = "tweet_archive";
$alsosql = new Palsosql_Client($database_connection, $single_server);
//$alsosql->echo_command       = 1;
//$alsosql->echo_response      = 1;
//$alsosql->mysql_echo_command = 1;

$one_hour       = 3600;
$one_day        = 86400;
$now            = gettimeofday(true);
$yesterday      = $now - $one_day;
$two_days_ago   = $now - (2 * $one_day); 

$user_id             = 44;
$z_name              = "tweets";
$z_obj               = $z_name . ":" . $user_id;
$mysql_archive_table = $z_name . "_archive";
$temp_mysql_table    = "user_" . gmdate("M_d_Y", time());

$drop  = @$_GET['drop'];
if ($drop) {
    $alsosql->del($z_obj);
    try {$alsosql->dropTable($temp_mysql_table);} catch (Exception $e) { }
    $alsosql->m_query("delete from $mysql_archive_table;");
}

populate_tweets($alsosql, $z_name, $user_id, $yesterday, $two_days_ago, 0);

$zset_cache = new Zset_Cache($alsosql);
$zset_cache->archive($z_name, $user_id, $two_days_ago, $yesterday);

$display_archive_query = "select * from $mysql_archive_table order by score";
$result = $alsosql->m_query($display_archive_query);

echo "<br/>";
echo "MYSQL ARCHIVE TABLE<br/>";
while ($row = $alsosql->m_fetch_assoc($result)) {
    echo "&nbsp;&nbsp;" . 
         "DATE: <strong>"  . $row['score']   . "</strong> " .
         "TWEET: <strong>" . $row['tweets']  . "</strong><br/>";
}
$alsosql->m_free_result($result);

$alsosql->zremrangebyscore($z_obj, -1, $yesterday);

echo "<br/>";
echo "REDIS ZSET POST ARCHIVING<br/>";
print_ar($alsosql->zrange($z_obj, 0, -1));
echo "<br/>";

echo "<br/>";
echo "REDIS ZSET WITH MYSQL ARCHIVE<br/>";
print_ar($zset_cache->zrange($z_name, $user_id, 0, -1, ""));
echo "<br/>";

?>
