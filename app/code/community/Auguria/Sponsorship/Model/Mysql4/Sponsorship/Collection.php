<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Model_Mysql4_Sponsorship_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('auguria_sponsorship/sponsorship');
    }
    
	public function addAttributeToFilter($nameField, $value)
    {
        $this->getSelect()
            ->where($nameField.' = ?', $value);
        return $this;
    }
    
	public function addDateToFilter($nameField, $value)
    {
        $this->getSelect()
            ->where('TO_DAYS(NOW()) - TO_DAYS('.$nameField.') <= ?', $value);
        return $this;
    }

    public function addChildNameToSelect()
    {
        $this->getSelect()
        ->from(null, array("child_name"=>new Zend_Db_Expr('CONCAT(child_firstname," ",child_lastname)')));
        return $this;
    }
}