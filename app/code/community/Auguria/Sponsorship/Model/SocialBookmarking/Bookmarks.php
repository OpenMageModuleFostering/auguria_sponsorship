<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Model_SocialBookmarking_Bookmarks extends Magentix_SocialBookmarking_Model_Bookmarks
{
	public function _construct() {
        Mage_Core_Model_Abstract::_construct();
        $this->_init('socialbookmarking/bookmarks');
    }
    
    /* Return array with page informations : URL, bit.ly URL and Meta title */
	public function getPageInfos() {
		return array('url'=>$this->getPUrl(),'bitly'=>$this->getPBitly(),'title'=>$this->getPTitle());
	}
	
	/* Return page URL */
	private function getPUrl()
	{
		$param = "";		
		if (Mage::getSingleton('customer/session')->getCustomerId())
			$param = 'sponsor_id/'.Mage::getSingleton('customer/session')->getCustomerId().'/';
		
		$id = Mage::getSingleton('customer/session')->getCustomerId();
		return preg_replace(array('/\?___SID=U/'),array(''),Mage::getUrl('*/*/*', array('_current' => true, '_use_rewrite' => false)).$param);
	}
	
	/* Return page bit.ly URL */
	private function getPBitly() {

		$urls = Mage::getModel('socialbookmarking/urls')->getCollection()->addFieldToFilter('url',$this->getPUrl());
		if(count($urls)) {
			foreach($urls as $u) return $u->getBitly();
		} else {
			$config = Mage::getStoreConfig('socialbookmarking/bitly');

			foreach($config as $c => $i) if(empty($i)) return $this->getPUrl();

			$bitly = 'http://api.bit.ly/shorten?version='.$config['version'].'&longUrl='.urlencode($this->getPUrl()).'&login='.$config['login'].'&apiKey='.$config['key'];
			if($response = @file_get_contents($bitly)) {
				$json = json_decode($response,true);
				if(isset($json['results']) && isset($json['results'][$this->getPUrl()]['shortUrl'])) {

					$model = Mage::getModel('socialbookmarking/urls');
					$model->setData('url',$this->getPUrl());
					$model->setData('bitly',$json['results'][$this->getPUrl()]['shortUrl']);
					$model->save();

					return $json['results'][$this->getPUrl()]['shortUrl'];
				} else {
					return $this->getPUrl();
				}
			} else {
				return $this->getPUrl();
			}
		}
	}
}