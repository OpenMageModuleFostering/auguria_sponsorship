<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Adminhtml_OpeninviterController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('auguria_sponsorship/openinviter')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction()
	{
		$this->_initAction()
			->renderLayout();
	}
	
	public function newAction()
	{
		$this->_forward('edit');
	}
	
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('auguria_sponsorship/sponsorshipopeninviter')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('openinviter_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('auguria_sponsorship/openinviter');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('auguria_sponsorship/adminhtml_openinviter_edit'))
				->_addLeft($this->getLayout()->createBlock('auguria_sponsorship/adminhtml_openinviter_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('auguria_sponsorship')->__('Invitation does not exist'));
			$this->_redirect('*/*/');
		}
	}
  
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
			if(isset($_FILES['openinviterimage']['name']) && $_FILES['openinviterimage']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('openinviterimage');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media').DS.'sponsorship'.DS.'openinviter' ;
					$uploader->save($path, $_FILES['openinviterimage']['name'] );
					
				} catch (Exception $e) {
		      
		        }
	        
		        //this way the name is saved in DB
	  			$data['image'] = 'sponsorship'.DS.'openinviter'.DS.$_FILES['openinviterimage']['name'];
			}
	  			
	  		if(isset($data['openinviterimage']['delete'])) $data['image'] = '';
			
			$model = Mage::getModel('auguria_sponsorship/sponsorshipopeninviter');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('auguria_sponsorship')->__('Provider was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('auguria_sponsorship')->__('Unable to find provider to save'));
        $this->_redirect('*/*/');
	}
 	
	public function deleteAction()
	{
		if ($this->getRequest()->getParam('id') > 0)
		{
			try
			{
				$model = Mage::getModel('auguria_sponsorship/sponsorshipopeninviter');				 
				$model->setId($this->getRequest()->getParam('id'))->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('auguria_sponsorship')->__('Provider was successfully deleted'));
				$this->_redirect('*/*/');
			}
			catch (Exception $e)
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction()
    {
        $providersIds = $this->getRequest()->getParam('openinviter');
        if (!is_array($providersIds))
        {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('auguria_sponsorship')->__('Please select provider(s)'));
        }
        else
        {
            try
            {
                foreach ($providersIds as $providerId)
                {
                    $provider = Mage::getModel('auguria_sponsorship/sponsorshipopeninviter')->load($providerId);
                    $provider->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($providersIds)
                    )
                );
            }
            catch (Exception $e)
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $providersIds = $this->getRequest()->getParam('openinviter');
        if(!is_array($providersIds))
        {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select provider(s)'));
        }
        else
        {
            try
            {
                foreach ($providersIds as $providerId)
                {
                    $provider = Mage::getModel('auguria_sponsorship/sponsorshipopeninviter')->load($providerId)
                        ->load($providerId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($providersIds))
                );
            }
            catch (Exception $e)
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
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
}