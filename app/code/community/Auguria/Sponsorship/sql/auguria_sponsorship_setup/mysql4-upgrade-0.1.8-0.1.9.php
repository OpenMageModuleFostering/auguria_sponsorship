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
	ALTER TABLE {$this->getTable('auguria_sponsorship/sponsorship')} ADD COLUMN `message` text;
	ALTER TABLE {$this->getTable('auguria_sponsorship/sponsorship')} ADD COLUMN `parent_mail` varchar(255);
	ALTER TABLE {$this->getTable('auguria_sponsorship/sponsorship')} ADD COLUMN `parent_name` varchar(255);
	ALTER TABLE {$this->getTable('auguria_sponsorship/sponsorship')} ADD COLUMN `subject` varchar(255);
	ALTER TABLE {$this->getTable('auguria_sponsorship/sponsorship')} ADD COLUMN `datetime_boost` datetime;
	
	UPDATE {$this->getTable('auguria_sponsorship')} SET `datetime_boost` = `datetime` WHERE ISNULL(`datetime_boost`);
	    ");
} 
$installer->endSetup(); 