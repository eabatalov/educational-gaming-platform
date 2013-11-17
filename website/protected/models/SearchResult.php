<?php

/**
 * Container of a single SearchResult
 *
 * @author eugene
 */
class SearchResult {

    /*
     * @throws: InvalidArgumentException
     */
    function __construct($objectType, $object) {
        if (!isset(self::$OBJECT_TYPES_MAP[$objectType]))
            throw new InvalidArgumentException("Unknown object type of search result");
        if (!$object instanceof self::$OBJECT_TYPES_MAP[$objectType])
            throw new InvalidArgumentException("Passed object has invalid object type");

        $this->objectType = $objectType;
        $this->object = $object;
    }

    public function getObjectType() {
        return $this->objectType;
    }

    public function getObject() {
        return $this->object;
    }

    public function setObjectType($objectType) {
        $this->objectType = $objectType;
    }

    public function setObject($object) {
        $this->object = $object;
    }

    private $objectType;
    private $object;

    private static $OBJECT_TYPES_MAP = array(
        SearchRequest::OBJ_TYPE_USER => "User",
        SearchRequest::OBJ_TYPE_GAME => NULL
    );
}