<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;

$installer->startSetup();

$installer->run("

CREATE TABLE IF NOT EXISTS {$this->getTable('sponsorship_openinviter')} (
  `sponsorship_openinviter_id` int(11) unsigned NOT NULL auto_increment,
  `code` varchar(255) NOT NULL,
  `image` varchar(255) default NULL,
  `name` varchar(255) default NULL,
  `status` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`sponsorship_openinviter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup(); 