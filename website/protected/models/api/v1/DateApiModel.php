<?php

/**
 * Description of DateApiModel
 *
 * @author eugene
 */
class DateApiModel {
    /*
     * @throws: InvalidArgumentException if very basic validation has failed
     */
    public function initFromArray($fieldsArray) {
        TU::throwIfNot(is_array($fieldsArray), TU::INTERNAL_ERROR_EXCEPTION);
        $this->year = TU::getValueOrThrow("year", $fieldsArray);
        $this->month = TU::getValueOrThrow("month", $fieldsArray);
        $this->day = TU::getValueOrThrow("day", $fieldsArray);

        TU::throwIfNot(is_numeric($this->year) &&
            is_numeric($this->month) && is_numeric($this->day), TU::INVALID_ARGUMENT_EXCEPTION);

        $this->year = intval($this->year);
        $this->month = intval($this->month);
        $this->day = intval($this->day);
    }

    public function initFromDate(Date $date) {
        $this->year = $date->getYear();
        $this->month = $date->getMonth();
        $this->day = $date->getDay();
    }

    public function toDate() {
        return new Date($this->year, $this->day, $this->month);
    }

    //Ints
    public $year;
    public $month;
    public $day;
}