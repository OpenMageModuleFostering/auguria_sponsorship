<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Model_Bookmarks extends Magentix_SocialBookmarking_Model_Bookmarks
{
	/* Return page URL */
	private function getPUrl()
	{
		mage::log('rewrite ok');
		$param = "";		
		if (Mage::getSingleton('customer/session')->getCustomerId())
			$param = '?sponsor_id='.Mage::getSingleton('customer/session')->getCustomerId();
		
		$id = Mage::getSingleton('customer/session')->getCustomerId();
		return preg_replace(array('/\?___SID=U/'),array(''),Mage::helper('core/url')->getCurrentUrl().$param);
	}
}