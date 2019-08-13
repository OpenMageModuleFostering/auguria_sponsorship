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
	ALTER TABLE {$this->getTable('auguria_sponsorship/sponsorship')} DROP COLUMN `status`;
	ALTER TABLE {$this->getTable('auguria_sponsorship/sponsorship')} DROP COLUMN `created_time`;
	ALTER TABLE {$this->getTable('auguria_sponsorship/sponsorship')} CHANGE `update_time` `datetime` datetime NULL;
	    ");
}
$installer->endSetup(); 