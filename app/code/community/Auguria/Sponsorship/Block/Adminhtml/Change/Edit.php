<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Adminhtml_Change_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
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
        $this->_blockGroup = 'sponsorship';
        $this->_controller = 'adminhtml_change';
        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('adminhtml')->__('Save'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('change_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'change_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'change_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('change_data') && Mage::registry('change_data')->getId() ) {
            return Mage::helper('sponsorship')->__("Edition of the change '%s'", $this->htmlEscape(Mage::registry('change_data')->getId()));
        } else {
            return Mage::helper('sponsorship')->__('Add a change');
        }
    }
}