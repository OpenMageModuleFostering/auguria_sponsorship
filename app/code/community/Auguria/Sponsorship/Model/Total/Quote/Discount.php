<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Model_Total_Quote_Discount extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
	protected $_mode;
	protected $_activated;
	protected $_amount;
	protected $_store;
	
    public function __construct()
    {
        $this->setCode('auguria_sponsorship_discount');
    }
	
    protected function _getMode() {
    	if (!isset($this->_mode)) {
    		$this->_mode = Mage::helper('auguria_sponsorship/config')->getModuleMode();
    	}
		return 	$this->_mode;
    }
    
    protected function _getActivated()
    {
    	if (!isset($this->_activated)) {
    		$this->_activated = Mage::helper('auguria_sponsorship/config')->getCartExchangeActivated($this->_getMode());
    	}
		return 	$this->_activated;
    }
    
    /**
     * Collect address discount amount
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Auguria_Sponsorship_Model_Total_Quote_Discount
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
    	parent::collect($address);
    	$this->_store = $address->getQuote()->getStore();
		if (!isset($this->_amount)
		&& $address->getSubtotal()>0) {
			$discountApplyIncludedTax = Mage::getStoreConfig('tax/calculation/discount_tax');
			$discountApplyBeforeTax = Mage::getStoreConfig('tax/calculation/apply_after_discount');
			
			//Apply discount before tax
			if ($discountApplyBeforeTax) {				
				$maxAmount = $address->getBaseSubtotalWithDiscount();
				if ($maxAmount>0) {
					$this->_getAmount($address, $maxAmount);
					//recalcul tax amount with new amount
					$amountForTaxCalculation = $maxAmount - $this->_amount;
					$taxAmount = ($address->getBaseTaxAmount()*$amountForTaxCalculation)/$maxAmount;
					//@TODO add shipping tax if needed
					$address->setBaseTotalAmount('tax', $taxAmount);
					$address->setTotalAmount('tax', $this->_store->convertPrice($taxAmount));
					
				}
				else {
					$this->_amount = 0;
				}
				
			}
			//Apply discount after tax
			else {
				$maxAmount = $address->getBaseSubtotalWithDiscount();
				//Discount calculated included tax
				if ($discountApplyIncludedTax) {
					$maxAmount += $address->getBaseTaxAmount();
				}
				$this->_getAmount($address, $maxAmount);
			}

			
			
			if ($this->_amount > 0) {
				$baseAuguriaSponsorshipDiscountAmount = $this->_amount*-1;
				$auguriaSponsorshipDiscountAmount = $this->_store->convertPrice($baseAuguriaSponsorshipDiscountAmount);
				
				$this->_setAmount($auguriaSponsorshipDiscountAmount);
				$this->_setBaseAmount($baseAuguriaSponsorshipDiscountAmount);
				
		        //$address->setGrandTotal($address->getGrandTotal() + $address->getAuguriaSponsorshipDiscountAmount());
		        //$address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseAuguriaSponsorshipDiscountAmount());
			}
    	}
        return $this;
    }
    
    protected function _getAmount($address, $maxValue)
    {
    	$modes = Mage::helper('auguria_sponsorship')->getCash();
    	$this->_amount = 0;
		
    	if (count($modes)>0) {
			
			$activated = $this->_getActivated();
	    	foreach ($modes as $mode=>$value) {
				if (isset($activated[$mode])
				&& $activated[$mode]==true
				&& $maxValue>$this->_amount
				&& $value>0) {
					$sold = $maxValue - $this->_amount - $value;
					if ($sold >= 0) {
						$this->_amount += $value;
					}
					else {
						$value = $maxValue-$this->_amount;
						$this->_amount += $value;
					}
					if ($mode == 'fidelity') {
						$address->setAuguriaSponsorshipFidelityPointsUsed($value);
					}
					elseif ($mode == 'sponsorship') {
						$address->setAuguriaSponsorshipSponsorPointsUsed($value);
					}
					elseif ($mode == 'accumulated') {
						$address->setAuguriaSponsorshipAccumulatedPointsUsed($value);
					}
				}
	    	}
    	}
    	return $this->_amount;
    }
    
    /**
     * Add discount total information to address
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Auguria_Sponsorship_Model_Total_Quote_Discount
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
    	if ($this->_amount > 0) {
	    	$address->addTotal(array(
	                'code'=>$this->getCode(),
	                'title'=>$this->getLabel(),
	                'value'=>$address->getAuguriaSponsorshipDiscountAmount()
	        ));
    	}
        return $this;
    }
    
    public function getLabel()
    {
        return Mage::helper('auguria_sponsorship')->__('Sponsorship and fidelity discount');
    }
    
}
