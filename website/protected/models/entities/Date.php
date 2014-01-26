<?php

/**
 * Description of Date
 *
 * @author eugene
 */
class Date {

    function __construct($year, $day, $month) {
        $this->setYear($year);
        $this->setDay($day);
        $this->setMonth($month);
        TU::throwIfNot(checkdate($this->getMonth(), $this->getDay() , $this->getYear()),
            TU::INVALID_ARGUMENT_EXCEPTION, "Invalid overall date value");
    }

    public function getYear() {
        return $this->year;
    }

    public function getDay() {
        return $this->day;
    }

    public function getMonth() {
        return $this->month;
    }

    public function setYear($year) {
        TU::throwIfNot(is_int($year), TU::INVALID_ARGUMENT_EXCEPTION, "Year should be integer");
        $this->year = $year;
    }

    public function setDay($day) {
        TU::throwIfNot(is_int($day), TU::INVALID_ARGUMENT_EXCEPTION, "Day should be integer");
        $this->day = $day;
    }

    public function setMonth($month) {
        TU::throwIfNot(is_int($month), TU::INVALID_ARGUMENT_EXCEPTION, "Month should be integer");
        $this->month = $month;
    }
    //Ints
    private $year, $day, $month;
}
