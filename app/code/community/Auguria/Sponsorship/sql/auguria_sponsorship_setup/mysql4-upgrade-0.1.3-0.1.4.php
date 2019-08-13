<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;

$installer->startSetup();
if (!$installer->hasSponsorshipInstall()) {
	$installer->run("	
	CREATE TABLE IF NOT EXISTS {$this->getTable('sponsorship_fidelity_log')} (
	  `sponsorship_fidelity_log_id` int(11) unsigned NOT NULL auto_increment,
	  `customer_id` int(10) unsigned NOT NULL,
	  `record_id` int(10) unsigned NOT NULL,
	  `record_type` enum('order', 'gift', 'coupon_code', 'cash', 'admin') NOT NULL,
	  `datetime` datetime NOT NULL,
	  `points` decimal(12,4) NOT NULL default '0.0000',
	  PRIMARY KEY (`sponsorship_fidelity_log_id`),
	  KEY `INDEX_FIDELITY_LOG_CUSTOMER_ID` (`customer_id`),
	  KEY `INDEX_FIDELITY_LOG_RECORD_ID` (`record_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS {$this->getTable('sponsorship_sponsor_log')} (
	  `sponsorship_sponsor_log_id` int(10) unsigned NOT NULL auto_increment,
	  `godson_id` int(10) unsigned NOT NULL,
	  `sponsor_id` int(10) unsigned NOT NULL,
	  `record_id` int(10) unsigned NOT NULL,
	  `record_type` enum('order', 'gift', 'coupon_code', 'cash', 'admin') NOT NULL,
	  `datetime` datetime NOT NULL,
	  `points` decimal(12,4) NOT NULL default '0.0000',
	  PRIMARY KEY (`sponsorship_sponsor_log_id`),
	  KEY `INDEX_SPONSOR_LOG_GODSON_ID` (`godson_id`),
	  KEY `INDEX_SPONSOR_LOG_SPONSOR_ID` (`sponsor_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	    ");
}
$installer->endSetup(); 