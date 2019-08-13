<?php
/**
 * Resolve bug to display customer eav attribute in admin form
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
			->setData( 'used_in_forms', array( 'adminhtml_customer' ) )
			->save();
	
	Mage::getSingleton( 'eav/config' )
			->getAttribute( 'customer', 'fidelity_points' )
			->setData( 'used_in_forms', array( 'adminhtml_customer' ) )
			->save();
	
	Mage::getSingleton( 'eav/config' )
			->getAttribute( 'customer', 'sponsor_points' )
			->setData( 'used_in_forms', array( 'adminhtml_customer' ) )
			->save();
			
	$installer->updateAttribute('customer', 'sponsor', 'group', 'Default');
	$installer->updateAttribute('customer', 'fidelity_points', 'group', 'Default');
	$installer->updateAttribute('customer', 'sponsor_points', 'group', 'Default');
			
	$installer->updateAttribute('customer', 'sponsor', 'sort_order', 250);
	$installer->updateAttribute('customer', 'fidelity_points', 'sort_order', 251);
	$installer->updateAttribute('customer', 'sponsor_points', 'sort_order', 252);
}

$installer->endSetup(); 