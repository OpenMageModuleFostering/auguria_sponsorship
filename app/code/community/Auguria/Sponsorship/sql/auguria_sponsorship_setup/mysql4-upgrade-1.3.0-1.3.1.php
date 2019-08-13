<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $this Auguria_Sponsorship_Model_Eav_Entity_Setup */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($this->getTable('auguria_sponsorship_change'), 'coupon_code', 'TEXT NULL');

$installer->endSetup(); 