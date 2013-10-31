<?php

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-10-31 at 00:48:13.
 */
class ModelObjectTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers ModelObject::valueChanged
     * @covers ModelObject::getChanges
     */
    public function testValueChanged() {
        $model = new ModelObject();
        $model->valueChanged("foo", 1, 2);
        $model->valueChanged("foo", 1, 2);
        $model->valueChanged("bar", 1, 2);
        $model->valueChanged("bar1", "1", "1");
        $model->valueChanged("bar2", new UserRole(UserRole::ADMIN),
            new UserRole(UserRole::CUSTOMER));
        $model->valueChanged("foo", 2, 3);

        $changes = $model->getValueChanges();
        foreach ($changes as $change) {
            if ($change->getField() == "foo") {
                assert($change->getOldVal() == 1);
                assert($change->getNewVal() == 3);
            } else if ($change->getField() == "bar") {
                assert($change->getOldVal() == 1);
                assert($change->getNewVal() == 2);
            } else if ($change->getField() == "bar1") {
                assert(FALSE, "bar1 is not changed!");
            } else if ($change->getField() == "bar2") {
                assert($change->getOldVal() == new UserRole(UserRole::ADMIN));
                assert($change->getNewVal() == new UserRole(UserRole::CUSTOMER));
            }
        }
    }

    /**
     * @covers ModelObject::valueChanged
     * @covers ModelObject::getChanges
     */
    public function testValueChangedArrayStructure() {
        $model = new ModelObject();
        $model->valueChanged("foo", 1, 2);
        $model->valueChanged("bar", 2, 3);
        assert($model->getValueChanges() == array(
            new ModelChangeRecord("foo", 1, 2),
            new ModelChangeRecord("bar", 2, 3)
        ));
    }
}