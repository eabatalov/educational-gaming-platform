#!/bin/bash
CURL="curl -v"
POST_JSON=$CURL" -X POST -d "
GET_JSON=$CURL" -X GET -G --data-urlencode "
PUT_JSON=$CURL"-X PUT -d "
DELETE_JSON=$CURL" -X DELETE "
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

function createUser() {
	$POST_JSON '{ user : { id: "NULL", email: "'$EMAIL'",
		name: "Eugene", surname: "Batalov", is_online: true, role: "customer" }, password: "'$PASSWORD'" }' $HOST\api/user
}

function testGetToken() {

	echo
	echo "Testing action auth/token GET"
	$GET_JSON 'request={email: "'$EMAIL'", password: "'$PASSWORD'", client_id: "'$LEARZING_TEST_CLIENT_ID'"}' $TOKEN_URL
	echo
	echo "Testing action auth/token with GET absent client_id"
	$GET_JSON 'request={email: "'$EMAIL'", password: "'$PASSWORD'" }' $TOKEN_URL
	echo
	echo "Testing action auth/token GET with invalid client_id"
	$GET_JSON 'request={email: "'$EMAIL'", password: "'$PASSWORD'", client_id: "invalidclientid"}' $TOKEN_URL
}

function testDeleteToken() {
	AUTH_HEADER='-H Authorization:Bearer'$LEARZING_TEST_ACCESS_TOKEN
	echo $AUTH_HEADER

	echo
	echo "Testing action auth/token DELETE"
	$DELETE_JSON $AUTH_HEADER -d '{access_token: "'$LEARZING_TEST_ACCESS_TOKEN'", client_id: "'$LEARZING_TEST_CLIENT_ID'"}' $TOKEN_URL

	echo
	echo "Testing action auth/token DELETE with invalid double deletion"
	$DELETE_JSON $AUTH_HEADER -d '{access_token: "'$LEARZING_TEST_ACCESS_TOKEN'", client_id: "'$LEARZING_TEST_CLIENT_ID'"}' $TOKEN_URL

	echo
	echo "Testing action auth/token DELETE without authorization"
	$DELETE_JSON -d '{access_token: "'$LEARZING_TEST_ACCESS_TOKEN'", client_id: "'$LEARZING_TEST_CLIENT_ID'"}' $TOKEN_URL
}
createUser
testGetToken
testDeleteToken
