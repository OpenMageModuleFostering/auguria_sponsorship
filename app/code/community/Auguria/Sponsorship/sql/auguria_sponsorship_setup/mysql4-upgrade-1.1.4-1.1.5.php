<?php
/**
 * Add fields points used to remove points from customer
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = new Mage_Sales_Model_Mysql4_Setup('core_setup');

$installer->addAttribute('quote_address', 'auguria_sponsorship_fidelity_points_used', array(
	        'type'              => 'decimal',
	        'backend'           => '',
	        'frontend'          => '',
	        'label'             => 'Fidelity points used',
	        'input'             => 'text',
	        'class'             => '',
	        'source'            => '',
	        'visible'           => true,
	        'required'          => false,
	        'user_defined'      => false,
	        'default'           => 0,
	        'searchable'        => false,
	        'filterable'        => false,
	        'comparable'        => false,
	        'visible_on_front'  => false,
	        'unique'            => false,
	    ));

$installer->addAttribute('quote_address', 'auguria_sponsorship_sponsor_points_used', array(
	        'type'              => 'decimal',
	        'backend'           => '',
	        'frontend'          => '',
	        'label'             => 'Sponsorship points used',
	        'input'             => 'text',
	        'class'             => '',
	        'source'            => '',
	        'visible'           => true,
	        'required'          => false,
	        'user_defined'      => false,
	        'default'           => 0,
	        'searchable'        => false,
	        'filterable'        => false,
	        'comparable'        => false,
	        'visible_on_front'  => false,
	        'unique'            => false,
	    ));

$installer->addAttribute('quote_address', 'auguria_sponsorship_accumulated_points_used', array(
	        'type'              => 'decimal',
	        'backend'           => '',
	        'frontend'          => '',
	        'label'             => 'Sponsorship and fidelity points used',
	        'input'             => 'text',
	        'class'             => '',
	        'source'            => '',
	        'visible'           => true,
	        'required'          => false,
	        'user_defined'      => false,
	        'default'           => 0,
	        'searchable'        => false,
	        'filterable'        => false,
	        'comparable'        => false,
	        'visible_on_front'  => false,
	        'unique'            => false,
	    ));


$installer->addAttribute('order', 'auguria_sponsorship_fidelity_points_used', array(
	        'type'              => 'decimal',
	        'backend'           => '',
	        'frontend'          => '',
	        'label'             => 'Fidelity points used',
	        'input'             => 'text',
	        'class'             => '',
	        'source'            => '',
	        'visible'           => true,
	        'required'          => false,
	        'user_defined'      => false,
	        'default'           => 0,
	        'searchable'        => false,
	        'filterable'        => false,
	        'comparable'        => false,
	        'visible_on_front'  => false,
	        'unique'            => false,
	    ));

$installer->addAttribute('order', 'auguria_sponsorship_sponsor_points_used', array(
	        'type'              => 'decimal',
	        'backend'           => '',
	        'frontend'          => '',
	        'label'             => 'Sponsorship points used',
	        'input'             => 'text',
	        'class'             => '',
	        'source'            => '',
	        'visible'           => true,
	        'required'          => false,
	        'user_defined'      => false,
	        'default'           => 0,
	        'searchable'        => false,
	        'filterable'        => false,
	        'comparable'        => false,
	        'visible_on_front'  => false,
	        'unique'            => false,
	    ));

$installer->addAttribute('order', 'auguria_sponsorship_accumulated_points_used', array(
	        'type'              => 'decimal',
	        'backend'           => '',
	        'frontend'          => '',
	        'label'             => 'Sponsorship and fidelity points used',
	        'input'             => 'text',
	        'class'             => '',
	        'source'            => '',
	        'visible'           => true,
	        'required'          => false,
	        'user_defined'      => false,
	        'default'           => 0,
	        'searchable'        => false,
	        'filterable'        => false,
	        'comparable'        => false,
	        'visible_on_front'  => false,
	        'unique'            => false,
	    ));
$installer->endSetup(); 