<?php

require_once 'SharedConfigurations.php';
require_once 'Predisql.php';

$redisql = new Predisql_Client($database_connection, $single_server);
//$redisql->echo_command  = 1;
//$redisql->echo_response = 1;

$drop  = @$_GET['drop'];
if ($drop) {
    try {$redisql->dropTable("user"); } catch (Exception $e) { }
    try {$redisql->dropTable("user_address"); } catch (Exception $e) { }
    try {$redisql->dropTable("user_payment"); } catch (Exception $e) { }
    echo "<br/>";
    echo "<br/>";
}

echo "First populate user:id:[name,age,status] <br/>";
echo "Then  populate user:id:address[street,city,zipcode] <br/>";
echo "Then  populate user:id:payment[type,account] <br/>";

$redisql->set("user:1:name", "bill");
$redisql->set("user:1:age", " 33");
$redisql->set("user:1:status", " member");
$redisql->set("user:1:address:street", "12345 main st");
$redisql->set("user:1:address:city", "capitol city");
$redisql->set("user:1:address:zipcode", "55566");
$redisql->set("user:1:payment:type", "credit card");
$redisql->set("user:1:payment:account", "1234567890");

$redisql->set("user:2:name", "jane");
$redisql->set("user:2:age", "22");
$redisql->set("user:2:status", "premium");
$redisql->set("user:2:address:street", "345 side st");
$redisql->set("user:2:address:city", "capitol city");
$redisql->set("user:2:address:zipcode", "55566");
$redisql->set("user:2:payment:type", "checking");
$redisql->set("user:2:payment:account", "44441111");

$redisql->set("user:3:name", "ken");
$redisql->set("user:3:age", "44");
$redisql->set("user:3:status", "guest");
$redisql->set("user:3:address:street", "876 big st");
$redisql->set("user:3:address:city", "houston");
$redisql->set("user:3:address:zipcode", "87654");
$redisql->set("user:3:payment:type", "cash");

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
$redisql->normalize("user", "address,payment");
echo "<br/>";
echo "<br/>";

$redisql->select("user.pk,user.name,user.status,user_address.city,user_address.street,user_address.pk,user_address.zipcode", "user,user_address", "user.pk=user_address.pk AND user.pk BETWEEN 1 AND 5");

echo "<br/>";
echo "<br/>";
echo "If pure lookup speed of a SINGLE column is the dominant use case<br/>";
echo "We can now denorm the Redisql tables into redis hash-tables<br/>";
echo "which are faster for this use-case<br/>";
echo "<br/>";

echo "denorm user \user:*\<br/>";
$redisql->denormalize("user", 'user:*');
echo "HGETALL user:1<br/>";
print_r($redisql->hgetall("user:1"));
echo "<br/>";
echo "denorm user \user:*:payment\<br/>";
$redisql->denormalize("user_payment", 'user:*:payment');
echo "HGETALL user:1:payment<br/>";
print_r($redisql->hgetall("user:1:payment"));
echo "<br/>";
echo "denorm user \user:*:address\<br/>";
$redisql->denormalize("user_address", 'user:*:address');
echo "HGETALL user:1:address<br/>";
print_r($redisql->hgetall("user:1:address"));
echo "<br/>";

?>
