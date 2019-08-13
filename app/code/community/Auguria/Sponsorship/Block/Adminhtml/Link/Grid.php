<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Adminhtml_Link_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('linkGrid');
      $this->setDefaultSort('id_parrain');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
  	  $customer = Mage::getModel('customer/customer');
  	  $firstname  = $customer->getAttribute('firstname');
  	  $lastname   = $customer->getAttribute('lastname');
  	  $core = Mage::getSingleton('core/resource');

      $collection = Mage::getResourceModel('customer/customer_collection')
                        ->addAttributeToSelect("sponsor")
                        ->addNameToSelect()
                        ->addExpressionAttributeToSelect('sponsor_name',
                            new Zend_Db_Expr('CONCAT((select cev.value from '.$core->getTableName('customer_entity_varchar').' cev where cev.entity_id={{sponsor}} and cev.attribute_id='.(int) $firstname->getAttributeId().')," ",(select cev.value from '.$core->getTableName('customer_entity_varchar').' cev where cev.entity_id={{sponsor}} and cev.attribute_id='.(int) $lastname->getAttributeId().'))'),
                            'sponsor')
                        ->addExpressionAttributeToSelect('date_last_order',
                            new Zend_Db_Expr('(select max(so.created_at) from '.$core->getTableName('sales/order').' so where so.customer_id={{entity_id}})'),
                            'entity_id')
                        ->addAttributeToFilter("sponsor", array('neq'=> array(0)))
      ;

      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('id', array(
          'header'    => Mage::helper('auguria_sponsorship')->__('ID'),
          'align'     =>'right',
          'width'     =>'20px',
          'index'     => 'entity_id',
      ));

      $this->addColumn('id_parrain', array(
          'header'    => Mage::helper('auguria_sponsorship')->__('Sponsor ID'),
          'align'     =>'right',
          'width'     =>'20px',
          'index'     => 'sponsor',
      ));

      $this->addColumn('parrain', array(
          'header'    => Mage::helper('auguria_sponsorship')->__('Sponsor'),
          'align'     =>'left',
          'index'     => 'sponsor_name',
      ));

      $this->addColumn('id_filleul', array(
          'header'    => Mage::helper('auguria_sponsorship')->__('Godson ID'),
          'align'     =>'right',
          'width'     =>'20px',
          'index'     => 'entity_id',
      ));

      $this->addColumn('filleul', array(
          'header'    => Mage::helper('auguria_sponsorship')->__('Godson'),
          'align'     =>'left',
          'index'     => 'name',
      ));

       $this->addColumn('date_inscription', array(
          'header'    => Mage::helper('auguria_sponsorship')->__('Listing date'),
          'align'     =>'left',
          'align'     =>'right',
          'index'     => 'created_at',
      	  'type'	  => 'datetime',
          'format'	  => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
      ));

      $this->addColumn('date_last_order', array(
          'header'    => Mage::helper('auguria_sponsorship')->__('Last order date'),
          'align'     =>'left',
          'align'     =>'right',
          'index'     => 'date_last_order',
      	  'type'	  => 'datetime',
          'format'	  => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
      ));

		$this->addExportType('*/*/exportCsv', Mage::helper('auguria_sponsorship')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('auguria_sponsorship')->__('XML'));

      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id_filleul');
        $this->getMassactionBlock()->setFormFieldName('sponsorship');

        $statuses = Mage::getSingleton('auguria_sponsorship/changestatut')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('sponsor', array(
             'label'=> Mage::helper('auguria_sponsorship')->__('Supprimer Parrainages'),
             'url'  => $this->getUrl('*/*/massDelete', array('_current'=>true)),
             'confirm'  => Mage::helper('auguria_sponsorship')->__('Are you sure to process mass action?')
             )
        );
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}