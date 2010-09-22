<?php

require_once 'SharedConfigurations.php';
require_once 'Predisql.php';

$redisql = new Predisql_Client($database_connection, $single_server);
$redisql->echo_command  = 1;
$redisql->echo_response = 1;

require_once 'redisql_example_functions.php';

works($redisql);

//jstore_worker_location_table($redisql);
//jstore_worker_location_hash($redisql);
?>
