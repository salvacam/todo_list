create database todo_list default character set utf8 collate utf8_unicode_ci;
grant all on todo_list.* to user@localhost identified by 'pass';
flush privileges;

use todo_list;

CREATE TABLE IF NOT EXISTS todo_task (
    id int(11) NOT NULL primary key auto_increment,
    texto varchar(100) NOT NULL,
    estado int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB collate utf8_unicode_ci;
