<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();

$setup->addAttribute('customer', 'iban', array(
        'type'              => 'varchar',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Iban',
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
    ));
	
    $setup->addAttribute('customer', 'siret', array(
        'type'              => 'varchar',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Siret',
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
    ));
    
$setup->endSetup();