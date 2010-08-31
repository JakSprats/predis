<?php

require_once 'SharedConfigurations.php';
require_once 'Alsosql.php';

$alsosql = new Palsosql_Client($database_connection, $single_server);
//$alsosql->echo_command  = 1;
//$alsosql->echo_response = 1;

$drop  = @$_GET['drop'];
if ($drop) {
    try {$alsosql->dropTable("user"); } catch (Exception $e) { }
    try {$alsosql->dropTable("user_address"); } catch (Exception $e) { }
    try {$alsosql->dropTable("user_payment"); } catch (Exception $e) { }
    echo "<br/>";
    echo "<br/>";
}

echo "First populate user:id:[name,age,status] <br/>";
echo "Then  populate user:id:address[street,city,zipcode] <br/>";
echo "Then  populate user:id:payment[type,account] <br/>";

$alsosql->set("user:1:name", "bill");
$alsosql->set("user:1:age", " 33");
$alsosql->set("user:1:status", " member");
$alsosql->set("user:1:address:street", "12345 main st");
$alsosql->set("user:1:address:city", "capitol city");
$alsosql->set("user:1:address:zipcode", "55566");
$alsosql->set("user:1:payment:type", "credit card");
$alsosql->set("user:1:payment:account", "1234567890");

$alsosql->set("user:2:name", "jane");
$alsosql->set("user:2:age", "22");
$alsosql->set("user:2:status", "premium");
$alsosql->set("user:2:address:street", "345 side st");
$alsosql->set("user:2:address:city", "capitol city");
$alsosql->set("user:2:address:zipcode", "55566");
$alsosql->set("user:2:payment:type", "checking");
$alsosql->set("user:2:payment:account", "44441111");

$alsosql->set("user:3:name", "ken");
$alsosql->set("user:3:age", "44");
$alsosql->set("user:3:status", "guest");
$alsosql->set("user:3:address:street", "876 big st");
$alsosql->set("user:3:address:city", "houston");
$alsosql->set("user:3:address:zipcode", "87654");
$alsosql->set("user:3:payment:type", "cash");

echo "Keys are now populated<br/>";
echo "<br/>";
echo "<br/>";
echo "Finally search through all redis keys using <br/>";
echo "&nbsp;&nbsp;the primary wildcard:\"user\" <br/>";
echo "&nbsp;&nbsp;and then search through those results using:<br/>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;1.) the secondary wildcard: \"*:address\" <br/>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;2.) the secondary wildcard: \"*:payment\" <br/>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;3.) non matching stil match the primary wildcard <br/>";
echo "<br/>";
echo "The 3 results will be normalised into the tables:<br/>";
echo "&nbsp;&nbsp;1.) user_address<br/>";
echo "&nbsp;&nbsp;2.) user_payment<br/>";
echo "&nbsp;&nbsp;3.) user<br/>";
echo "<br/>";
$alsosql->normalize("user", "address,payment");
echo "<br/>";
echo "<br/>";

$alsosql->select("user.pk,user.name,user.status,user_address.city,user_address.street,user_address.pk,user_address.zipcode", "user,user_address", "user.pk=user_address.pk AND user.pk BETWEEN 1 AND 5");

echo "<br/>";
echo "<br/>";
echo "If pure lookup speed of a SINGLE column is the dominant use case<br/>";
echo "We can now denorm the Alsosql tables into redis hash-tables<br/>";
echo "which are faster for this use-case<br/>";
echo "<br/>";

echo "denorm user \user:*\<br/>";
$alsosql->denormalize("user", 'user:*');
echo "HGETALL user:1<br/>";
print_r($alsosql->hgetall("user:1"));
echo "<br/>";
echo "denorm user \user:*:payment\<br/>";
$alsosql->denormalize("user_payment", 'user:*:payment');
echo "HGETALL user:1:payment<br/>";
print_r($alsosql->hgetall("user:1:payment"));
echo "<br/>";
echo "denorm user \user:*:address\<br/>";
$alsosql->denormalize("user_address", 'user:*:address');
echo "HGETALL user:1:address<br/>";
print_r($alsosql->hgetall("user:1:address"));
echo "<br/>";

?>
