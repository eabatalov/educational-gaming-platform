<?php

namespace model;

/**
 * Description of UserRole
 *
 * @author eugene
 */
class UserRole {
    //Enum for val
    const CUSTOMER = 0;
    const ADMIN = 1;
    const ANALYST = 2;

    function __construct($val) {
        assert(is_int($val));
        assert($val >= self::CUSTOMER && $val <= self::ANALYST);
        $this->val = $val;
    }

    public function getVal() {
        return $this->val;
    }

    public $val;
}
