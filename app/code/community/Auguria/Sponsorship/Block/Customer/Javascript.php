<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Customer_Javascript extends Mage_Core_Block_Template
{
    public function _construct()
    {
        $this->setTemplate('auguria/sponsorship/customer/javascript.phtml');
    }
    
    public function getInvit($param)
    {
    	$value = '';
    	$cookie = new Mage_Core_Model_Cookie;
    	if (Mage::getSingleton('core/session')->getData($param))
    	{
    		$value = Mage::getSingleton('core/session')->getData($param);
    		
    	}
    	elseif ($cookie->get('sponsorship_'.$param))
    	{
    		$value = $cookie->get('sponsorship_'.$param);
    	}
    	return $value;
    }
    
    public function isJs(){
    	return true;
    }
    
}