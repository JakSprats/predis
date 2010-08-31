/* global */
create table zset_archive_meta (name varchar(64) primary key, most_recent timestamp NOT NULL, least_recent timestamp NOT NULL);

/* for table "tweets" */
create table tweets_archive(id int primary key auto_increment, user_id int NOT NULL, score timestamp NOT NULL, tweets text NOT NULL);
