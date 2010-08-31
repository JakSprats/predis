<?php
require_once 'Predis.php';

$single_server = array(
    'host'     => '127.0.0.1', 
    'port'     => 6379, 
    'database' => 0
);

$multiple_servers = array(
    array(
       'host'     => '127.0.0.1',
       'port'     => 6379,
       'database' => 15,
       'alias'    => 'first',
    ),
    array(
       'host'     => '127.0.0.1',
       'port'     => 6380,
       'database' => 15,
       'alias'    => 'second',
    ),
);

$database_connection = array(
    'host'   => '127.0.0.1', 
    //'port'   => 3306, 
    'user'     => 'root',
    'password' => '',
    'name'     => 'joomla',
);

?>
