<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Adminhtml_Change_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('changeGrid');
      $this->setDefaultSort('sponsorship_change_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getResourceModel('sponsorship/change_collection')
                            ->addNameToSelect()
      ;
      /*
      $collection = Mage::getModel('sponsorship/change')->getCollection();
      foreach ($collection as $change)
      {
          $customer = mage::getModel("customer/customer")->load($change->getCustomerId());
          $change->setCustomerName($customer->getFirstname()." ".$customer->getLastname());
          $change->setValue(sprintf('%.2f',($change->getValue())));
          $change->setPoints(floor($change->getPoints()));
      }*/
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('sponsorship_change_id', array(
          'header'    => Mage::helper('sponsorship')->__('ID'),
          'align'     =>'right',
          'width'     => '20px',
          'index'     => 'sponsorship_change_id',
      ));

      $this->addColumn('customer_id', array(
          'header'    => Mage::helper('sponsorship')->__('Customer ID'),
          'align'     =>'right',
      	  'width'     => '20px',
          'index'     => 'customer_id',
      ));

      $this->addColumn('customer_name', array(
          'header'    => Mage::helper('sponsorship')->__('Customer name'),
          'align'     =>'left',
          'filter'  => false,
          'index'     => 'customer_name',
      ));

	  
      $this->addColumn('type', array(
			'header'    => Mage::helper('sponsorship')->__('Type'),
			'index'     => 'type',
      		'type'      => 'options',
          	'options'   => array(
              'cash' => 'Cash',
              'gift' => 'Cadeaux',
      		  'coupon' => 'Bons de réductions',
          ),
      ));
      
      $this->addColumn('module', array(
			'header'    => Mage::helper('sponsorship')->__('Module'),
			'index'     => 'module',
      		'type'      => 'options',
          	'options'   => array(
              'fidelity' => 'Fidélité',
              'sponsor' => 'Parrainage',
      	),
      ));
      
      $this->addColumn('points', array(
			'header'    => Mage::helper('sponsorship')->__('Points'),
                        'align'     =>'right',
			'index'     => 'points',
      ));
      
      $this->addColumn('value', array(
			'header'    => Mage::helper('sponsorship')->__('Value'),
                        'align'     =>'right',
			'index'     => 'value',
      ));
	  
      $this->addColumn('datetime', array(
          'header'    => Mage::helper('sponsorship')->__('Date'),
          'align'     =>'right',
          'index'     => 'datetime',
      	  'type'	  => 'datetime',
      	  'format'	  => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT), 
      ));
      
      $this->addColumn('statut', array(
          'header'    => Mage::helper('sponsorship')->__('Status'),
          'align'     => 'left',
          'index'     => 'statut',
          'type'      => 'options',
          'options'   => array(
              'waiting' => 'En attente',
              'exported' => 'Exporté',
      		  'solved' => 'Réglé',
      		  'canceled' => 'Annulé',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('sponsorship')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('sponsorship')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('sponsorship')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('sponsorship')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('change_id');
        $this->getMassactionBlock()->setFormFieldName('change');

  //      $this->getMassactionBlock()->addItem('delete', array(
    //         'label'    => Mage::helper('sponsorship')->__('Delete'),
     //        'url'      => $this->getUrl('*/*/massDelete'),
    //         'confirm'  => Mage::helper('sponsorship')->__('Are you sure?')
     //   ));
        
        $statuses = Mage::getSingleton('sponsorship/changestatut')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('statut', array(
             'label'=> Mage::helper('sponsorship')->__('Update status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'confirm'  => Mage::helper('sponsorship')->__('Are you sure to process mass action?'),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'statut',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('sponsorship')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}