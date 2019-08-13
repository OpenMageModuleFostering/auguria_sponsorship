<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Openinviter extends Mage_Core_Block_Template
{	
    public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
    public function getOpenInviterStep()
    {
    	$form = Mage::getSingleton('customer/session')->getData('openinviter_form');
        if (isset($form['step']))
        	return $form['step'];
        elseif($this->getRequest()->getParam('step'))
        	return $this->getRequest()->getParam('step');
        else
        	return 'get_contacts';
    }
    
    public function getOpenIniviterPlugins()
    {
    	$form = Mage::getSingleton('customer/session')->getData('openinviter_form');
        if (isset($form['plugins']))
        	return $form['plugins'];
        else
        	return array();
    }
    
    public function getOpenInviterTypes()
    {
    	$form = Mage::getSingleton('customer/session')->getData('openinviter_form');
        if (isset($form['types']))
        	return $form['types'];
        else
        	return array();
    }
    
    public function getOpenInviterEmailBox()
    {
    	$form = Mage::getSingleton('customer/session')->getData('openinviter_form');
        if (isset($form['email_box']))
        	return $form['email_box'];
    	
    }
    
    public function getOpenInviterProviderBox()
    {
    	$form = Mage::getSingleton('customer/session')->getData('openinviter_form');
        if (isset($form['provider_box']))
        	return $form['provider_box'];
    }
    
    public function getOpenInviterContacts()
    {
    	$form = Mage::getSingleton('customer/session')->getData('openinviter_form');
        if (isset($form['contacts']))
        	return $form['contacts'];
    }
}