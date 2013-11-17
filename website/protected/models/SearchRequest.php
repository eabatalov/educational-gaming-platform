<?php

/**
 * Incapsulates data needed to define request to ISearchService
 *
 * @author eugene
 */
class SearchRequest {

    /*
     * @throws: InvalidArgumentException
     */
    function __construct($objectType, $query) {
        if ($objectType != self::OBJ_TYPE_ALL &&
            !in_array($objectType, self::$OBJECT_TYPES))
                throw new InvalidArgumentException("Unknown object type to search for");
        if (!is_string($query))
            throw new InvalidArgumentException("Search query should be string");
        //TODO once query becomes complicated, perform its grammar check here
        $this->objectType = $objectType;
        $this->query = $query;
    }

    public function getObjectType() {
        return $this->objectType;
    }

    public function getQuery() {
        return $this->query;
    }

    public function setObjectType($objectType) {
        $this->objectType = $objectType;
    }

    public function setQuery($query) {
        $this->query = $query;
    }

    /*
     * Can't be returned to client so use it as special value.
     * Don't add to any maps, arrays of valid object type values
     */
    const OBJ_TYPE_ALL = "all";
    //Real object types
    const OBJ_TYPE_USER = "OBJ_TYPE_USER";
    const OBJ_TYPE_GAME = "OBJ_TYPE_GAME";
    private static $OBJECT_TYPES = array(
        self::OBJ_TYPE_USER,
        self::OBJ_TYPE_GAME
    );
    
    private $objectType;
    private $query;
}