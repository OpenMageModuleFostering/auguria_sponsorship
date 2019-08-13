<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Helper_Config extends Mage_Core_Helper_Abstract
{
	/**
	 * Get module mode from config
	 * @return string
	 */
	public function getModuleMode()
	{
		return Mage::getStoreConfig('auguria_sponsorship/general/module_mode');
	}

	public function getPointsTypes()
	{
		$types = Array();
		$mode = $this->getModuleMode();
		if ($this->isAccumulatedEnabled()) {
			$types[] = 'accumulated';
		}
		if($this->isFidelityEnabled()) {
			$types[] = 'fidelity';
		}
		if($this->isSponsorshipEnabled()) {
			$types[] = 'sponsor';
		}
		return $types;
	}

	/**
	 * Check if fidelity mode is activated (accumulated not included)
	 * @return boolean
	 */
	public function isFidelityEnabled()
	{
		$mode = $this->getModuleMode();
		if($mode=='fidelity'
				|| $mode=='separated') {
			return true;
		}
		return false;
	}

	/**
	 * Check if sponsorship mode is activated (accumulated not included)
	 * @return boolean
	 */
	public function isSponsorshipEnabled()
	{
		$mode = $this->getModuleMode();
		if($mode=='sponsorship'
				|| $mode=='separated') {
			return true;
		}
		return false;
	}

	/**
	 * Check if accumulated mode is activated
	 * @return boolean
	 */
	public function isAccumulatedEnabled()
	{
		$mode = $this->getModuleMode();
		if($mode=='accumulated') {
			return true;
		}
		return false;
	}

	/**
	 * Get points validity from configuration depending on mode
	 * @param string $mode
	 * @return int
	 */
	public function getPointsValidity($mode=null)
	{
		$config = Array();
		if ($mode==null) {
			$mode = $this->getModuleMode();
		}

		if ($mode=='accumulated') {
			$config['accumulated'] = Mage::getStoreConfig('auguria_sponsorship/accumulated/points_validity');
		}
		else {
			if($mode=='separated'
					|| $mode=='fidelity') {
				$config['fidelity'] = Mage::getStoreConfig('auguria_sponsorship/fidelity/points_validity');
			}
			if($mode=='separated'
					|| $mode=='sponsorship') {
				$config['sponsorship'] = Mage::getStoreConfig('auguria_sponsorship/sponsor/points_validity');
			}
		}
		return $config;
	}

	public function getFidelityPointsValidity($mode=null)
	{
		if ($mode==null) {
			$mode = $this->getModuleMode();
		}

		if ($mode=='accumulated') {
			return Mage::getStoreConfig('auguria_sponsorship/accumulated/points_validity');
		}
		elseif ($mode=='separated'
				|| $mode=='fidelity') {
			return  Mage::getStoreConfig('auguria_sponsorship/fidelity/points_validity');
		}
		return 0;
	}

	public function getSponsorshipPointsValidity($mode=null)
	{
		if ($mode==null) {
			$mode = $this->getModuleMode();
		}

		if ($mode=='accumulated') {
			return Mage::getStoreConfig('auguria_sponsorship/accumulated/points_validity');
		}
		elseif ($mode=='separated'
				|| $mode=='sponsorship') {
			return  Mage::getStoreConfig('auguria_sponsorship/sponsor/points_validity');
		}
		return 0;
	}


	/**
	 * Get points to cash value from configuration depending on mode
	 * @param string $mode
	 * @return float
	 */
	public function getPointsToCash($mode=null)
	{
		$config = Array();
		if ($mode==null) {
			$mode = $this->getModuleMode();
		}
		if ($mode=='accumulated') {
			$config['accumulated'] = Mage::getStoreConfig('auguria_sponsorship/accumulated/points_to_cash');
		}
		else {
			if($mode=='separated'
					|| $mode=='fidelity') {
				$config['fidelity'] = Mage::getStoreConfig('auguria_sponsorship/fidelity/fidelity_points_to_cash');
			}
			if($mode=='separated'
					|| $mode=='sponsorship') {
				$config['sponsorship'] = Mage::getStoreConfig('auguria_sponsorship/sponsor/sponsor_points_to_cash');
			}
		}
		return $config;
	}

	/**
	 * Check if cart exchange is activated depending on module mode
	 * @param string $mode
	 * @return boolean
	 */
	public function getCartExchangeActivated($mode=null)
	{
		$activated = Array();
		if ($mode==null) {
			$mode = $this->getModuleMode();
		}
		if ($mode=='accumulated') {
			$activated['accumulated'] = Mage::getStoreConfig('auguria_sponsorship/accumulated/cart');
		}
		else {
			if($mode=='separated'
					|| $mode=='fidelity') {
				$activated['fidelity'] = Mage::getStoreConfig('auguria_sponsorship/fidelity/fidelity_cart');
			}
			if($mode=='separated'
					|| $mode=='sponsorship') {
				$activated['sponsorship'] = Mage::getStoreConfig('auguria_sponsorship/sponsor/sponsor_cart');
			}
		}
		return $activated;
	}

	public function isInvitAllowedWithoutOrder($mode=null)
	{
		if ($mode==null) {
			$mode = $this->getModuleMode();
		}

		if ($mode=='accumulated') {
			return Mage::getStoreConfig('auguria_sponsorship/accumulated/optional_order');
		}
		elseif ($mode=='sponsorship'
				|| $mode=='separated') {
			return Mage::getStoreConfig('auguria_sponsorship/sponsor/sponsor_optional_order');
		}
		return 0;
	}

	/**
	 * Get sponsorship percent from configuration depending on module mode
	 * @param string $mode
	 * @return float
	 */
	public function getSponsorPercent($mode=null)
	{
		if ($mode==null) {
			$mode = $this->getModuleMode();
		}

		if ($mode=='accumulated') {
			return Mage::getStoreConfig('auguria_sponsorship/accumulated/sponsor_percent');
		}
		elseif ($mode=='sponsorship'
				|| $mode=='separated') {
			return Mage::getStoreConfig('auguria_sponsorship/sponsor/sponsor_percent');
		}
		return 0;
	}


	/**
	 * Check if invit registred users is allowed
	 * @param string $mode
	 * @return boolean
	 */
	public function isRegistredUsersInvitationAllowed($mode=null)
	{
		if ($mode==null) {
			$mode = $this->getModuleMode();
		}

		if ($mode=='accumulated') {
			return Mage::getStoreConfig('auguria_sponsorship/accumulated/allow_invit_registred_users');
		}
		elseif ($mode=='sponsorship'
				|| $mode=='separated') {
			return Mage::getStoreConfig('auguria_sponsorship/sponsor/allow_invit_registred_users');
		}
		return 0;
	}

	/**
	 * Get points earned for subscription to the newsletter depending on module mode
	 * @param string $mode
	 * @return int
	 */
	public function getFidelityNewsletterPoints($mode=null)
	{
		if ($mode==null) {
			$mode = $this->getModuleMode();
		}

		if ($mode=='accumulated') {
			return Mage::getStoreConfig('auguria_sponsorship/accumulated/newsletter_points');
		}
		elseif ($mode=='fidelity'
				|| $mode=='separated') {
			return Mage::getStoreConfig('auguria_sponsorship/fidelity/newsletter_points');
		}
		return 0;
	}

	/**
	 * Get points earned for first order depending on module mode
	 * @param string $mode
	 * @return int
	 */
	public function getFidelityFirstOrderPoints($mode=null)
	{
		if ($mode==null) {
			$mode = $this->getModuleMode();
		}

		if ($mode=='accumulated') {
			return Mage::getStoreConfig('auguria_sponsorship/accumulated/first_order_points');
		}
		elseif ($mode=='fidelity'
				|| $mode=='separated') {
			return Mage::getStoreConfig('auguria_sponsorship/fidelity/first_order_points');
		}
		return 0;
	}

	/**
	 * Get points earned for first order depending on module mode
	 * @param string $mode
	 * @return int
	 */
	public function getGodsonFirstOrderPoints($mode=null)
	{
		if ($mode==null) {
			$mode = $this->getModuleMode();
		}

		if ($mode=='accumulated') {
			return Mage::getStoreConfig('auguria_sponsorship/accumulated/godson_first_order_points');
		}
		elseif ($mode=='sponsorship'
				|| $mode=='separated') {
			return Mage::getStoreConfig('auguria_sponsorship/sponsor/godson_first_order_points');
		}
		return 0;
	}


	/**
	 * Get sponsorship max levels from configuration depending on module mode
	 * @param string $mode
	 * @return float
	 */
	public function getSponsorLevels($mode=null)
	{
		if ($mode==null) {
			$mode = $this->getModuleMode();
		}

		if ($mode=='accumulated') {
			return Mage::getStoreConfig('auguria_sponsorship/accumulated/sponsor_levels');
		}
		elseif ($mode=='sponsorship'
				|| $mode=='separated') {
			return Mage::getStoreConfig('auguria_sponsorship/sponsor/sponsor_levels');
		}
		return 0;
	}



	/**
	 * Check if sponsorship notification is enabled depending on module mode
	 * @param string $mode
	 * @return boolean
	 */
	public function isSponsorshipNotificationEnabled($mode=null)
	{
		if ($mode==null) {
			$mode = $this->getModuleMode();
		}

		if ($mode=='accumulated') {
			return Mage::getStoreConfig('auguria_sponsorship/accumulated/notification_enabled');
		}
		elseif ($mode=='sponsorship'
				|| $mode=='separated') {
			return Mage::getStoreConfig('auguria_sponsorship/sponsor/notification_enabled');
		}
		return false;
	}

	/**
	 * Check if sponsorship notification is enabled depending on module mode
	 * @param string $mode
	 * @return boolean
	 */
	public function getSponsorshipNotificationTemplate($mode=null)
	{
		if ($mode==null) {
			$mode = $this->getModuleMode();
		}

		if ($mode=='accumulated') {
			return Mage::getStoreConfig('auguria_sponsorship/accumulated/notification');
		}
		elseif ($mode=='sponsorship'
				|| $mode=='separated') {
			return Mage::getStoreConfig('auguria_sponsorship/sponsor/notification');
		}
		return false;
	}

	/**
	 * Get sponsorship invitation validity depending on module mode
	 * @param string $mode
	 * @return float
	 */
	public function getInvitationValidity()
	{

		return Mage::getStoreConfig('auguria_sponsorship/invitation/sponsor_invitation_validity');
	}

	public function cancelFidelityEarnedPointsOnCreditMemo($mode=null)
	{
		if ($mode==null) {
			$mode = $this->getModuleMode();
		}

		if ($mode=='accumulated') {
			return Mage::getStoreConfig('auguria_sponsorship/accumulated/cancel_earned_points');
		}
		elseif ($mode=='fidelity'
				|| $mode=='separated') {
			return Mage::getStoreConfig('auguria_sponsorship/fidelity/cancel_earned_points');
		}
		return false;

	}

	public function cancelSponsorshipEarnedPointsOnCreditMemo($mode=null)
	{
		if ($mode==null) {
			$mode = $this->getModuleMode();
		}

		if ($mode=='accumulated') {
			return Mage::getStoreConfig('auguria_sponsorship/accumulated/cancel_earned_points');
		}
		elseif ($mode=='sponsorship'
				|| $mode=='separated') {
			return Mage::getStoreConfig('auguria_sponsorship/sponsor/cancel_earned_points');
		}
		return false;

	}

	public function cancelUsedPointsOnCreditMemo($type)
	{
		if ($type=='accumulated') {
			return Mage::getStoreConfig('auguria_sponsorship/accumulated/cancel_cart_points');
		}
		elseif ($type=='sponsorship') {
			return Mage::getStoreConfig('auguria_sponsorship/sponsor/cancel_cart_points');
		}
		elseif ($type=='fidelity') {
			return Mage::getStoreConfig('auguria_sponsorship/fidelity/cancel_cart_points');
		}
		return false;

	}

	public function getAdministratorEmail(){		
		return Mage::getStoreConfig('auguria_sponsorship/cash/administrator_email');
	}
}