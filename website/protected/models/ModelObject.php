<?php
 
/**
 * Add validation and changes tracking functionality
 * to derived class
 *
 * @author eugene
 */
class ModelObject extends CFormModel {

    public function __construct() {
        $this->changes = NULL;        
        $this->disableTrackCnt = 0;
    }

    /*
     * Call in the beginning of your constructor
     * to temporarely disable changes tracking
     */
    protected function dsChangeTracking() {
        ++$this->disableTrackCnt;
    }

    /*
     * Call at the end of your constructor
     * to enable changes tracking
     */
    protected function enChangeTracking() {
        --$this->disableTrackCnt;
    }

    /*
     * Call when value of field is changed
     * @param field: string name of field which value is changed
     * @param oldVal:
     * @param newVal:
     * @returns: nothing
     */
    public function valueChanged($fieldName, $oldVal, $newVal) {
        if ($this->disableTrackCnt > 0) {
            return;
        }

        if (array_key_exists($fieldName, $this->getChanges())) {
            $change = &$this->getChanges()[$fieldName];
            if ($newVal == $change->getOldVal()) {
                unset($change);
            } else {
                $change->setNewVal($newVal);
            }
        } else {
            if ($newVal != $oldVal) {
                $this->getChanges()[$fieldName] =
                    new ModelChangeRecord($fieldName, $oldVal, $newVal);
            }
        }
    }

    /*
     * Get changed values of current model object
     * @returns: array of ModelChangeRecords
     */
    public function getValueChanges() {
        return array_values($this->getChanges());
    }

    private function getChanges() {
        if ($this->changes == NULL) {
            $this->changes = array();
        }
        return $this->changes;
    }

    //Map field name => ModelChangeRecord
    private $changes;
    //Temporarily disable values change tracking while model object is constructed
    private $disableTrackCnt;
}