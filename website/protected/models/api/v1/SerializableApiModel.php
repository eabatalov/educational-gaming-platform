<?php

/**
 * Generic class for all serializable API models
 *
 * @author eugene
 */
class SerializableApiModel {
    /*
     * @param filterFields: NULL or FieldsFilterApiModel.
     *  if not NULL contains list of fields to put to resulting array.
     */
    public function toArray($filterFields = NULL) {
        $myFields = get_object_vars($this);
        if ($filterFields === NULL) {
            return $myFields;
        }

        $result = array();
        /*foreach ($myFields as $field => $fieldVal) {
            if ($fields === NULL || in_array($field, $fields))
                    $result[$field] = $fieldVal;
        }*/
        
        foreach ($filterFields->fields as $field) {
            TU::throwIfNot(isset($myFields[$field]), TU::INVALID_ARGUMENT_EXCEPTION,
                "unknown filed name in \"fields\"");
            $result[$field] = $myFields[$field];
        }
        return $result;
    }
}
