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
        $this->setTemplate('sponsorship/customer/widget/virement.phtml');
    }

    public function isEnabled()
    {	
    	//Ajouter si module parrainage actif
    	
    	//iban et siret actifs si le client est parrain
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

/*
    public function isRequired()
    {
        return 'req' == $this->getConfig('taxvat_show');
    }
*/
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }
}
