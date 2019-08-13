<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Adminhtml_Link extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {    
    $this->_controller = 'adminhtml_link';
    $this->_blockGroup = 'auguria_sponsorship';
    $this->_headerText = $this->__('Sponsorships list');
    parent::__construct();
    $this->removeButton('add');
  }
}