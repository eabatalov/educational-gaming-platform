<?php

/**
 * Description of PasswordChangeApiModel
 *
 * @author eugene
 */
class PasswordChangeApiModel {

    function __construct() {
        
    }

    /*
     * @throw InvalidArgumentException if basic validation has failed
     */
    public function initFromArray($fieldsArray) {
        TU::throwIfNot(is_array($fieldsArray), TU::INTERNAL_ERROR_EXCEPTION);
        $this->old = TU::getValueOrThrow("old", $fieldsArray);
        $this->new = TU::getValueOrThrow("new", $fieldsArray);
        TU::throwIfNot(is_string($this->old) && is_string($this->new), TU::INVALID_ARGUMENT_EXCEPTION,
            "Password should be string");
    }

    public $old;
    public $new;
}
