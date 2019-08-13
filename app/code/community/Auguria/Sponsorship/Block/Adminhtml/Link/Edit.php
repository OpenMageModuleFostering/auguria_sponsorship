<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Adminhtml_Link_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        Mage_Adminhtml_Block_Widget_Container::__construct();
        
        if (!$this->hasData('template')) {
            $this->setTemplate('widget/form/container.phtml');
        }

        $this->_addButton('back', array(
            'label'     => Mage::helper('adminhtml')->__('Back'),
            'onclick'   => 'setLocation(\'' . $this->getBackUrl() . '\')',
            'class'     => 'back',
        ), -1);
        $this->_addButton('reset', array(
            'label'     => Mage::helper('adminhtml')->__('Reset'),
            'onclick'   => 'setLocation(window.location.href)',
        ), -1);

        $objId = $this->getRequest()->getParam($this->_objectId);
        
        $this->_addButton('save', array(
            'label'     => Mage::helper('adminhtml')->__('Save'),
            'onclick'   => 'editForm.submit();',
            'class'     => 'save',
        ), 1);
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'auguria_sponsorship';
        $this->_controller = 'adminhtml_link';
        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('adminhtml')->__('Delete'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('link_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'link_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'link_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('link_data') && Mage::registry('link_data')->getId() ) {
            return Mage::helper('auguria_sponsorship')->__("Edition of the sponsorship '%s'", $this->htmlEscape(Mage::registry('link_data')->getId()));
        } else {
            return Mage::helper('auguria_sponsorship')->__('Add a sponsorship');
        }
    }
}