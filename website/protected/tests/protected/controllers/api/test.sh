#!/bin/bash
POST_JSON="curl -v -X POST -d "
GET_JSON="curl -v -X GET -G --data-urlencode "
PUT_JSON="curl -v -X PUT -d "
DELETE_JSON="curl -v -X DELETE -d "
FILTER_RESULT=''
#' 2>&1 | grep result'
HOST="http://localhost:8080/"

cd ../../../../../../setup/
python3 setup.py > /dev/null
cd $PWD

EMAIL="foo@bar.com"
PASSWORD="111111"
USERID="101"

function createUser(){
	$POST_JSON '{ email: "", password: "", request : {user : { id: "NULL", email: "'$EMAIL'",
		name: "Eugene", surname: "Batalov", is_online: true, role: "customer" }, password: "'$PASSWORD'" } }' $HOST\api/user \
		$FILTER_RESULT
}

function testApiUserController(){
	echo
	echo "Test actionRegisterUser with invalid input"
	$POST_JSON '{ email: "", password: "", request : {user : { id: "NULL", email: "foo",
		name: "Eugene", surname: "", is_online: true, role: "customer" }, password: "123" } }' $HOST\api/user

	echo
	echo "Test actionRegisterUser"
	createUser

	echo
	echo "Test actionLoginUser"
	$GET_JSON  'data={ email : "", password : "" , request : { cred : { email : "'$EMAIL'", password : "'$PASSWORD'" } } }' $HOST/api/login

	echo
	echo "Test actionModifyUser"
	$PUT_JSON '{ email : "'$EMAIL'", password : "'$PASSWORD'", request : {user : { id: "'$USERID'", email: "'$EMAIL'",
		name: "Chuck", surname: "Norris", is_online: true, role: "customer" }, password: "'$PASSWORD'" } }' $HOST\api/user

	echo
	echo "Test actionGetUser"
	$GET_JSON 'data={ email : "'$EMAIL'", password : "'$PASSWORD'", request : { userid : "'$USERID'" } }' $HOST/api/user

	echo
	echo "Test actionGetUser on another user"
	$GET_JSON 'data={ email : "'$EMAIL'", password : "'$PASSWORD'", request : { userid : 50 } }' $HOST/api/user

	echo
}

function testApiFriendsController(){
	echo
	createUser
	echo

	echo "Test actionAddFriend"
	$POST_JSON '{ email : "'$EMAIL'", password : "'$PASSWORD'", request : { userid : "51" } }' $HOST/api/friends $FILTER_RESULT
	$POST_JSON '{ email : "'$EMAIL'", password : "'$PASSWORD'", request : { userid : "52" } }' $HOST/api/friends $FILTER_RESULT
	$POST_JSON '{ email : "'$EMAIL'", password : "'$PASSWORD'", request : { userid : "53" } }' $HOST/api/friends $FILTER_RESULT
	$POST_JSON '{ email : "'$EMAIL'", password : "'$PASSWORD'", request : { userid : "54" } }' $HOST/api/friends $FILTER_RESULT

	echo "Test actionDeleteFriend"
	$DELETE_JSON '{ email : "'$EMAIL'", password : "'$PASSWORD'", request : { userid : "53" } }' $HOST/api/friends $FILTER_RESULT

	echo "Test actionGetFriends"
	$GET_JSON 'data={ email : "'$EMAIL'", password : "'$PASSWORD'", request : { userid : "'$USERID'" } }' $HOST/api/friends $FILTER_RESULT

	echo
}

function testApiSearchController(){
	echo "Test actionSearch"

}

#testApiUserController
#testApiFriendsController
testApiSearchController
