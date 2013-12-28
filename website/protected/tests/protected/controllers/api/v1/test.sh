#!/bin/bash
POST_JSON="curl -v -X POST -d"
GET_JSON="curl -v -X GET -G --data-urlencode "
PUT_JSON="curl -v -X PUT -d "
DELETE_JSON="curl -v -X DELETE -d "
#' 2>&1 | grep result'
HOST="http://localhost:8080/"

cd ../../../../../../../setup/
python3 setup.py > /dev/null
cd $PWD

EMAIL="foo@bar.com"
PASSWORD="111111"
USERID="11"
LEARZING_TEST_CLIENT_ID="LearzingTestingAPIClient123456"
LEARZING_TEST_ACCESS_TOKEN="LearzingTestingAPIToken1234567"
TOKEN_URL=$HOST/auth/token

function createUser(){
	$POST_JSON '{ user : { id: "NULL", email: "'$EMAIL'",
		name: "Eugene", surname: "Batalov", is_online: true, role: "customer" }, password: "'$PASSWORD'" }' $HOST\api/user

	ACCESS_TOKEN=$(
	curl -X GET -G --data-urlencode 'request={email: "'$EMAIL'", password: "'$PASSWORD'", client_id: "'$LEARZING_TEST_CLIENT_ID'"}' $TOKEN_URL |\
		egrep -o 'access_token":".+",' | sed 's/access_token":"//' | sed 's/"//' | sed 's/,//')
	echo "Got ACCESS_TOKEN of new user:" $ACCESS_TOKEN
	echo
	AUTH_HEADER='-H Authorization:Bearer'$ACCESS_TOKEN
}

function testApiUserController(){
	echo
	echo "Test actionRegisterUser"
	createUser

	echo
	echo "Test actionRegisterUser with invalid input"
	$POST_JSON '{ user : { id: "NULL", email: "foo",
		name: "Eugene", surname: "", is_online: true, role: "customer" }, password: "123" }' $HOST\api/user

	echo
	echo "Test actionModifyUser"
	$PUT_JSON '{ user : { id: "'$USERID'", email: "'$EMAIL'",
		name: "Chuck", surname: "Norris", is_online: true, role: "customer" }, password: "'$PASSWORD'" }' $AUTH_HEADER $HOST\api/user

	echo
	echo "Test actionGetUser"
	$GET_JSON 'request={ userid : "'$USERID'" }' $AUTH_HEADER $HOST/api/user

	echo
	echo "Test actionGetUser on another user"
	$GET_JSON 'request={ userid : 2, fields: ["name", "role", "email", "is_online"] }' $AUTH_HEADER $HOST/api/user

	echo
}

function testApiFriendsController(){
	echo
	createUser
	echo
	echo "Test actionAddFriend"
	$POST_JSON '{ userid : "1" }' $AUTH_HEADER $HOST/api/friends
	$POST_JSON '{ userid : "2" }' $AUTH_HEADER $HOST/api/friends
	$POST_JSON '{ userid : "3" }' $AUTH_HEADER $HOST/api/friends
	$POST_JSON '{ userid : "4" }' $AUTH_HEADER $HOST/api/friends

	echo
	echo "Test actionDeleteFriend"
	$DELETE_JSON '{ userid : "3" }' $AUTH_HEADER $HOST/api/friends

	echo
	echo "Test actionGetFriends"
	$GET_JSON 'request={ userid : "'$USERID'", fields : ["email"] }' $AUTH_HEADER $HOST/api/friends

	echo
	echo "Test actionGetFriends paginated"
	$GET_JSON 'request={ userid : "'$USERID'", paging : { offset: 0, limit: 2, total: 4} }' $AUTH_HEADER $HOST/api/friends
	echo
}

function testApiSearchController(){
	echo
	createUser
	echo
	echo "Test actionSearch"
	$GET_JSON 'request={ query : "'$EMAIL'", object_type : "all" }' $AUTH_HEADER $HOST/api/search
	echo
	echo "Test actionSearch with BIG result"
	$GET_JSON 'request={ query : "Surname_", object_type : "all" }' $AUTH_HEADER $HOST/api/search
	echo
	echo "Test actionSearch with BIG result and pagination"
	$GET_JSON 'request={ query : "Surname_", object_type : "all", paging : {offset : "8", limit : 2} }' $AUTH_HEADER $HOST/api/search

}

#Only one should be uncommented at the same time
#testApiUserController
#testApiFriendsController
testApiSearchController
