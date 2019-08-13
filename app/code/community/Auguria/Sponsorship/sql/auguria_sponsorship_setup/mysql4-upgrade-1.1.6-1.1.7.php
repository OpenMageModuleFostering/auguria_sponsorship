<?php
/**
 * Update change table to add module accumulated
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;

$installer->getConnection()->changeColumn($this->getTable("auguria_sponsorship/change"),
    "module", "module",
    "enum('fidelity', 'sponsor', 'accumulated') NOT NULL"
);

$installer->endSetup(); 