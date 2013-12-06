<?php

/**
 * Adaptor from/to JSON API SearchResult object to SearchResult model object
 *
 * @author eugene
 */
class SearchResultApiModel {

    /*
     * @throws: InvalidArgumentException if very basic validation has failed
     */
    public function initFromSearchResult(SearchResult $searchResult) {
        $this->object_type =
            self::ObjectTypeToApiObjectType($searchResult->getObjectType());
        switch ($searchResult->getObjectType()) {
            case SearchRequest::OBJ_TYPE_USER:
                $this->object = new UserApiModel();
                $this->object->initFromUser($searchResult->getObject());
                break;
            default:
               throw new InvalidArgumentException("Unsupported object type");
        }
    }

    /*
     * @param fields: if not NULL contains list of fields to put to resulting array
     * @throws: InvalidArgumentException if very basic validation has failed
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

    public $object_type;
    public $object;

    /*
     * @throws: InvalidArgumentException
     */
    public static function apiObjectTypeToObjectType($apiObjectType) {
        return TU::getValueOrThrow($apiObjectType, self::$API_OBJECT_TYPE_TO_OBJECT_TYPE);
    }

    /*
     * @throws: InvalidArgumentException
     */
    public static function ObjectTypeToApiObjectType($objectType) {
        return TU::getValueOrThrow($objectType, self::$OBJECT_TYPE_TO_API_OBJECT_TYPE);
    }

    private static $OBJECT_TYPE_TO_API_OBJECT_TYPE = array(
        SearchRequest::OBJ_TYPE_USER => "user"
    );

    private static $API_OBJECT_TYPE_TO_OBJECT_TYPE = array(
        "user" => SearchRequest::OBJ_TYPE_USER
    );
}