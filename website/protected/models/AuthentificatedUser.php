<?php

/**
 * This user was authorized on server and is current user,
 * Beeing him we are able to change info and set/get password.
 * @author eugene
 */
class AuthentificatedUser extends User {
    
    /*
     * Fabric method. Use as constructor.
     * @param id: optional, used for internal data provider and testing purposes
     */
    static public function createInstance($email, $name, $surname, $isActive,
                            $role, $password, $id = NULL) {
        return new AuthentificatedUser(FALSE, $email, $name, $surname, $isActive,
                            $role, $password, $id);
    }

    /*
     * Overrides corresponding ModelObject method
     */
    static public function createEmpty() {
        //All fields are NULL
        return new AuthentificatedUser(TRUE);
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        TU::throwIfNot(is_string($password), TU::INVALID_ARGUMENT_EXCEPTION);
        $this->valueChanged(self::CH_PASS, $this->password, $password);
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

    /*
     * @returns: User object which represents the same user
     */
    public function getUser() {
        return User::createInstance(
                $this->getEmail(),
                $this->getName(),
                $this->getSurname(),
                $this->getIsActive(),
                $this->getRole(),
                $this->getId()
        );
    }

    public function rules() {
        $rules = parent::rules();
        array_push($rules,
            array("password" ,'required'),
            array('password', 'length', 'min' => 6, 'max' => 100,
                'encoding' => 'utf-8')
            //array('password_repeat', 'required', 'on'=>'register'),
            //array('password', 'compare', 'compareAttribute'=>'password_repeat', 'on'=>'register'),
        );
        return $rules;
    }

    //public just because we can't hide constructor if it was public in one of parent classes
    public function __construct($mkEmpty, $email = NULL, $name = NULL, $surname = NULL,
                            $isActive = NULL, $role = NULL, $password = NULL, $id = NULL) {
        assert(is_bool($mkEmpty));
        if (!$mkEmpty) {
            parent::__construct(FALSE, $email, $name, $surname, $isActive,
                            $role, $id);
            $this->dsChangeTracking();
            $this->setPassword($password);
            $this->enChangeTracking();
        }
    }

    //Str[100]
    //TODO add more rules to password validation, add password confirmation
    private $password;
    //ModelObject constants for changes supply
    const CH_PASS = parent::CH_LAST;
}
