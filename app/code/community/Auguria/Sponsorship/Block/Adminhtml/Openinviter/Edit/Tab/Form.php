<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Adminhtml_Openinviter_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('openinviter_form', array('legend'=>Mage::helper('sponsorship')->__("Provider information")));
      $openinviter = Mage::getModel('sponsorship/openinviter');
	  $plugins = $openinviter->getPluginsArray();
	  
      $fieldset->addField('code', 'select', array(
          'label'     => Mage::helper('sponsorship')->__('Code'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'code',
      	  'values'    => $plugins
      ));
      
      $fieldset->addField('name', 'text', array(
          'label'     => Mage::helper('sponsorship')->__('Name'),
          'name'      => 'name',
	  ));

      $fieldset->addField('status', 'select', array(
			'label'     => Mage::helper('sponsorship')->__('Status'),
			'name'      => 'status',
			'values'    => array(
			  array(
				  'value'     => 1,
				  'label'     => Mage::helper('sponsorship')->__('Enabled'),
			  ),
			
			  array(
				  'value'     => 2,
				  'label'     => Mage::helper('sponsorship')->__('Disabled'),
			  ),
			),
		));
	  
	  $fieldset->addField('image', 'image', array(
			'label'     => Mage::helper('sponsorship')->__('Image'),
			'required'  => false,
			'name'      => 'openinviterimage',
		));
		
      if ( Mage::getSingleton('adminhtml/session')->getSponsorshipData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getSponsorshipData());
          Mage::getSingleton('adminhtml/session')->setSponsorshipData(null);
      } elseif ( Mage::registry('openinviter_data') ) {
          $form->setValues(Mage::registry('openinviter_data')->getData());
      }
      return parent::_prepareForm();
  }
}