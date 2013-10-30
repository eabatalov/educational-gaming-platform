<?php

/**
 * Basic immutable user description.
 * Can be used, passed everywhere on the
 * platform.
 * To change user's info you need his AuthentificatedUser instance,
 * @author eugene
 */
class User {
    
    function __construct($id, $email, $name, $surname, $isActive,
                            $userDesc, $role) {
        $this->setId($id);
        $this->setEmail($email);
        $this->setName($name);
        $this->setSurname($surname);
        $this->setIsActive($isActive);
        $this->setUserDesc($userDesc);
        $this->setRole($role);
    }

    public function getId() {
        return $this->id;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getName() {
        return $this->name;
    }

    public function getSurname() {
        return $this->surname;
    }

    public function getIsActive() {
        return $this->isActive;
    }

    public function getUserDesc() {
        return $this->userDesc;
    }

    public function getRole() {
        return $this->role;
    }

    public function setId($id) {
        assert(is_numeric($id));
        $this->id = $id;
    }

    protected function setEmail($email) {
        assert(is_string($email));
        $this->email = $email;
    }

    protected function setName($name) {
        assert(is_string($name));
        $this->name = $name;
    }

    protected function setSurname($surname) {
        assert(is_string($surname));
        $this->surname = $surname;
    }

    protected function setIsActive($isActive) {
        $this->isActive = $isActive;
    }

    protected function setUserDesc($userDesc) {
        assert(is_string($userDesc));
        $this->userDesc = $userDesc;
    }

    protected function setRole($role) {
        assert($role instanceof UserRole);
        $this->role = $role;
    }

    //Int
    private $id;
    //Str[50]
    private $email;
    //Str[25]
    private $name;
    //Str[25]
    private $surname;
    //Bool
    private $isActive;
    //Str[500]
    private $userDesc;
    //UserRole
    private $role;    
}