#!/bin/bash
POST_JSON="curl -v -X POST -d "
GET_JSON="curl -v -X GET "
PUT_JSON="curl -v -X PUT -d "
DELETE_JSON="curl -v -X DELETE -d "
HOST="http://localhost:8080/"

cd ../../../../../../setup/
python3 setup.py > /dev/null
cd $PWD

EMAIL="foo@bar.com"
PASSWORD="111111"

#echo
#echo "Test actionRegisterUser with invalid input"
#$POST_JSON '{ email: "", password: "", request : {user : { id: "NULL", email: "foo",
#	name: "Eugene", surname: "", is_online: true, role: "customer" }, password: "123" } }' $HOST\api/user

echo
echo "Test actionRegisterUser"
$POST_JSON '{ email: "", password: "", request : {user : { id: "NULL", email: "'$EMAIL'",
	name: "Eugene", surname: "Batalov", is_online: true, role: "customer" }, password: "'$PASSWORD'" } }' $HOST\api/user

echo
echo "Test actionLoginUser"
$GET_JSON -G --data-urlencode 'data={ email : "", password : "" , request : { cred : { email : "'$EMAIL'", password : "'$PASSWORD'" } } }' $HOST/api/login

echo
echo "Test actionModifyUser"
$PUT_JSON '{ email : "'$EMAIL'", password : "'$PASSWORD'", request : {user : { id: "101", email: "'$EMAIL'",
	name: "Chuck", surname: "Norris", is_online: true, role: "customer" }, password: "'$PASSWORD'" } }' $HOST\api/user

echo
echo "Test actionGetUser"
$GET_JSON -G --data-urlencode 'data={ email : "'$EMAIL'", password : "'$PASSWORD'", request : { userid : 101 } }' $HOST/api/user

echo
echo "Test actionGetUser on another user"
$GET_JSON -G --data-urlencode 'data={ email : "'$EMAIL'", password : "'$PASSWORD'", request : { userid : 50 } }' $HOST/api/user

echo
