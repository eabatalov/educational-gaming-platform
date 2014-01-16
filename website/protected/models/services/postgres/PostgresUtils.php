<?php

require_once(dirname(__FILE__) . '/config.php');

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
        return _PG_HOST;
    }

    static public function getDbName() {
        return _PG_DB_NAME;
    }

    static public function getUserName() {
        return _PG_USER;
    }

    static public function getPassword() {
        return _PG_PASSWORD;
    }

    static public function getConnString() {
        return "host= " . self::getHostName() . " " .
            "dbname=" . self::getDbName() ." " .
            "user=" . self::getUserName() . " " .
            "password=" . self::getPassword();
    }
}