<?php
/**
 * Update log tables and change table to add cart type
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;

$installer->getConnection()->changeColumn($this->getTable('sponsorship_sponsor_log'),
	    "record_type", "record_type",
	    "enum('order', 'gift', 'coupon_code', 'cash', 'admin', 'first', 'cart') NOT NULL"
	);
$installer->getConnection()->changeColumn($this->getTable('sponsorship_fidelity_log'),
    "record_type", "record_type",
    "enum('order', 'gift', 'coupon_code', 'cash', 'admin', 'first', 'cart') NOT NULL"
);
$installer->getConnection()->changeColumn($this->getTable("auguria_sponsorship/change"),
    "type", "type",
    "enum('gift', 'coupon', 'cash', 'cart') NOT NULL"
);

$installer->endSetup(); 