<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Sales_Order_Totals extends Mage_Sales_Block_Order_Totals
{
	protected function _initTotals()
    {
    	parent::_initTotals();
    	$source = $this->getSource();
    	if (((float)$this->getSource()->getAuguriaSponsorshipDiscountAmount()) != 0) {
	        $auguriaSponsorshipDiscountTotal = new Varien_Object(array(
	            'code'      => 'auguria_sponsorship_discount',
	            'value'=> $source->getAuguriaSponsorshipDiscountAmount(),
	            'label'=> Mage::helper('auguria_sponsorship')->__('Sponsorship and fidelity discount')
	        ));
	        $this->addTotal($auguriaSponsorshipDiscountTotal);
    	}
        return $this;
    }
}