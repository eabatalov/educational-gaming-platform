<?php

/**
 * Stores particular change in a single field of a model class
 *
 * @author eugene
 */
class ModelChangeRecord {

    function __construct($field, $oldVal, $newVal, $arg = NULL) {
        $this->setField($field);
        $this->setOldVal($oldVal);
        $this->setNewVal($newVal);
        $this->setArg($arg);
    }

    public function getField() {
        return $this->field;
    }

    public function getOldVal() {
        return $this->oldVal;
    }

    public function getNewVal() {
        return $this->newVal;
    }

    public function setField($field) {
        $this->field = $field;
    }

    public function setOldVal($oldVal) {
        $this->oldVal = $oldVal;
    }

    public function setNewVal($newVal) {
        $this->newVal = $newVal;
    }

    public function getArg() {
        return $this->arg;
    }

    public function setArg($arg) {
        $this->arg = $arg;
    }

    //special vals for collection elements
    const ADDED = "ADDED";
    const REMOVED = "REMOVED";

    private $field;
    private $oldVal;
    private $newVal;
    //Optional argument which depends on field name
    private $arg;
}
