<?php

/**
 * Represents all the information about pagination of
 * collection requested from API.
 *
 * @author eugene
 */
class PagingApiModel {

    /*
     * @throws: InvalidArgumentException if very basic validation has failed
     */
    public function initFromArray($fieldsArray) {
        $this->offset = TU::getValueOrThrow("offset", $fieldsArray);
        $this->limit = TU::getValueOrThrow("limit", $fieldsArray);
        $this->total = TU::getValueOrDefault("total", $fieldsArray, 0);

        TU::throwIfNot(is_numeric($this->limit) &&
            is_numeric($this->total), TU::INVALID_ARGUMENT_EXCEPTION);

        $this->limit = intval($this->limit);
        $this->total = intval($this->total);
    }

    public function initFromPaging(Paging $paging) {
        $this->offset = $paging->getOffset();
        if ($paging->getSub() !== 0) {
            $this->offset = $this->offset . "-" . strval($paging->getSub());
        }
        $this->limit = $paging->getLimit();
        $this->total = $paging->getTotal();
    }

    /*
     * @returns Paging instance created from this api model instance
     */
    public function toPaging() {
        return new Paging($this->offset, $this->limit, $this->total);
    }

    //Index of first element in requested collection (1 is the first) or self::LAST
    public $offset;
    //Max size of requested collection
    public $limit;
    //Size of not limited collection
    public $total;
}