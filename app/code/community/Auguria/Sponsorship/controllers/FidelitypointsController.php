<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_FidelitypointsController extends Mage_Core_Controller_Front_Action
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
    	$this->getLayout()->getBlock('customer');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }

    public function changeAction()
    {
    	$module = $this->getRequest()->getParam('module');
    	$type = $this->getRequest()->getParam('type');
    	$resultValidate = $this->validateChange($module, $type);
    	if ($resultValidate == 'valide' )
    	{
    		$this->loadLayout();
    		$this->getLayout()
	        		->getBlock('customer');
	        $this->_initLayoutMessages('customer/session');
	        $this->_initLayoutMessages('catalog/session');
	        $this->renderLayout();
    	}
    	elseif ($resultValidate == 'inactif' )
    	{
    		$this->_redirect('*/*/');
    	}
    	elseif ($resultValidate == 'account' )
    	{
    		$this->_redirectUrl(Mage::helper('customer')->getAccountUrl().'edit');
    	}
    }
    
    public function saveAction()
    {
    	if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*');
    	}
    	else
    	{
    		$module = $this->getRequest()->getPost('module');
    		$type = $this->getRequest()->getPost('type');
    		$points = $this->getRequest()->getPost('points');
	    	$resultValidate = $this->validateChange($module, $type, $points);
    		$value = $this->getValue($points, $type, $module);
    		
	    	if ($resultValidate == 'valide' )
	    	{
	    		try
	    		{
	    			$customerId = Mage::getSingleton('customer/session')->getId();
	    			$customer = Mage::getModel("customer/customer")->load($customerId);
	    			
	    			//récupération des points clients
	    			$getdatapoints = 'get'.ucfirst($module).'Points';
        			$cPoints = $customer->$getdatapoints();
        			//décrémentation des points du client
	    			$setdatapoints = 'set'.ucfirst($module).'Points';
	    			$newPoints = $cPoints - $points;
	    			$customer->$setdatapoints($newPoints);
	    			$customer->save();
        			
	    			$dateTime = Mage::getModel('core/date')->date();
	    			$statut = "waiting";

	    			//inscription de l'opération dans la table des echanges
	    			$echange = Mage::getModel('sponsorship/change');
	    			$row = array(
					    'customer_id' => $customerId,
					    'type' => $type,
					    'module' => $module,
                                            'statut' => $statut,
					    'datetime' => $dateTime,
					    'points' => $points,
                                            'value' => $value
		    			);
		    		$echange->setData($row);
					$echange->save();
					$echangeId = $echange->getId();
	    			
    				//inscription dans les logs  
    				$log = Mage::getModel('sponsorship/'.$module.'log');			
    				if ($module == 'fidelity')
    				{
	    				$data = array(
					    'customer_id' => $customerId,
					    'record_id' => $echangeId,
					    'record_type' => $type,
					    'datetime' => $dateTime,
					    'points' => -$points
		    			);
    				}
    				elseif ($module == 'sponsor')
    				{
		    			$data = array(
					    'godson_id' => $customerId,
		    			'sponsor_id' => $customerId,
					    'record_id' => $echangeId,
					    'record_type' => $type,
					    'datetime' => $dateTime,
					    'points' => -$points
		    			);
    				}
					$log->setData($data);
					$log->save();
					
	    			Mage::getSingleton('customer/session')->addSuccess(Mage::helper('sponsorship')->__('Your request has been submitted, you will soon receive an email confirmation.'));
    				$this->_redirect('*/*/');
	    		}
    			
	    		catch (Exception $e) {
	    			Mage::log($e->getMessage());
	    			Mage::getSingleton('customer/session')->addError(Mage::helper('sponsorship')->__("An error occurred while saving your request."));
	    			$this->_redirect('*/*/');
	    		}
	    	}
	    	elseif ($resultValidate == 'inactif' )
	    	{
	    		$this->_redirect('*/*/');
	    	}
	    	elseif ($resultValidate == 'account' )
	    	{
	    		$this->_redirectUrl(Mage::helper('customer')->getAccountUrl().'edit');
	    	}
    		elseif ($resultValidate == 'points' )
	    	{
	    		$this->_redirect('*/*/change', Array('module'=>$module, 'type'=>$type));
	    	}
                elseif ($resultValidate == 'mail' )
	    	{
                    Mage::getSingleton('customer/session')->addError(Mage::helper('sponsorship')->__("An error occurred while sending mail."));
                    $this->_redirect('*/*/change', Array('module'=>$module, 'type'=>$type));
	    	}
    	}
    }
    
    protected function validateChange($module, $type, $points='')
    {
	$options = Mage::getBlockSingleton('sponsorship/customer_account_pointsDetail');		
    	$session = Mage::getSingleton('customer/session');
    	
    	$Module = ucfirst($module);
    	$Type = ucfirst($type);
    	
    	$changeEnabled = 'get'.$Module.$Type.'Config';
    	$moduleEnabled = 'get'.$Module.'EnabledConfig';
    	
    	if (($options->$moduleEnabled()==1 && $options->$changeEnabled()==1))
    	{
    		$validate = 'validate'.$Type;
    		return $this->$validate($module,$points);    		
    	}
    	else
    	{
    		$session->addError(Mage::helper('sponsorship')->__('The exchange of %s points in %s is disabled.',$this->__($module),$this->__($type)));
    		return 'inactif';
    	}
    }
    protected function validateCoupon ($module, $points='')
    {
        $validate = 'valide';
    	$customerId = Mage::getSingleton('customer/session')->getId();
        $customer = Mage::getModel("customer/customer")->load($customerId);

        //vérification que les points à changer sonts inférieurs aux points du client
        if ($points != '')
        {
        	$data = 'get'.ucfirst($module).'Points';
        	$cPoints = $customer->$data();
        	if ($points>$cPoints)
        	{
        		Mage::getSingleton('customer/session')->addError(Mage::helper('sponsorship')->__("You do not have as many points."));
    			$validate = 'points';
        	}
                else
                {
                    //création de la règle panier---------------------------------------------------
                    $options = Mage::getBlockSingleton('sponsorship/customer_account_pointsDetail');
                    $srModel = Mage::getModel('salesrule/rule');
                    $date = now();
                    $chars = array("-", ":", " ");
                    $simpledate = str_replace($chars, "", $date);
                    $couponCode = $customerId."-".$points.$simpledate;//idClient-date-module-somme
                    $name = $this->__("%s points exchange for customer (%s)", $this->__($module), $customerId);
                    $description = $name;
                    $customerGroupId = $customer->getGroupId();
                    $discountAmount = $this->getValue ($points, "coupon", $module);
                    $websiteId = $customer->getWebsiteId();
                    $data = array
                    (
                        "name" => $name,
                        "description" => $description,
                        "from_date" => $date,
                        "coupon_code" => $couponCode,
                        "uses_per_coupon" => 1,
                        //"uses_per_customer" => 1,
                        "customer_group_ids" => Array(0 => $customerGroupId),//get customer group
                        "is_active" => 1,
                        "conditions_serialized" => 'a:7:{s:4:"type";s:32:"salesrule/rule_condition_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:5:{s:4:"type";s:32:"salesrule/rule_condition_address";s:9:"attribute";s:9:"total_qty";s:8:"operator";s:1:">";s:5:"value";s:1:"0";s:18:"is_value_processed";b:0;}}}',
                        "actions_serialized" => 'a:6:{s:4:"type";s:40:"salesrule/rule_condition_product_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";}',
                        "stop_rules_processing" => 0,
                        "is_advanced" => 1,
                        "sort_order" => 0,
                        "simple_action" => "by_fixed",
                        "discount_amount" => $discountAmount,
                        "discount_qty" => 1.0000,
                        "discount_step" => 0,
                        "simple_free_shipping" => 0,
                        "times_used" => 0,
                        "is_rss" => 1,
                        "website_ids" => Array(0 => $websiteId)//get website id
                    );
                    $srModel->setData($data);
                    $srModel->save();
                    //envoi du mail--------------------------------------------------------------
                    //construction du message
                    $mailTemplate = Mage::getModel('sponsorship/Core_Email_Template');

                    $sender_name = Mage::getStoreConfig('trans_email/ident_sales/name');
                    $sender_email = Mage::getStoreConfig('trans_email/ident_sales/email');

                    $subject = $this->__('%s vouchers points exchange', Mage::helper('sponsorship/mail')->getStoreName());

                    $sender  = array("name"=>$sender_name, "email"=>$sender_email);
                    $recipient_email = $customer->getData('email');

                    $postObject = new Varien_Object();
                    $postObject->setData(Array ("sender_name" => $sender_name,
                                                "sender_email" => $sender_email,
                                                "recipient_firstname" => $customer->getFirstname(),
                                                "recipient_lastname" => $customer->getLastname(),
                                                "discount_amount" => $discountAmount,
                                                "subject" => $subject,
                                                "coupon_code" => $couponCode,
                                                "store_name" => Mage::helper('sponsorship/mail')->getStoreName()
                                            ));

                    $mailTemplate->setDesignConfig(array('area' => 'fronted'))
                                            ->setReplyTo($sender_email)
                                            //->setReturnPath($sender_email)
                                            ->sendTransactional(
                                                    "sponsorship_email_coupon",
                                                    $sender,
                                                    $recipient_email,
                                                    $customer->getFirstname().' '.$customer->getLastname(),
                                                    array('data' => $postObject)
                                            );
                    if (!$mailTemplate->getSentSuccess())
                    {
                        $validate = "mail";
                    }

                }
        }
        return $validate;
    }
    
    protected function validateGift ($module, $points='')
    {
    	return 'valide';
    }

    protected function validateCash ($module, $points=0)
    {
    	$validate = 'valide';
    	$customerId = Mage::getSingleton('customer/session')->getId();
    	$maxCash = "";
    	$options = Mage::getBlockSingleton('sponsorship/customer_account_pointsDetail');
    	
    	//Validation qu'un Iban est renseigné
    	$customer = Mage::getModel("customer/customer")->load($customerId);
    	   	
    	if (!$customer->getIban()) {
    		Mage::getSingleton('customer/session')->addError(Mage::helper('sponsorship')->__('To change your points into cash, you must indicate your IBAN.'));
    		$validate = 'account';
    	}
    	
    	//Validation qu'un Siret est renseigné si le max de cash pour un particulier est atteint
    	if ($module=='sponsor') {
    		$maxCash = $options->getSponsorMaxCashConfig ();
    		$timeMaxCash = $options->getSponsorTimeMaxCashConfig ();
    	}
    	elseif ($module=='fidelity') {
    		$maxCash = $options->getFidelityMaxCashConfig ();
    		$timeMaxCash = $options->getFidelityTimeMaxCashConfig ();
    	}
    	$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		$select = $read->select()
			->from($resource->getTableName('sponsorship/change'), 'SUM(points)')
            ->where('customer_id=?', $customerId)
            ->where('module=?', $module)
            ->where('type=?', 'cash')
            ->where('statut!=?', 'canceled')
            ->where('TO_DAYS(NOW()) - TO_DAYS(datetime) <=?', $timeMaxCash);
        
        $cashPoints = $read->fetchOne($select);
                
        //Addition des points déjà changés avec les points demandés
        $cashPoints = $points+$cashPoints;
        
        //vérification que le total ne dépasse pas le maximum autorisé
        if ($cashPoints >= $maxCash) {        	
        	if (!$customer->getSiret())
        	{
        		Mage::getSingleton('customer/session')->addError(Mage::helper('sponsorship')->__('To change more cash, you must specify a company number.'));
    			$validate = 'account';
        	}
        }
        
        //vérification que les points à changer sonts inférieurs aux points du client
        if ($points != '')
        {
        	$data = 'get'.ucfirst($module).'Points';
        	$cPoints = $customer->$data();
        	if ($points>$cPoints)
        	{
        		Mage::getSingleton('customer/session')->addError(Mage::helper('sponsorship')->__("You do not have as many points."));
    			$validate = 'points';
        	}
        }
    	return $validate;
    }
    
    protected function getValue ($points, $type, $module)
    {
    	//if ($type == 'cash')
    	//recuperation du taux de conversion
    	$options = Mage::getBlockSingleton('sponsorship/customer_account_pointsDetail');
    	$getPointsToCash = 'get'.ucfirst($module).'PointsToCashConfig';
    	$PointsToCash = $options->$getPointsToCash();
    	$value = round($points*$PointsToCash,2);    		
    	return $value;
    }
}