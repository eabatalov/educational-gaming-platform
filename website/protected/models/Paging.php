<?php

/**
 * Represents all the information about pagination of
 * a collection.
 *
 * @author eugene
 */
class Paging {

    function __construct($offset = "0", $limit = self::MAX_LIMIT, $total = 0) {
        $this->setOffset($offset);
        $this->setLimit($limit);
        $this->setTotal($total);
    }

    const LAST_ITEM = "LAST";
    const MAX_LIMIT = 200;

    public function getOffset() {
        return $this->offset;
    }

    public function getSub() {
        return $this->sub;
    }

    public function getLimit() {
        return $this->limit;
    }

    public function getTotal() {
        return $this->total;
    }

    public function setOffset($offset) {
        $this->offset = $offset;
        $this->sub = 0;

        if (!is_numeric($this->offset)) {
            TU::throwIfNot(
                intval($this->offset) >= 0 &&
                strlen($this->offset) >= strlen(self::LAST_ITEM) &&
                substr($this->offset, 0, strlen(self::LAST_ITEM)) === self::LAST_ITEM
                , TU::INVALID_ARGUMENT_EXCEPTION);

            if (strlen($this->offset) > strlen(self::LAST_ITEM)) {
                TU::throwIfNot(strlen($this->offset) > (strlen(self::LAST_ITEM) + 1/*-NUM*/),
                        TU::INVALID_ARGUMENT_EXCEPTION);
                TU::throwIfNot(
                    substr($this->offset, strlen(self::LAST_ITEM), 1) === "-",
                    TU::INVALID_ARGUMENT_EXCEPTION);
                $this->sub = substr($this->offset, strlen(self::LAST_ITEM) + 1);
                TU::throwIfNot(is_numeric($this->sub), TU::INVALID_ARGUMENT_EXCEPTION);
                $this->sub = intval($this->sub);
            }
        }
    }

    public function setLimit($limit) {
        TU::throwIfNot(
            is_int($limit) &&
            $limit >= 0,
            TU::INVALID_ARGUMENT_EXCEPTION);

        if ($limit > self::MAX_LIMIT)
            $limit = self::MAX_LIMIT;
        $this->limit = $limit;
    }

    public function setTotal($total) {
        TU::throwIfNot(
            is_int($total) &&
            $total >= 0,
            TU::INVALID_ARGUMENT_EXCEPTION);

        $this->total = $total;
    }
    
    //Index of first element in requested collection (1 is the first) or self::LAST
    //String
    private $offset;
    //Number to substract from offset to get the real start of the collection
    //Int64
    private $sub;
    //Max size of requested collection
    //Int64
    private $limit;
    //Size of not limited collection
    //Int64
    private $total;
}