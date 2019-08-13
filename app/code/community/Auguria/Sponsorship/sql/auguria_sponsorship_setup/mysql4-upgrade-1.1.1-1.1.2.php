<?php
/**
 * Set mode according to configuration
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;

$sponsorship = $installer->getConnection()->fetchOne("SELECT `value` FROM `{$this->getTable('core_config_data')}` WHERE `path` = 'auguria_sponsorship/sponsor/sponsor_enabled';");
$fidelity = $installer->getConnection()->fetchOne("SELECT `value` FROM `{$this->getTable('core_config_data')}` WHERE `path` = 'auguria_sponsorship/fidelity/fidelity_enabled';");

if ($sponsorship && $fidelity) {
	$installer->setConfigData('auguria_sponsorship/general/module_mode','separated');
}
elseif($sponsorship) {
	$installer->setConfigData('auguria_sponsorship/general/module_mode','sponsorship');
}
elseif($fidelity) {
	$installer->setConfigData('auguria_sponsorship/general/module_mode','fidelity');
}
else {
	$installer->setConfigData('auguria_sponsorship/general/module_mode','separated');
}

$installer->endSetup(); 