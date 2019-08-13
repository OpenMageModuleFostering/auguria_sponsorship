<?php
/**
 * Add customer attributes to manage points validity
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;

$installer->addAttribute('customer', 'points_validity', array(
	        'type'              => 'datetime',
	        'backend'           => 'eav/entity_attribute_backend_datetime',
	        'frontend'          => 'eav/entity_attribute_frontend_datetime',
	        'label'             => 'Points validity',
	        'input'             => 'date',
	        'class'             => '',
	        'source'            => '',
	        'visible'           => true,
	        'required'          => false,
	        'user_defined'      => false,
	        'default'           => '',
	        'searchable'        => false,
	        'filterable'        => false,
	        'comparable'        => false,
	        'visible_on_front'  => false,
	        'unique'            => false,
			'used_in_forms'		=> array('adminhtml_customer'),
			'group'				=> 'Default',
			'sort_order'		=> 200
	    ));

$installer->addAttribute('customer', 'sponsorship_points_validity', array(
	        'type'              => 'datetime',
	        'backend'           => 'eav/entity_attribute_backend_datetime',
	        'frontend'          => 'eav/entity_attribute_frontend_datetime',
	        'label'             => 'Sponsorship points validity',
	        'input'             => 'date',
	        'class'             => '',
	        'source'            => '',
	        'visible'           => true,
	        'required'          => false,
	        'user_defined'      => false,
	        'default'           => '',
	        'searchable'        => false,
	        'filterable'        => false,
	        'comparable'        => false,
	        'visible_on_front'  => false,
	        'unique'            => false,
			'used_in_forms'		=> array('adminhtml_customer'),
			'group'				=> 'Default',
			'sort_order'		=> 200
	    ));
	    
$installer->addAttribute('customer', 'fidelity_points_validity', array(
	        'type'              => 'datetime',
	        'backend'           => 'eav/entity_attribute_backend_datetime',
	        'frontend'          => 'eav/entity_attribute_frontend_datetime',
	        'label'             => 'Fidelity points validity',
	        'input'             => 'date',
	        'class'             => '',
	        'source'            => '',
	        'visible'           => true,
	        'required'          => false,
	        'user_defined'      => false,
	        'default'           => '',
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