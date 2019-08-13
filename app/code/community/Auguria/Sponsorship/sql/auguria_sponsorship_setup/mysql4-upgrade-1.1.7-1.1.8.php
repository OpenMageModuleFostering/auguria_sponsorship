<?php
/**
 * Include log tables in one
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;
$installer->startSetup();

$installer->run("
	ALTER TABLE `{$this->getTable('sponsorship_sponsor_log')}` RENAME TO `{$this->getTable('auguria_sponsorship/log')}`;
	ALTER TABLE `{$this->getTable('auguria_sponsorship/log')}` ADD COLUMN `customer_id` int(10) unsigned NULL;
	ALTER TABLE `{$this->getTable('auguria_sponsorship/log')}` CHANGE COLUMN `sponsorship_sponsor_log_id` `sponsorship_log_id` int(10) unsigned NOT NULL auto_increment;
	
	ALTER TABLE `{$this->getTable('auguria_sponsorship/log')}` MODIFY COLUMN `godson_id` int(10) unsigned NULL;
	ALTER TABLE `{$this->getTable('auguria_sponsorship/log')}` MODIFY COLUMN `sponsor_id` int(10) unsigned NULL;
	
	ALTER TABLE `{$this->getTable('auguria_sponsorship/log')}` ADD INDEX `INDEX_LOG_CUSTOMER_ID` (`customer_id`);
	ALTER TABLE `{$this->getTable('auguria_sponsorship/log')}` ADD INDEX `INDEX_LOG_RECORD_ID` (`record_id`);
");

$fidelityLogs = $installer->getConnection()->fetchAll("SELECT * FROM `{$installer->getTable('sponsorship_fidelity_log')}`;");
if (count($fidelityLogs)>0) {
	foreach ($fidelityLogs as $fidelityLog) {
		$log = Mage::getModel('auguria_sponsorship/log');
		$log->setData($fidelityLog);
		$log->save();
	}
}

$installer->run("
	DROP TABLE `{$this->getTable('sponsorship_fidelity_log')}`;
");

$installer->endSetup(); 