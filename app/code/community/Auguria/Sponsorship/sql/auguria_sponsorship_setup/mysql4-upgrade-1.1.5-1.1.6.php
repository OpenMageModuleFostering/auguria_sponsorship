<?php
/**
 * Add accumulated field to customer
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;

$installer->addAttribute('customer', 'accumulated_points', array(
	        'type'              => 'decimal',
	        'backend'           => '',
	        'frontend'          => '',
	        'label'             => 'Points',
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
			'used_in_forms'		=> array('adminhtml_customer'),
			'group'				=> 'Default',
			'sort_order'		=> 200
	    ));
$installer->endSetup(); 