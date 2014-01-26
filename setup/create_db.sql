-- WARNING: Due to python db interface limitations add \-\-CMD after each command

DROP SCHEMA IF EXISTS egp CASCADE;
--CMD
CREATE SCHEMA egp;
--CMD
--CREATE EXTENSION "uuid-ossp" WITH SCHEMA egp;
--CMD
DO
$BODY$
BEGIN
IF NOT EXISTS (SELECT 1 FROM pg_type where typname = 'role_t') THEN
	CREATE TYPE egp.role_t AS ENUM ('CUSTOMER', 'ADMIN', 'ANALYST');
END IF;
IF NOT EXISTS (SELECT 1 FROM pg_type where typname = 'gender_t') THEN
	CREATE TYPE egp.gender_t AS ENUM ('MALE', 'FEMALE');
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
	role egp.role_t NOT NULL,
	-- name of avatar picture
	avatar varchar NULL,
	birthday date NULL,
	gender egp.gender_t NULL
);
--CMD
CREATE TABLE egp.friendnships (
	requestor int8 NOT NULL,
	acceptor int8 NOT NULL,
	CONSTRAINT pk_friendship PRIMARY KEY (requestor, acceptor)
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
CREATE TABLE egp.api_clients
(
	id serial NOT NULL PRIMARY KEY,
	global_id char(30) NOT NULL UNIQUE,
	name varchar(50) NOT NULL
);
--CMD
CREATE TABLE egp.api_access_tokens
(
	access_token char(30) NOT NULL PRIMARY KEY, -- The actual value of access token
	user_id int8 NOT NULL, -- User which this token authentificates
	client_id integer NOT NULL,
	CONSTRAINT fk_api_access_tokens_clientid FOREIGN KEY (client_id)
	REFERENCES egp.api_clients (id),
	CONSTRAINT fk_api_access_tokens_userid FOREIGN KEY (user_id)
	REFERENCES egp.users (id)
);
--CMD
--Handles all the skills known to platform and maintains them in tree form
CREATE TABLE egp.skills
(
	id serial NOT NULL PRIMARY KEY,
	name varchar,
	parent_skill int,
	is_leaf boolean NOT NULL DEFAULT FALSE,
	CONSTRAINT fk_skills_parent_id_skill_id FOREIGN KEY (parent_skill)
	REFERENCES egp.skills (id)
);
-- Stores user skills
--CMD
CREATE TABLE egp.user_skills
(
	user_id int NOT NULL,
	skill_id int NOT NULL,
	value int NOT NULL DEFAULT 0,
	CONSTRAINT pk_user_skills PRIMARY KEY (user_id, skill_id),
	CONSTRAINT fk_user_skills_skill_id FOREIGN KEY (skill_id)
	REFERENCES egp.skills (id),
	CONSTRAINT fk_user_skills_user_id FOREIGN KEY (user_id)
	REFERENCES egp.users (id)
);
--CMD
