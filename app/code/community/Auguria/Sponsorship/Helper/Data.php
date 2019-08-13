<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isASponsor($customerId = null)
	{
		if ($customerId == null) {
    		$customerId = Mage::getSingleton('customer/session')->getCustomerId();
    	}
		$godchildren = Mage::getResourceModel('customer/customer_collection')
							->addAttributeToFilter('sponsor', $customerId);
		if ($godchildren->count()>0) {
			return true;
		}
		return false;
	}
	
    public function haveOrder ($customerId = null)
    {
    	if ($customerId == null) {
    		$customerId = Mage::getSingleton('customer/session')->getCustomerId();
    	}
    	$commande = Mage::getModel("sales/order")
                    ->getCollection()
                    ->addAttributeToFilter('customer_id',$customerId);
    	if ($commande->getData()) {
    		return true;
    	}
    	else {
    		return false;
    	}
    }

    //Ajout de la methode pour rechercher l'id d'un parrain eventuel
    public function searchSponsorId($emailCustomer)
    {
        $idParrain = '';
        $cookie = new Mage_Core_Model_Cookie;
        $sponsorInvitationValidity = Mage::getStoreConfig('auguria_sponsorship/sponsor/sponsor_invitation_validity');
        //recherche du mail dans les logs actifs
        $parrainageLog = Mage::getModel("auguria_sponsorship/sponsorship")
                        ->getCollection()
                                ->addAttributeToFilter('child_mail', $emailCustomer)
                                ->addDateToFilter('datetime', $sponsorInvitationValidity)
                                ->setOrder('datetime','asc')
                                ->getLastItem();
        //si mail dans les logs parrain selection du log actif le plus ancien
        $parrain = $parrainageLog->getData('parent_id');
        if (isset($parrain) && $parrain != '') {
                $idParrain = $parrain;
        }
        //sinon, si il existe on prend l'id stocké dans le cookie
        else {
                if ($cookie->get('sponsorship_id')) {
                        $idParrain = $cookie->get('sponsorship_id');
                }
        //sinon, si il existe on prend l'id stocké dans la session : au cas où les cookies sont désactivés.
                else {
                        if (Mage::getSingleton('core/session')->getData('sponsor_id')) {
                                $idParrain = Mage::getSingleton('core/session')->getData('sponsor_id');
                        }
                }
        }
        return $idParrain;
    }
    
	/**
     * Check SIRET format
     * @string $siret
     * @return boolean
     */
    public function isSiret ($siret)
    {
    	$siret = str_replace ( ' ', '', $siret );
    	if (strlen ( $siret ) != 14 || !is_numeric ( $siret )) {
			return false;
		}
		$siren = substr ( $siret, 0, 9 );
		if (! $this->isSiren ( $siren )) {
			return false;
		}
		$total = 0;
		for($i = 0; $i < 14; $i++) {
			$temp = substr ( $siret, $i, 1 );
			if ($i % 2 == 0) {
				$temp *= 2;
				if ($temp > 9) {
					$temp -= 9;
				}
			}
			$total += $temp;
		}
		return (($total % 10) == 0);
	}
	
	/** 
	 * Check SIREN format
	 * @param string $siren
	 * @return boolean
	 */
	public function isSiren($siren)
	{
		$siren = str_replace ( ' ', '', $siren );
		if (strlen ( $siren ) != 9 || !is_numeric ( $siren )) {
			return false;
		}
		$total = 0;
		for($i = 0; $i < 9; $i++) {
			$temp = substr ( $siren, $i, 1 );
			if ($i % 2 == 1) {
				$temp *= 2;
				if ($temp > 9) {
					$temp -= 9;
				}
			}
			$total += $temp;
		}
		return (($total % 10) == 0);
	}
	
	public function getPoints($customer=null)
	{
		if($customer==null){
			$customer = Mage::getSingleton('customer/session')->getCustomer();
		}
		$mode = Mage::helper('auguria_sponsorship/config')->getModuleMode();
		$points = Array();
		
		if ($mode=='accumulated') {
			$points['accumulated'] = (float)$customer->getAccumulatedPoints();
		}
		else {
			if($mode=='separated'
			|| $mode=='fidelity') {
				$points['fidelity'] = (float)$customer->getFidelityPoints();
			}
			if($mode=='separated'
			|| $mode=='sponsorship') {
				$points['sponsorship'] = (float)$customer->getSponsorPoints();
			}
		}
		return $points;
	}
	
	public function getCustomerAvailablePoints()
	{
		$customerPoints = $this->getPoints();
		$customerSessionPoints = Mage::getSingleton('customer/session')->getData('auguria_gift_points');
		$availablePoints = Array();
		if (count($customerPoints>0)) {
			foreach ($customerPoints as $mode=>$points) {
				if (isset($customerSessionPoints[$mode])) {
					$availablePoints[$mode] = $points-(float)$customerSessionPoints[$mode];
				}
				else {
					$availablePoints[$mode] = $points;
				}
			}
		}
		return $availablePoints;
	}
	
	public function getFidelityPoints($customer=null)
	{
		if($customer==null){
			$customer = Mage::getSingleton('customer/session')->getCustomer();
		}
		$mode = Mage::helper('auguria_sponsorship/config')->getModuleMode();
		$points = 0;
		
		if ($mode=='accumulated') {
			$points = (float)$customer->getAccumulatedPoints();
		}
		elseif($mode=='separated'
			|| $mode=='fidelity') {
				$points = (float)$customer->getFidelityPoints();
		}
		return $points;
	}
	
	public function getSponsorshipPoints($customer=null)
	{
		if($customer==null){
			$customer = Mage::getSingleton('customer/session')->getCustomer();
		}
		$mode = Mage::helper('auguria_sponsorship/config')->getModuleMode();
		$points = 0;
		
		if ($mode=='accumulated') {
			$points = (float)$customer->getAccumulatedPoints();
		}
		elseif($mode=='separated'
			|| $mode=='sponsorship') {
				$points = (float)$customer->getSponsorPoints();
		}
		return $points;
	}
	
	public function setFidelityPoints($customer=null, $points=0)
	{
		if($customer==null){
			$customer = Mage::getSingleton('customer/session')->getCustomer();
		}
		
		if (Mage::helper('auguria_sponsorship/config')->isAccumulatedEnabled()) {
    		$pointsType = 'accumulated_points';
    	}
    	else {
    		$pointsType = 'fidelity_points';
    	}
		
    	$customer->setData($pointsType, $points);
		
		return $customer;
	}
	
	public function setSponsorshipPoints($customer=null, $points=0)
	{
		if($customer==null){
			$customer = Mage::getSingleton('customer/session')->getCustomer();
		}
		
		if (Mage::helper('auguria_sponsorship/config')->isAccumulatedEnabled()) {
    		$pointsType = 'accumulated_points';
    	}
    	else {
    		$pointsType = 'sponsor_points';
    	}
		
    	$customer->setData($pointsType, $points);
		
		return $customer;
	}
	
	public function setSponsorshipValidity($customer=null)
	{
		if($customer==null){
			$customer = Mage::getSingleton('customer/session')->getCustomer();
		}
		if (Mage::helper('auguria_sponsorship/config')->isAccumulatedEnabled()) {
    		$validityType = 'points_validity';
    	}
    	else {
    		$validityType = 'sponsor_points_validity';
    	}
		$validity = $this->getPointsValidity('sponsorship');
		if(isset($validity['sponsorship'])) {
			$customer->setData($validityType,$validity['sponsorship']);
		}
		
		return $customer;
	}
	
	public function setFidelityValidity($customer=null)
	{
		if($customer==null){
			$customer = Mage::getSingleton('customer/session')->getCustomer();
		}
		if (Mage::helper('auguria_sponsorship/config')->isAccumulatedEnabled()) {
    		$validityType = 'points_validity';
    	}
    	else {
    		$validityType = 'fidelity_points_validity';
    	}
		$validity = $this->getPointsValidity('fidelity');
		if(isset($validity['fidelity'])) {
			$customer->setData($validityType,$validity['fidelity']);
		}
		
		return $customer;
	}
	
	public function addFidelityPoints($customer=null, $points=0)
	{
		if($customer==null){
			$customer = Mage::getSingleton('customer/session')->getCustomer();
		}
		$originalPoints = $this->getFidelityPoints($customer);
		
		$this->setFidelityPoints($customer, (float)$originalPoints+(float)$points);
		$this->setFidelityValidity($customer);
		
		return $customer;
	}
	
	public function addSponsorshipPoints($customer=null, $points=0)
	{
		if($customer==null){
			$customer = Mage::getSingleton('customer/session')->getCustomer();
		}
		$originalPoints = $this->getSponsorshipPoints($customer);
		
		$this->setSponsorshipPoints($customer, (float)$originalPoints+(float)$points);
		$this->setSponsorshipValidity($customer);
		
		return $customer;
	}
	
	public function getPointsValidity($type=null)
	{
		$validity = Array();
		$dateValidity = Array();
		
		if ($type==null) {
			$validity['fidelity'] = Mage::helper('auguria_sponsorship/config')->getFidelityPointsValidity();
			$validity['sponsorship'] = Mage::helper('auguria_sponsorship/config')->getSponsorshipPointsValidity();
		}
		elseif($type=='fidelity') {
			$validity['fidelity'] = Mage::helper('auguria_sponsorship/config')->getFidelityPointsValidity();
		}
		elseif($type=='sponsorship') {
			$validity['sponsorship'] = Mage::helper('auguria_sponsorship/config')->getSponsorshipPointsValidity();
		}
		
		if (count($validity)>0) {
			foreach ($validity as $type=>$day) {
				if ($day>0) {
					$date = new Zend_Date();
			    	$date->addDay($day);
			    	$dateValidity[$type] = $date->toString(Zend_Date::ISO_8601);
				}
			}
		}
		return $dateValidity;
	}
	
	public function getCash($customer=null)
	{
		if($customer==null){
			$customer = Mage::getSingleton('customer/session')->getCustomer();
		}
		$points = $this->getCustomerAvailablePoints();
		$mode = Mage::helper('auguria_sponsorship/config')->getModuleMode();
		$pointsToCash = Mage::helper('auguria_sponsorship/config')->getPointsToCash($mode);
		$cash = Array();
		if ($mode=='accumulated') {
			$cash['accumulated'] = (float)$pointsToCash['accumulated']*(float)$points['accumulated'];
		}
		else {
			if($mode=='separated'
			|| $mode=='fidelity') {
				$cash['fidelity'] = (float)$pointsToCash['fidelity']*(float)$points['fidelity'];
			}
			if($mode=='separated'
			|| $mode=='sponsorship') {
				$cash['sponsorship'] = (float)$pointsToCash['sponsorship']*(float)$points['sponsorship'];
			}
		}
		return $cash;
	}
	
	/**
     * Get sponsor id from customer id
     * @param integer $cId
     */
    public function getSponsorId($cId)
    {
    	$customer = Mage::getModel('customer/customer')->load($cId);
    	$sponsorId = $customer->getSponsor();
    	if ($sponsorId != null && $sponsorId != 0) {
    		return $sponsorId;
    	}
    	else {
    		return false;
    	}
    }
}