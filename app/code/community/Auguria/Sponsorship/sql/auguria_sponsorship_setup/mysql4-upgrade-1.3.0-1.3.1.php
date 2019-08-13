<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;

$installer->getConnection()->addColumn($this->getTable('auguria_sponsorship_change'), 'coupon_code', 'TEXT NULL');


$installer->getConnection()->addIndex(
    $installer->getTable('salesrule/coupon'),
    $installer->getIdxName('salesrule/coupon', array('coupon_code')),
    array('code')
);

$installer->endSetup(); 