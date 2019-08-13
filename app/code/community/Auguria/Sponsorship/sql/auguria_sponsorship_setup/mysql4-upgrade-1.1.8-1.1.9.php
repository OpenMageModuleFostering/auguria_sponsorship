<?php
/**
 * Add enum type "validty" to log table
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;

$installer->getConnection()->changeColumn($this->getTable("auguria_sponsorship/log"),
	    "record_type", "record_type",
	    "enum('order', 'gift', 'coupon_code', 'cash', 'admin', 'first', 'cart', 'validity') NOT NULL"
	);
$installer->endSetup(); 