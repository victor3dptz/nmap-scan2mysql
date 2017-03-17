DROP TABLE IF EXISTS host;
CREATE TABLE  IF NOT EXISTS host (
         id int(11) NOT NULL auto_increment,
	 date date NOT NULL,
	 time time NOT NULL,
	 ip varchar(15) NOT NULL,
	 mac varchar(17) NULL,
	 device varchar(100) NULL,
	 os varchar(500) NULL,
	 scantime int NULL,
         PRIMARY KEY  (id))  ENGINE=InnoDB;
DROP TABLE IF EXISTS port;
CREATE TABLE IF NOT EXISTS port (
	 pid int(11) NOT NULL auto_increment,
	 id int(11) NOT NULL,
	 port smallint UNSIGNED NOT NULL,
	 protocol varchar(3) NOT NULL,
	 state varchar(10) NOT NULL,
	 service varchar(20) NOT NULL,
	 version varchar(100) NULL,
	 PRIMARY KEY (pid)) ENGINE=InnoDB;
