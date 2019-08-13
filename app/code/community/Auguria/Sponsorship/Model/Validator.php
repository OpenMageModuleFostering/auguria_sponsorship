<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Model_Validator extends Mage_SalesRule_Model_Validator
{
    protected function _construct()
    {
        Mage_Core_Model_Abstract::_construct();
        $this->_init('salesrule/validator');
    }
    
    public function process(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        $item->setDiscountAmount(0);
        $item->setBaseDiscountAmount(0);
        $item->setDiscountPercent(0);
        $item->setCartFidelityPoints(0);
        $item->setCartSponsorPoints(0);
        $quote      = $item->getQuote();
        $address    = $this->_getAddress($item);

        //Clearing applied rule ids for quote and address
        if ($this->_isFirstTimeProcessRun !== true){
            $this->_isFirstTimeProcessRun = true;
            $quote->setAppliedRuleIds('');
            $address->setAppliedRuleIds('');
        }

        $itemPrice  = $item->getDiscountCalculationPrice();
        if ($itemPrice !== null) {
            $baseItemPrice = $item->getBaseDiscountCalculationPrice();
        } else {
            $itemPrice = $item->getCalculationPrice();
            $baseItemPrice = $item->getBaseCalculationPrice();
        }

        $appliedRuleIds = array();
        foreach ($this->_getRules() as $rule) {
            /* @var $rule Mage_SalesRule_Model_Rule */
            if (!$this->_canProcessRule($rule, $address)) {
                continue;
            }

            if (!$rule->getActions()->validate($item)) {
                continue;
            }
            $qty = $item->getTotalQty();
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
                    $step = $rule->getDiscountStep();
                    if ($step) {
                        $qty = floor($qty/$step)*$step;
                    }
                    $discountAmount    = ($qty*$itemPrice - $item->getDiscountAmount()) * $rulePercent/100;
                    $baseDiscountAmount= ($qty*$baseItemPrice - $item->getBaseDiscountAmount()) * $rulePercent/100;

                    if (!$rule->getDiscountQty() || $rule->getDiscountQty()>$qty) {
                        $discountPercent = min(100, $item->getDiscountPercent()+$rulePercent);
                        $item->setDiscountPercent($discountPercent);
                    }
                    break;
                case 'to_fixed':
                    $quoteAmount = $quote->getStore()->convertPrice($rule->getDiscountAmount());
                    $discountAmount    = $qty*($itemPrice-$quoteAmount);
                    $baseDiscountAmount= $qty*($baseItemPrice-$rule->getDiscountAmount());
                    break;

                case 'by_fixed':
                    $step = $rule->getDiscountStep();
                    if ($step) {
                        $qty = floor($qty/$step)*$step;
                    }
                    $quoteAmount        = $quote->getStore()->convertPrice($rule->getDiscountAmount());
                    $discountAmount     = $qty*$quoteAmount;
                    $baseDiscountAmount = $qty*$rule->getDiscountAmount();
                    break;

                case 'cart_fixed':
                    $cartRules = $address->getCartFixedRules();
                    if (!isset($cartRules[$rule->getId()])) {
                        $cartRules[$rule->getId()] = $rule->getDiscountAmount();
                    }
                    if ($cartRules[$rule->getId()] > 0) {
                        $quoteAmount        = $quote->getStore()->convertPrice($cartRules[$rule->getId()]);
                        /**
                         * We can't use row total here because row total not include tax
                         */
                        $discountAmount     = min($itemPrice*$qty - $item->getDiscountAmount(), $quoteAmount);
                        $baseDiscountAmount = min($baseItemPrice*$qty - $item->getBaseDiscountAmount(), $cartRules[$rule->getId()]);
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
                    $discountAmount    = $free*$itemPrice;
                    $baseDiscountAmount= $free*$baseItemPrice;
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

            $discountAmount = $result->getDiscountAmount();
            $baseDiscountAmount = $result->getBaseDiscountAmount();

            //On garde uniquement la règle qui apporte leplus de points
            $cartFidelityPoints = max($item->getCartFidelityPoints(), $cartFidelityPoints);
            $cartSponsorPoints = max($item->getCartSponsorPoints(), $cartSponsorPoints);

            $percentKey = $item->getDiscountPercent();
            /**
             * Process "delta" rounding
             */
            if ($percentKey) {
                $delta      = isset($this->_roundingDeltas[$percentKey]) ? $this->_roundingDeltas[$percentKey] : 0;
                $baseDelta  = isset($this->_baseRoundingDeltas[$percentKey]) ? $this->_baseRoundingDeltas[$percentKey] : 0;
                $discountAmount+= $delta;
                $baseDiscountAmount+=$baseDelta;

                $this->_roundingDeltas[$percentKey]     = $discountAmount - $quote->getStore()->roundPrice($discountAmount);
                $this->_baseRoundingDeltas[$percentKey] = $baseDiscountAmount - $quote->getStore()->roundPrice($baseDiscountAmount);
                $discountAmount = $quote->getStore()->roundPrice($discountAmount);
                $baseDiscountAmount = $quote->getStore()->roundPrice($baseDiscountAmount);
            } else {
                $discountAmount     = $quote->getStore()->roundPrice($discountAmount);
                $baseDiscountAmount = $quote->getStore()->roundPrice($baseDiscountAmount);
            }

            /**
             * We can't use row total here because row total not include tax
             * Discount can be applied on price included tax
             */
            $discountAmount     = min($item->getDiscountAmount()+$discountAmount, $itemPrice*$qty);
            $baseDiscountAmount = min($item->getBaseDiscountAmount()+$baseDiscountAmount, $baseItemPrice*$qty);

            $item->setDiscountAmount($discountAmount);
            $item->setBaseDiscountAmount($baseDiscountAmount);
            $item->setCartFidelityPoints($cartFidelityPoints);
            $item->setCartSponsorPoints($cartSponsorPoints);

            $appliedRuleIds[$rule->getRuleId()] = $rule->getRuleId();

            if ($rule->getCouponCode() && ( strtolower($rule->getCouponCode()) == strtolower($this->getCouponCode()))) {
                $address->setCouponCode($this->getCouponCode());
            }
            $this->_addDiscountDescription($address, $rule);
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