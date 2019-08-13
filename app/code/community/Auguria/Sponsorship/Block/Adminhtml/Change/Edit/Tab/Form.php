<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Adminhtml_Change_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('change_form', array('legend'=>Mage::helper('sponsorship')->__('Change information')));
     
      $fieldset->addField('customer_id', 'text', array(
          'label'     => Mage::helper('sponsorship')->__('Customer'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'customer_id',
      ))->setReadonly(true, true);

      $fieldset->addField('type', 'select', array(
          'label'     => Mage::helper('sponsorship')->__('Type'),
          'required'  => true,
          'name'      => 'type',
      	  'values'    => array(
              array(
                  'value'     => 'cash',
                  'label'     => Mage::helper('sponsorship')->__('Cash'),
              ),

              array(
                  'value'     => 'gift',
                  'label'     => Mage::helper('sponsorship')->__('Gifts'),
              ),
              array(
                  'value'     => 'coupon',
                  'label'     => Mage::helper('sponsorship')->__('Vouchers'),
              ),
           ),
	  ))->setReadonly(true, true);
	  
	  $fieldset->addField('module', 'select', array(
          'label'     => Mage::helper('sponsorship')->__('Module'),
          'required'  => true,
          'name'      => 'module',
      	  'values'    => array(
              array(
                  'value'     => 'fidelity',
                  'label'     => Mage::helper('sponsorship')->__('Fidelity'),
              ),

              array(
                  'value'     => 'sponsor',
                  'label'     => Mage::helper('sponsorship')->__('Sponsorship'),
              ),
           ),
	  ))->setReadonly(true, true);
		
      $fieldset->addField('statut', 'select', array(
          'label'     => Mage::helper('sponsorship')->__('Status'),
          'name'      => 'statut',
          'values'    => array(
              array(
                  'value'     => 'waiting',
                  'label'     => Mage::helper('sponsorship')->__('Waiting'),
              ),

              array(
                  'value'     => 'exported',
                  'label'     => Mage::helper('sponsorship')->__('Exported'),
              ),
              array(
                  'value'     => 'solved',
                  'label'     => Mage::helper('sponsorship')->__('Solved'),
              ),

              array(
                  'value'     => 'canceled',
                  'label'     => Mage::helper('sponsorship')->__('Canceled'),
              ),
          ),
      ));
      
      $fieldset->addField('datetime', 'date', array(
          'label'     => Mage::helper('sponsorship')->__('Creation date'),
      	  'title'     => Mage::helper('sponsorship')->__('Creation date'),
          'required'  => true,
          'name'      => 'datetime',
      	  'image'  => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'/adminhtml/default/default/images/grid-cal.gif',
          'input_format' => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
		  'format' => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT), 
	  ))->setReadonly(true, true);

      if ( Mage::getSingleton('adminhtml/session')->getSponsorshipData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getSponsorshipData());
          Mage::getSingleton('adminhtml/session')->setSponsorshipData(null);
      } elseif ( Mage::registry('change_data') ) {
          $form->setValues(Mage::registry('change_data')->getData());
      }
      return parent::_prepareForm();
  }
}