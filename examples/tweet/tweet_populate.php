<?php

require_once '../SharedConfigurations.php';
require_once 'Alsosql.php';

function populate_tweets($alsosql,
                         $z_name,
                         $user_id,
                         $yesterday,
                         $two_days_ago,
                         $dump_stats) {
    $z_obj   = $z_name . ":" . $user_id;

    $one_hour = 3600;
    $when     = $two_days_ago;
    $alsosql->zadd($z_obj, $when, "today is hot");

    $when += $one_hour;
    $alsosql->zadd($z_obj, $when, "i mean real hot");

    $when += $one_hour;
    $alsosql->zadd($z_obj, $when, "i need an AC");

    $when += $one_hour;
    $alsosql->zadd($z_obj, $when, "im using the laptop as it generates less heat");

    $when += $one_hour;
    $alsosql->zadd($z_obj, $when, "switching to iphone");

    $when += $one_hour;
    $alsosql->zadd($z_obj, $when, "laying around like a dog in the alabama summer");

    $when += $one_hour;
    $alsosql->zadd($z_obj, $when, "just constructed a cooling apparatus made of a fan blowing over some frozen fish sticks");

    $when += $one_hour;
    $alsosql->zadd($z_obj, $when, "time to go the movie theaters");

    if ($dump_stats == 1) {
        echo "REDIS ZSET DAY ONE<br/>";
        print_ar($alsosql->zrange($z_obj, 0, -1));
        echo "<br/>";
    }

    $when = $yesterday;
    $alsosql->zadd($z_obj, $when, "its nice today");
    $when += $one_hour;

    $alsosql->zadd($z_obj, $when, "going to target to get an AC");
    $when += $one_hour;

    $alsosql->zadd($z_obj, $when, "got an AC");
    $when += $one_hour;

    $alsosql->zadd($z_obj, $when, "dont need AC today");
    $when += $one_hour;

    $alsosql->zadd($z_obj, $when, "california is funny like that not enough really hot days to need an AC");
    $when += $one_hour;

    $alsosql->zadd($z_obj, $when, "going to return AC ... dumb dumb");
    $when += $one_hour;

    if ($dump_stats == 1) {
        echo "REDIS ZSET DAY ONE AND TWO<br/>";
        print_ar($alsosql->zrange($z_obj, 0, -1));
        echo "<br/>";
    }
}
?>
