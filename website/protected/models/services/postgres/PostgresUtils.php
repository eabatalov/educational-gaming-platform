<?php

/**
 * @author eugene
 */
class PostgresUtils {

    public static function boolToPGBool($val) {
        assert(is_bool($val));
        if ($val) {
            return 'y';
        } else {
            return 'n';
        }
    }

    public static function PGBoolToPHP($val) {
        assert(is_string($val));
        if($val === 't' || $val === 'true' ||
            $val==='y' || $val==='yes' ||
            $val==='on' || $val==='1') {
            return TRUE; 
        } else {
            return FALSE;
        }
    }

    static public function getHostName() {
        return "localhost";
    }

    static public function getDbName() {
        return "postgres";
    }

    static public function getUserName() {
        return "postgres";
    }

    static public function getPassword() {
        return "111";
    }

    static public function getConnString() {
        return "host= " . self::getHostName() . " " .
            "dbname=" . self::getDbName() ." " .
            "user=" . self::getUserName() . " " .
            "password=" . self::getPassword();
    }
}