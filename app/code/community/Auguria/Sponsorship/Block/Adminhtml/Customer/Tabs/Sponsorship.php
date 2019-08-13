<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Adminhtml_Customer_Tabs_Sponsorship extends Mage_Adminhtml_Block_Template
implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	public function __construct()
	{
		$this->setTemplate('auguria/sponsorship/customer/tabs/sponsorship.phtml');
	}

	public function getTabLabel()
	{
		return $this->__('Fidelity and Sponsorship');
	}

	/**
	 * Return Tab title
	 *
	 * @return string
	 */
	public function getTabTitle()
	{
		return $this->__('Fidelity and Sponsorship');
	}
    
	/**
	 * Can show tab in tabs
	 *
	 * @return boolean
	 */
	public function canShowTab()
    {
        if (Mage::registry('current_customer')->getId()) {
            return true;
        }
        return false;
    }
	/**
	 * Tab is hidden
	 *
	 * @return boolean
	 */
	public function isHidden()
    {
        if (Mage::registry('current_customer')->getId()) {
            return false;
        }
        return true;
    }


	public function getCustomer()
	{
		if (!$this->_customer) {
			$this->_customer = Mage::registry('current_customer');
		}
		return $this->_customer;
	}
	
	public function formatDate($date=null, $format='short', $showTime=false)
	{
		if (empty($date)) {
			return '';
		}
		else {
			return parent::formatDate($date, $format, $showTime);
		}
	}
}