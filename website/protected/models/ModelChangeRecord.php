<?php

/**
 * Stores particular change in a single field of a model class
 *
 * @author eugene
 */
class ModelChangeRecord {

    function __construct($field, $oldVal, $newVal) {
        $this->setField($field);
        $this->setOldVal($oldVal);
        $this->setNewVal($newVal);
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

    private $field;
    private $oldVal;
    private $newVal;
}
