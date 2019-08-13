<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_BoostController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
    		$this->_redirectUrl(Mage::helper('customer')->getAccountUrl());
        }
    }
    
    public function indexAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('boost');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }

    public function postAction()
    {
        $post = $this->getRequest()->getPost();
        if ( $post )
        {
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);
            $mail = Mage::helper('auguria_sponsorship/mail');
            $mails = $mail->processMail($post);
            $validation = $mail->validateMail($mails);


            if ($validation == true)
            {
                $isCustomer = $mail->recipientMailIsCustomer($mails[0]["recipient_email"]);
                if ($isCustomer == true)
                {
                    Mage::getSingleton('customer/session')->addError($this->__("%s is already an email to our customers",$mails[0]["recipient_email"]));
                    $this->_redirect("*/points/");
                }
                else
                {
                    if ($mail->sendMail($mails[0]))
                    {
                        Mage::getSingleton('customer/session')->addSuccess(Mage::helper('auguria_sponsorship')->__("Your email has been successfully sent."));
                        if (!$mail->saveMail($mails[0]))
                        {
                            Mage::getSingleton('customer/session')->addError($this->__("But it could not be saved."));
                        }
                        $this->_redirect("*/points/");
                    }
                    else
                    {
                        //Mage::getSingleton('customer/session')->addError($this->__("Une erreur est survenue, le mail destiné à %s n'a pas pu être délivré.",$recipient));
                        Mage::getSingleton('customer/session')->addError($this->__("Your mail has not been sent, please try again later."));
                        $this->_redirect("*/*/",Array("sponsorship_id"=>$post["sponsorship_id"]));
                    }
                }
            }
            else
            {
                Mage::getSingleton('customer/session')->addError($this->__("Please check the form fields."));
                $this->_redirect("*/*/",Array("sponsorship_id"=>$post["sponsorship_id"]));
            }
            $translate->setTranslateInline(true);
        }
        else
        {
                $this->_redirect("*/points/");
        }
    }
}
