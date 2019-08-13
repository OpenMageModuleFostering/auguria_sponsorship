<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;

$installer->startSetup();
if ($installer->hasSponsorshipInstall()) {
	$installer->run("
		ALTER TABLE `{$this->getTable('sponsorship_change')}` RENAME `{$this->getTable('auguria_sponsorship/change')}`;
	");
}
else {
	$installer->run("
	CREATE TABLE IF NOT EXISTS {$this->getTable('auguria_sponsorship/change')} (
	  `sponsorship_change_id` int(11) unsigned NOT NULL auto_increment,
	  `customer_id` int(10) unsigned NOT NULL,
	  `type` enum('gift', 'coupon', 'cash') NOT NULL,
	  `module` enum('fidelity', 'sponsor') NOT NULL,
	  `statut` enum('waiting', 'exported', 'solved', 'canceled') NOT NULL,
	  `datetime` datetime NOT NULL,
	  `points` decimal(12,4) NOT NULL default '0.0000',
	  `value` varchar(250) NOT NULL,
	  PRIMARY KEY (`sponsorship_change_id`),
	  KEY `INDEX_CHANGE_CUSTOMER_ID` (`customer_id`),
	  KEY `INDEX_CHANGE_TYPE` (`type`),
	  KEY `INDEX_CHANGE_MODULE` (`module`),
	  KEY `INDEX_CHANGE_STATUT` (`statut`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	");
	
	$installer->getConnection()->changeColumn($this->getTable('sponsorship_sponsor_log'),
	    'record_type', 'record_type',
	    'enum("order","gift","coupon","cash","admin") CHARACTER SET utf8 NOT NULL'
	);
	$installer->getConnection()->changeColumn($this->getTable('sponsorship_fidelity_log'),
	    'record_type', 'record_type',
	    'enum("order","gift","coupon","cash","admin") CHARACTER SET utf8 NOT NULL'
	);
}
$installer->endSetup();