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
}
