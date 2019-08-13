<?php
/**
 * Add record type 'first' for first order points in sponsor and fidelity logs
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;

$installer->startSetup();

if (!$setup->hasSponsorshipInstall()) {
	$installer->run("
	ALTER TABLE {$this->getTable('sponsorship_fidelity_log')} CHANGE `record_type` `record_type`
		ENUM('order', 'gift', 'coupon_code', 'cash', 'admin', 'first') NOT NULL;
	ALTER TABLE {$this->getTable('sponsorship_sponsor_log')} CHANGE `record_type` `record_type`
		ENUM('order', 'gift', 'coupon_code', 'cash', 'admin', 'first') NOT NULL;
	");
}
$installer->endSetup(); 