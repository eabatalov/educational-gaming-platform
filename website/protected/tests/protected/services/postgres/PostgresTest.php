<?php

/**
 * Description of PostgresTest
 *
 * @author eugene
 */
class PostgresTest extends PHPUnit_Framework_TestCase {

    static public function setUpBeforeClass() {
        $output = array();
        $status = 0;
        assert(chdir(dirname(__FILE__) . "/../../../../../../../setup/"));
        $setup_db = "./setup.py --host=" . PostgresUtils::getHostName() .
                " --db=" . PostgresUtils::getDbName() .
                " --user=" . PostgresUtils::getUserName() .
                " --pass=" . PostgresUtils::getPassword();
        print $setup_db . '\n';
        exec($setup_db, $output, $status);
        assert($status == 0, var_export($output, true));
    }
}
