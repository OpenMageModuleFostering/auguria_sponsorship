<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$setup = new Mage_Sales_Model_Mysql4_Setup('core_setup');
$setup->startSetup();

$setup->addAttribute('quote_item', 'cart_fidelity_points', array(
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
        'default'           => '',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'unique'            => false,
    ));
    
$setup->addAttribute('quote_item', 'cart_sponsor_points', array(
        'type'              => 'decimal',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Points Parrainage',
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
    
$setup->endSetup();