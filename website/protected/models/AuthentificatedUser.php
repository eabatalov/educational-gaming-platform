<?php

/**
 * This user was authorized on server and is current user,
 * Beeing him we are able to change info and set/get password.
 * @author eugene
 */
class AuthentificatedUser extends User {
    
    function __construct($id, $email, $name, $surname, $isActive,
                            $userDesc, $role, $password) {
        parent::__construct($id, $email, $name, $surname, $isActive,
                            $userDesc, $role);
        $this->setPassword($password);
    }

    public function getPassword() {
        return $this->password;
    }

    protected function setEmail($email) {
        parent::setEmail($email);
    }

    protected function setIsActive($isActive) {
        parent::setIsActive($isActive);
    }

    protected function setName($name) {
        parent::setName($name);
    }

    protected function setPassword($password) {
        assert(is_string($password));
        $this->password = $password;
    }

    protected function setRole($role) {
        parent::setRole($role);
    }

    protected function setSurname($surname) {
        parent::setSurname($surname);
    }

    protected function setUserDesc($userDesc) {
        parent::setUserDesc($userDesc);
    }

    //Str[100]
    private $password;
}
