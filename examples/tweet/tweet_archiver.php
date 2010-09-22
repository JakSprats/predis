<?php

/*
  REQUIREMENTS: to run this script
         1.) the database "tweet_archive" must exist
         2.) the tables defined in "table_definitions.sql" need to exist
*/
require_once '../SharedConfigurations.php';
require_once 'Predisql.php';
require_once '../ZsetCache.php';

require_once 'tweet_populate.php';
require_once 'tweet_helper.php';

$database_connection['name'] = "tweet_archive";
$redisql = new Predisql_Client($database_connection, $single_server);
//$redisql->echo_command       = 1;
//$redisql->echo_response      = 1;
//$redisql->mysql_echo_command = 1;

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
    $redisql->del($z_obj);
    try {$redisql->dropTable($temp_mysql_table);} catch (Exception $e) { }
    $redisql->m_query("delete from $mysql_archive_table;");
}

populate_tweets($redisql, $z_name, $user_id, $yesterday, $two_days_ago, 0);

$zset_cache = new Zset_Cache($redisql);
$zset_cache->archive($z_name, $user_id, $two_days_ago, $yesterday);

$display_archive_query = "select * from $mysql_archive_table order by score";
$result = $redisql->m_query($display_archive_query);

echo "<br/>";
echo "MYSQL ARCHIVE TABLE<br/>";
while ($row = $redisql->m_fetch_assoc($result)) {
    echo "&nbsp;&nbsp;" . 
         "DATE: <strong>"  . $row['score']   . "</strong> " .
         "TWEET: <strong>" . $row['tweets']  . "</strong><br/>";
}
$redisql->m_free_result($result);

$redisql->zremrangebyscore($z_obj, -1, $yesterday);

echo "<br/>";
echo "REDIS ZSET POST ARCHIVING<br/>";
print_ar($redisql->zrange($z_obj, 0, -1));
echo "<br/>";

echo "<br/>";
echo "REDIS ZSET WITH MYSQL ARCHIVE<br/>";
print_ar($zset_cache->zrange($z_name, $user_id, 0, -1, ""));
echo "<br/>";

?>
