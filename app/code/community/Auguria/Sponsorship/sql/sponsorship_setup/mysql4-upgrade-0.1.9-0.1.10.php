<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();

$setup->addAttribute('customer', 'special_rate', array(
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Special Rate',
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