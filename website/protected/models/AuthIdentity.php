<?php

/**
 * Description of AuthIdentity
 *
 * @author eugene
 */
class AuthIdentity extends EGPIdentityBase {

    public function __construct($userEmail, $userPassword) {
        parent::__construct();
        $this->email = $userEmail;
        $this->password  =$userPassword;
    }

    public function authenticate() {
        if (parent::authenticate())
            return TRUE;

        try {
            $userStorage = new PostgresUserStorage();
            $authUser = $userStorage->getAuthentificatedUser($this->email, $this->password);
            $this->setAuthUser($authUser);
        } catch(Exception $ex) {
            if ($ex instanceof InvalidArgumentException) {
                if (isset(self::$EXCEPTION_CODE_MAP[$ex->getCode()])) {
                    $this->errorCode = self::$EXCEPTION_CODE_MAP[$ex->getCode()]['code'];
                    $this->errorMessage = self::$EXCEPTION_CODE_MAP[$ex->getCode()]['text'];
                } else {
                    $this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
                    $this->errorMessage = 'Unknown identification error';
                }
            }
        }
        return $this->errorCode == self::ERROR_NONE;
    }

    private $email;
    private $password;
    private static $EXCEPTION_CODE_MAP = array(
        IUserStorage::ERROR_INVALID_PASSWORD =>
            array('code' => self::ERROR_PASSWORD_INVALID, 'text' => 'Invalid email or password'),
        IUserStorage::ERROR_NO_USER_WITH_SUCH_EMAIL =>
            array('code' => self::ERROR_UNKNOWN_IDENTITY, 'text' => 'Invalid email or password'));
}