<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Customer_Widget_Virement extends Mage_Customer_Block_Widget_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('auguria/sponsorship/customer/widget/virement.phtml');
    }

    public function isEnabled()
    {
    	//iban et siret actifs si le client est parrain ou qu'il a des points de fidélité
		$customer = Mage::getModel('customer/customer')
                ->getCollection()
    			->addAttributeToFilter('sponsor', $this->getCustomer()->getId());
    	if ($customer->getData() || $this->getCustomer()->getFidelityPoints() != null) {
    		return true;
    	}
    	else {
    		return false;
    	}
    }
    
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }
}
