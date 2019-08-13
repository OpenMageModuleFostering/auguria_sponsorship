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
        return Mage::getStoreConfig('auguria_sponsorship/invitation/max_recipients');
    }

    public function getUserName()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn())
            return '';
         
        $form = Mage::getSingleton('customer/session')->getData('sponsorship_form');
        if (isset($form['sender']['name']))
        	return $form['sender']['name'];
        else
        {
	        $customer = Mage::getSingleton('customer/session')->getCustomer();
	        return trim($customer->getFirstname()).' '.trim($customer->getLastname());
        }
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
        if (!Mage::getSingleton('customer/session')->isLoggedIn())
            return '';
        $form = Mage::getSingleton('customer/session')->getData('sponsorship_form');
        if (isset($form['sender']['email']))
        	return $form['sender']['email'];
        else
        {
	        $customer = Mage::getSingleton('customer/session')->getCustomer();
	        return $customer->getEmail();
        }
    }
    
    public function getSubject()
    {
    	if (!Mage::getSingleton('customer/session')->isLoggedIn())
            return '';
        
        $form = Mage::getSingleton('customer/session')->getData('sponsorship_form');
        if (isset($form['message']['subject']))
        	return $form['message']['subject'];
        else
        	return Mage::helper('auguria_sponsorship/mail')->getSubject();
    }
    
	public function getBodyMessage()
    {
    	if (!Mage::getSingleton('customer/session')->isLoggedIn())
            return '';
        
        $form = Mage::getSingleton('customer/session')->getData('sponsorship_form');
        if (isset($form['message']['body']))
        	return $form['message']['body'];
        else
        	return Mage::helper('auguria_sponsorship/mail')->getMessage();
    }
    
    public function getFooterMessage()
    {
        return Mage::helper('auguria_sponsorship/mail')->getFooterMessage ($this->getCustomerId());
    }

    public function getBackUrl()
    {
        $back_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->getUrl('customer/account/');
        return $back_url;
    }
    
    public function getActiveProviders()
    {
    	$providers = Mage::getResourceModel('auguria_sponsorship/sponsorshipopeninviter_collection')
    					->addFieldToFilter('status', 1)
    					;    	
    	return $providers->getItems();
    }
}