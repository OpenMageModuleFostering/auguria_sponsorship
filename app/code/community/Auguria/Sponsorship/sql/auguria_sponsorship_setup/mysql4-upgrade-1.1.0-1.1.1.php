<?php
/**
 * Create fields to allow apply auguria_sponsorship_discount in quote
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = new Mage_Sales_Model_Mysql4_Setup('core_setup');

$installer->addAttribute('quote_address', 'base_auguria_sponsorship_discount_amount', array(
	        'type'              => 'decimal',
	        'backend'           => '',
	        'frontend'          => '',
	        'label'             => 'Base discount',
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
$installer->addAttribute('quote_address', 'auguria_sponsorship_discount_amount', array(
	        'type'              => 'decimal',
	        'backend'           => '',
	        'frontend'          => '',
	        'label'             => 'Discount',
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

$installer->addAttribute('order', 'base_auguria_sponsorship_discount_amount', array(
	        'type'              => 'decimal',
	        'backend'           => '',
	        'frontend'          => '',
	        'label'             => 'Base discount',
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
$installer->addAttribute('order', 'auguria_sponsorship_discount_amount', array(
	        'type'              => 'decimal',
	        'backend'           => '',
	        'frontend'          => '',
	        'label'             => 'Discount',
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

$installer->addAttribute('invoice', 'base_auguria_sponsorship_discount_amount', array(
	        'type'              => 'decimal',
	        'backend'           => '',
	        'frontend'          => '',
	        'label'             => 'Base discount',
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
$installer->addAttribute('invoice', 'auguria_sponsorship_discount_amount', array(
	        'type'              => 'decimal',
	        'backend'           => '',
	        'frontend'          => '',
	        'label'             => 'Discount',
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