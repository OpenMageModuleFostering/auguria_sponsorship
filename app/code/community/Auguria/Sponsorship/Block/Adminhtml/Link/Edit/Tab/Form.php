<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Adminhtml_Link_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('link_form', array('legend'=>Mage::helper('auguria_sponsorship')->__('Sponsorship information')));
     
      $fieldset->addField('entity_id', 'text', array(
          'label'     => Mage::helper('auguria_sponsorship')->__('Godson'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'entity_id',
      ))->setReadonly(true);

      $fieldset->addField('sponsor', 'text', array(
          'label'     => Mage::helper('auguria_sponsorship')->__('Sponsor'),
          'name'      => 'sponsor',
      ));

      if ( Mage::getSingleton('adminhtml/session')->getSponsorshipData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getSponsorshipData());
          Mage::getSingleton('adminhtml/session')->setSponsorshipData(null);
      } elseif ( Mage::registry('link_data') ) {
          $form->setValues(Mage::registry('link_data')->getData());
      }
      return parent::_prepareForm();
  }
}