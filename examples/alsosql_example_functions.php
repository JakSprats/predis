<?php

require_once '../lib/Predis.php';
require_once 'Alsosql.php';

function init_external($alsosql) {
  $alsosql->createTable("external", "id int primary key, division int, health int, salary TEXT, name TEXT");
  $alsosql->createIndex("external:division:index", "external.division");
  $alsosql->createIndex("external:health:index  ", "external.health");
}
function init_healthplan($alsosql) {
  $alsosql->createTable("healthplan", "id int primary key, name TEXT");
}
function init_division($alsosql) {
  $alsosql->createTable("division", "id int primary key, name TEXT, location TEXT");
  $alsosql->createIndex("division:name:index", "division.name");
}
function init_subdivision($alsosql) {
  $alsosql->createTable("subdivision", "id int primary key, division int, name TEXT");
  $alsosql->createIndex("subdivision:division:index", "subdivision.division");
}
function init_employee($alsosql) {
  $alsosql->createTable("employee", "id int primary key, division int, salary TEXT, name TEXT");
  $alsosql->createIndex("employee:name:index", "employee.name");
  $alsosql->createIndex("employee:division:index", "employee.division");
}
function init_customer($alsosql) {
  $alsosql->createTable("customer", "id int primary key, employee int, name TEXT, hobby TEXT");
  $alsosql->createIndex("customer:employee:index", "customer.employee");
  $alsosql->createIndex("customer:hobby:index   ", "customer.hobby");
}
function init_worker($alsosql) {
  $alsosql->createTable("worker", "id int primary key, division int, health int, salary TEXT, name TEXT");
  $alsosql->createIndex("worker:division:index", "worker.division");
  $alsosql->createIndex("worker:health:index  ", "worker.health");
}

function insert_external($alsosql) {
  $alsosql->insert("external", "1,66,1,15000.99,marieanne");
  $alsosql->insert("external", "2,33,3,75000.77,rosemarie");
  $alsosql->insert("external", "3,11,2,55000.55,johnathan");
  $alsosql->insert("external", "4,22,1,25000.99,bartholemew");
}
function insert_healthplan($alsosql) {
  $alsosql->insert("healthplan", "1,none");
  $alsosql->insert("healthplan", "2,kaiser");
  $alsosql->insert("healthplan", "3,general");
  $alsosql->insert("healthplan", "4,extended");
  $alsosql->insert("healthplan", "5,foreign");
}
function insert_subdivision($alsosql) {
  $alsosql->insert("subdivision", "1,11,middle-management");
  $alsosql->insert("subdivision", "2,11,top-level");
  $alsosql->insert("subdivision", "3,44,trial");
  $alsosql->insert("subdivision", "4,44,research");
  $alsosql->insert("subdivision", "5,22,factory");
  $alsosql->insert("subdivision", "6,22,field");
}
function insert_division($alsosql) {
  $alsosql->insert("division", "11,bosses,N.Y.C");
  $alsosql->insert("division", "22,workers,Chicago");
  $alsosql->insert("division", "33,execs,Dubai");
  $alsosql->insert("division", "55,bankers,Zurich");
  $alsosql->insert("division", "66,janitors,Detroit");
  $alsosql->insert("division", "44,lawyers,L.A.");
}
function insert_employee($alsosql) {
  $alsosql->insert("employee", "1,11,10000.99,jim");
  $alsosql->insert("employee", "2,22,2000.99,jack");
  $alsosql->insert("employee", "3,33,30000.99,bob");
  $alsosql->insert("employee", "4,22,3000.99,bill");
  $alsosql->insert("employee", "5,22,5000.99,tim");
  $alsosql->insert("employee", "6,66,60000.99,jan");
  $alsosql->insert("employee", "7,77,7000.99,beth");
  $alsosql->insert("employee", "8,88,80000.99,kim");
  $alsosql->insert("employee", "9,99,9000.99,pam");
  $alsosql->insert("employee", "11,111,111000.99,sammy");
}
function insert_customer($alsosql) {
  $alsosql->insert("customer", "1,2,johnathan,sailing");
  $alsosql->insert("customer", "2,3,bartholemew,fencing");
  $alsosql->insert("customer", "3,3,jeremiah,yachting");
  $alsosql->insert("customer", "4,4,christopher,curling");
  $alsosql->insert("customer", "6,4,jennifer,stamps");
  $alsosql->insert("customer", "7,4,marieanne,painting");
  $alsosql->insert("customer", "8,5,rosemarie,violin");
  $alsosql->insert("customer", "9,5,bethany,choir");
  $alsosql->insert("customer", "10,6,gregory,dance");
}
function insert_worker($alsosql) {
  $alsosql->insertAndReturnSize("worker", "1,11,2,60000.66,jim");
  $alsosql->insertAndReturnSize("worker", "2,22,1,30000.33,jack");
  $alsosql->insertAndReturnSize("worker", "3,33,4,90000.99,bob");
  $alsosql->insertAndReturnSize("worker", "4,44,3,70000.77,bill");
  $alsosql->insertAndReturnSize("worker", "6,66,1,10000.99,jan");
  $alsosql->insertAndReturnSize("worker", "7,66,1,11000.99,beth");
  $alsosql->insertAndReturnSize("worker", "8,11,2,68888.99,mac");
  $alsosql->insertAndReturnSize("worker", "9,22,1,31111.99,ken");
  $alsosql->insertAndReturnSize("worker", "10,33,4,111111.99,seth");
}

function initer($alsosql) {
  try {
    init_worker($alsosql);
    init_customer($alsosql);
    init_employee($alsosql);
    init_division($alsosql);
    init_subdivision($alsosql);
    init_healthplan($alsosql);
    init_external($alsosql);
  } catch (Exception $exception) {
    echo "EXCEPTION IN INITER: <br/>";
    //echo "EXCEPTION IN INITER: " . $exception . "<br/>";
  }
}
function inserter($alsosql) {
  try {
    insert_worker($alsosql);
    insert_customer($alsosql);
    insert_employee($alsosql);
    insert_division($alsosql);
    insert_subdivision($alsosql);
    insert_healthplan($alsosql);
    insert_external($alsosql);
  } catch (Exception $exception) {
    echo "EXCEPTION IN INSERTER: <br/>";
    //echo "EXCEPTION IN INSERTER: " . $exception . "<br/>";
  }
}

function selecter($alsosql) {
  $alsosql->select("*", "division", "id = 22");
  $alsosql->select("name, location", "division", "id = 22"); 
  $alsosql->select("*", "employee", "id = 2"); 
  $alsosql->select("name,salary", "employee", "id = 2"); 
  $alsosql->select("*", "customer", "id = 2"); 
  $alsosql->select("name", "customer", "id = 2");
  $alsosql->select("*", "worker", "id = 7");   
  $alsosql->select("name, salary, division", "worker", "id = 7");
  $alsosql->select("*", "subdivision", "id = 2");  
  $alsosql->select("name,division", "subdivision", "id = 2");
  $alsosql->select("*", "healthplan", "id = 2");   
  $alsosql->select("name", "healthplan", "id = 2");  
  $alsosql->select("*", "external", "id = 3"); 
  $alsosql->select("name,salary,division", "external", "id = 3");
}

function updater($alsosql) {
  $alsosql->select("*", "employee", "id = 1");
  $alsosql->update("employee", "salary=50000,name=NEWNAME,division=66", "id = 1");
  $alsosql->select("*", "employee", "id = 1");
  $alsosql->update("employee", "id=100", "id = 1");
  $alsosql->select("*", "employee", "id = 100");
}

function delete_employee($alsosql) {
  $alsosql->select("name,salary", "employee", "id = 3");
  $alsosql->delete("employee", "id = 3");
  $alsosql->select("name,salary", "employee", "id = 3");
}
function delete_customer($alsosql) {
  $alsosql->select("name, hobby", "customer", "id = 7");
  $alsosql->delete("customer", "id = 7");
  $alsosql->select("name, hobby", "customer", "id = 7");
}
function delete_division($alsosql) {
  $alsosql->select("name, location", "division", "id = 33");
  $alsosql->delete("division", "id = 33");
  $alsosql->select("name, location", "division", "id = 33");
}
  
function deleter($alsosql) {
  delete_employee($alsosql);
  delete_customer($alsosql);
  delete_division($alsosql);
}

function iselecter_division($alsosql) {
  $alsosql->select("id,name,location", "division", "name BETWEEN a AND z");
}
function iselecter_employee($alsosql) {
  $alsosql->select("id,name,salary,division", "employee", "division BETWEEN 11 AND 55");
}
function iselecter_customer($alsosql) {
  $alsosql->select("hobby,id,name,employee", "customer", "hobby BETWEEN a AND z");
}
function iselecter_customer_employee($alsosql) {
  $alsosql->select("employee,name,id", "customer", "employee BETWEEN 3 AND 6");
}
function iselecter_worker($alsosql) {
  $alsosql->select("id,health,name,salary,division", "worker", "health BETWEEN 1 AND 3");
}
function iselecter($alsosql) {
  iselecter_division($alsosql);
  iselecter_employee($alsosql);
  iselecter_customer($alsosql);
}
function iupdater_customer($alsosql) {
  $alsosql->update("customer", "hobby=fishing,employee=6", "hobby BETWEEN v AND z");
}
function iupdater_customer_rev($alsosql) {
  $alsosql->update("customer", "hobby=ziplining,employee=7", "hobby BETWEEN f AND g");
}
function ideleter_customer($alsosql) {
  $alsosql->delete("customer", "employee BETWEEN 4 AND 5");
}


function join_div_extrnl($alsosql) {
  $alsosql->select("division.name,division.location,external.name,external.salary", "division,external", "division.id=external.division AND division.id BETWEEN 11 AND 80");
}

function join_div_wrkr($alsosql) {
  $alsosql->select("division.name,division.location,worker.name,worker.salary", "division,worker", "division.id = worker.division AND division.id BETWEEN 11 AND 33");

}

function join_wrkr_health($alsosql) {
  $alsosql->select("worker.name,worker.salary,healthplan.name", "worker,healthplan", "worker.health = healthplan.id AND healthplan.id BETWEEN 1 AND 5");
  $alsosql->select("healthplan.name,worker.name,worker.salary", "healthplan,worker", "healthplan.id=worker.health AND healthplan.id BETWEEN 1 AND 5");
}

function join_div_wrkr_sub($alsosql) {
  $alsosql->select("division.name,division.location,worker.name,worker.salary,subdivision.name", "division,worker,subdivision", "division.id = worker.division AND division.id = subdivision.division AND division.id BETWEEN 11 AND 33");

}

function join_div_sub_wrkr($alsosql) {
  $alsosql->select("division.name,division.location,subdivision.name,worker.name,worker.salary", "division,subdivision,worker", "division.id = subdivision.division AND division.id = worker.division AND division.id BETWEEN 11 AND 33");
}

function joiner($alsosql) {
  join_div_extrnl($alsosql);
  join_div_wrkr($alsosql);
  join_wrkr_health($alsosql);
  join_div_wrkr_sub($alsosql);
  join_div_sub_wrkr($alsosql);
}


function works($alsosql) {
  initer($alsosql);
  inserter($alsosql);
  selecter($alsosql);
  iselecter($alsosql);
  updater($alsosql);
  iselecter_employee($alsosql);
  deleter($alsosql);
  iselecter($alsosql);
  iupdater_customer($alsosql);
  iselecter_customer($alsosql);
  ideleter_customer($alsosql);
  iselecter_customer_employee($alsosql);
  joiner($alsosql);
  //$alsosql->dump("customer");
}

function single_join_div_extrnl($alsosql) {
  init_division($alsosql);
  insert_division($alsosql);
  init_external($alsosql);
  insert_external($alsosql);
  join_div_extrnl($alsosql);
}

function single_join_wrkr_health_rev($alsosql) {
  init_worker($alsosql);
  insert_worker($alsosql);
  init_healthplan($alsosql);
  insert_healthplan($alsosql);
  $alsosql->select("healthplan.name,worker.name,worker.salary", "healthplan,worker", "healthplan.id=worker.health AND healthplan.id BETWEEN 1 AND 5");
}

function single_join_wrkr_health($alsosql) {
  init_worker($alsosql);
  insert_worker($alsosql);
  init_healthplan($alsosql);
  insert_healthplan($alsosql);
  $alsosql->select("worker.name,worker.salary,healthplan.name", "worker,healthplan", "worker.health=healthplan.id AND healthplan.id BETWEEN 1 AND 5");
}

function single_join_sub_wrkr($alsosql) {
  init_division($alsosql);
  insert_division($alsosql);
  init_worker($alsosql);
  insert_worker($alsosql);
  init_subdivision($alsosql);
  insert_subdivision($alsosql);
  join_div_sub_wrkr($alsosql);
}

function scan_external($alsosql) {
  $alsosql->scanSelect("name,salary", "external", "salary BETWEEN 15000.99 AND 25001.01");
}
function scan_healthpan($alsosql) {
   $alsosql->scanSelect("*", "healthplan", "name BETWEEN a AND k");
}

function istore_worker_name_list($alsosql) {
  $alsosql->selectStore("name", "worker", "division BETWEEN 11 AND 33", "RPUSH", "l_worker_name");
  $alsosql->lrange("l_worker_name",0,10);
}

function istore_worker_hash_name_salary($alsosql) {
  $alsosql->selectStore("name,salary", "worker", "division BETWEEN 11 AND 33", "HSET", "h_worker_name_to_salary");
  print_r($alsosql->hkeys("h_worker_name_to_salary"));
  echo "<br/>";
  print_r($alsosql->hvals("h_worker_name_to_salary"));
  echo "<br/>";
}

function jstore_div_subdiv($alsosql) {
  try { $alsosql->dropTable("normal_div_subdiv"); } catch (Exception $e) { }
  $alsosql->selectStore("subdivision.id,subdivision.name,division.name", "subdivision,division", "subdivision.division = division.id AND division.id BETWEEN 11 AND 44", "INSERT", "normal_div_subdiv");
  $alsosql->dump("normal_div_subdiv");
}

function jstore_worker_location_hash($alsosql) {
  $alsosql->selectStore("external.name,division.location", "external,division", "external.division=division.id AND division.id BETWEEN 11 AND 80", "HSET", "worker_city_hash");
  print_r($alsosql->hkeys("worker_city_hash"));
  echo "<br/>";
  print_r($alsosql->hvals("worker_city_hash"));
  echo "<br/>";
}

function jstore_worker_location_table($alsosql) {
  try { $alsosql->dropTable("w_c_tbl"); } catch (Exception $e) { }
  $alsosql->selectStore("external.name,division.location", "external,division", "external.division=division.id AND division.id BETWEEN 11 AND 80", "INSERT", "w_c_tbl");
  $alsosql->dump("w_c_tbl");
}

function bad_syntax($alsosql) {
  try { $alsosql->createTable(); } catch (Exception $e) { echo $e . "<br/>";  }
  try { $alsosql->createIndex(); } catch (Exception $e) { echo $e . "<br/>";  }
  try { $alsosql->insert(); } catch (Exception $e) { echo $e . "<br/>";  }
  try { $alsosql->select(); } catch (Exception $e) { echo $e . "<br/>";  }
  try { $alsosql->delete(); } catch (Exception $e) { echo $e . "<br/>";  }
  try { $alsosql->update(); } catch (Exception $e) { echo $e . "<br/>";  }
  try { $alsosql->select(); } catch (Exception $e) { echo $e . "<br/>";  }
  try { $alsosql->scanSelect(); } catch (Exception $e) { echo $e . "<br/>";  }
  try { $alsosql->desc(); } catch (Exception $e) { echo $e . "<br/>";  }
  try { $alsosql->dump(); } catch (Exception $e) { echo $e . "<br/>";  }
}
?>
