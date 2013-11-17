<?php

/**
 * Adaptor from/to JSON API SearchRequest object to SearchRequest model object
 *
 * @author eugene
 */
class SearchRequestApiModel {
    /*
     * @throws: InvalidArgumentException if very basic validation has failed
     */
    public function initFromArray($fieldsArray) {
        $this->query = TU::getValueOrThrow("query", $fieldsArray);
        $this->object_type = TU::getValueOrThrow("object_type", $fieldsArray);
    }

    /*
     * @throws: InvalidArgumentException if validation has failed
     */
    public function toSearchRequest() {
        return new SearchRequest(
            $this->object_type == SearchRequest::OBJ_TYPE_ALL ?
                SearchRequest::OBJ_TYPE_ALL :
                SearchResultApiModel::apiObjectTypeToObjectType($this->object_type),
            $this->query
        );
    }

    public $query;
    public $object_type;
}