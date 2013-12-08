<?php

/**
 * Generic class for all serializable API models
 *
 * @author eugene
 */
class SerializableApiModel {
    /*
     * @param fields: if not NULL contains list of fields to put to resulting array
     */
    public function toArray($fields) {
        $result = array();
        $myFields = get_object_vars($this);

        foreach ($myFields as $field => $fieldVal) {
            if ($fields == NULL || in_array($field, $fields))
                    $result[$field] = $fieldVal;
        }
        return $result;
    }
}
