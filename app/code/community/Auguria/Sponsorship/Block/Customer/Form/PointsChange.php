<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Customer_Form_PointsChange extends Mage_Customer_Block_Account_Dashboard
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
    public function getChange()
    {
    	$module = $this->getRequest()->getParam('module');
    	$type = $this->getRequest()->getParam('type');
    	return Array ('module'=>$module, 'type'=>$type);
    }
    
	public function getCustomerId()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return '';
        }
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        return ($customerId);
    }
    
	public function getFidelityPoints()
    {
    	$customer = Mage::getModel("customer/customer")->load($this->getCustomerId());
    	$cFP = $customer->getData('fidelity_points');
		return $cFP;
    }
    
	public function getSponsorPoints()
    {
    	$customer = Mage::getModel("customer/customer")->load($this->getCustomerId());
    	$cSP = $customer->getData('sponsor_points');
		return $cSP;
    }
    
	public function getAccumulatedPoints()
    {
    	$customer = Mage::getModel("customer/customer")->load($this->getCustomerId());
    	$cSP = $customer->getData('accumulated_points');
		return $cSP;
    }
    
    public function getPointsToCashConfig ($Module)
    {
    	$options = Mage::getBlockSingleton('auguria_sponsorship/customer_account_pointsDetail');
    	$PointsToCashFunction = 'get'.$Module.'PointsToCashConfig';
		$PointsToCash = $options->$PointsToCashFunction();
		return $PointsToCash;
    }
}