<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Model_Mysql4_Change_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('auguria_sponsorship/change');
    }

    public function addAttributeToFilter($nameField, $value)
    {
        $this->getSelect()
            ->where($nameField.' = ?', $value);
        return $this;
    }

    public function addNameToSelect()
    {
    	$customer = Mage::getModel('customer/customer');
  	  	$firstname  = $customer->getAttribute('firstname');
  	  	$lastname   = $customer->getAttribute('lastname');
    	$core = Mage::getSingleton('core/resource');
        $this->getSelect()
        ->from(null, array("customer_name"=>new Zend_Db_Expr('CONCAT((select cev.value from '.$core->getTableName('customer_entity_varchar').' cev where cev.entity_id=customer_id and cev.attribute_id='.(int) $firstname->getAttributeId().')," ",(select cev.value from '.$core->getTableName('customer_entity_varchar').' cev where cev.entity_id=customer_id and cev.attribute_id='.(int) $lastname->getAttributeId().'))')));
        return $this;
    }
}