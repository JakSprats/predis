<?
require_once 'SharedConfigurations.php';
require_once 'Alsosql.php';

/*
Requirements: 
  1.) Create Mysql database "test"
  2.) issue the following SQL commands
    A.) CREATE TABLE employee_data ( emp_id int unsigned not null auto_increment primary key, f_name varchar(20), l_name varchar(20), title varchar(30), age int, yos int, salary int, perks int, email varchar(60) );
    B.) INSERT INTO employee_data (f_name, l_name, title, age, yos, salary, perks, email) values ("Beth", "Smith", "CTO", 39, 1, 90000,  10000, "beth@bignet.com");
    C.) INSERT INTO employee_data (f_name, l_name, title, age, yos, salary, perks, email) values ("Bill", "Jones", "Manager", 29, 3, 100000,  20000, "jim@bignet.com");

Then ....
  call this script w/ the following URL variables
    "?table=employee_data"
  To call this repeatedly (and each time dropping the Alsosql table, use:
    "?table=employee_data&drop=1"
*/

$database_connection['name'] = "test"; // Mysql DatabaseName
$alsosql = new Palsosql_Client($database_connection, $single_server, NULL);
$alsosql->echo_command       = 1;
$alsosql->echo_response      = 1;
$alsosql->mysql_echo_command = 1;

$table  = @$_GET['table'];
if (!$table) {
    echo "this script makes the mysql table designated by the URL variable \"table=\" schemaless<br/>";
    return;
}

$drop  = @$_GET['drop'];

if ($drop) {
    try {$alsosql->dropTable($table); } catch (Exception $e) { }
}

try { $alsosql->importFromMysql($table); } catch (Exception $e) { }

$alsosql->dump($table);

$wildcard = $table . ':*';
$alsosql->denormalize($table, $wildcard);

echo "HGETALL $table:1<br/>";
print_r($alsosql->hgetall("$table:1"));
echo "<br/>";

?>
