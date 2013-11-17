<?php

/**
 * friends API service request handler
 *
 * @author eugene
 */
class ApiFriendsController extends ApiController {
    /*
     * GET ( request : { userid : String } ): get list of friends visible to current user of user with id @userid
	RETURNS: friends: [User]
     */
    public function actionGetFriends() {
        
    }

    /*
     * POST ( request : { userid : String } ): add new friend with id @userid to current user's friend list
     */
    public function actionAddFriend() {
        
    }
}
