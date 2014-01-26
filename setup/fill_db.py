#!/usr/bin/python3
from random import randint
from datetime import date

INSERT_USER_SQL = """
	INSERT INTO egp.users(name, surname, email, is_active,
		password, role, avatar, birthday, gender)
	VALUES ($1::varchar, $2::varchar, $3::varchar,
		$4::boolean, $5::varchar, $6::egp.role_t, $7::varchar,
		$8::date, $9::egp.gender_t);
	"""

INSERT_FRIENDSHIP_SQL = """
	INSERT INTO egp.friendnships(requestor, acceptor)
		VALUES ($1::bigint, $2::bigint);
	"""

INSERT_API_CLIENT_SQL = """
	INSERT INTO egp.api_clients(global_id, name)
		VALUES ($1::char(30), $2::varchar);
	"""

INSERT_API_TOKEN_SQL = """
	INSERT INTO egp.api_access_tokens
		(access_token, user_id, client_id)
	VALUES ($1::varchar, $2::bigint, (SELECT id from egp.api_clients WHERE global_id=$3::varchar));
	"""

INSERT_SKILL_SQL = """
	INSERT INTO egp.skills
		(name, parent_skill, is_leaf)
	VALUES ($1::varchar, $2::int, $3::bool);
	"""

INSERT_USER_SKILL_SQL = """
	INSERT INTO egp.user_skills
		(user_id, skill_id, value)
	VALUES ($1::int, $2::int, $3::int);
	"""

insert_user_ps = conn.prepare(INSERT_USER_SQL)
insert_friendship_ps = conn.prepare(INSERT_FRIENDSHIP_SQL)
insert_api_client_ps = conn.prepare(INSERT_API_CLIENT_SQL)
insert_api_token_ps = conn.prepare(INSERT_API_TOKEN_SQL)
insert_skill_ps = conn.prepare(INSERT_SKILL_SQL);
insert_user_skill_ps = conn.prepare(INSERT_USER_SKILL_SQL);


default_user = { 'name' : 'Name_', 'surname' : 'Surname_',
	'email' : '_Email@example.com', 'is_active' : False,
	'password' : 'Password_', 'role' : 'CUSTOMER',
	'avatar' : None, 'birthday' : None, 'gender' : None }

CUSTOMER_NUM = 10
FRIEND_MAX_NUM = 10
SERIAL_START = 1

print('CREATING USER')
for i in range(0, CUSTOMER_NUM):
	unique = str(i)
	new_user = [
		default_user['name'] + unique,
		default_user['surname'] + unique,
		unique + default_user['email'],
		default_user['is_active'],
		default_user['password'] + unique,
		default_user['role'],
		default_user['avatar'],
		default_user['birthday'],
		default_user['gender'],
		]
	insert_user_ps(new_user[0],
			new_user[1],
			new_user[2],
			new_user[3],
			new_user[4],
			new_user[5],
			new_user[6],
			new_user[7],
			new_user[8],
			)
print('CREATED USERS')

print('CREATING FRIENDSHIPS')
friendships = []
for user_id in range(SERIAL_START, CUSTOMER_NUM + SERIAL_START):
	for friend_num in range(1, randint(0, FRIEND_MAX_NUM)):
		new_friend_id = randint(SERIAL_START, CUSTOMER_NUM)
		if (user_id == new_friend_id):
			continue
		friendships += [(user_id, new_friend_id), (new_friend_id, user_id)]

for (uid1, uid2) in set(friendships):
		insert_friendship_ps(uid1, uid2)
print('CREATED FRIENDSHIPS')

print('CREATING API CLIENTS')
insert_api_client_ps("LearzingTestingAPIClient123456", "Learzing testing")
insert_api_client_ps("8FbuxX7wMSjOJtp4hniVL7QimO7X9r", "Learzing website")
insert_api_client_ps("OlWkSbius0IIx6924BMBg58F38Xea5", "Learzing server backend")
insert_api_client_ps("fbf4aRfDx88dnvIdwwavX3C5EVH06c", "English idioms game")
print('CREATED API CLIENTS')

print('CREATING API TOKENS')
insert_api_token_ps("LearzingTestingAPIToken1234567", 1, "LearzingTestingAPIClient123456")
print('CREATED API TOKENS')

print('CREATING SKILLS')
# TODO It is better to form a tree of skills in memory and then dump it as table rows
LEAF_SKILL_IDS = []
insert_skill_ps('Languages', None, False)
PREV_SKILL_ID = SERIAL_START
insert_skill_ps('English', PREV_SKILL_ID, False)
PREV_SKILL_ID += 1
ENGLISH_SKILL_ID = PREV_SKILL_ID
insert_skill_ps('Idioms', ENGLISH_SKILL_ID, True)
PREV_SKILL_ID += 1
LEAF_SKILL_IDS += [PREV_SKILL_ID]
insert_skill_ps('Slang', ENGLISH_SKILL_ID, True)
PREV_SKILL_ID += 1
LEAF_SKILL_IDS += [PREV_SKILL_ID]
insert_skill_ps('Vocabulary', ENGLISH_SKILL_ID, True)
PREV_SKILL_ID += 1
LEAF_SKILL_IDS += [PREV_SKILL_ID]
print('CREATED SKILLS')

print('CREATING USER SKILLS')
LEAF_SKILLS_LAST_IX = len(LEAF_SKILL_IDS) - 1
user_skills = []
for user_id in range(SERIAL_START, CUSTOMER_NUM + SERIAL_START):
	for skill_num in range(0, randint(1, len(LEAF_SKILL_IDS))):
		skill_id = LEAF_SKILL_IDS[randint(0, LEAF_SKILLS_LAST_IX)]
		user_skills += [(user_id, skill_id)]

for (user_id, skill_id) in set(user_skills):
	skill_value = randint(0, 100)
	insert_user_skill_ps(user_id, skill_id, skill_value)
print('CREATED USER SKILLS')
