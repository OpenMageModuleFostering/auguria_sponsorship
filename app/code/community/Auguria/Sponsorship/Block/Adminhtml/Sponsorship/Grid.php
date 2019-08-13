<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Adminhtml_Sponsorship_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('sponsorshipGrid');
      //$this->setUseAjax(true);
      $this->setDefaultSort('sponsorship_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getResourceModel('auguria_sponsorship/sponsorship_collection')
                ;//->addChildNameToSelect();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('sponsorship_id', array(
          'header'    => Mage::helper('auguria_sponsorship')->__('ID'),
          'align'     =>'right',
          //'width'     => '40px',
          'index'     => 'sponsorship_id',
      ));
  
      $this->addColumn('parent_id', array(
          'header'    => Mage::helper('auguria_sponsorship')->__('Related ID'),
          'align'     =>'right',
      	  //'width'     => '40px',
          'index'     => 'parent_id',
      ));

      $this->addColumn('parent_name', array(
			'header'    => Mage::helper('auguria_sponsorship')->__('Related name'),
			'index'     => 'parent_name',
      ));

      $this->addColumn('parent_mail', array(
          'header'    => Mage::helper('auguria_sponsorship')->__('Related mail'),
          'align'     =>'left',
          'index'     => 'parent_mail',
      ));

      $this->addColumn('child_firstname', array(
			'header'    => Mage::helper('auguria_sponsorship')->__('Guest first name'),
			'index'     => 'child_firstname',
      ));
      $this->addColumn('child_lastname', array(
			'header'    => Mage::helper('auguria_sponsorship')->__('Guest last name'),
			'index'     => 'child_lastname',
      ));
  
      $this->addColumn('child_mail', array(
			'header'    => Mage::helper('auguria_sponsorship')->__('Guest mail'),
			'index'     => 'child_mail',
      ));

      $this->addColumn('message', array(
			'header'    => Mage::helper('auguria_sponsorship')->__('Message'),
			'index'     => 'message',
      ));

      $this->addColumn('datetime', array(
          'header'    => Mage::helper('auguria_sponsorship')->__("Date"),
          'align'     =>'right',
          'index'     => 'datetime',
      	  'type'	  => 'datetime',
      	  'format'	  => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
      ));

      $this->addColumn('datetime_boost', array(
          'header'    => Mage::helper('auguria_sponsorship')->__("Recovery date"),
          'align'     =>'right',
          'index'     => 'datetime_boost',
      	  'type'	  => 'datetime',
      	  'format'	  => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('auguria_sponsorship')->__('Action'),
                //'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('auguria_sponsorship')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('auguria_sponsorship')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('auguria_sponsorship')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}