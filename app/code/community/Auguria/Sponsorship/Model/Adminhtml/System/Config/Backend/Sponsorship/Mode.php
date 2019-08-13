<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Model_Adminhtml_System_Config_Backend_Sponsorship_Mode extends Mage_Core_Model_Config_Data
{
    protected function _afterSave()
    {
        $value = $this->getValue();
        $config = Mage::getModel('core/config');
        if ($value=='accumulated') {
        	$config->saveConfig('auguria_sponsorship/accumulated/accumulated_enabled', 1);
        	$config->saveConfig('auguria_sponsorship/fidelity/fidelity_enabled', 0);
        	$config->saveConfig('auguria_sponsorship/sponsor/sponsor_enabled', 0);        	
        }
        elseif ($value=='fidelity') {
			$config->saveConfig('auguria_sponsorship/accumulated/accumulated_enabled', 0);
			$config->saveConfig('auguria_sponsorship/fidelity/fidelity_enabled', 1);
			$config->saveConfig('auguria_sponsorship/sponsor/sponsor_enabled', 0);
		}
        elseif ($value=='sponsorship') {
			$config->saveConfig('auguria_sponsorship/accumulated/accumulated_enabled', 0);
			$config->saveConfig('auguria_sponsorship/fidelity/fidelity_enabled', 0);
			$config->saveConfig('auguria_sponsorship/sponsor/sponsor_enabled', 1);
        }
        elseif ($value=='separated') {
			$config->saveConfig('auguria_sponsorship/accumulated/accumulated_enabled', 0);
			$config->saveConfig('auguria_sponsorship/fidelity/fidelity_enabled', 1);
			$config->saveConfig('auguria_sponsorship/sponsor/sponsor_enabled', 1);
        }
        return $this;
    }
}
