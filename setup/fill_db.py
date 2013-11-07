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
insert_user_ps = conn.prepare(INSERT_USER_SQL)
insert_friendship_ps = conn.prepare(INSERT_FRIENDSHIP_SQL)

default_user = { 'name' : 'Name_', 'surname' : 'Surame_',
	'email' : 'Email_', 'is_active' : False,
	'password' : 'Password_', 'role' : 'CUSTOMER'}

CUSTOMER_NUM = 100
FRIEND_MAX_NUM = 10

print('CREATING USER')
for i in range(0, CUSTOMER_NUM):
	suffix = str(i)
	new_user = [
		default_user['name'] + suffix,
		default_user['surname'] + suffix,
		default_user['email'] + suffix,
		default_user['is_active'],
		default_user['password'] + suffix,
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
