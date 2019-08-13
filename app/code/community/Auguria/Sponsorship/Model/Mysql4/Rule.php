<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Model_Mysql4_Rule extends Mage_CatalogRule_Model_Mysql4_Rule
{
	protected function _construct()
    {
        parent::_construct();
    }
    
    public function applyAllRulesForDateRange($fromDate=null, $toDate=null, $productId=null)
    {
        $write = $this->_getWriteAdapter();
        $write->beginTransaction();

        Mage::dispatchEvent('catalogrule_before_apply', array('resource'=>$this));

        $clearOldData = false;
        if ($fromDate === null) {
            $fromDate = mktime(0,0,0,date('m'),date('d')-1);
            /**
             * If fromDate not specified we can delete all data oldest than 1 day
             * We have run it for clear table in case when cron was not installed
             * and old data exist in table
             */
            $clearOldData = true;
        }
        if (is_string($fromDate)) {
            $fromDate = strtotime($fromDate);
        }
        if ($toDate === null) {
            $toDate = mktime(0,0,0,date('m'),date('d')+1);
        }
        if (is_string($toDate)) {
            $toDate = strtotime($toDate);
        }

        $product = null;
        if ($productId instanceof Mage_Catalog_Model_Product) {
            $product = $productId;
            $productId = $productId->getId();
        }

        $this->removeCatalogPricesForDateRange($fromDate, $toDate, $productId);
        if ($clearOldData) {
            $this->deleteOldData($fromDate, $productId);
        }

        try {
	        /**
	         * Update products rules prices per each website separatly
	         * because of max join limit in mysql
	         */
	        foreach (Mage::app()->getWebsites(false) as $website) {
	            $productsStmt = $this->_getRuleProductsStmt(
	               $fromDate,
	               $toDate,
	               $productId,
	               $website->getId()
	            );

	            $dayPrices  = array();
	            
	            $dayFidelityPoints = array();
	            $daySponsorPoints = array();
	            $stopFlags  = array();
	            $prevKey    = null;
				
	            while ($ruleData = $productsStmt->fetch()) {
	            	
	            	$ruleActionOperator = $ruleData['action_operator'];
	            	
	                $ruleProductId = $ruleData['product_id'];
	                $productKey= $ruleProductId . '_'
	                   . $ruleData['website_id'] . '_'
	                   . $ruleData['customer_group_id'];
					
	                if ($prevKey && ($prevKey != $productKey)) {
	                    $stopFlags = array();
	                }

	                /**
	                 * Build prices for each day
	                 */
	                //si c'est une règle de prix
	                if ($ruleActionOperator == 'to_fixed' ||
	                	$ruleActionOperator == 'to_percent' ||
	                	$ruleActionOperator == 'by_fixed' ||
	                	$ruleActionOperator == 'by_percent')
	                {
		                for ($time=$fromDate; $time<=$toDate; $time+=self::SECONDS_IN_DAY) {
		                    if (($ruleData['from_time']==0 || $time >= $ruleData['from_time'])
		                        && ($ruleData['to_time']==0 || $time <=$ruleData['to_time'])) {
	
		                        $priceKey = $time . '_' . $productKey;
	
		                        if (isset($stopFlags[$priceKey])) {
		                            continue;
		                        }
	
		                        if (!isset($dayPrices[$priceKey])) {
		                            $dayPrices[$priceKey] = array(
		                                'rule_date'         => $time,
		                                'website_id'        => $ruleData['website_id'],
		                                'customer_group_id' => $ruleData['customer_group_id'],
		                                'product_id'        => $ruleProductId,
		                                'rule_price'        => $this->_calcRuleProductPrice($ruleData),
		                                'latest_start_date' => $ruleData['from_time'],
		                                'earliest_end_date' => $ruleData['to_time'],
		                            );
		                        }
		                        else {
		                            $dayPrices[$priceKey]['rule_price'] = $this->_calcRuleProductPrice(
		                                $ruleData,
		                                $dayPrices[$priceKey]
		                            );
		                            $dayPrices[$priceKey]['latest_start_date'] = max(
		                                $dayPrices[$priceKey]['latest_start_date'],
		                                $ruleData['from_time']
		                            );
		                            $dayPrices[$priceKey]['earliest_end_date'] = min(
		                                $dayPrices[$priceKey]['earliest_end_date'],
		                                $ruleData['to_time']
		                            );
		                        }
	
		                        if ($ruleData['action_stop']) {
		                            $stopFlags[$priceKey] = true;
		                        }
		                    }
		                }
	                }
	                //si c'est une règle de points fidélité
					elseif ($ruleActionOperator == 'fidelity_points_to_percent' ||
							$ruleActionOperator == 'fidelity_points_to_fixed')
	            	{
		                for ($time=$fromDate; $time<=$toDate; $time+=self::SECONDS_IN_DAY) {
		                    if (($ruleData['from_time']==0 || $time >= $ruleData['from_time'])
		                        && ($ruleData['to_time']==0 || $time <=$ruleData['to_time'])) {
	
		                        $priceKey = $time . '_' . $productKey;
	
		                        if (isset($stopFlags[$priceKey])) {
		                            continue;
		                        }
	
		                        if (!isset($dayFidelityPoints[$priceKey])) {
		                            $dayFidelityPoints[$priceKey] = array(
		                                'rule_date'         => $time,
		                                'website_id'        => $ruleData['website_id'],
		                                'customer_group_id' => $ruleData['customer_group_id'],
		                                'product_id'        => $ruleProductId,
		                                'rule_price'        => $this->_calcRuleProductPrice($ruleData),
		                                'latest_start_date' => $ruleData['from_time'],
		                                'earliest_end_date' => $ruleData['to_time'],
		                            );
		                        }
		                        else {
		                            $dayFidelityPoints[$priceKey]['rule_price'] = $this->_calcRuleProductPrice(
		                                $ruleData,
		                                $dayFidelityPoints[$priceKey]
		                            );
		                            $dayFidelityPoints[$priceKey]['latest_start_date'] = max(
		                                $dayFidelityPoints[$priceKey]['latest_start_date'],
		                                $ruleData['from_time']
		                            );
		                            $dayFidelityPoints[$priceKey]['earliest_end_date'] = min(
		                                $dayFidelityPoints[$priceKey]['earliest_end_date'],
		                                $ruleData['to_time']
		                            );
		                        }
	
		                        if ($ruleData['action_stop']) {
		                            $stopFlags[$priceKey] = true;
		                        }
		                    }
		                }
	                }
	                else if ($ruleActionOperator == 'sponsor_points_to_percent' ||
							$ruleActionOperator == 'sponsor_points_to_fixed')
					{
						for ($time=$fromDate; $time<=$toDate; $time+=self::SECONDS_IN_DAY) {
		                    if (($ruleData['from_time']==0 || $time >= $ruleData['from_time'])
		                        && ($ruleData['to_time']==0 || $time <=$ruleData['to_time'])) {
	
		                        $priceKey = $time . '_' . $productKey;
	
		                        if (isset($stopFlags[$priceKey])) {
		                            continue;
		                        }
	
		                        if (!isset($daySponsorPoints[$priceKey])) {
		                            $daySponsorPoints[$priceKey] = array(
		                                'rule_date'         => $time,
		                                'website_id'        => $ruleData['website_id'],
		                                'customer_group_id' => $ruleData['customer_group_id'],
		                                'product_id'        => $ruleProductId,
		                                'rule_price'        => $this->_calcRuleProductPrice($ruleData),
		                                'latest_start_date' => $ruleData['from_time'],
		                                'earliest_end_date' => $ruleData['to_time'],
		                            );
		                        }
		                        else {
		                            $daySponsorPoints[$priceKey]['rule_price'] = $this->_calcRuleProductPrice(
		                                $ruleData,
		                                $daySponsorPoints[$priceKey]
		                            );
		                            $daySponsorPoints[$priceKey]['latest_start_date'] = max(
		                                $daySponsorPoints[$priceKey]['latest_start_date'],
		                                $ruleData['from_time']
		                            );
		                            $daySponsorPoints[$priceKey]['earliest_end_date'] = min(
		                                $daySponsorPoints[$priceKey]['earliest_end_date'],
		                                $ruleData['to_time']
		                            );
		                        }
	
		                        if ($ruleData['action_stop']) {
		                            $stopFlags[$priceKey] = true;
		                        }
		                    }
		                }
					}//fin elsif
	                
	                $prevKey = $productKey;

	                if ((count($dayPrices)+count($dayFidelityPoints)+count($daySponsorPoints))>100) {
	                    $this->_saveRuleProductPrices($dayPrices);
	                    $this->_saveRuleProductPoints($dayFidelityPoints,'fidelity');
	                    $this->_saveRuleProductPoints($daySponsorPoints,'sponsor');
	                    $dayPrices = array();
	                    $dayFidelityPoints = array();
	                    $daySponsorPoints = array();
	                }
	            }
	            $this->_saveRuleProductPrices($dayPrices);
	            $this->_saveRuleProductPoints($dayFidelityPoints,'fidelity');
	            $this->_saveRuleProductPoints($daySponsorPoints,'sponsor');
	        }
	        $this->_saveRuleProductPrices($dayPrices);
	        $this->_saveRuleProductPoints($dayFidelityPoints,'fidelity');
	        $this->_saveRuleProductPoints($daySponsorPoints,'sponsor');
	        $write->commit();

        } catch (Exception $e) {
            $write->rollback();
            throw $e;
        }

        $productCondition = Mage::getModel('catalog/product_condition')
            ->setTable($this->getTable('catalogrule/affected_product'))
            ->setPkFieldName('product_id');
        Mage::dispatchEvent('catalogrule_after_apply', array(
            'product'=>$product,
            'product_condition' => $productCondition
        ));
        $write->delete($this->getTable('catalogrule/affected_product'));

        return $this;
    }

    protected function _calcRuleProductPrice($ruleData, $productData=null)
    {
        if ($productData !== null && isset($productData['rule_price'])) {
            $productPrice = $productData['rule_price'];
        }
        else {
            $websiteId = $ruleData['website_id'];
            if (isset($ruleData['website_'.$websiteId.'_price'])) {
                $productPrice = $ruleData['website_'.$websiteId.'_price'];
            }
            else {
                $productPrice = $ruleData['default_price'];
            }
        }

        $amount = $ruleData['action_amount'];
        switch ($ruleData['action_operator']) {
            case 'to_fixed':
                $productPrice = $amount;
                break;
            case 'fidelity_points_to_fixed':
                $productPrice = $amount;
                break;
            case 'sponsor_points_to_fixed':
                $productPrice = $amount;
                break;
            case 'to_percent':
                $productPrice= $productPrice*$amount/100;
                break;
            case 'fidelity_points_to_percent':
                $productPrice= $productPrice*$amount/100;
                break;
            case 'sponsor_points_to_percent':
                $productPrice= $productPrice*$amount/100;
                break;
            case 'by_fixed':
                $productPrice -= $amount;
                break;
            case 'by_percent':
                $productPrice = $productPrice*(1-$amount/100);
                break;
        }

        $productPrice = max($productPrice, 0);
        return Mage::app()->getStore()->roundPrice($productPrice);
    }
    
	protected function _saveRuleProductPoints($arrData,$type)
    {
        if (empty($arrData)) {
            return $this;
        }
        $header = 'replace into '.$this->getTable('auguria_sponsorship/catalog'.$type.'point').' (
                rule_date,
                website_id,
                customer_group_id,
                product_id,
                rule_point,
                latest_start_date,
                earliest_end_date
            ) values ';
        $rows = array();
        $productIds = array();
        foreach ($arrData as $data) {
            $productIds[$data['product_id']] = true;
            $data['rule_date']          = $this->formatDate($data['rule_date'], false);
            $data['latest_start_date']  = $this->formatDate($data['latest_start_date'], false);
            $data['earliest_end_date']  = $this->formatDate($data['earliest_end_date'], false);
            $rows[] = '(' . $this->_getWriteAdapter()->quote($data) . ')';
        }
        $query = $header.join(',', $rows);
        $insertQuery = 'REPLACE INTO ' . $this->getTable('catalogrule/affected_product') . ' (product_id)  VALUES ' .
            '(' . join('),(', array_keys($productIds)) . ')';
        $this->_getWriteAdapter()->query($insertQuery);
        $this->_getWriteAdapter()->query($query);
        return $this;
    }
    
    public function removeCatalogPricesForDateRange($fromDate, $toDate, $productId=null)
    {
        $write = $this->_getWriteAdapter();
        $conds = array();
        $cond = $write->quoteInto('rule_date between ?', $this->formatDate($fromDate));
        $cond = $write->quoteInto($cond.' and ?', $this->formatDate($toDate));
        $conds[] = $cond;
        if (!is_null($productId)) {
            $conds[] = $write->quoteInto('product_id=?', $productId);
        }

        /**
         * Add information about affected products
         * It can be used in processes which related with product price (like catalog index)
         */
        $select = $this->_getWriteAdapter()->select()
            ->from($this->getTable('catalogrule/rule_product_price'), 'product_id')
            ->where(implode(' AND ', $conds));
        $insertQuery = 'REPLACE INTO ' . $this->getTable('catalogrule/affected_product') . ' (product_id)' . $select->__toString();
        $this->_getWriteAdapter()->query($insertQuery);
        $write->delete($this->getTable('catalogrule/rule_product_price'), $conds);
        
        $selectFidelityPoint = $this->_getWriteAdapter()->select()
            ->from($this->getTable('auguria_sponsorship/catalogfidelitypoint'), 'product_id')
            ->where(implode(' AND ', $conds));
        $insertQuery = 'REPLACE INTO ' . $this->getTable('catalogrule/affected_product') . ' (product_id)' .$selectFidelityPoint->__toString();
        $this->_getWriteAdapter()->query($insertQuery);
        $write->delete($this->getTable('auguria_sponsorship/catalogfidelitypoint'), $conds);
        
        $selectSponsorPoint = $this->_getWriteAdapter()->select()
            ->from($this->getTable('auguria_sponsorship/catalogsponsorpoint'), 'product_id')
            ->where(implode(' AND ', $conds));
        $insertQuery = 'REPLACE INTO ' . $this->getTable('catalogrule/affected_product') . ' (product_id)' .$selectSponsorPoint->__toString();
        $this->_getWriteAdapter()->query($insertQuery);
        $write->delete($this->getTable('auguria_sponsorship/catalogsponsorpoint'), $conds);
        
        return $this;
    }
    
	public function deleteOldData($date, $productId=null)
    {
    	/*@TODO add sponsorship/catalogsponsopoint delete ?*/
        $write = $this->_getWriteAdapter();
        $conds = array();
        $conds[] = $write->quoteInto('rule_date<?', $this->formatDate($date));
        if (!is_null($productId)) {
            $conds[] = $write->quoteInto('product_id=?', $productId);
        }
        $write->delete($this->getTable('catalogrule/rule_product_price'), $conds);
        $write->delete($this->getTable('auguria_sponsorship/catalogfidelitypoint'), $conds);
        return $this;
    }
}