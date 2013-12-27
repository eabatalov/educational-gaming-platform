<?php

/**
 * Represents all the information about fields filtering of an
 * object requested from API.
 *
 * @author eugene
 */
class FieldsFilterApiModel {
    /*
     * @throws: InvalidArgumentException if very basic validation has failed
     */
    public function initFromArray($fieldsArray) {
        TU::throwIfNot(is_array($fieldsArray), TU::INVALID_ARGUMENT_EXCEPTION,
            "\"fields\" should be json array");
        foreach ($fieldsArray as $field)
            TU::throwIfNot(is_string($field), TU::INVALID_ARGUMENT_EXCEPTION,
                "\"fields\" item should be string");

        $this->fields = $fieldsArray;
    }
    //array()
    public $fields;
}
