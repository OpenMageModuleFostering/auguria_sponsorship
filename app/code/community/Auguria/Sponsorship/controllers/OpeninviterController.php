<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_OpeninviterController extends Mage_Core_Controller_Front_Action
{        
    public function preDispatch()
    {
        parent::preDispatch();
		//verification que le module de parrainage est activé
        if( !Mage::helper('auguria_sponsorship/config')->isSponsorshipEnabled()
        	&& !Mage::helper('auguria_sponsorship/config')->isAccumulatedEnabled() ){
            $this->_redirect('');
        }
        //verification que l'utilisateur est logué
    	elseif (!Mage::getSingleton('customer/session')->isLoggedIn()) {
    		$this->_redirect('customer/account');
        }
        //verification que l'envoie de mail est autorisé sans commande
        //et qu'une commande a été passée
        elseif (!Mage::helper('auguria_sponsorship/config')->isInvitAllowedWithoutOrder()
        		&& !Mage::helper('auguria_sponsorship')->haveOrder()) {
        	$this->_redirect('customer/account');
        }
    }
    
    public function indexAction()
    {
    	$inviter = Mage::getModel('auguria_sponsorship/openinviter');
    	$session = Mage::getSingleton('customer/session');
    	$old_form = Mage::getSingleton('customer/session')->getData('openinviter_form');
    	
    	if (!isset($old_form))
    		$old_form = array();
    		
        $new_form = array (
    				'plugins'=>$inviter->getOpenIniviterPlugins(),
    				'types'=>$inviter->getOpenInviterTypes(),
        			'step'=>'get_contacts'
    			);
    	$form = array_merge($old_form, $new_form);
    	
    	if ($this->getRequest()->getParam('provider'))
    	{
    		$form = array_merge($form, array('provider_box'=>$this->getRequest()->getParam('provider')));
    	}       			
    	
    	$session->setData('openinviter_form', $form);
    	
        $this->loadLayout();
        $this->getLayout()->getBlock('auguria_sponsorship/openinviter');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
       
        //Ajout d'un message d'erreur si l'envoie de mail n'est pas autorisé sans commande et que le client n'a pas commandé
    	if (!!Mage::helper('auguria_sponsorship/config')->isInvitAllowedWithoutOrder()
    		&& !Mage::helper('auguria_sponsorship')->haveOrder()) {
			Mage::getSingleton('customer/session')->addError(Mage::helper('auguria_sponsorship')->__('You must already have purchased to sponsor.'));
        }
    }
    
    public function importAction()
    {
    	try
    	{
			$post = $this->getRequest()->getPost();
			$session = Mage::getSingleton('customer/session');
			$session->setData('openinviter_form', $post);
	        if ( $post )
	        {
	        	if (empty($post['email_box']))
					Mage::getSingleton('customer/session')->addError(Mage::helper('auguria_sponsorship')->__("Email missing !"));
				if (empty($post['password_box']))
					Mage::getSingleton('customer/session')->addError(Mage::helper('auguria_sponsorship')->__("Password missing !"));
				if (empty($post['provider_box']))
					Mage::getSingleton('customer/session')->addError(Mage::helper('auguria_sponsorship')->__("Provider missing !"));
				
				$messages = Mage::getSingleton('customer/session')->getMessages();
				$errors = $messages->getErrors();
				$form['step']= 'get_contacts';
				if (!count($errors))
				{
					$inviter = Mage::getModel('auguria_sponsorship/openinviter');
					$inviter->getOpenIniviterPlugins();
					include_once (Mage::getModuleDir('', 'Auguria_Sponsorship').'/Lib/OpenInviter/plugins/'.$post['provider_box'].'.plg.php');				
					$result = $inviter->startPlugin($post['provider_box']);				
					$contacts;
					
					$internal = $inviter->getInternalError();
					if ($internal)
						Mage::getSingleton('customer/session')->addError(Mage::helper('auguria_sponsorship')->__($internal));
					
					elseif (!$inviter->login($post['email_box'],$post['password_box']))
					{
						$internal=$inviter->getInternalError();
						$message = ($internal ? $internal : "Login failed. Please check the email and password you have provided and try again later !");
						Mage::getSingleton('customer/session')->addError(Mage::helper('auguria_sponsorship')->__($message));				
					}
					elseif (false===$contacts=$inviter->getMyContacts())
					{
						Mage::getSingleton('customer/session')->addError(Mage::helper('auguria_sponsorship')->__("Unable to get contacts !"));
					}
					else
					{
						$new_form = array (
						    				'step'=>'send_invites',
						    				'oi_session_id'=>$inviter->getSessionID(),
											'contacts'=>$contacts
						    			);
	    				$form = array_merge($post, $new_form);
	    				$session->setData('openinviter_form', $form);
					}
				}  
				if ($form['step']=='send_invites')
				{       
	            	$this->loadLayout();
			        $this->getLayout()->getBlock('auguria_sponsorship/openinviter');
			        $this->_initLayoutMessages('customer/session');
			        $this->_initLayoutMessages('catalog/session');
			        $this->renderLayout();	
				}
	            else        
	            	$this->_redirect("*/*/");
	        }
	        else
	        {
	            $this->_redirect("*/*/");
	        }
    	}
    	catch (Exception $e)
    	{
    		Mage::getSingleton('customer/session')->addError(Mage::helper('auguria_sponsorship')->__("An exception occured !"));
    		$this->_redirect("*/*/");
    	}
    }
    
}
