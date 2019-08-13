<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$setup = $this;
$setup->startSetup();

if (!$setup->hasSponsorshipInstall()) {
	$setup->addAttribute('customer', 'sponsor', array(
	        'type'              => 'int',
	        'backend'           => '',
	        'frontend'          => '',
	        'label'             => 'Parrain',
	        'input'             => 'text',
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
	    ));
	    
	$setup->addAttribute('customer', 'fidelity_points', array(
	        'type'              => 'decimal',
	        'backend'           => '',
	        'frontend'          => '',
	        'label'             => 'Points Fidélité',
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
	
	$setup->addAttribute('customer', 'sponsor_points', array(
	        'type'              => 'decimal',
	        'backend'           => '',
	        'frontend'          => '',
	        'label'             => 'Points de Parrainage',
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
}
$setup->endSetup();