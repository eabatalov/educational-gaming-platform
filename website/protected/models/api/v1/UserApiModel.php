<?php

/**
 * Adaptor from/to JSON API User object to User model object
 *
 * @author eugene
 */
class UserApiModel extends SerializableApiModel {

    /*
     * @throws: InvalidArgumentException if very basic validation has failed
     */
    public function initFromUser(User $user) {
        $this->id = $user->getId();
        $this->email = $user->getEmail();
        $this->name = $user->getName();
        $this->surname = $user->getSurname();
        $this->is_online = self::toAPIIsOnline($user->getIsActive());
        $this->avatar = $user->getAvatar();
        $birthDate = NULL;
        if ($user->getBirthDate() !== NULL) {
            $birthDate = new DateApiModel();
            $birthDate->initFromDate($user->getBirthDate());
        }
        $this->birthdate = $birthDate;
        $this->gender = self::toApiGender($user->getGender());
        $this->role = self::toAPIRole($user->getRole());
    }

    /*
     * @throws: InvalidArgumentException if very basic validation has failed
     */
    public function initFromArrayOnCreate($fieldArray) {
        $this->email = TU::getValueOrThrow("email", $fieldArray);
        $this->name = TU::getValueOrThrow("name", $fieldArray);
        $this->surname = TU::getValueOrThrow("surname", $fieldArray);

        $this->avatar = TU::getValueOrNull("avatar", $fieldArray);
        $this->birthdate = TU::getValueOrNull("birthdate", $fieldArray);
        $this->gender = TU::getValueOrNull("gender", $fieldArray);

        //Set defaults for this model object
        $this->id = 0;
        $this->is_online = $this->toAPIIsOnline(FALSE);
        $this->role = $this->toAPIRole(UserRole::CUSTOMER);
    }

    public function initFromArrayOnUpdate($fieldArray) {
        //id cannot be changed
        $this->email = TU::getValueOrThrow("email", $fieldArray);
        $this->name = TU::getValueOrThrow("name", $fieldArray);
        $this->surname = TU::getValueOrThrow("surname", $fieldArray);
        $this->is_online = TU::getValueOrThrow("is_online", $fieldArray);
        $this->role = TU::getValueOrThrow("role", $fieldArray);
        $this->avatar = TU::getValueOrNull("avatar", $fieldArray);
        $this->birthdate = TU::getValueOrNull("birthdate", $fieldArray);
        $this->gender = TU::getValueOrNull("gender", $fieldArray);
    }

    /*
     * @throws: InvalidArgumentException if very basic validation has failed
     */
    public function toUser() {
        $birthDate = NULL;
        if ($this->birthdate !== NULL)
            $birthDate = new DateApiModel($this->birthdate);

        return User::createUInstance(
                $this->email,
                $this->name,
                $this->surname,
                self::fromAPIIsOnline($this->is_online),
                self::fromAPIRole($this->role),
                array(
                    User::OPT_AVATAR => $this->avatar,
                    User::OPT_BIRTH_DATE => $birthDate,
                    User::OPT_GENDER => self::fromApiGender($this->gender)
                ),
                $this->id
        );
    }

    /*
     * @throws: InvalidArgumentException if very basic validation has failed
     */
    public function toUserFieldsArray() {
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
    public $avatar;
    public $birthdate;
    public $gender;

    /*
     * API spec values conversion and validation
     * @throws: InvalidArgumentException is $value has invalid value
     */
    protected static function toAPIRole($value) {
        if ($value === UserRole::CUSTOMER)
            return "customer";
        if ($value === UserRole::ADMIN)
            return "admin";
        self::throwInvalidConversionError($value, "role", TRUE);
    }

    protected static function fromAPIRole($value) {
        if ($value === "customer")
            return UserRole::CUSTOMER;
        self::throwInvalidConversionError($value, "role", FALSE);
    }

    protected static function toAPIIsOnline($value) {
        if ($value === TRUE)
            return TRUE;
        if ($value === FALSE)
            return FALSE;
        self::throwInvalidConversionError($value, "is_online", TRUE);
    }

    protected static function fromAPIIsOnline($value) {
        if ($value === TRUE)
            return TRUE;
        if ($value === FALSE)
            return FALSE;
        self::throwInvalidConversionError($value, "is_online ", FALSE);
    }

    const GENDER_MALE = "male";
    const GENDER_FEMALE = "female";
    protected static function toApiGender($value) {
        if ($value === NULL)
            return NULL;
        TU::throwIfNot(is_string($value), TU::INVALID_ARGUMENT_EXCEPTION,
            "Gender should be string");
        if ($value === UserGender::FEMALE)
            return self::GENDER_FEMALE;
        else if ($value === UserGender::MALE)
            return self::GENDER_MALE;
        else throw new InvalidArgumentException("Invalid gender value: " . $value);
    }

    protected static function fromApiGender($value) {
        if ($value === NULL)
            return NULL;
        TU::throwIfNot(is_string($value), TU::INVALID_ARGUMENT_EXCEPTION,
            "Gender should be string");
        if ($value === self::GENDER_FEMALE)
            return UserGender::FEMALE;
        else if ($value === self::GENDER_MALE)
            return UserGender::MALE;
        else throw new InvalidArgumentException("Invalid gender value: " . $value);
    }

    protected static function throwInvalidConversionError($value, $field, $toApi) {
        throw new InvalidArgumentException("Invalid value of field " . $field . PHP_EOL .
            "To API conversion: " . var_export($toApi, TRUE) . PHP_EOL .
            "Value: " . var_export($value, TRUE));
    }
}