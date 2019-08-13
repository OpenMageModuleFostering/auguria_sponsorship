<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Adminhtml_Sponsorship extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_sponsorship';
		$this->_blockGroup = 'auguria_sponsorship';
		$this->_headerText = $this->__('Invitations list');
		parent::__construct();
		$this->removeButton('add');
	}
}