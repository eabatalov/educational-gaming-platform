-- WARNING: Due to python db interface limitations add \-\-CMD after each command

DROP SCHEMA IF EXISTS egp CASCADE;
--CMD
CREATE SCHEMA egp;
--CMD
DO
$BODY$
BEGIN
IF NOT EXISTS (SELECT 1 FROM pg_type where typname = 'role_t') THEN
	CREATE TYPE egp.role_t AS ENUM ('CUSTOMER', 'ADMIN', 'ANALYST');
END IF;
END;
$BODY$;
--CMD
CREATE TABLE egp.users (
	id serial8 NOT NULL PRIMARY KEY,
	name varchar(50) NOT NULL,
	surname	varchar(50) NOT NULL,
	email varchar(50) NOT NULL UNIQUE,
	is_active boolean NOT NULL DEFAULT FALSE,
	password varchar(100) NOT NULL,
	role egp.role_t NOT NULL
);
--CMD
CREATE TABLE egp.friendnships (
	requestor int8 NULL,
	acceptor int8 NULL
);
--CMD
ALTER TABLE egp.friendnships
	ADD CONSTRAINT "fk_friendships_users_requestor"
	FOREIGN KEY("requestor")
	REFERENCES egp.users("id");
--CMD
ALTER TABLE egp.friendnships
	ADD CONSTRAINT "fk_friendships_users_acceptor"
	FOREIGN KEY("acceptor")
	REFERENCES egp.users("id");
--CMD
-- Hybrid authentification table
-- Maps (loginprovider, loginproviderIdentity) -> userId from egp.users
CREATE TABLE  egp.ha_logins (
	id serial NOT NULL PRIMARY KEY,
	loginprovider character varying(50) NOT NULL,
	loginprovideridentifier character varying(102) NOT NULL,
	userid int8 NOT NULL
);
--CMD
ALTER TABLE egp.ha_logins
	ADD CONSTRAINT "fk_ha_logins_userId"
	FOREIGN KEY("userid")
	REFERENCES egp.users("id");
--CMD
