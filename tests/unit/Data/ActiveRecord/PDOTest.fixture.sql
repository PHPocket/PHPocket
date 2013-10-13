CREATE TABLE User(
   id             INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
   name           TEXT    NOT NULL,
   age            INT     NOT NULL,
   address        CHAR(50),
   rating         REAL
);

INSERT INTO `User` (`id`,`name`,`age`,`address`,`rating`) VALUES (1,'User one', 22, 'Some street', 0.22);
INSERT INTO `User` (`name`,`age`,`address`,`rating`) VALUES ('User two', 44, 'NY', 2342.111);

CREATE TABLE StringKey(
  key CHAR(40) PRIMARY KEY     NOT NULL,
  value            INT     NOT NULL
);

INSERT INTO `StringKey` (`key`, `value`) VALUES ('hash', 555);
INSERT INTO `StringKey` (`key`, `value`) VALUES ('foo', 123);