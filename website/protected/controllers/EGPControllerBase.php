<?php
/*
 * Basic functionality needed for any EGP controller
 * @author eugene
 */

abstract class EGPControllerBase extends CController {

    protected function beforeAction($action) {
        try {
            parent::beforeAction($action);
            $auth = new LearzingAuth();
            $auth->authenticateRequest();
        } catch(InvalidArgumentException $ex) {
            $this->sendBadRequest();
        } catch (Exception $ex) {
            $this->sendInternalError();
        }
        return TRUE;
    }
    /*
     * Call this func when unrecoverable error occured.
     * For example when exception of any origin was caught.
     * @returns: nothing
     * @throws: nothing
     */
    protected abstract function sendInternalError(Exception $exception = NULL);
    /*
     * Call this func when user's arguments are invalid in some way
     * @returns: nothing
     * @throws: nothing
     */
    protected abstract function sendBadRequest(InvalidArgumentException $exception = NULL);
    /*
     * Call this func when user is not authorized to perform requested operation
     * @returns: nothing
     * @throws: nothing
     */
    protected abstract function sendUnAuthorized($message,  $errorCode = NULL);

    /*
     * Sends response with AUTHORIZATION FAILED semantic to client if user is not authentificated
     */
    protected function requireAuthentification()
    {
        if (LearzingAuth::getCurrentAccessToken() == NULL)
            $this->sendUnAuthorized('Current user should be authenticated '
                    . 'to perform requested operation');
    }

    /*
     * Sends response with AUTHORIZATION FAILED semantic to client if user is authentificated
     */
    protected function requireNoAuthentification()
    {
        if (LearzingAuth::getCurrentAccessToken() != NULL)
            $this->sendUnAuthorized("Current user shouldn't be authenticated "
                    . 'to perform requested operation');
    }
}