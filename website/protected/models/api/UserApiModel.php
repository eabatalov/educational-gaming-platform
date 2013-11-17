<?php

/**
 * Adaptor from/to JSON API User object to User model object
 *
 * @author eugene
 */
class UserApiModel {

    /*
     * @throws: InvalidArgumentException if very basic validation has failed
     */
    public function initFromUser(User $user) {
        $this->id = $user->getId();
        $this->email = $user->getEmail();
        $this->name = $user->getName();
        $this->surname = $user->getSurname();
        $this->is_online = self::toAPIIsOnline($user->getIsActive());
        $this->role = self::toAPIRole($user->getRole());
    }

    /*
     * @throws: InvalidArgumentException if very basic validation has failed
     */
    public function initFromArray($fieldArray) {
        $this->id = TU::getValueOrThrow("id", $fieldArray);
        $this->email = TU::getValueOrThrow("email", $fieldArray);
        $this->name = TU::getValueOrThrow("name", $fieldArray);
        $this->surname = TU::getValueOrThrow("surname", $fieldArray);
        $this->is_online = TU::getValueOrThrow("is_online", $fieldArray);
        $this->role = TU::getValueOrThrow("role", $fieldArray);
    }

    /*
     * @throws: InvalidArgumentException if very basic validation has failed
     */
    public function toUser() {
        return User::createUInstance(
                $this->email,
                $this->name,
                $this->surname,
                self::fromAPIIsOnline($this->is_online),
                self::fromAPIRole($this->role),
                $this->id
        );
    }

    /*
     * @throws: InvalidArgumentException if very basic validation has failed
     */
    public function toArray() {
        $user = $this->toUser();
        return array(
                "email" => $user->getEmail(),
                "name" => $user->getName(),
                "surname" => $user->getSurname(),
                "isActive" => $user->getIsActive(),
                "role" => $user->getRole(),
                "id" => $user->getId(),
        );
    }

    /* Fields are public to be serialized to JSON.
     * Field names conform EGP API spec */
    public $id;
    public $email;
    public $name;
    public $surname;
    public $is_online;
    public $role;

    /*
     * API spec values conversion and validation
     * @throws: InvalidArgumentException is $value has invalid value
     */
    protected static function toAPIRole($value) {
        if ($value == UserRole::CUSTOMER)
            return "customer";
        self::throwInvalidConversionError($value, "role", TRUE);
    }

    protected static function fromAPIRole($value) {
        if ($value == "customer")
            return UserRole::CUSTOMER;
        self::throwInvalidConversionError($value, "role", FALSE);
    }

    protected static function toAPIIsOnline($value) {
        if ($value == TRUE)
            return "true";
        if ($value == FALSE)
            return "false";
        self::throwInvalidConversionError($value, "is_online", TRUE);
    }

    protected static function fromAPIIsOnline($value) {
        if ($value == "true")
            return TRUE;
        if ($value == "false")
            return FALSE;
        self::throwInvalidConversionError($value, "is_online", FALSE);
    }

    protected static function throwInvalidConversionError($value, $field, $toApi) {
        throw new InvalidArgumentException("Invalid value of field " . $field . PHP_EOL .
            "To API conversion: " . $toApi . PHP_EOL .
            "Value: " . var_export($value, TRUE));
    }
}