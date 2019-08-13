<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Adminhtml_Openinviter_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('openinviter_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('auguria_sponsorship')->__('Provider detail'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('auguria_sponsorship')->__('Provider detail'),
          'title'     => Mage::helper('auguria_sponsorship')->__('Provider detail'),
          'content'   => $this->getLayout()->createBlock('auguria_sponsorship/adminhtml_openinviter_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}