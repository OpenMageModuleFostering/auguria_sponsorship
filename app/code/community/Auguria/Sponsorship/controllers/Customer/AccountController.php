<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
require_once 'Mage/Customer/controllers/AccountController.php';
class Auguria_Sponsorship_Customer_AccountController extends Mage_Customer_AccountController
{
	
	
	public function createPostAction()
    {
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        if ($this->getRequest()->isPost()) {
            $errors = array();

            if (!$customer = Mage::registry('current_customer')) {
                $customer = Mage::getModel('customer/customer')->setId(null);
            }

            foreach (Mage::getConfig()->getFieldset('customer_account') as $code=>$node) {
                if ($node->is('create') && ($value = $this->getRequest()->getParam($code)) !== null) {
                    $customer->setData($code, $value);
                }
            }

            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $customer->setIsSubscribed(1);
            }

            /**
             * Initialize customer group id
             */
            $customer->getGroupId();

            if ($this->getRequest()->getPost('create_address')) {
                $address = Mage::getModel('customer/address')
                    ->setData($this->getRequest()->getPost())
                    ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                    ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false))
                    ->setId(null);
                $customer->addAddress($address);

                $errors = $address->validate();
                if (!is_array($errors)) {
                    $errors = array();
                }
            }
            //Début modification createPostAction()
			if ($sponsorId = mage::helper("sponsorship/data")->searchSponsorId($customer->getEmail())) {
				$customer->setData('sponsor',$sponsorId);
				$cookie = new Mage_Core_Model_Cookie;
				if ($cookie->get('sponsorship_id')) {
					$cookie->delete('sponsorship_id');
					$cookie->delete('sponsorship_email');
					$cookie->delete('sponsorship_firstname');
					$cookie->delete('sponsorship_lastname');
				}
			}
			     
            //Fin modification createPostAction()
            
            try {
                $validationCustomer = $customer->validate();
                if (is_array($validationCustomer)) {
                    $errors = array_merge($validationCustomer, $errors);
                }
                $validationResult = count($errors) == 0;

                if (true === $validationResult) {
                    $customer->save();

                    if ($customer->isConfirmationRequired()) {
                        $customer->sendNewAccountEmail('confirmation', $this->_getSession()->getBeforeAuthUrl());
                        $this->_getSession()->addSuccess($this->__('Account confirmation is required. Please, check your e-mail for confirmation link. To resend confirmation email please <a href="%s">click here</a>.',
                            Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())
                        ));
                        $this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
                        return;
                    }
                    else {
                        $this->_getSession()->setCustomerAsLoggedIn($customer);
                        $url = $this->_welcomeCustomer($customer);
                        $this->_redirectSuccess($url);
                        return;
                    }
                } else {
                    $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
                    if (is_array($errors)) {
                        foreach ($errors as $errorMessage) {
                            $this->_getSession()->addError($errorMessage);
                        }
                    }
                    else {
                        $this->_getSession()->addError($this->__('Invalid customer data'));
                    }
                }
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                    ->setCustomerFormData($this->getRequest()->getPost());
            }
            catch (Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Can\'t save customer'));
            }
        }

        $this->_redirectError(Mage::getUrl('*/*/create', array('_secure'=>true)));
    }
    
	public function editPostAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/edit');
        }

        if ($this->getRequest()->isPost()) {
            $customer = Mage::getModel('customer/customer')
                ->setId($this->_getSession()->getCustomerId())
                ->setWebsiteId($this->_getSession()->getCustomer()->getWebsiteId());

            $fields = Mage::getConfig()->getFieldset('customer_account');
            foreach ($fields as $code=>$node) {
                if ($node->is('update') && ($value = $this->getRequest()->getParam($code)) !== null) {
                    $customer->setData($code, $value);
                }
            }
            
            /*
             * Ajout des champs iban et siret
             */

            if ($this->getRequest()->getParam('iban')) {
            	$customerIban = $this->getRequest()->getPost('iban');
            	$customerIban = str_replace(CHR(32),"",$customerIban);
            	$customerIban = str_replace("-","",$customerIban);
            	$customer->setData('iban', $customerIban);
            }
            
            if ($this->getRequest()->getParam('siret')) {
            	$customerSiret = $this->getRequest()->getPost('siret');
            	$customerSiret = str_replace(CHR(32),"",$customerSiret);
            	$customerSiret = str_replace("-","",$customerSiret);
            	$customer->setData('siret', $customerSiret);
            }
            
            $errors = $customer->validate();
            if (!is_array($errors)) {
                $errors = array();
            }

            /**
             * we would like to preserver the existing group id
             */
            if ($this->_getSession()->getCustomerGroupId()) {
                $customer->setGroupId($this->_getSession()->getCustomerGroupId());
            }
            if ($this->getRequest()->getParam('change_password')) {
                $currPass = $this->getRequest()->getPost('current_password');
                $newPass  = $this->getRequest()->getPost('password');
                $confPass  = $this->getRequest()->getPost('confirmation');

                if (empty($currPass) || empty($newPass) || empty($confPass)) {
                    $errors[] = $this->__('Password fields can\'t be empty.');
                }

                if ($newPass != $confPass) {
                    $errors[] = $this->__('Please make sure your passwords match.');
                }

                $oldPass = $this->_getSession()->getCustomer()->getPasswordHash();
                if (strpos($oldPass, ':')) {
                    list($_salt, $salt) = explode(':', $oldPass);
                } else {
                    $salt = false;
                }

                if ($customer->hashPassword($currPass, $salt) == $oldPass) {
                    $customer->setPassword($newPass);
                } else {
                    $errors[] = $this->__('Invalid current password');
                }
            }

            if (!empty($errors)) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
                foreach ($errors as $message) {
                    $this->_getSession()->addError($message);
                }
                $this->_redirect('*/*/edit');
                return $this;
            }


            try {
                $customer->save();
                $this->_getSession()->setCustomer($customer)
                    ->addSuccess($this->__('Account information was successfully saved'));

                $this->_redirect('customer/account');
                return;
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Can\'t save customer'));
            }
        }

        $this->_redirect('*/*/edit');
    }
}
?>