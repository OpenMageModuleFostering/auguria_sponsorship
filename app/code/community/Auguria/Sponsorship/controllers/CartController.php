<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_CartController extends Mage_Core_Controller_Front_Action
{
    public function usePointsAction()
    {
        $type = $this->getRequest()->getParam('sponsorship_type');
        if ($type == 'sponsor') {
            $type = 'sponsorship';
        }
        Mage::getSingleton('checkout/session')->setData('use_sponsorship_points_'.$type, true);
        $this->_redirectUrl(Mage::getUrl('checkout/cart'));
    }

    public function doNotUsePointsAction()
    {
        $type = $this->getRequest()->getParam('sponsorship_type');
        if ($type == 'sponsor') {
            $type = 'sponsorship';
        }
        Mage::getSingleton('checkout/session')->setData('use_sponsorship_points_'.$type, false);
        $this->_redirectUrl(Mage::getUrl('checkout/cart'));
    }
}