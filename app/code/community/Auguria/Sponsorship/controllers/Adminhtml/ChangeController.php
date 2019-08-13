<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Adminhtml_ChangeController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('change/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('auguria_sponsorship/change')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			Mage::register('change_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('change/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('auguria_sponsorship/adminhtml_change_edit'))
				->_addLeft($this->getLayout()->createBlock('auguria_sponsorship/adminhtml_change_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('auguria_sponsorship')->__("This change doesn't exist"));
			$this->_redirect('*/*/');
		}
	}
 
	public function saveAction() {
		if ($cdata = $this->getRequest()->getPost()) {
			
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS ;
					$uploader->save($path, $_FILES['filename']['name'] );
					
				} catch (Exception $e) {
		      
		        }
	        
		        //this way the name is saved in DB
	  			$cdata['filename'] = $_FILES['filename']['name'];
			}
	  			
	  		try {
				
	  			//Enregistrements spécifiques à une annulation---------------------------
				$changeId = $this->getRequest()->getParam('id');
				$this->statusCanceled ($changeId,$cdata['statut']);
				
				//Enregistrement dans la table "change"
				$model = Mage::getModel('auguria_sponsorship/change');
				$model->setData($cdata)
					->setId($this->getRequest()->getParam('id'));				
				$model->save();
				
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('auguria_sponsorship')->__("The change has been successfully recorded"));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('auguria_sponsorship')->__("Unable to find change to save"));
        $this->_redirect('*/*/');
	}
	
    public function massStatusAction()
    {
        $sponsorshipIds = $this->getRequest()->getParam('change');
        if(!is_array($sponsorshipIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('auguria_sponsorship')->__("Please select changes"));
        } else {
            try {
                foreach ($sponsorshipIds as $sponsorshipId) {
                	
                	//Enregistrements spécifiques à une annulation---------------------------
					$this->statusCanceled ($sponsorshipId,$this->getRequest()->getParam('statut'));
					
					//Enregistrements dans change
                    $sponsorship = Mage::getSingleton('auguria_sponsorship/change')
                        ->load($sponsorshipId)
                        ->setStatut($this->getRequest()->getParam('statut'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($sponsorshipIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'sponsorshipChange.csv';
        $content    = $this->getLayout()->createBlock('auguria_sponsorship/adminhtml_change_grid')
            ->getCsv();
        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'sponsorshipChange.xml';
        $content    = $this->getLayout()->createBlock('auguria_sponsorship/adminhtml_change_grid')
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
    
    protected function statusCanceled ($changeId, $statut)
    {
    	$model = Mage::getSingleton('auguria_sponsorship/change');
		$model->load($changeId);
		
		//si statut passe à annulé
		if ($model->getStatut() != 'canceled' && $statut == 'canceled')
		{
			//Ajustement en fonction du module (Fidelity ou Sponsor)
			$module = $model->getModule();
			$points = $model->getPoints();
			
			$Module = ucfirst($module);
			$setPoints = 'set'.$Module.'Points';
			$getPoints = 'get'.$Module.'Points';
			
			//Mise à jour des points sur le client
			$cId = $model->getCustomerId();
			
			$customer = Mage::getModel('customer/customer')->load($cId);
			$cPoints = $customer->$getPoints();
			$customer->$setPoints($cPoints+$points);
			$customer->save();
			
			//Mise à jour des logs
			$log = Mage::getModel('auguria_sponsorship/log');
			$dateTime = Mage::getModel('core/date')->gmtDate();
			
			$data = array(
			    'customer_id' => $cId,
			    'record_id' => $changeId,
			    'record_type' => 'admin',
			    'datetime' => $dateTime,
			    'points' => $points
    		);
			$log->setData($data);
			$log->save();
			
		}
		//si statut passe de annulé à autre
		elseif ($model->getStatut() == 'canceled' && $statut != 'canceled')
		{
			
			//Ajustement en fonction du module (Fidelity ou Sponsor)
			$module = $model->getModule();
			$points = $model->getPoints();
			
			$Module = ucfirst($module);
			$setPoints = 'set'.$Module.'Points';
			$getPoints = 'get'.$Module.'Points';
			
			//Mise à jour des points sur le client
			$cId = $model->getCustomerId();
			
			$customer = Mage::getModel('customer/customer')->load($cId);
			$cPoints = $customer->$getPoints();
			$customer->$setPoints($cPoints-$points);
			$customer->save();
			
			//Mise à jour des logs
			$log = Mage::getModel('auguria_sponsorship/log');
			$dateTime = Mage::getModel('core/date')->gmtDate();			
    		$data = array(
			    'customer_id' => $cId,
			    'record_id' => $changeId,
			    'record_type' => 'admin',
			    'datetime' => $dateTime,
			    'points' => -$points
    		);
			$log->setData($data);
			$log->save();
		}
    }
}