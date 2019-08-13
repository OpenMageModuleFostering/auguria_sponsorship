<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Customer_Widget_Name extends Mage_Customer_Block_Widget_Name
{
    public function _construct()
    {
        Mage_Customer_Block_Widget_Abstract::_construct();

        // default template location
        $this->setTemplate('auguria/sponsorship/customer/widget/name.phtml');
    }
    
    public function getInvit ($param)
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
}