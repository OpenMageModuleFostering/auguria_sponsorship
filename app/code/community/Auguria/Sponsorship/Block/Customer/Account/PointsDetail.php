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
    
    public function getFidelityEnabledConfig ()
    {
    $fidelity_enabled = Mage::getStoreConfig('sponsorship/fidelity/fidelity_enabled');
    return $fidelity_enabled;
    }

    public function getFidelityCashConfig ()
    {
            $fidelity_cash = Mage::getStoreConfig('sponsorship/fidelity/fidelity_cash');
    return $fidelity_cash;
    }

    public function getFidelityCouponConfig ()
    {
            $fidelity_coupon = Mage::getStoreConfig('sponsorship/fidelity/fidelity_coupon');
            return $fidelity_coupon;
    }

    public function getFidelityGiftConfig ()
    {
            $fidelity_gift = Mage::getStoreConfig('sponsorship/fidelity/fidelity_gift');
    return $fidelity_gift;
    }

    public function getFidelityMaxCashConfig ()
    {
            $fidelity_max_cash = Mage::getStoreConfig('sponsorship/fidelity/fidelity_max_cash');
    return $fidelity_max_cash;
    }

    public function getFidelityTimeMaxCashConfig ()
    {
    $fidelity_time_max_cash = Mage::getStoreConfig('sponsorship/fidelity/fidelity_time_max_cash');
    return $fidelity_time_max_cash;
    }

    public function getSponsorEnabledConfig ()
    {
            $sponsor_enabled = Mage::getStoreConfig('sponsorship/sponsor/sponsor_enabled');
    return $sponsor_enabled;
    }

    public function getSponsorCashConfig ()
    {
    $sponsor_cash = Mage::getStoreConfig('sponsorship/sponsor/sponsor_cash');
    return $sponsor_cash;
    }

    public function getSponsorCouponConfig ()
    {
    $sponsor_coupon = Mage::getStoreConfig('sponsorship/sponsor/sponsor_coupon');
    return $sponsor_coupon;
    }

    public function getSponsorGiftConfig ()
    {
    $sponsor_gift = Mage::getStoreConfig('sponsorship/sponsor/sponsor_gift');
    return $sponsor_gift;
    }

    public function getSponsorMaxCashConfig ()
    {
    $sponsor_max_cash = Mage::getStoreConfig('sponsorship/sponsor/sponsor_max_cash');
    return $sponsor_max_cash;
    }

    public function getSponsorTimeMaxCashConfig ()
    {
    $sponsor_time_max_cash = Mage::getStoreConfig('sponsorship/sponsor/sponsor_time_max_cash');
    return $sponsor_time_max_cash;
    }

    public function getSponsorPointsToCashConfig ()
    {
            $sponsor_points_to_cash = Mage::getStoreConfig('sponsorship/sponsor/sponsor_points_to_cash');
    return $sponsor_points_to_cash;
    }

    public function getFidelityPointsToCashConfig ()
    {
            $fidelity_points_to_cash = Mage::getStoreConfig('sponsorship/fidelity/fidelity_points_to_cash');
    return $fidelity_points_to_cash;
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
								->addAttributeToFilter('sponsor', $this->getUserId());
		$parrains = $parrains->getData();
		return $parrains;
    }
    
    public function getNbParrainages ($customerId)
    {
        $sponsor = Mage::getModel("customer/customer")
                        ->getCollection()
                        //->addExpressionAttributeToSelect('nbSponsor','COUNT("entity_id")','sponsor')
                        ->addFilter('e.is_active', 1)
                        ->addAttributeToFilter('sponsor', $customerId)
                        //->groupByAttribute('entity_id');
                        ->count();
        return $sponsor;
    }
    
    public function getDateDernCde ($customerId)
    {
    	$commande = Mage::getModel("sales/order")
                        ->getCollection()
                        ->addAttributeToFilter('customer_id',$customerId)
                        ->addAttributeToSort('created_at', 'desc')
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
                            ->addAttributeToSort('created_at', 'desc')
                            ->setPageSize(5);
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
    
    public function getBranchPoints($customerId)
    {
    	try {		
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		
		$select = $read->select()
			->from($resource->getTableName('sponsorship/sponsorlog'), 'SUM(points)')
            ->where('godson_id=?', $customerId)
            ->where('sponsor_id=?', $this->getUserId());
        return $read->fetchOne($select);
    	}
    	catch (Exception $e) {
    	}
    }

    public function hasChange($module)
    {
        $changes = mage::getModel("sponsorship/change")
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
        $changes = mage::getModel("sponsorship/change")
            ->getCollection()
            ->addAttributeToFilter("module", $module)
            ->addAttributeToFilter("customer_id", $this->getUserId())
            ->setOrder('datetime', 'desc')
            ->setPageSize(5);
        return $changes;
    }

    public function getInvits()
    {
        /*
         *  select s.*  from sponsorship s
            where s.parent_id =1
            and s.child_mail not in (select ce.email from customer_entity ce )
            and s.datetime = (select max(sp.datetime) from sponsorship sp where sp.parent_id=1 and sp.child_mail = s.child_mail)
         */
        $resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
        $select = $read->select()
		->from(Array("s"=>$resource->getTableName('sponsorship/sponsorship')),
                            Array("*"=>"s.*"))
                ->where('s.parent_id=?', $this->getUserId())
                ->where('TO_DAYS(NOW()) - TO_DAYS(s.datetime) <= ?', Mage::getStoreConfig('sponsorship/invit_email/sponsor_invitation_validity'))
                ->where('s.child_mail NOT IN ?', new Zend_Db_Expr('(select ce.email from '.$resource->getTableName('customer_entity').' ce)'))
                ->where('s.datetime = ?', new Zend_Db_Expr('(select max(sp.datetime) from '.$resource->getTableName('sponsorship/sponsorship').' sp where sp.parent_id=s.parent_id and sp.child_mail = s.child_mail)'));
        return $read->fetchAll($select);
    }
}