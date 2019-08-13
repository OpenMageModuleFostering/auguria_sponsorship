<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Model_Adminhtml_System_Config_Backend_Sponsorship_NaturalNumber extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
        $value = $this->getValue();        
    	if (!Zend_Validate::is($value, 'NotEmpty')) {
    		Mage::throwException(Mage::helper('auguria_sponsorship')->__("A value is required."));
        }
        if (!Zend_Validate::is($value, 'Digits')) {
        	Mage::throwException(Mage::helper('auguria_sponsorship')->__("'%s' is not a natural number.", $value));
        }
        $validator = new Zend_Validate_GreaterThan(-1);
		if (!$validator->isValid($value)) {
        	Mage::throwException(Mage::helper('auguria_sponsorship')->__("'%s' is not a natural number.", $value));
        }
        return $this;
    }
}