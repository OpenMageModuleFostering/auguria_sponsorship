<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_IndexController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
		//verification que le module de parrainage est activé
		$config = Mage::helper('auguria_sponsorship/config');
        if( !$config->isAccumulatedEnabled() && !$config->isSponsorshipEnabled()) {
            $this->_redirect('');
        }
        //verification que l'utilisateur est logué
    	elseif (!Mage::getSingleton('customer/session')->isLoggedIn()) {
    		$this->_redirectUrl(Mage::helper('customer')->getAccountUrl());
        }
        //verification que l'envoie de mail est autorisé sans commande et qu'une commande a été passée
        elseif (!Mage::getStoreConfig('auguria_sponsorship/sponsor/sponsor_optional_order') &&
        		!Mage::helper('auguria_sponsorship')->haveOrder()) {        	
        	$this->_redirecturl(Mage::helper('customer')->getAccountUrl());
        }
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('auguria_sponsorship');
        // ->setFormAction( Mage::getUrl('sponsorship/index/post') );
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();

        //Ajout d'un message d'erreur si l'envoie de mail n'est pas autorisé sans commande et que le client n'a pas commandé
    	if (!Mage::getStoreConfig('auguria_sponsorship/sponsor/sponsor_optional_order')) {
        	if (!Mage::helper('auguria_sponsorship/data')->haveOrder()) {
        		Mage::getSingleton('customer/session')->addError(Mage::helper('auguria_sponsorship')->__('You must already have purchased to sponsor.'));
        	}
        }
    }

    public function sendAction()
    {
		$post = $this->getRequest()->getPost();
		$session = Mage::getSingleton('customer/session');
		$session->setData('sponsorship_form', $post);
        if ( $post )
        {
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);
            $mail = Mage::helper('auguria_sponsorship/mail');
            $mails = $mail->processMail($post);
            $validation = $mail->validateMail($mails);
            $checksend = false;
            $checksave = false;
            if ($validation == true && isset($post['recipient']))
            {
                foreach ($mails as $email)
                {
                	if ($email["recipient_email"] != Mage::getSingleton('customer/session')->getCustomer()->getEmail()) {
	                	$customerId = Mage::getSingleton('customer/session')->getCustomerId();
	                    $isCustomer = $mail->recipientMailIsCustomer($email["recipient_email"]);
	                    if ($isCustomer == true)
	                    {
	                        Mage::getSingleton('customer/session')->addError($this->__("%s is already an email to our customers",$email["recipient_email"]));
	                    }
	                    else
	                    {
	                        if ($mail->sendMail($email))
	                        {
	                            if ($checksend == false)
	                            {
	                                Mage::getSingleton('customer/session')->addSuccess(Mage::helper('auguria_sponsorship')->__("Your email has been successfully sent."));
	                                //remove recipient from session
	                                $form = Mage::getSingleton('customer/session')->getData('sponsorship_form');
	                                $form['recipient']=array();
	                                $session->setData('sponsorship_form', $form);
	                                $checksend = true;
	                            }
	                            if (!$mail->saveMail($email))
	                            {
	                                if ($checksave == false)
	                                {
	                                   Mage::getSingleton('customer/session')->addError($this->__("But it could not be saved."));
	                                    $checksave = true;
	                                }
	                            }
	                        }
	                        else
	                        {
	                            Mage::getSingleton('customer/session')->addError($this->__("An error occurred, the mail to %s could not be delivred.",$email["recipient_email"]));
	                        }
	                    }
                	}
                	else {
                		Mage::getSingleton('customer/session')->addError($this->__("You can't send an invitation to yourself"));
                	}
                }
            }
            else
            {
                Mage::getSingleton('customer/session')->addError($this->__("Please check the form fields."));
                $this->_redirect("*/*/");
            }
            $translate->setTranslateInline(true);
            $this->_redirect("*/*/");
        }
        else
        {
            $this->_redirect("*/*/");
        }
    }

}
