<?php

require_once 'Predisql.php';

function init_external($redisql) {
  $redisql->createTable("external", "id int primary key, division int, health int, salary TEXT, name TEXT");
  $redisql->createIndex("external:division:index", "external.division");
  $redisql->createIndex("external:health:index  ", "external.health");
}
function init_healthplan($redisql) {
  $redisql->createTable("healthplan", "id int primary key, name TEXT");
}
function init_division($redisql) {
  $redisql->createTable("division", "id int primary key, name TEXT, location TEXT");
  $redisql->createIndex("division:name:index", "division.name");
}
function init_subdivision($redisql) {
  $redisql->createTable("subdivision", "id int primary key, division int, name TEXT");
  $redisql->createIndex("subdivision:division:index", "subdivision.division");
}
function init_employee($redisql) {
  $redisql->createTable("employee", "id int primary key, division int, salary TEXT, name TEXT");
  $redisql->createIndex("employee:name:index", "employee.name");
  $redisql->createIndex("employee:division:index", "employee.division");
}
function init_customer($redisql) {
  $redisql->createTable("customer", "id int primary key, employee int, name TEXT, hobby TEXT");
  $redisql->createIndex("customer:employee:index", "customer.employee");
  $redisql->createIndex("customer:hobby:index   ", "customer.hobby");
}
function init_worker($redisql) {
  $redisql->createTable("worker", "id int primary key, division int, health int, salary TEXT, name TEXT");
  $redisql->createIndex("worker:division:index", "worker.division");
  $redisql->createIndex("worker:health:index  ", "worker.health");
}

function insert_external($redisql) {
  $redisql->insert("external", "1,66,1,15000.99,marieanne");
  $redisql->insert("external", "2,33,3,75000.77,rosemarie");
  $redisql->insert("external", "3,11,2,55000.55,johnathan");
  $redisql->insert("external", "4,22,1,25000.99,bartholemew");
}
function insert_healthplan($redisql) {
  $redisql->insert("healthplan", "1,none");
  $redisql->insert("healthplan", "2,kaiser");
  $redisql->insert("healthplan", "3,general");
  $redisql->insert("healthplan", "4,extended");
  $redisql->insert("healthplan", "5,foreign");
}
function insert_subdivision($redisql) {
  $redisql->insert("subdivision", "1,11,middle-management");
  $redisql->insert("subdivision", "2,11,top-level");
  $redisql->insert("subdivision", "3,44,trial");
  $redisql->insert("subdivision", "4,44,research");
  $redisql->insert("subdivision", "5,22,factory");
  $redisql->insert("subdivision", "6,22,field");
}
function insert_division($redisql) {
  $redisql->insert("division", "11,bosses,N.Y.C");
  $redisql->insert("division", "22,workers,Chicago");
  $redisql->insert("division", "33,execs,Dubai");
  $redisql->insert("division", "55,bankers,Zurich");
  $redisql->insert("division", "66,janitors,Detroit");
  $redisql->insert("division", "44,lawyers,L.A.");
}
function insert_employee($redisql) {
  $redisql->insert("employee", "1,11,10000.99,jim");
  $redisql->insert("employee", "2,22,2000.99,jack");
  $redisql->insert("employee", "3,33,30000.99,bob");
  $redisql->insert("employee", "4,22,3000.99,bill");
  $redisql->insert("employee", "5,22,5000.99,tim");
  $redisql->insert("employee", "6,66,60000.99,jan");
  $redisql->insert("employee", "7,77,7000.99,beth");
  $redisql->insert("employee", "8,88,80000.99,kim");
  $redisql->insert("employee", "9,99,9000.99,pam");
  $redisql->insert("employee", "11,111,111000.99,sammy");
}
function insert_customer($redisql) {
  $redisql->insert("customer", "1,2,johnathan,sailing");
  $redisql->insert("customer", "2,3,bartholemew,fencing");
  $redisql->insert("customer", "3,3,jeremiah,yachting");
  $redisql->insert("customer", "4,4,christopher,curling");
  $redisql->insert("customer", "6,4,jennifer,stamps");
  $redisql->insert("customer", "7,4,marieanne,painting");
  $redisql->insert("customer", "8,5,rosemarie,violin");
  $redisql->insert("customer", "9,5,bethany,choir");
  $redisql->insert("customer", "10,6,gregory,dance");
}
function insert_worker($redisql) {
  $redisql->insertAndReturnSize("worker", "1,11,2,60000.66,jim");
  $redisql->insertAndReturnSize("worker", "2,22,1,30000.33,jack");
  $redisql->insertAndReturnSize("worker", "3,33,4,90000.99,bob");
  $redisql->insertAndReturnSize("worker", "4,44,3,70000.77,bill");
  $redisql->insertAndReturnSize("worker", "6,66,1,10000.99,jan");
  $redisql->insertAndReturnSize("worker", "7,66,1,11000.99,beth");
  $redisql->insertAndReturnSize("worker", "8,11,2,68888.99,mac");
  $redisql->insertAndReturnSize("worker", "9,22,1,31111.99,ken");
  $redisql->insertAndReturnSize("worker", "10,33,4,111111.99,seth");
}

function initer($redisql) {
  try {
    init_worker($redisql);
    init_customer($redisql);
    init_employee($redisql);
    init_division($redisql);
    init_subdivision($redisql);
    init_healthplan($redisql);
    init_external($redisql);
  } catch (Exception $exception) {
    echo "EXCEPTION IN INITER: <br/>";
    //echo "EXCEPTION IN INITER: " . $exception . "<br/>";
  }
}
function inserter($redisql) {
  try {
    insert_worker($redisql);
    insert_customer($redisql);
    insert_employee($redisql);
    insert_division($redisql);
    insert_subdivision($redisql);
    insert_healthplan($redisql);
    insert_external($redisql);
  } catch (Exception $exception) {
    echo "EXCEPTION IN INSERTER: <br/>";
    //echo "EXCEPTION IN INSERTER: " . $exception . "<br/>";
  }
}

function selecter($redisql) {
  $redisql->select("*", "division", "id = 22");
  $redisql->select("name, location", "division", "id = 22"); 
  $redisql->select("*", "employee", "id = 2"); 
  $redisql->select("name,salary", "employee", "id = 2"); 
  $redisql->select("*", "customer", "id = 2"); 
  $redisql->select("name", "customer", "id = 2");
  $redisql->select("*", "worker", "id = 7");   
  $redisql->select("name, salary, division", "worker", "id = 7");
  $redisql->select("*", "subdivision", "id = 2");  
  $redisql->select("name,division", "subdivision", "id = 2");
  $redisql->select("*", "healthplan", "id = 2");   
  $redisql->select("name", "healthplan", "id = 2");  
  $redisql->select("*", "external", "id = 3"); 
  $redisql->select("name,salary,division", "external", "id = 3");
}

function updater($redisql) {
  $redisql->select("*", "employee", "id = 1");
  $redisql->update("employee", "salary=50000,name=NEWNAME,division=66", "id = 1");
  $redisql->select("*", "employee", "id = 1");
  $redisql->update("employee", "id=100", "id = 1");
  $redisql->select("*", "employee", "id = 100");
}

function delete_employee($redisql) {
  $redisql->select("name,salary", "employee", "id = 3");
  $redisql->delete("employee", "id = 3");
  $redisql->select("name,salary", "employee", "id = 3");
}
function delete_customer($redisql) {
  $redisql->select("name, hobby", "customer", "id = 7");
  $redisql->delete("customer", "id = 7");
  $redisql->select("name, hobby", "customer", "id = 7");
}
function delete_division($redisql) {
  $redisql->select("name, location", "division", "id = 33");
  $redisql->delete("division", "id = 33");
  $redisql->select("name, location", "division", "id = 33");
}
  
function deleter($redisql) {
  delete_employee($redisql);
  delete_customer($redisql);
  delete_division($redisql);
}

function iselecter_division($redisql) {
  $redisql->select("id,name,location", "division", "name BETWEEN a AND z");
}
function iselecter_employee($redisql) {
  $redisql->select("id,name,salary,division", "employee", "division BETWEEN 11 AND 55");
}
function iselecter_customer($redisql) {
  $redisql->select("hobby,id,name,employee", "customer", "hobby BETWEEN a AND z");
}
function iselecter_customer_employee($redisql) {
  $redisql->select("employee,name,id", "customer", "employee BETWEEN 3 AND 6");
}
function iselecter_worker($redisql) {
  $redisql->select("id,health,name,salary,division", "worker", "health BETWEEN 1 AND 3");
}
function iselecter($redisql) {
  iselecter_division($redisql);
  iselecter_employee($redisql);
  iselecter_customer($redisql);
}
function iupdater_customer($redisql) {
  $redisql->update("customer", "hobby=fishing,employee=6", "hobby BETWEEN v AND z");
}
function iupdater_customer_rev($redisql) {
  $redisql->update("customer", "hobby=ziplining,employee=7", "hobby BETWEEN f AND g");
}
function ideleter_customer($redisql) {
  $redisql->delete("customer", "employee BETWEEN 4 AND 5");
}


function join_div_extrnl($redisql) {
  $redisql->select("division.name,division.location,external.name,external.salary", "division,external", "division.id=external.division AND division.id BETWEEN 11 AND 80");
}

function join_div_wrkr($redisql) {
  $redisql->select("division.name,division.location,worker.name,worker.salary", "division,worker", "division.id = worker.division AND division.id BETWEEN 11 AND 33");

}

function join_wrkr_health($redisql) {
  $redisql->select("worker.name,worker.salary,healthplan.name", "worker,healthplan", "worker.health = healthplan.id AND healthplan.id BETWEEN 1 AND 5");
  $redisql->select("healthplan.name,worker.name,worker.salary", "healthplan,worker", "healthplan.id=worker.health AND healthplan.id BETWEEN 1 AND 5");
}

function join_div_wrkr_sub($redisql) {
  $redisql->select("division.name,division.location,worker.name,worker.salary,subdivision.name", "division,worker,subdivision", "division.id = worker.division AND division.id = subdivision.division AND division.id BETWEEN 11 AND 33");

}

function join_div_sub_wrkr($redisql) {
  $redisql->select("division.name,division.location,subdivision.name,worker.name,worker.salary", "division,subdivision,worker", "division.id = subdivision.division AND division.id = worker.division AND division.id BETWEEN 11 AND 33");
}

function joiner($redisql) {
  join_div_extrnl($redisql);
  join_div_wrkr($redisql);
  join_wrkr_health($redisql);
  join_div_wrkr_sub($redisql);
  join_div_sub_wrkr($redisql);
}


function works($redisql) {
  initer($redisql);
  inserter($redisql);
  selecter($redisql);
  iselecter($redisql);
  updater($redisql);
  iselecter_employee($redisql);
  deleter($redisql);
  iselecter($redisql);
  iupdater_customer($redisql);
  iselecter_customer($redisql);
  ideleter_customer($redisql);
  iselecter_customer_employee($redisql);
  joiner($redisql);
  //$redisql->dump("customer");
}

function single_join_div_extrnl($redisql) {
  init_division($redisql);
  insert_division($redisql);
  init_external($redisql);
  insert_external($redisql);
  join_div_extrnl($redisql);
}

function single_join_wrkr_health_rev($redisql) {
  init_worker($redisql);
  insert_worker($redisql);
  init_healthplan($redisql);
  insert_healthplan($redisql);
  $redisql->select("healthplan.name,worker.name,worker.salary", "healthplan,worker", "healthplan.id=worker.health AND healthplan.id BETWEEN 1 AND 5");
}

function single_join_wrkr_health($redisql) {
  init_worker($redisql);
  insert_worker($redisql);
  init_healthplan($redisql);
  insert_healthplan($redisql);
  $redisql->select("worker.name,worker.salary,healthplan.name", "worker,healthplan", "worker.health=healthplan.id AND healthplan.id BETWEEN 1 AND 5");
}

function single_join_sub_wrkr($redisql) {
  init_division($redisql);
  insert_division($redisql);
  init_worker($redisql);
  insert_worker($redisql);
  init_subdivision($redisql);
  insert_subdivision($redisql);
  join_div_sub_wrkr($redisql);
}

function scan_external($redisql) {
  $redisql->scanSelect("name,salary", "external", "salary BETWEEN 15000.99 AND 25001.01");
}
function scan_healthpan($redisql) {
   $redisql->scanSelect("*", "healthplan", "name BETWEEN a AND k");
}

function istore_worker_name_list($redisql) {
  $redisql->selectStore("name", "worker", "division BETWEEN 11 AND 33", "RPUSH", "l_worker_name");
  $redisql->lrange("l_worker_name",0,10);
}

function istore_worker_hash_name_salary($redisql) {
  $redisql->selectStore("name,salary", "worker", "division BETWEEN 11 AND 33", "HSET", "h_worker_name_to_salary");
  print_r($redisql->hkeys("h_worker_name_to_salary"));
  echo "<br/>";
  print_r($redisql->hvals("h_worker_name_to_salary"));
  echo "<br/>";
}

function jstore_div_subdiv($redisql) {
  try { $redisql->dropTable("normal_div_subdiv"); } catch (Exception $e) { }
  $redisql->selectStore("subdivision.id,subdivision.name,division.name", "subdivision,division", "subdivision.division = division.id AND division.id BETWEEN 11 AND 44", "INSERT", "normal_div_subdiv");
  $redisql->dump("normal_div_subdiv");
}

function jstore_worker_location_hash($redisql) {
  $redisql->selectStore("external.name,division.location", "external,division", "external.division=division.id AND division.id BETWEEN 11 AND 80", "HSET", "worker_city_hash");
  print_r($redisql->hkeys("worker_city_hash"));
  echo "<br/>";
  print_r($redisql->hvals("worker_city_hash"));
  echo "<br/>";
}

function jstore_worker_location_table($redisql) {
  try { $redisql->dropTable("w_c_tbl"); } catch (Exception $e) { }
  $redisql->selectStore("external.name,division.location", "external,division", "external.division=division.id AND division.id BETWEEN 11 AND 80", "INSERT", "w_c_tbl");
  $redisql->dump("w_c_tbl");
}

function bad_syntax($redisql) {
  try { $redisql->createTable(); } catch (Exception $e) { echo $e . "<br/>";  }
  try { $redisql->createIndex(); } catch (Exception $e) { echo $e . "<br/>";  }
  try { $redisql->insert(); } catch (Exception $e) { echo $e . "<br/>";  }
  try { $redisql->select(); } catch (Exception $e) { echo $e . "<br/>";  }
  try { $redisql->delete(); } catch (Exception $e) { echo $e . "<br/>";  }
  try { $redisql->update(); } catch (Exception $e) { echo $e . "<br/>";  }
  try { $redisql->select(); } catch (Exception $e) { echo $e . "<br/>";  }
  try { $redisql->scanSelect(); } catch (Exception $e) { echo $e . "<br/>";  }
  try { $redisql->desc(); } catch (Exception $e) { echo $e . "<br/>";  }
  try { $redisql->dump(); } catch (Exception $e) { echo $e . "<br/>";  }
}
?>
