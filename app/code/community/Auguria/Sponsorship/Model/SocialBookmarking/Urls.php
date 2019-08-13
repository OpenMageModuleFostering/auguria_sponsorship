<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Model_SocialBookmarking_Urls extends Magentix_SocialBookmarking_Model_Urls
{ 
	public function getCurrentUrl()
	{
		$param = "";		
		if (Mage::getSingleton('customer/session')->getCustomerId())
		{
			$param = 'sponsor_id/'.Mage::getSingleton('customer/session')->getCustomerId().'/';
			return Mage::getUrl('*/*/*', array('_current' => true, '_use_rewrite' => false)).$param;
		}
		else
		{
			return preg_replace('/\?___SID=U/','',Mage::helper('core/url')->getCurrentUrl());
		}
	}
}