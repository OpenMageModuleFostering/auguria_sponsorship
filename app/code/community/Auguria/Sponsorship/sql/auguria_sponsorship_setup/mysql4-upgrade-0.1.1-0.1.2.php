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
		ALTER TABLE `{$this->getTable('catalogrule_product_fidelity_point')}` RENAME `{$this->getTable('auguria_sponsorship/catalogfidelitypoint')}`;
		ALTER TABLE `{$this->getTable('catalogrule_product_sponsor_point')}` RENAME `{$this->getTable('auguria_sponsorship/catalogsponsorpoint')}`;
	");
}
else {
	$installer->run("	
		ALTER TABLE {$this->getTable('catalogrule_product')} CHANGE `action_operator` `action_operator`
		ENUM( 'to_fixed', 'to_percent', 'by_fixed', 'by_percent', 'fidelity_points_to_percent', 'fidelity_points_to_fixed', 'sponsor_points_to_percent', 'sponsor_points_to_fixed' ) 
		CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'to_fixed'; 
		
	CREATE TABLE IF NOT EXISTS {$this->getTable('auguria_sponsorship/catalogfidelitypoint')} (
	  `rule_product_fidelity_point_id` int(10) unsigned NOT NULL auto_increment,
	  `rule_date` date NOT NULL default '0000-00-00',
	  `customer_group_id` smallint(5) unsigned NOT NULL default '0',
	  `product_id` int(10) unsigned NOT NULL default '0',
	  `rule_point` decimal(12,4) NOT NULL default '0.0000',
	  `website_id` smallint(5) unsigned NOT NULL,
	  `latest_start_date` date default NULL,
	  `earliest_end_date` date default NULL,
	  PRIMARY KEY  (`rule_product_fidelity_point_id`),
	  UNIQUE KEY `rule_date` (`rule_date`,`website_id`,`customer_group_id`,`product_id`),
	  KEY `FK_catalogrule_product_fidelity_point_customergroup` (`customer_group_id`),
	  KEY `FK_catalogrule_product_fidelity_point_website` (`website_id`),
	  KEY `FK_CATALOGRULE_PRODUCT_FIDELITY_POINT_PRODUCT` (`product_id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS {$this->getTable('auguria_sponsorship/catalogsponsorpoint')} (
	  `rule_product_sponsor_point_id` int(10) unsigned NOT NULL auto_increment,
	  `rule_date` date NOT NULL default '0000-00-00',
	  `customer_group_id` smallint(5) unsigned NOT NULL default '0',
	  `product_id` int(10) unsigned NOT NULL default '0',
	  `rule_point` decimal(12,4) NOT NULL default '0.0000',
	  `website_id` smallint(5) unsigned NOT NULL,
	  `latest_start_date` date default NULL,
	  `earliest_end_date` date default NULL,
	  PRIMARY KEY  (`rule_product_sponsor_point_id`),
	  UNIQUE KEY `rule_date` (`rule_date`,`website_id`,`customer_group_id`,`product_id`),
	  KEY `FK_catalogrule_product_sponsor_point_customergroup` (`customer_group_id`),
	  KEY `FK_catalogrule_product_sponsor_point_website` (`website_id`),
	  KEY `FK_CATALOGRULE_PRODUCT_SPONSOR_POINT_PRODUCT` (`product_id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
		
	    ");
}
$installer->endSetup(); 