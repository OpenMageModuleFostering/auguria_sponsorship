<?php
class test
{
	public function process(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        $item->setFreeShipping(false);
        $item->setDiscountAmount(0);
        $item->setBaseDiscountAmount(0);
        $item->setDiscountPercent(0);
        $item->setCartFidelityPoints(0);
        $item->setCartSponsorPoints(0);

        $quote = $item->getQuote();
        if ($item instanceof Mage_Sales_Model_Quote_Address_Item) {
            $address = $item->getAddress();
        } elseif ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }

        $customerId = $quote->getCustomerId();
        $ruleCustomer = Mage::getModel('salesrule/rule_customer');
        $appliedRuleIds = array();

        foreach ($this->_rules as $rule) {
            /* @var $rule Mage_SalesRule_Model_Rule */
            /**
             * already tried to validate and failed
             */
            if ($rule->getIsValid() === false) {
                continue;
            }

            if ($rule->getIsValid() !== true) {
                /**
                 * too many times used in general
                 */
                if ($rule->getUsesPerCoupon() && ($rule->getTimesUsed() >= $rule->getUsesPerCoupon())) {
                    $rule->setIsValid(false);
                    continue;
                }
                /**
                 * too many times used for this customer
                 */
                $ruleId = $rule->getId();
                if ($ruleId && $rule->getUsesPerCustomer()) {
                    $ruleCustomer->loadByCustomerRule($customerId, $ruleId);
                    if ($ruleCustomer->getId()) {
                        if ($ruleCustomer->getTimesUsed() >= $rule->getUsesPerCustomer()) {
                            continue;
                        }
                    }
                }
                $rule->afterLoad();
                /**
                 * quote does not meet rule's conditions
                 */
                if (!$rule->validate($address)) {
                    $rule->setIsValid(false);
                    continue;
                }
                /**
                 * passed all validations, remember to be valid
                 */
                $rule->setIsValid(true);
            }

            /**
             * although the rule is valid, this item is not marked for action
             */
            if (!$rule->getActions()->validate($item)) {
                continue;
            }
            $qty = $item->getQty();
            if ($item->getParentItem()) {
                $qty*= $item->getParentItem()->getQty();
            }
            $qty = $rule->getDiscountQty() ? min($qty, $rule->getDiscountQty()) : $qty;
            $rulePercent = min(100, $rule->getDiscountAmount());
            $discountAmount = 0;
            $baseDiscountAmount = 0;
            $cartFidelityPoints = 0;
            $cartSponsorPoints = 0;
            switch ($rule->getSimpleAction()) {
                case 'to_percent':
                    $rulePercent = max(0, 100-$rule->getDiscountAmount());
                    //no break;

                case 'by_percent':
                    if ($step = $rule->getDiscountStep()) {
                        $qty = floor($qty/$step)*$step;
                    }
                    $discountAmount    = ($qty*$item->getCalculationPrice() - $item->getDiscountAmount()) * $rulePercent/100;
                    $baseDiscountAmount= ($qty*$item->getBaseCalculationPrice() - $item->getBaseDiscountAmount()) * $rulePercent/100;

                    if (!$rule->getDiscountQty() || $rule->getDiscountQty()>$qty) {
                        $discountPercent = min(100, $item->getDiscountPercent()+$rulePercent);
                        $item->setDiscountPercent($discountPercent);
                    }
                    break;

                case 'to_fixed':
                    $quoteAmount = $quote->getStore()->convertPrice($rule->getDiscountAmount());
                    $discountAmount    = $qty*($item->getCalculationPrice()-$quoteAmount);
                    $baseDiscountAmount= $qty*($item->getBaseCalculationPrice()-$rule->getDiscountAmount());
                    break;

                case 'by_fixed':
                    if ($step = $rule->getDiscountStep()) {
                        $qty = floor($qty/$step)*$step;
                    }
                    $quoteAmount = $quote->getStore()->convertPrice($rule->getDiscountAmount());
                    $discountAmount    = $qty*$quoteAmount;
                    $baseDiscountAmount= $qty*$rule->getDiscountAmount();
                    break;

                case 'cart_fixed':
                    $cartRules = $address->getCartFixedRules();
                    if (!isset($cartRules[$rule->getId()])) {
                        $cartRules[$rule->getId()] = $rule->getDiscountAmount();
                    }
                    if ($cartRules[$rule->getId()] > 0) {
                        $quoteAmount = $quote->getStore()->convertPrice($cartRules[$rule->getId()]);
                        $discountAmount = min($item->getRowTotal(), $quoteAmount);
                        $baseDiscountAmount = min($item->getBaseRowTotal(), $cartRules[$rule->getId()]);
                        $cartRules[$rule->getId()] -= $baseDiscountAmount;
                    }
                    $address->setCartFixedRules($cartRules);
                    break;

                case 'buy_x_get_y':
                    $x = $rule->getDiscountStep();
                    $y = $rule->getDiscountAmount();
                    if (!$x || $y>=$x) {
                        break;
                    }
                    $buy = 0; $free = 0;
                    while ($buy+$free<$qty) {
                        $buy += $x;
                        if ($buy+$free>=$qty) {
                            break;
                        }
                        $free += min($y, $qty-$buy-$free);
                        if ($buy+$free>=$qty) {
                            break;
                        }
                    }
                    $discountAmount    = $free*$item->getCalculationPrice();
                    $baseDiscountAmount= $free*$item->getBaseCalculationPrice();
                    break;

           //début case perso-------------------------------------------------------------------
           //fielity
                case 'fidelity_points_by_fixed':
                    if ($step = $rule->getDiscountStep()) {
                        $qty = floor($qty/$step)*$step;
                    }
                    $cartFidelityPoints = $qty*$rule->getDiscountAmount();
                	break;

                case 'fidelity_points_by_percent':
                    if ($step = $rule->getDiscountStep()) {
                        $qty = floor($qty/$step)*$step;
                    }
                    $cartFidelityPoints = ($qty * ($item->getBaseCalculationPrice() - $item->getBaseDiscountAmount())) * $rulePercent/100;
                    break;

                case 'fidelity_points_cart_fixed':
                    $cartRules = $address->getCartFixedRules();
                    if (!isset($cartRules[$rule->getId()])) {
                        $cartRules[$rule->getId()] = $rule->getDiscountAmount();
                    }
                    if ($cartRules[$rule->getId()] > 0) {
                        $cartFidelityPoints = max($item->getCartFidelityPoints(), $cartRules[$rule->getId()]);
                        $cartRules[$rule->getId()] -= $cartFidelityPoints;
                    }
                    $address->setCartFixedRules($cartRules);
                    break;
           //sponsor
                case 'sponsor_points_by_fixed':
                    if ($step = $rule->getDiscountStep()) {
                        $qty = floor($qty/$step)*$step;
                    }
                    $cartSponsorPoints = $qty*$rule->getDiscountAmount();
                	break;

                case 'sponsor_points_by_percent':
                    if ($step = $rule->getDiscountStep()) {
                        $qty = floor($qty/$step)*$step;
                    }
                    $cartSponsorPoints = ($qty * ($item->getBaseCalculationPrice() - $item->getBaseDiscountAmount())) * $rulePercent/100;
                    break;

                case 'sponsor_points_cart_fixed':
                    $cartRules = $address->getCartFixedRules();
                    if (!isset($cartRules[$rule->getId()])) {
                        $cartRules[$rule->getId()] = $rule->getDiscountAmount();
                    }
                    if ($cartRules[$rule->getId()] > 0) {
                        $cartSponsorPoints = max($item->getCartFidelityPoints(), $cartRules[$rule->getId()]);
                        $cartRules[$rule->getId()] -= $cartSponsorPoints;
                    }
                    $address->setCartFixedRules($cartRules);
                    break;
		//fin case perso-------------------------------------------------------------------
            }

            $result = new Varien_Object(array(
                'discount_amount'      => $discountAmount,
                'base_discount_amount' => $baseDiscountAmount,
            	'cart_fidelity_points' => $cartFidelityPoints,
            	'cart_sponsor_points' => $cartSponsorPoints,
            ));
            Mage::dispatchEvent('salesrule_validator_process', array(
                'rule'    => $rule,
                'item'    => $item,
                'address' => $address,
                'quote'   => $quote,
                'qty'     => $qty,
                'result'  => $result,
            ));

            $discountAmount     = $quote->getStore()->roundPrice($discountAmount);
            $baseDiscountAmount = $quote->getStore()->roundPrice($baseDiscountAmount);
            $discountAmount     = min($item->getDiscountAmount()+$discountAmount, $item->getRowTotal());
            $baseDiscountAmount = min($item->getBaseDiscountAmount()+$baseDiscountAmount, $item->getBaseRowTotal());

            //On garde uniquement la règle qui apporte leplus de points
            $cartFidelityPoints = max($item->getCartFidelityPoints(), $cartFidelityPoints);
            $cartSponsorPoints = max($item->getCartSponsorPoints(), $cartSponsorPoints);

            $item->setDiscountAmount($discountAmount);
            $item->setBaseDiscountAmount($baseDiscountAmount);

            $item->setCartFidelityPoints($cartFidelityPoints);
            $item->setCartSponsorPoints($cartSponsorPoints);

            switch ($rule->getSimpleFreeShipping()) {
                case Mage_SalesRule_Model_Rule::FREE_SHIPPING_ITEM:
                    $item->setFreeShipping($rule->getDiscountQty() ? $rule->getDiscountQty() : true);
                    break;

                case Mage_SalesRule_Model_Rule::FREE_SHIPPING_ADDRESS:
                    $address->setFreeShipping(true);
                    break;
            }

            $appliedRuleIds[$rule->getRuleId()] = $rule->getRuleId();

            if ($rule->getCouponCode() && ( strtolower($rule->getCouponCode()) == strtolower($this->getCouponCode()))) {
                $address->setCouponCode($this->getCouponCode());
            }

            if ($rule->getStopRulesProcessing()) {
                break;
            }
        }

        $item->setAppliedRuleIds(join(',',$appliedRuleIds));
        $address->setAppliedRuleIds($this->mergeIds($address->getAppliedRuleIds(), $appliedRuleIds));
        $quote->setAppliedRuleIds($this->mergeIds($quote->getAppliedRuleIds(), $appliedRuleIds));
        return $this;
    }
}
?>
