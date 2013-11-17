<?php

/**
 * messaging API service request handler
 *
 * @author eugene
 */
class ApiMessagingController extends ApiController {
    /*
     * Javascript types:
        Message: { from: userid, to: userid, date: string/date JS type, time: string/time JS type: body: string }
     */
    /*GET ( request : { userid : String, start : String, end: String } ):
        get list of messages with sequential number from @start to @end in conversation of current user and user with id @userid.
	@start: message sequential number starting from 1
	@end: message sequential number
		You can use 'LAST-x' value for @start and @end, where x is some number to perform selection of messages relative to the last message.
	RETURNS: messages: [Message]
     *
     */
    public function actionGetMessages() {
        
    }
    /*
     * POST ( request : { userid : String, text : String } ): send message with body @text from current user to user with id @userid
     */
    public function actionSendMessage() {
        
    }
}
