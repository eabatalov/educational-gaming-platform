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
    * Fabric method. Use as constructor.
    * @arg id: is optional and
    *   almost for internal data provider usage and for testing purposes
    */
    static public function createUInstance($email, $name, $surname, $isActive,
                            $role, $id = NULL) {
        return new User(FALSE, $email, $name, $surname, $isActive, $role, $id);
    }
 
    /*
     * Overrides corresponding ModelObject method
     */
    static public function createEmpty() {
        return new User(TRUE);
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

    public function getDisplayName() {
        return $this->getName() . " " . $this->getSurname();
    }

    public function getIsActive() {
        return $this->isActive;
    }

    public function getRole() {
        return $this->role;
    }

    private function setId($id) {
        TU::throwIfNot(is_numeric($id), TU::INVALID_ARGUMENT_EXCEPTION);
        $this->valueChanged(self::CH_ID, $this->id, $id);
        $this->id = $id;
    }

    protected function setEmail($email) {
        TU::throwIfNot(is_string($email), TU::INVALID_ARGUMENT_EXCEPTION);
        $this->valueChanged(self::CH_EMAIL, $this->email, $email);
        $this->email = $email;
    }

    protected function setName($name) {
        TU::throwIfNot(is_string($name), TU::INVALID_ARGUMENT_EXCEPTION);
        $this->valueChanged(self::CH_NAME, $this->name, $name);
        $this->name = $name;
    }

    protected function setSurname($surname) {
        TU::throwIfNot(is_string($surname), TU::INVALID_ARGUMENT_EXCEPTION);
        $this->valueChanged(self::CH_SURNAME, $this->surname, $surname);
        $this->surname = $surname;
    }

    protected function setIsActive($isActive) {
        TU::throwIfNot(is_bool($isActive), TU::INVALID_ARGUMENT_EXCEPTION);
        $this->valueChanged(self::CH_ISACTIVE, $this->isActive, $isActive);
        $this->isActive = $isActive;
    }

    protected function setRole($role) {
        TU::throwIfNot(is_string($role), TU::INVALID_ARGUMENT_EXCEPTION);
        TU::throwIfNot(in_array($role, UserRole::$ROLES), TU::INVALID_ARGUMENT_EXCEPTION);
        $this->valueChanged(self::CH_ROLE, $this->role, $role);
        $this->role = $role;
    }

    static private $validationRules = array(
            array('email, name, surname, role', 'required'),
            //value checking
            array('email, name, surname', 'length', 'min' => 1, 'max' => 50, 'encoding' => 'utf-8'),
            array('email', 'email'),
    );

    public function rules() {
        return self::$validationRules;
    }

    //public just because we can't hide constructor if it was public in one of parent classes
    public function __construct($mkEmpty, $email = NULL, $name = NULL, $surname = NULL,
                                $isActive = NULL, $role = NULL, $id = NULL) {
        assert(is_bool($mkEmpty));
        if (!$mkEmpty) {
            parent::__construct();
            $this->dsChangeTracking();
            if ($id != NULL) {
                $this->setId($id);
            }
            $this->setEmail($email);
            $this->setName($name);
            $this->setSurname($surname);
            $this->setIsActive($isActive);
            $this->setRole($role);
            $this->enChangeTracking();
        }
    }

    public function setAttributes($values, $safeOnly = true) {
        if (isset($values["id"])) {
            TU::throwIf($values["id"] != $this->id, TU::INVALID_ARGUMENT_EXCEPTION,
                    "User id cannot be changed");
        }
        parent::setAttributes($values, $safeOnly);
    }

    //Int
    private $id;
    //Str[50]
    private $email;
    //Str[50]
    private $name;
    //Str[50]
    private $surname;
    //Bool
    private $isActive;
    //int
    private $role;
    //ModelObject constants for changes supply
    const CH_ID = 1;
    const CH_EMAIL = 2;
    const CH_NAME = 3;
    const CH_SURNAME = 4;
    const CH_ISACTIVE = 5;
    const CH_ROLE = 6;
    const CH_LAST = 7;
}