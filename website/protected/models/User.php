<?php

/**
 * Basic immutable user description.
 * Can be used, passed everywhere on the
 * platform.
 * To change user's info you need his AuthentificatedUser instance,
 * @author eugene
 */
class User extends ModelObject {

    /*
    * @arg id: optional,
    * almost for internal data provider usage and for testing purposes
    */
    public function __construct($email, $name, $surname, $isActive,
                            $userDesc, $role, $id = NULL) {
        parent::__construct();
        $this->dsChangeTracking();
        if ($id != NULL) {
            $this->setId($id);
        }
        $this->setEmail($email);
        $this->setName($name);
        $this->setSurname($surname);
        $this->setIsActive($isActive);
        $this->setDescription($userDesc);
        $this->setRole($role);
        $this->enChangeTracking();
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

    public function getDescription() {
        return $this->description;
    }

    public function getRole() {
        return $this->role;
    }

    private function setId($id) {
        assert(is_numeric($id));
        $this->valueChanged(self::CH_ID, $this->id, $id);
        $this->id = $id;
    }

    protected function setEmail($email) {
        assert(is_string($email));
        $this->valueChanged(self::CH_EMAIL, $this->email, $email);
        $this->email = $email;
    }

    protected function setName($name) {
        assert(is_string($name));
        $this->valueChanged(self::CH_NAME, $this->name, $name);
        $this->name = $name;
    }

    protected function setSurname($surname) {
        assert(is_string($surname));
        $this->valueChanged(self::CH_SURNAME, $this->surname, $surname);
        $this->surname = $surname;
    }

    protected function setIsActive($isActive) {
        $this->valueChanged(self::CH_ISACTIVE, $this->isActive, $isActive);
        $this->isActive = $isActive;
    }

    protected function setDescription($userDesc) {
        assert(is_string($userDesc));
        $this->valueChanged(self::CH_DESCR, $this->description, $userDesc);
        $this->description = $userDesc;
    }

    protected function setRole($role) {
        assert($role instanceof UserRole);
        $this->valueChanged(self::CH_ROLE, $this->role, $role);
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
    private $description;
    //UserRole
    private $role;
    //ModelObject constants for changes supply
    const CH_ID = 1;
    const CH_EMAIL = 2;
    const CH_NAME = 3;
    const CH_SURNAME = 4;
    const CH_ISACTIVE = 5;
    const CH_DESCR = 6;
    const CH_ROLE = 7;
    const CH_LAST = 8;
}