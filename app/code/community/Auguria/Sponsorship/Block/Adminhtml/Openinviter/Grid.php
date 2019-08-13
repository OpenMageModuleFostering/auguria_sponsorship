<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Adminhtml_Openinviter_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('openinviterGrid');
      	$this->setDefaultSort('code');
      	$this->setDefaultDir('ASC');
      	$this->setSaveParametersInSession(true);
  	}

  	protected function _prepareCollection()
  	{
      	$collection = Mage::getResourceModel('auguria_sponsorship/sponsorshipopeninviter_collection')
                ;
      	$this->setCollection($collection);
      	return parent::_prepareCollection();
  	}

  	protected function _prepareColumns()
  	{
      	$this->addColumn('sponsorship_openinviter_id', array(
          'header'    => Mage::helper('auguria_sponsorship')->__('ID'),
          'align'     =>'right',
          'index'     => 'sponsorship_openinviter_id',
      	));
		
      	$this->addColumn('code', array(
			'header'    => Mage::helper('auguria_sponsorship')->__('Code'),
			'index'     => 'code',
      	));
		
      	$this->addColumn('name', array(
			'header'    => Mage::helper('auguria_sponsorship')->__('Name'),
			'index'     => 'name',
      	));
		
      	$this->addColumn('image', array(
			'header'    => Mage::helper('auguria_sponsorship')->__('Image'),
      		'align'     =>'center',
			'width'     => '50px',
			'filter'    => false,
			'sortable'  => false,
			'renderer'  => 'auguria_sponsorship/adminhtml_widget_grid_renderer',
      	));
	  	
      	$this->addColumn('status', array(
			'header'    => Mage::helper('auguria_sponsorship')->__('Status'),
			'index'     => 'status',
			'type'      => 'options',
			'options'   => array(
			  1 => Mage::helper('auguria_sponsorship')->__('Enabled'),
			  2 => Mage::helper('auguria_sponsorship')->__('Disabled'),
			),
		));
		
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('auguria_sponsorship')->__('Action'),
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
	  
      	return parent::_prepareColumns();
  	}

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('sponsorship_openinviter_id');
		$this->getMassactionBlock()->setFormFieldName('openinviter');
		
		$this->getMassactionBlock()->addItem('delete', array(
			'label'    => Mage::helper('auguria_sponsorship')->__('Delete'),
			'url'      => $this->getUrl('*/*/massDelete'),
			'confirm'  => Mage::helper('auguria_sponsorship')->__('Are you sure?')
		));
		
		$statuses = Mage::getSingleton('auguria_sponsorship/status')->getOptionArray();
		
		array_unshift($statuses, array('label'=>'', 'value'=>''));
		$this->getMassactionBlock()->addItem('status', array(
			'label'=> Mage::helper('auguria_sponsorship')->__('Change status'),
			'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
			'additional' => array(
				'visibility' => array(
					'name' => 'status',
					'type' => 'select',
					'class' => 'required-entry',
					'label' => Mage::helper('auguria_sponsorship')->__('Status'),
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