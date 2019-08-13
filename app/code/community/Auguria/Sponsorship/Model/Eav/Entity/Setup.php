<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Model_Eav_Entity_Setup extends Mage_Eav_Model_Entity_Setup
{
	public function hasSponsorshipInstall()
	{
		if ($this->tableExists($this->getTable('sponsorship'))) {
			$columns = $this->getConnection()->fetchCol("SHOW COLUMNS FROM `{$this->getTable('sponsorship')}`;");			
			if (in_array('sponsorship_id', $columns)
				&& in_array('parent_id', $columns)
				&& in_array('child_mail', $columns)
				&& in_array('child_firstname', $columns)
				&& in_array('child_lastname', $columns)) {
				$this->setConfigData('auguria_sponsorship/has_old_revision', true);
			}
		}
		$hasOldRevision = $this->getConnection()->fetchOne("SELECT `value` FROM `{$this->getTable('core_config_data')}` WHERE `path` = 'auguria_sponsorship/has_old_revision';");
		if ($hasOldRevision == 1) {
			$oldVersion = $this->getConnection()->fetchOne("SELECT `data_version` FROM `{$this->getTable('core_resource')}` WHERE `code` = 'sponsorship_setup';");
			$newVersion = $this->getConnection()->fetchOne("SELECT `version` FROM `{$this->getTable('core_resource')}` WHERE `code` = 'auguria_sponsorship_setup';");
			if (version_compare($oldVersion, $newVersion) > 0) {
				return true;
			}
		}
		return false;
	}
}