DROP TABLE IF EXISTS host;
CREATE TABLE  IF NOT EXISTS host (
         id int(11) NOT NULL auto_increment,
	 mac varchar(17) NOT NULL UNIQUE,
	 macname varchar(100) NULL,
	 device varchar(100) NULL,
	 os varchar(500) NULL,
         PRIMARY KEY  (id))  ENGINE=InnoDB;
DROP TABLE IF EXISTS port;
CREATE TABLE IF NOT EXISTS port (
	 id int(11) NOT NULL auto_increment,
	 host_id int(11) NOT NULL,
	 date date NOT NULL,
	 hostname varchar(200) NULL,
	 ip varchar(15) NULL,
	 port smallint UNSIGNED NOT NULL,
	 protocol varchar(3) NOT NULL,
	 state varchar(10) NOT NULL,
	 service varchar(20) NOT NULL,
	 version varchar(100) NULL,
	 scantime int(11) NULL,
	 PRIMARY KEY (id)) ENGINE=InnoDB;
DROP TABLE IF EXISTS scan_log;
CREATE TABLE IF NOT EXISTS scan_log (
	 id int(11) NOT NULL auto_increment,
	 host_id int(11) NOT NULL,
	 date date NOT NULL,
	 ip varchar(15) NOT NULL,
	 PRIMARY KEY (id)) ENGINE=InnoDB;
