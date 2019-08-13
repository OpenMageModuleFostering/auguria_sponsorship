<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$setup = $this;
$setup->startSetup();

	$setup->addAttribute('customer', 'bic', array(
			'type'              => 'varchar',
			'backend'           => '',
			'frontend'          => '',
			'label'             => 'Bic',
			'input'             => 'text',
			'class'             => '',
			'source'            => '',
			'visible'           => true,
			'required'          => false,
			'user_defined'      => true,
			'default'           => '',
			'searchable'        => false,
			'filterable'        => false,
			'comparable'        => false,
			'visible_on_front'  => true,
			'unique'            => false,
			'used_in_forms'		=> array('adminhtml_customer'),
			'group'				=> 'Default',
			'sort_order'		=> 312
	));

$setup->endSetup();