<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Customer_Account_PointsDetail extends Mage_Customer_Block_Account_Dashboard
{
	public function _prepareLayout()
	{
		return parent::_prepareLayout();
	}

	/**
	 * Fidelity configuration
	 */

	public function isFidelityChangeEnabled ()
	{
		$isEnable = false;
		if ($this->getFidelityCashConfig()) {
			$isEnable = true;
		}
		elseif ($this->getFidelityCouponConfig()) {
			$isEnable = true;
		}
		elseif ($this->getFidelityGiftConfig()) {
			$isEnable = true;
		}
		return $isEnable;
	}

	public function getFidelityEnabledConfig ()
	{
		return Mage::helper('auguria_sponsorship/config')->isFidelityEnabled();
	}

	public function getFidelityCashConfig ()
	{
		$fidelity_cash = Mage::getStoreConfig('auguria_sponsorship/fidelity/fidelity_cash');
		return $fidelity_cash;
	}

	public function getFidelityCouponConfig ()
	{
		$fidelity_coupon = Mage::getStoreConfig('auguria_sponsorship/fidelity/fidelity_coupon');
		return $fidelity_coupon;
	}

	public function getFidelityGiftConfig ()
	{
		$fidelity_gift = Mage::getStoreConfig('auguria_sponsorship/fidelity/fidelity_gift');
		return $fidelity_gift;
	}

	public function getFidelityMaxCashConfig ()
	{
		$fidelity_max_cash = Mage::getStoreConfig('auguria_sponsorship/fidelity/fidelity_max_cash');
		return $fidelity_max_cash;
	}

	public function getFidelityTimeMaxCashConfig ()
	{
		$fidelity_time_max_cash = Mage::getStoreConfig('auguria_sponsorship/fidelity/fidelity_time_max_cash');
		return $fidelity_time_max_cash;
	}

	public function getFidelityPointsToCashConfig ()
	{
		$fidelity_points_to_cash = Mage::getStoreConfig('auguria_sponsorship/fidelity/fidelity_points_to_cash');
		return $fidelity_points_to_cash;
	}

	public function getFidelityEnableSiretConfig ()
	{
		$enableSiret = Mage::getStoreConfig('auguria_sponsorship/fidelity/enable_check_company_number');
		return $enableSiret;
	}

	/**
	 * Sponsorship configuration
	 */

	public function isSponsorChangeEnabled ()
	{
		$isEnable = false;
		if ($this->getSponsorCashConfig()) {
			$isEnable = true;
		}
		elseif ($this->getSponsorCouponConfig()) {
			$isEnable = true;
		}
		elseif ($this->getSponsorGiftConfig()) {
			$isEnable = true;
		}
		return $isEnable;
	}

	public function getSponsorEnabledConfig ()
	{
		return Mage::helper('auguria_sponsorship/config')->isSponsorshipEnabled();
	}

	public function getSponsorCashConfig ()
	{
		$sponsor_cash = Mage::getStoreConfig('auguria_sponsorship/sponsor/sponsor_cash');
		return $sponsor_cash;
	}

	public function getSponsorCouponConfig ()
	{
		$sponsor_coupon = Mage::getStoreConfig('auguria_sponsorship/sponsor/sponsor_coupon');
		return $sponsor_coupon;
	}

	public function getSponsorGiftConfig ()
	{
		$sponsor_gift = Mage::getStoreConfig('auguria_sponsorship/sponsor/sponsor_gift');
		return $sponsor_gift;
	}

	public function getSponsorMaxCashConfig ()
	{
		$sponsor_max_cash = Mage::getStoreConfig('auguria_sponsorship/sponsor/sponsor_max_cash');
		return $sponsor_max_cash;
	}

	public function getSponsorTimeMaxCashConfig ()
	{
		$sponsor_time_max_cash = Mage::getStoreConfig('auguria_sponsorship/sponsor/sponsor_time_max_cash');
		return $sponsor_time_max_cash;
	}

	public function getSponsorPointsToCashConfig ()
	{
		$sponsor_points_to_cash = Mage::getStoreConfig('auguria_sponsorship/sponsor/sponsor_points_to_cash');
		return $sponsor_points_to_cash;
	}

	public function getSponsorEnableSiretConfig ()
	{
		$enableSiret = Mage::getStoreConfig('auguria_sponsorship/sponsor/enable_check_company_number');
		return $enableSiret;
	}

	/**
	 * Accumulated configuration
	 */

	public function isAccumulatedChangeEnabled ()
	{
		$isEnable = false;
		if ($this->getAccumulatedCashConfig()) {
			$isEnable = true;
		}
		elseif ($this->getAccumulatedCouponConfig()) {
			$isEnable = true;
		}
		elseif ($this->getAccumulatedGiftConfig()) {
			$isEnable = true;
		}
		return $isEnable;
	}

	public function getAccumulatedEnabledConfig ()
	{
		return Mage::helper('auguria_sponsorship/config')->isAccumulatedEnabled();
	}

	public function getAccumulatedCashConfig ()
	{
		$accumulated_cash = Mage::getStoreConfig('auguria_sponsorship/accumulated/cash');
		return $accumulated_cash;
	}

	public function getAccumulatedCouponConfig ()
	{
		$accumulated_coupon = Mage::getStoreConfig('auguria_sponsorship/accumulated/coupon');
		return $accumulated_coupon;
	}

	public function getAccumulatedGiftConfig ()
	{
		$accumulated_gift = Mage::getStoreConfig('auguria_sponsorship/accumulated/gift');
		return $accumulated_gift;
	}

	public function getAccumulatedMaxCashConfig ()
	{
		$accumulated_max_cash = Mage::getStoreConfig('auguria_sponsorship/accumulated/max_cash');
		return $accumulated_max_cash;
	}

	public function getAccumulatedTimeMaxCashConfig ()
	{
		$accumulated_time_max_cash = Mage::getStoreConfig('auguria_sponsorship/accumulated/time_max_cash');
		return $accumulated_time_max_cash;
	}

	public function getAccumulatedPointsToCashConfig ()
	{
		$accumulated_points_to_cash = Mage::getStoreConfig('auguria_sponsorship/accumulated/points_to_cash');
		return $accumulated_points_to_cash;
	}

	public function getAccumulatedEnableSiretConfig ()
	{
		$enableSiret = Mage::getStoreConfig('auguria_sponsorship/accumulated/enable_check_company_number');
		return $enableSiret;
	}




	public function getUserId()
	{
		if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
			return '';
		}
		$customer = Mage::getSingleton('customer/session')->getCustomerId();
		return ($customer);
	}

	public function getParrainages() {
		$parrains = Mage::getModel("customer/customer")
		->getCollection()
		->addNameToSelect()
		->addAttributeToSort('created_at', 'desc')
		->addAttributeToFilter('sponsor', $this->getUserId());
		$parrains = $parrains->getData();
		return $parrains;
	}

	public function getNbParrainages ($customerId)
	{
		$sponsor = Mage::getModel("customer/customer")
		->getCollection()
		->addFilter('e.is_active', 1)
		->addAttributeToFilter('sponsor', $customerId)
		->count();
		return $sponsor;
	}

	public function getDateDernCde ($customerId)
	{
		$commande = Mage::getModel("sales/order")
		->getCollection()
		->addAttributeToFilter('customer_id',$customerId)
		->addAttributeToSort('created_at', 'asc')
		->getLastItem();
		if ($commande) {
			return $commande['created_at'];
		}
		else {
			return null;
		}
	}

	public function getCommandes()
	{
		$commandes = Mage::getModel("sales/order")
		->getCollection()
		->addAttributeToFilter('customer_id',$this->getUserId())
		->addAttributeToSort('created_at', 'desc');
		//->setPageSize(5);
		return $commandes->getData();
	}

	public function getOrderFidelityPoints($orderId)
	{
		$order = Mage::getModel("sales/order")->load($orderId);
		$points = 0;

		foreach ($order->getAllItems() as $item)
		{
			$points = $points + $item->getCartFidelityPoints() + $item->getCatalogFidelityPoints();
		}
		return $points;
	}

	public function getOrderAccumulatedPoints($orderId)
	{
		$order = Mage::getModel("sales/order")->load($orderId);
		$points = 0;

		foreach ($order->getAllItems() as $item)
		{
			$points = $points + $item->getCartFidelityPoints() + $item->getCatalogFidelityPoints()+ $item->getCartSponsorPoints() + $item->getCatalogSponsorPoints();
		}
		return $points;
	}

	public function getOrderSponsorPoints($orderId)
	{
		$order = Mage::getModel("sales/order")->load($orderId);
		$points = 0;

		foreach ($order->getAllItems() as $item)
		{
			$points = $points + $item->getCartSponsorPoints() + $item->getCatalogSponsorPoints();
		}
		return $points;
	}

	public function getFidelityPoints()
	{
		$customer = Mage::getModel("customer/customer")->load($this->getUserId());
		$cFP = $customer->getData('fidelity_points');
		return $cFP;
	}

	public function getSponsorPoints()
	{
		$customer = Mage::getModel("customer/customer")->load($this->getUserId());
		$cSP = $customer->getData('sponsor_points');
		return $cSP;
	}

	public function getAccumulatedPoints()
	{
		$customer = Mage::getModel("customer/customer")->load($this->getUserId());
		$cSP = $customer->getData('accumulated_points');
		return $cSP;
	}

	public function getBranchPoints($customerId)
	{
		try {
			$resource = Mage::getSingleton('core/resource');
			$read = $resource->getConnection('core_read');

			$select = $read->select()
			->from($resource->getTableName('auguria_sponsorship/log'), 'SUM(points)')
			->where('godson_id=?', $customerId)
			->where('sponsor_id=?', $this->getUserId());
			return $read->fetchOne($select);
		}
		catch (Exception $e) {
		}
	}

	public function hasChange($module)
	{
		$changes = mage::getModel("auguria_sponsorship/change")
		->getCollection()
		->addAttributeToFilter("module", $module)
		->addAttributeToFilter("customer_id", $this->getUserId())
		->count();
		if ($changes)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function getChanges($module)
	{
		$changes = Mage::getModel("auguria_sponsorship/change")
		->getCollection()
		->addAttributeToFilter("module", $module)
		->addAttributeToFilter("customer_id", $this->getUserId())
		->setOrder('datetime', 'desc');
		//->setPageSize(5);
		return $changes;
	}

	public function getPointsMovement()
	{
		$logs = Mage::getResourceModel('auguria_sponsorship/log_collection');
		$logs->getSelect()->where('sponsor_id = ?', $this->getUserId());
		$logs->getSelect()->orWhere('customer_id = ?', $this->getUserId());
		return $logs;
	}

	public function getAccumulatedPointsValue($points)
	{
		$pointsToCash = Mage::helper('auguria_sponsorship/config')->getPointsToCash('accumulated');
		$result = $pointsToCash['accumulated']*$points;
		return $result;
	}

	public function isAccumulatedValidityEnabled()
	{
		$validity = Mage::helper('auguria_sponsorship/config')->getPointsValidity('accumulated');
		if((int)$validity['accumulated']>0){
			return true;
		}
		return false;
	}

	public function getAccumulatedValidity()
	{
		return $this->formatDate($this->getCustomer()->getPointsValidity(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
	}

	public function getAccumulatedMovementType($movement)
	{
		if ($movement->getRecordType()=='order' && $movement->getPoints()>0) {
			return $this->__('Order');
		}
		elseif($movement->getRecordType()=='order') {
			return $this->__('Order cancellation');
		}
		elseif($movement->getRecordType()=='cart' && $movement->getPoints()<0) {
			return $this->__('Buying');
		}
		elseif($movement->getRecordType()=='cart') {
			return $this->__('Buying cancellation');
		}
		elseif($movement->getRecordType()=='admin' ) {
			return $this->__('Manual changes');
		}
		elseif($movement->getRecordType()=='newsletter') {
			return $this->__('Newsletter subscription');
		}
		elseif($movement->getRecordType()=='validity') {
			return $this->__('Expiry');
		}
		elseif($movement->getRecordType()=='first' && $movement->getCustomerId()!=null) {
			return $this->__('First order');
		}
		elseif($movement->getRecordType()=='first') {
			return $this->__('Godchild first order');
		}
		elseif($movement->getRecordType()=='gift' && $movement->getPoints()<0) {
			return $this->__('Gift exchange');
		}
		elseif($movement->getRecordType()=='gift') {
			return $this->__('Gift exchange cancellation');
		}
		elseif($movement->getRecordType()=='cash' && $movement->getPoints()<0) {
			return $this->__('Cash exchange');
		}
		elseif($movement->getRecordType()=='cash') {
			return $this->__('Cash exchange cancellation');
		}
		elseif($movement->getRecordType()=='coupon_code' && $movement->getPoints()<0) {
			return $this->__('Coupon exchange');
		}
		elseif($movement->getRecordType()=='coupon_code') {
			return $this->__('Coupon exchange cancellation');
		}
		else {
			return $this->__('Movement');
		}
	}

	public function getInvits()
	{
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		$datetime = Mage::getModel('core/date')->gmtDate();
		$select = $read->select()
		->from(Array("s"=>$resource->getTableName('auguria_sponsorship/sponsorship')),array("*"=>"s.*"))
		->where('s.parent_id=?', $this->getUserId())
		->where('TO_DAYS("'.$datetime.'") - TO_DAYS(s.datetime) <= ?', Mage::getStoreConfig('auguria_sponsorship/invitation/sponsor_invitation_validity'))
		->where('s.child_mail NOT IN ?',
				new Zend_Db_Expr('(select ce.email from '.$resource->getTableName('customer_entity').' ce
						LEFT JOIN '.$resource->getTableName('eav_attribute').' AS ea ON ce.entity_type_id = ea.entity_type_id AND ea.backend_type = "int" AND ea.attribute_code = "sponsor"
						LEFT JOIN '.$resource->getTableName('customer_entity_int').' AS cev ON ce.entity_id = cev.entity_id AND ea.attribute_id = cev.attribute_id
						WHERE cev.value IS NOT NULL)'))
						->where('s.datetime = ?',
								new Zend_Db_Expr('(select max(sp.datetime) from '.$resource->getTableName('auguria_sponsorship/sponsorship').' sp where sp.parent_id=s.parent_id and sp.child_mail = s.child_mail)'))
								->order('datetime_boost')
								->order('datetime');
		return $read->fetchAll($select);
	}
}