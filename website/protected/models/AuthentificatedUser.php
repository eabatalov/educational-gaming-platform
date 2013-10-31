<?php

/**
 * This user was authorized on server and is current user,
 * Beeing him we are able to change info and set/get password.
 * @author eugene
 */
class AuthentificatedUser extends User {
    
    /*
     * @param id: optional, used for internal data provider and testing purposes
     */
    public function __construct($email, $name, $surname, $isActive,
                            $userDesc, $role, $password, $id = NULL) {
        parent::__construct($email, $name, $surname, $isActive,
                            $userDesc, $role, $id);
        $this->dsChangeTracking();
        $this->setPassword($password);
        $this->enChangeTracking();
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        assert(is_string($password));
        $this->valueChanged("password", $this->password, $password);
        $this->password = $password;
    }

    public function setEmail($email) {
        parent::setEmail($email);
    }

    public function setIsActive($isActive) {
        parent::setIsActive($isActive);
    }

    public function setName($name) {
        parent::setName($name);
    }

    public function setRole($role) {
        parent::setRole($role);
    }

    public function setSurname($surname) {
        parent::setSurname($surname);
    }

    public function setDescription($userDesc) {
        parent::setDescription($userDesc);
    }

    //Str[100]
    private $password;
}
