create table city
(
    id      int auto_increment
        primary key,
    name    varchar(255) not null,
    `index` int          not null
);

create table user
(
    id       int auto_increment
        primary key,
    name     varchar(255) not null,
    surname  varchar(255) not null,
    cityid   int          not null,
    filename varchar(255) not null
);
