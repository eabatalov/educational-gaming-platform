#!/usr/bin/python3
from random import randint

INSERT_USER_SQL = """
	INSERT INTO egp.users(name, surname, email, is_active,
		password, role)
	VALUES ($1::varchar, $2::varchar, $3::varchar,
		$4::boolean, $5::varchar, $6::egp.role_t);
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

insert_user_ps = conn.prepare(INSERT_USER_SQL)
insert_friendship_ps = conn.prepare(INSERT_FRIENDSHIP_SQL)
insert_api_client_ps = conn.prepare(INSERT_API_CLIENT_SQL)
insert_api_token_ps = conn.prepare(INSERT_API_TOKEN_SQL)


default_user = { 'name' : 'Name_', 'surname' : 'Surname_',
	'email' : '_Email@example.com', 'is_active' : False,
	'password' : 'Password_', 'role' : 'CUSTOMER'}

CUSTOMER_NUM = 10
FRIEND_MAX_NUM = 10

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
		]
	insert_user_ps(new_user[0],
			new_user[1],
			new_user[2],
			new_user[3],
			new_user[4],
			new_user[5],
			)
print('CREATED USERS')

print('CREATING FRIENDSHIPS')
SERIAL_START = 1
for user_id in range(SERIAL_START, CUSTOMER_NUM + SERIAL_START):
	for friend_num in range(1, randint(0, FRIEND_MAX_NUM)):
		new_friend_id = randint(SERIAL_START, CUSTOMER_NUM)
		if (user_id == new_friend_id):
			continue
		insert_friendship_ps(user_id, new_friend_id)
		insert_friendship_ps(new_friend_id, user_id)
print('CREATED FRIENDSHIPS')

print('CREATING API CLIENTS')
insert_api_client_ps("LearzingTestingAPIClient123456", "Learzing testing")
insert_api_client_ps("8FbuxX7wMSjOJtp4hniVL7QimO7X9r", "Learzing website")
insert_api_client_ps("OlWkSbius0IIx6924BMBg58F38Xea5", "Learzing server backend")
print('CREATED API CLIENTS')

print('CREATING API TOKENS')
insert_api_token_ps("LearzingTestingAPIToken1234567", 1, "LearzingTestingAPIClient123456")
print('CREATED API TOKENS')
