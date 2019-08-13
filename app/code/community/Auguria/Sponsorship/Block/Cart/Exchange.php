<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Cart_Exchange extends Mage_Core_Block_Template
{
    public function isActivated($module)
    {
        if ($module == 'sponsor') {
            $module = 'sponsorship';
        }
        $activated = Mage::helper('auguria_sponsorship/config')->getCartExchangeActivated($module);
        if (isset($activated[$module]) && $activated[$module] == true) {
            return true;
        }
        return false;
    }

    public function isAutomatic($module)
    {
        if ($module == 'sponsor') {
            $module = 'sponsorship';
        }
        $isAutomatic = Mage::helper('auguria_sponsorship/config')->isCartExchangeAutomatic($module);
        if (isset($isAutomatic[$module]) && $isAutomatic[$module] == true) {
            return true;
        }
        return false;
    }

    public function isPointUseActivated($module)
    {
        if ($module == 'sponsor') {
            $module = 'sponsorship';
        }
        if (Mage::getSingleton('checkout/session')->getData('use_sponsorship_points_'.$module) == true) {
            return true;
        }
        return false;
    }

    public function getAvailablePoints($module)
    {
        $availablePoints = Mage::helper('auguria_sponsorship')->getCustomerAvailablePoints();
        if ($module == 'sponsor') {
            $module = 'sponsorship';
        }
        if (isset($availablePoints[$module])) {
            return (float)$availablePoints[$module];
        }
        return 0;
    }

    public function getPointsToCashConfig($module)
    {
        if ($module == 'sponsor') {
            $module = 'sponsorship';
        }
        $pointsToCashConfig = Mage::helper('auguria_sponsorship/config')->getPointsToCash();
        if (isset($pointsToCashConfig[$module])) {
            return (float)$pointsToCashConfig[$module];
        }
        return 0;
    }

    public function getAvailableCash($module)
    {
        if ($module == 'sponsor') {
            $module = 'sponsorship';
        }
        $cash = Mage::helper('auguria_sponsorship')->getCash();
        if (isset($cash[$module])) {
            return (float)$cash[$module];
        }
        return 0;
    }
/*
    public function getDiscountAmount($module)
    {
        $subtotal = Mage::getSingleton('checkout/session')->getQuote()->getSubtotal();
        $availableCash = $this->getAvailableCash($module);
        return min(array($subtotal, $availableCash));
    }

    public function getReminingPoints($module, $availablePoints, $discountAmount)
    {
        $pointsToCash = $this->getPointsToCashConfig($module);
        if ($pointsToCash > 0) {
            $discountPoint = $discountAmount / $pointsToCash;
            return max(array(0, $availablePoints - $discountPoint));
        }
        return 0;
    }
*/
}