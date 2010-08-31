<?php

require_once 'SharedConfigurations.php';
require_once 'Alsosql.php';

$alsosql = new Palsosql_Client($database_connection, $single_server, NULL);
#$alsosql->echo_command       = 1;
#$alsosql->echo_response      = 1;
#$alsosql->mysql_echo_command = 1;

$tables = Array();
$tables[] = "jos_banner";
$tables[] = "jos_bannerclient";
$tables[] = "jos_bannertrack";
$tables[] = "jos_categories";
$tables[] = "jos_components";
$tables[] = "jos_contact_details";
$tables[] = "jos_content";
$tables[] = "jos_content_frontpage";
$tables[] = "jos_content_rating";
$tables[] = "jos_core_acl_aro";
$tables[] = "jos_core_acl_aro_groups";
$tables[] = "jos_core_acl_aro_map";
$tables[] = "jos_core_acl_aro_sections";
$tables[] = "jos_core_acl_groups_aro_map";
$tables[] = "jos_core_log_items";
$tables[] = "jos_core_log_searches";
$tables[] = "jos_groups";
$tables[] = "jos_menu";
$tables[] = "jos_menu_types";
$tables[] = "jos_messages";
$tables[] = "jos_messages_cfg";
$tables[] = "jos_migration_backlinks";
$tables[] = "jos_modules";
$tables[] = "jos_modules_menu";
$tables[] = "jos_newsfeeds";
$tables[] = "jos_plugins";
$tables[] = "jos_poll_data";
$tables[] = "jos_poll_date";
$tables[] = "jos_poll_menu";
$tables[] = "jos_polls";
$tables[] = "jos_sections";
$tables[] = "jos_session";
$tables[] = "jos_stats_agents";
$tables[] = "jos_templates_menu";
$tables[] = "jos_users";
$tables[] = "jos_weblinks";


$drop  = @$_GET['drop'];
if ($drop) {
    foreach($tables as $table){
        try {$alsosql->dropTable($table); } catch (Exception $e) { }
    }
}

foreach($tables as $table){
    try { $alsosql->importFromMysql($table); } catch (Exception $e) { }
}

?>
