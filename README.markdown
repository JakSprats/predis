# Predisql #

## Redisql ##

Redisql: Is a Hybrid Relational-Database/NOSQL-Datastore.
Project homepage: [http://code.google.com/p/redisql/](http://code.google.com/p/redisql/)

SQL Tables, SQL Statements and SQL Data-types are supported. Redisql is built on top of the NOSQL datastore redis and supports normalisation/denormalisation of redis data structures (lists,sets,hash-tables) into/from sql tables, as well as import/export of tables to/from Mysql. It is a data storage Swiss army knife.

Storing Data in NOSQL and/or SQL and converting to and fro can be done w/ a few straightforward SQL-esque commands. Redisql is optimised for a OLTP workload and is both extremely fast and extremely memory efficient. Redisql attains its speed by storing ALL of its data in RAM, periodically snapshot-saving the data to disk and by optimising to the SQL statements commonly used in OLTP workloads.

This Redisql client is a fork of Predis and is compliant w/ ALL redis commands (except Redisql has usurped the redis command "SELECT" in SQL's name, and replaced it with "CHANGEDB")

To view Redisql's functionality look at the library in lib/Predisql.php

NOTE: PHP 5.2 backport of this version is available here:
      http://allinram.info/alsosql/Predis_v0.6.1-PHP5.2_ALSOSQL_ENABLED.tgz
## Examples ##

- "examples/works.php" which calls "examples/redisql_example_functions.php", the latter containing MOST Redisql RDBMS use cases
- "examples/pop_denorm.php", which denormalises many redis STRINGS into an RediSQL table (and then just to display functionality, denormalises said table into a set of redis hash-tables)
- "examples/backup_redis_to_mysql.php" dumps all redis SETs, LISTs, ZSETs, and HASHes to normalised Mysql tables (in 15 lines of code)
- "examples/tweet/tweet_archiver.php" shows how Redisql can be used to create a Mysql Cache for a redis Zset (class in file "examples/ZsetCache.php"). This effectively and transparently adds hard-disk capabilities to the InMemory Database redis for rarely accessed (archived) data.

These simple examples should start to shed light on Redisql's ease-of-use and the flexibility it provides: morph data between different structures at the network level, between Mysql, Redisql and redis.

## About Predis ##

Predis is a flexible and feature-complete PHP client library for the Redis key-value database and "Redisql on top of Predis" is a fork that extends Predis to be able to communication with the Redisql Hybrid Relational-Database/NOSQL-Datastore.

## Main features ##

- Full Support for Redisql alpha release
- Full support for Redis 2.0. Different versions of Redis are supported via server profiles.
- Client-side sharding (support for consistent hashing and custom distribution strategies).
- Command pipelining on single and multiple connections (transparent).
- Lazy connections (connections to Redis instances are only established just in time).
- Flexible system to define and register your own set of commands to a client instance.


## Quick examples ##

See the [official wiki](http://wiki.github.com/nrk/predis) of the project for a more 
complete coverage of all the features available in Predis.

### Connecting to a local instance of Redisql and Mysql ###

You don't have to specify a tcp host and port when connecting to Redis instances running on the localhost on the default port, but you do have to specify the mysql connection:

    $database_connection = array(
        'host' => '127.0.0.1', 'user' => 'root', 'password' => '', 'name' => 'mydb',
    );
    $redisql = new Predisql_Client($database_connection);
    // IN SQL: CREATE TABLE healthplan (id int primary key, name TEXT)
    $redisql->createTable("healthplan", "id int primary key, name TEXT");
    // IN SQL: INSERT INTO healthplan VALUES (1,none)
    $redisql->insert("healthplan", "1,none");
    // IN SQL: INSERT INTO healthplan VALUES (2,kaiser)
    $redisql->insert("healthplan", "2, Kaiser Permanente");
    // IN SQL: SELECT * FROM  healthplan WHERE id = 2
    $redisql->select("*", "healthplan", "id = 2");

    // redis commands work w/ redisql as Predisql_Client extends Predis\Client()
    $redisql->set('library', 'predis');
    $value = $redisql->get('library');


### Pipelining multiple commands to a remote instance of Redis ##

Pipelining helps with performances when there is the need to issue many commands 
to a server in one go:

    $redisql = new Predisql_Client('redis://10.0.0.1:6379/');
    $replies = $redisql->pipeline(function($pipe) {
        $pipe->ping();
        $pipe->incrby('counter', 10);
        $pipe->incrby('counter', 30);
        $pipe->get('counter');
        $pipe->insert("healthplan", "3, Blue Shield");
        $pipe->insert("healthplan", "4, Aetna");
        $pipe->insert("healthplan", "5, Blue Cross");
    });


### Pipelining multiple commands to multiple instances of Redis (sharding) ##

NOTE: this is not yet supported in Redisql

Predis supports data sharding using consistent-hashing on keys on the client side. 
Furthermore, a pipeline can be initialized on a cluster of redis instances in the 
same exact way they are created on single connection. Sharding is still transparent 
to the user:

    $redis = Predis\Client::create(
        array('host' => '10.0.0.1', 'port' => 6379),
        array('host' => '10.0.0.2', 'port' => 6379)
    );

    $replies = $redis->pipeline(function($pipe) {
        for ($i = 0; $i < 1000; $i++) {
            $pipe->set("key:$i", str_pad($i, 4, '0', 0));
            $pipe->get("key:$i");
        }
    });


### Definition and runtime registration of new commands on the client ###

Let's suppose Redis just added the support for a brand new feature associated 
with a new command. If you want to start using the above mentioned new feature 
right away without messing with Predis source code or waiting for it to find 
its way into a stable Predis release, then you can start off by creating a new 
class that matches the command type and its behaviour and then bind it to a 
client instance at runtime. Actually, it is easier done than said:

    class BrandNewRedisCommand extends \Predis\MultiBulkCommand {
        public function getCommandId() { return 'NEWCMD'; }
    }

    $redis = new Predis\Client();
    $redis->getProfile()->registerCommand('BrandNewRedisCommand', 'newcmd');
    $redis->newcmd();


## Development ##

Predis is fully backed up by a test suite which tries to cover all the aspects of the 
client library and the interaction of every single command with a Redis server. If you 
want to work on Predis, it is highly recommended that you first run the test suite to 
be sure that everything is OK, and report strange behaviours or bugs.

The recommended way to contribute to Predis is to fork the project on GitHub, fix or 
add features on your newly created repository and then submit issues on the Predis 
issue tracker with a link to your repository. Obviously, you can use any other Git 
hosting provider of you preference. Diff patches will be accepted too, even though 
they are not the preferred way to contribute to Predis.

When modifying Predis please be sure that no warnings or notices are emitted by PHP 
by running the interpreter in your development environment with the "error_reporting"
variable set to E_ALL | E_STRICT.


## Dependencies ##

- PHP >= 5.3.0 (for the mainline client library)
- PHP >= 5.2.6 (for the backported client library)
- PHPUnit (needed to run the test suite)

## Links ##

### Project ###
- [Source code](http://github.com/JakSprats/predis/)
- [Wiki](http://wiki.github.com/JakSprats/predis/)
- [Issue tracker](http://github.com/JakSprats/predis/issues)

### Related ###
- [Redisql](http://github.com/JakSprats/Redisql)
- [Redis](http://code.google.com/p/redis/)
- [PHP](http://php.net/)
- [PHPUnit](http://www.phpunit.de/)
- [Git](http://git-scm.com/)

## Author ##

- [Daniele Alessandri](mailto:suppakilla@gmail.com)
- Redisql: [Russell Sullivan](mailto:jaksprats@gmail.com)

## Contributors ##

[Lorenzo Castelli](http://github.com/lcastelli)

## License ##

The code for Predis and "Predisql" are distributed under the terms of the MIT license (see LICENSE).
