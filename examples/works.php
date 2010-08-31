<?php

require_once 'SharedConfigurations.php';
require_once 'Alsosql.php';

$alsosql = new Palsosql_Client($database_connection, $single_server);
$alsosql->echo_command  = 1;
$alsosql->echo_response = 1;

require_once 'alsosql_example_functions.php';

works($alsosql);

//jstore_worker_location_table($alsosql);
//jstore_worker_location_hash($alsosql);
?>
