<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Model_Adminhtml_System_Config_Source_Mode
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'fidelity', 'label'=>Mage::helper('auguria_sponsorship')->__('Fidelity')),
            array('value' => 'sponsorship', 'label'=>Mage::helper('auguria_sponsorship')->__('Sponsorship')),
            array('value' => 'separated', 'label'=>Mage::helper('auguria_sponsorship')->__('Fidelity and Sponsorship (separated)')),
            array('value' => 'accumulated', 'label'=>Mage::helper('auguria_sponsorship')->__('Fidelity and Sponsorship (accumulated)')),
        );
    }

}
