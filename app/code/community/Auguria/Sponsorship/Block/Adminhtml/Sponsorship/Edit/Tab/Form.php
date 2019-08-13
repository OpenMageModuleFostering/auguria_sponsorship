<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Adminhtml_Sponsorship_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('sponsorship_form', array('legend'=>Mage::helper('auguria_sponsorship')->__("Invitation information")));
     
      $fieldset->addField('parent_id', 'text', array(
          'label'     => Mage::helper('auguria_sponsorship')->__('Related ID'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'parent_id',
      ));

      $fieldset->addField('child_mail', 'text', array(
          'label'     => Mage::helper('auguria_sponsorship')->__('Guest mail'),
          'required'  => true,
          'name'      => 'child_mail',
	  ));

	  $fieldset->addField('datetime', 'date', array(
            'name'      => 'datetime',
            'title'     => Mage::helper('auguria_sponsorship')->__("Date"),
            'label'     => Mage::helper('auguria_sponsorship')->__("Date"),
            'required'  => true,
	  		'image'  => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'/adminhtml/default/default/images/grid-cal.gif',
            'input_format' => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT), 
		    'format' => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
        ))->setReadonly(true, true); 

      if ( Mage::getSingleton('adminhtml/session')->getSponsorshipData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getSponsorshipData());
          Mage::getSingleton('adminhtml/session')->setSponsorshipData(null);
      } elseif ( Mage::registry('sponsorship_data') ) {
          $form->setValues(Mage::registry('sponsorship_data')->getData());
      }
      return parent::_prepareForm();
  }
}