<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Adminhtml_Openinviter_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'auguria_sponsorship';
        $this->_controller = 'adminhtml_openinviter';

        $this->_addButton('back', array(
            'label'     => Mage::helper('adminhtml')->__('Back'),
            'onclick'   => 'setLocation(\'' . $this->getBackUrl() . '\')',
            'class'     => 'back',
        ), -1);
        
        $this->_updateButton('save', 'label', Mage::helper('auguria_sponsorship')->__('Save'));
		$this->_updateButton('delete', 'label', Mage::helper('auguria_sponsorship')->__('Delete'));
		
    }

    public function getHeaderText()
    {
        if( Mage::registry('openinviter_data') && Mage::registry('openinviter_data')->getId() ) {
            return Mage::helper('auguria_sponsorship')->__("Edition of the Open inviter provider '%s'", $this->htmlEscape(Mage::registry('openinviter_data')->getName()));
        } else {
            return Mage::helper('auguria_sponsorship')->__('Add a provider');
        }
    }
}