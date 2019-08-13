<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Model_Mysql4_CatalogFidelityPoint extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the sponsorship_id refers to the key field in your database table.
        $this->_init('auguria_sponsorship/catalogfidelitypoint', 'rule_product_fidelity_point_id');
    }
}