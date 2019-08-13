<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Sponsorship extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }

    public function getSponsorship()
    { 
        if (!$this->hasData('sponsorship'))
        {
            $this->setData('sponsorship', Mage::registry('sponsorship'));
        }
        return $this->getData('sponsorship');
    }
    
    public function getMaxRecipients()
    {
        return Mage::getStoreConfig('sponsorship/invitation_email/max_recipients');
    }

    public function getUserName()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return '';
        }
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return trim($customer->getFirstname()).' '.trim($customer->getLastname());
    }

    public function getCustomerId ()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return '';
        }
    	return Mage::getSingleton('customer/session')->getCustomerId();
    }

    public function getUserEmail()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return '';
        }
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return $customer->getEmail();
    }

    public function getFooterMessage()
    {
        return Mage::helper('sponsorship/mail')->getFooterMessage ($this->getCustomerId());
    }

    public function getBackUrl()
    {
        $back_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->getUrl('customer/account/');
        return $back_url;
    }
}