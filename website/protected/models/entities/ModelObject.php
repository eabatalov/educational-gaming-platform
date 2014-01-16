<?php
 
/**
 * Adds validation and changes tracking functionality
 * to derived class
 *
 * @author eugene
 */
class ModelObject extends CFormModel {
    
    /*
     * Exception with this error code will be thrown on attempt to save
     * ModelObject which hasn't passed validation to persistent storage
     */
    const ERROR_INVALID_OBJECT = 0x1000001;
    const FATAL_ERROR_FIELD_NAME = 'Error';

    public function __construct() {
        $this->changes = NULL;        
        $this->disableTrackCnt = 0;
    }

    /*
     * Should be overiden by child classes.
     * Create new object with empty or default fields values
     * Use this object for binding with ActiveForm class in views
     */
    static public function createEmpty() {
        throw new BadMethodCallException("Should be overriden in child classes");
    }

    //==================== Errors in this model object ======================
    public function addFatalError(Exception $ex) {
        $this->addError(self::FATAL_ERROR_FIELD_NAME, TU::htmlFormatExceptionForUser($ex));
    }

    public function addHtmlFormattedError($attribute, $message) {
        $formatter = new CFormatter();
        $this->addError($attribute, $formatter->formatNtext($message));
    }

    //========================== CHANGES TRACKING ===========================
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
     * @param fieldId: 
     * @param oldVal:
     * @param newVal:
     * @returns: nothing
     */
    public function valueChanged($fieldId, $oldVal, $newVal) {
        if ($this->disableTrackCnt > 0) {
            return;
        }

        $changes = &$this->getChanges();
        if (array_key_exists($fieldId, $changes)) {
            $change = $changes[$fieldId];
            if ($newVal == $change->getOldVal()) {
                //wired way to delete array elem in php
                unset($changes[$fieldId]);
            } else {
                $change->setNewVal($newVal);
            }
        } else {
            if ($newVal != $oldVal) {
                $changes[$fieldId] =
                    new ModelChangeRecord($fieldId, $oldVal, $newVal);
            }
        }
    }

    /*
     * Get changed values of current model object
     * @returns: array of ModelChangeRecords
     */
    public function getValueChanges() {
        return $this->getChanges();
    }

    private function &getChanges() {
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