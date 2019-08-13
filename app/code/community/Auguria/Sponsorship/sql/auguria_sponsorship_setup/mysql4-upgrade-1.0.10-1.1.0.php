<?php
/**
 * Remove fields from adminhtml account form because we add a sponsorship tab
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;

$installer->startSetup();
if (!$setup->hasSponsorshipInstall()) {
	Mage::getSingleton( 'eav/config' )
			->getAttribute( 'customer', 'sponsor' )
			->setData( 'used_in_forms', array() )
			->save();
	
	Mage::getSingleton( 'eav/config' )
			->getAttribute( 'customer', 'special_rate' )
			->setData( 'used_in_forms', array() )
			->save();
	
	Mage::getSingleton( 'eav/config' )
			->getAttribute( 'customer', 'fidelity_points' )
			->setData( 'used_in_forms', array() )
			->save();
	
	Mage::getSingleton( 'eav/config' )
			->getAttribute( 'customer', 'sponsor_points' )
			->setData( 'used_in_forms', array() )
			->save();
	
	Mage::getSingleton( 'eav/config' )
			->getAttribute( 'customer', 'iban' )
			->setData( 'used_in_forms', array() )
			->save();
	
	Mage::getSingleton( 'eav/config' )
			->getAttribute( 'customer', 'siret' )
			->setData( 'used_in_forms', array() )
			->save();
	
	$installer->updateAttribute('customer', 'sponsor', 'group', 'Sponsorship');
	$installer->updateAttribute('customer', 'special_rate', 'group', 'Sponsorship');
	$installer->updateAttribute('customer', 'fidelity_points', 'group', 'Sponsorship');
	$installer->updateAttribute('customer', 'sponsor_points', 'group', 'Sponsorship');
	$installer->updateAttribute('customer', 'iban', 'group', 'Sponsorship');
	$installer->updateAttribute('customer', 'siret', 'group', 'Sponsorship');
}
$installer->endSetup(); 