<?php

/**
 * Enumeration of user roles
 *
 * @author eugene
 */
class UserRole {
    const CUSTOMER = 'CUSTOMER';
    const ADMIN = 'ADMIN';
    const ANALYST = 'ANALYST';
    public static $ROLES = array(self::CUSTOMER, self::ADMIN, self::ANALYST);
}
