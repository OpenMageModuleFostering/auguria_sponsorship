<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Adminhtml_LinkController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$layout = $this->loadLayout();
		$layout->_setActiveMenu('link/items');
		$layout->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		return $this;
	}

	public function indexAction()
	{
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('customer/customer')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			Mage::register('link_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('link/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('auguria_sponsorship/adminhtml_link_edit'))
				->_addLeft($this->getLayout()->createBlock('auguria_sponsorship/adminhtml_link_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('auguria_sponsorship')->__("This sponsorship doesn't exist"));
			$this->_redirect('*/*/');
		}
	}
        public function saveAction()
        {
            if ($data = $this->getRequest()->getPost())
            {
                try
                {
                    $model = Mage::getModel('customer/customer')->load($this->getRequest()->getParam('entity_id'));
                    $model->setSponsor($this->getRequest()->getParam('sponsor'));
                    $model->save();
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('auguria_sponsorship')->__('Sponsorship was successfully saved'));
                    Mage::getSingleton('adminhtml/session')->setFormData(false);

                    if ($this->getRequest()->getParam('back')) {
                            $this->_redirect('*/*/edit', array('id' => $model->getId()));
                            return;
                    }
                    $this->_redirect('*/*/');
                    return;
                }
                catch (Exception $e)
                {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('auguria_sponsorship')->__('Unable to find sponsorship to save'));
            $this->_redirect('*/*/');
	}
         public function exportCsvAction()
        {
            $fileName   = 'sponsorship.csv';
            $content    = $this->getLayout()->createBlock('auguria_sponsorship/adminhtml_link_grid')
                ->getCsv();

            $this->_sendUploadResponse($fileName, $content);
        }

        public function exportXmlAction()
        {
            $fileName   = 'sponsorship.xml';
            $content    = $this->getLayout()->createBlock('auguria_sponsorship/adminhtml_link_grid')
                ->getXml();

            $this->_sendUploadResponse($fileName, $content);
        }
        protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
        {
            $response = $this->getResponse();
            $response->setHeader('HTTP/1.1 200 OK','');
            $response->setHeader('Pragma', 'public', true);
            $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
            $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
            $response->setHeader('Last-Modified', date('r'));
            $response->setHeader('Accept-Ranges', 'bytes');
            $response->setHeader('Content-Length', strlen($content));
            $response->setHeader('Content-type', $contentType);
            $response->setBody($content);
            $response->sendResponse();
            die;
        }

        public function massDeleteAction()
        {
            $sponsorshipIds = $this->getRequest()->getParam('sponsorship');
            if(!is_array($sponsorshipIds))
            {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
            }
            else
            {
                try
                {
                    foreach ($sponsorshipIds as $sponsorshipId)
                    {
                        $sponsorship = Mage::getModel('customer/customer')->load($sponsorshipId);
                        $sponsorship->setSponsor("");
                        $sponsorship->save();
                    }
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($sponsorshipIds)
                    ));
                }
                catch (Exception $e)
                {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
            $this->_redirect('*/*/index');
        }
}