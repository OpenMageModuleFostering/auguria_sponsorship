<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Auguria_Sponsorship_Model_Observer
{

    /**
     * @param Varien_Event_Observer $observer
     */
    public function setCouponCode(Varien_Event_Observer $observer){
        if ($quote = $observer['quote']) {
            if($coupon_code = $quote->getData('coupon_code')){
                $change = Mage::getModel('auguria_sponsorship/change')->getCollection()->addAttributeToFilter('coupon_code', $coupon_code);
                if($coupon = $change->getFirstItem()){
                    $coupon->setData('statut', 'solved');
                    $coupon->save();
                }
            }
        }
    }

    /**
     * Calcul total order points on sales_order_payment_pay event
     * @param Varien_Event_Observer $observer
     */
    public function calcPoints($observer)
    {
        /**
         * @TODO : retrancher du total les points offerts
         */
        try {
            //modules actifs
            $moduleFidelity = Mage::helper('auguria_sponsorship/config')->isFidelityEnabled();
            $moduleSponsor = Mage::helper('auguria_sponsorship/config')->isSponsorshipEnabled();
            $moduleAccumulated = Mage::helper('auguria_sponsorship/config')->isAccumulatedEnabled();

            if ($moduleFidelity==1||$moduleSponsor==1||$moduleAccumulated==1)
            {
                //récupération de la commande et des articles
                $order = $observer->getInvoice()->getOrder();
                $orderDate = $order->getUpdatedAt();
                $orderId = $order->getEntityId();

                //définition du client
                $cId = $order->getCustomerId();

                //definition du websiteid
                $wId = $order->getStore()->getWebsiteId();

                //definition du groupe du client
                $customer = Mage::getModel('customer/customer')->load($cId);
                $gId = $customer->getGroupId();

                //definition du sponsor de premier niveau
                $sponsorId = (int)$customer->getSponsor();
                $sponsor = Mage::getModel('customer/customer')->load($sponsorId);
                $special_rate = (int)$sponsor->getData('special_rate');

                //variable de points
                $tCatalogFidelityPoints=0;
                $tCatalogSponsorPoints=0;
                $tCartFidelityPoints=0;
                $tCartSponsorPoints=0;

                //calcul des points catalogue et mise à jour de la commande pour chaque ligne
                foreach ($order->getAllItems() as $item)
                {

                    /* Bug Correction for dynamic bundle - october 2013 */
                    $isDynamicBundle = false;
                    if($parentItemId = $item->getParentItemId())
                    {
                        $parentItem = $item->getParentItem();
                        $parentProduct = $parentItem->getProduct();
                        $isBundle = ($parentProduct->getData('type_id') == "bundle") ? true : false;
                        $isDynamicPrice = (!$parentProduct->getData('price_type')) ? true : false;
                        // $parentProduct->getData('price_type')  -> 0 for dynamic and 1 for static
                        $isDynamicBundle = ($isBundle && $isDynamicPrice) ? true : false;
                    }
                    /* ! Bug Correction */

                    //Add points only if product have no parent
                    if (!$parentItemId || $isDynamicBundle)
                    {

                        $date = $item->getData('updated_at');
                        $pId = $item->getData('product_id');
                        $qte = $item->getData('qty_ordered');
                        $data = $item->getData();

                        if ($moduleFidelity==1 || $moduleAccumulated==1)
                        {
                            //récupération et affectation des points catalog pour chaque article commandé
                            $catalogFidelityPoints = (float)$this->getRulePoints($date, $wId, $gId, $pId,'fidelity');
                            //multiplication des points par la quantité
                            $catalogFidelityPoints = $catalogFidelityPoints*$qte;
                            //ajout des points aux items de commande
                            $data['catalog_fidelity_points'] = $catalogFidelityPoints;

                            //calcul du total de points catalogue
                            $tCatalogFidelityPoints = $tCatalogFidelityPoints+$catalogFidelityPoints;

                            //calcul du total de points panier
                            $tCartFidelityPoints = $tCartFidelityPoints+(float)$item->getCartFidelityPoints();

                        }

                        if (($moduleAccumulated==1 || $moduleSponsor==1) && $special_rate==0)
                        {
                            //récupération et affectation des points catalog pour chaque article commandé
                            $catalogSponsorPoints = $this->getRulePoints($date, $wId, $gId, $pId, 'sponsor');

                            //multiplication des points par la quantité
                            $catalogSponsorPoints = $catalogSponsorPoints*$qte;

                            //ajout des points aux items de commande
                            $data['catalog_sponsor_points'] = $catalogSponsorPoints;
                            //calcul du total de points catalogue
                            $tCatalogSponsorPoints = $tCatalogSponsorPoints+$catalogSponsorPoints;

                            //calcul du total de points panier
                            $tCartSponsorPoints = $tCartSponsorPoints+$item->getCartSponsorPoints();
                        }
                        //si un taux spécial est défini pour le parrain direct
                        elseif (($moduleAccumulated==1 || $moduleSponsor==1) && $special_rate!=0)
                        {
                            //Redéfinition du taux à appliquer dans la commande
                            //ajout des points aux items de commande
                            $data['catalog_sponsor_points'] = 0;
                            $specialratepoints = $item->getData('price')*$qte*$special_rate/100;
                            $data['cart_sponsor_points'] = $specialratepoints;
                        }

                        $item->setData($data);
                        $item->save();
                    }
                }
                //exit();
                $order->save();

                //Ajout du total des points fidelite
                if ($tCatalogFidelityPoints != 0 || $tCartFidelityPoints != 0 ) {
                    $this->_addFidelityPoints($customer, $tCatalogFidelityPoints+$tCartFidelityPoints, 'order', $orderId, $orderDate);
                    $customer->save();
                }

                //Ajout du total des points de parrainage si le parrain n'a pas de taux spécial
                if (($tCatalogSponsorPoints != 0 || $tCartSponsorPoints != 0) && $special_rate==0) {
                    $this->_addSponsorPoints($sponsor, $customer, $tCatalogSponsorPoints+$tCartSponsorPoints, 'order', $orderId, $orderDate);
                }
                //Ajout des points à partir du taux spécial au parrain direct uniquement
                elseif ($special_rate!=null && ($moduleSponsor==1 || $moduleAccumulated==1)) {
                    $this->_addSponsorSpecialPoints($sponsor, $customer, $specialratepoints, 'order', $orderId, $orderDate);
                }
            }
            return $this;
        }
        catch (Exception $e) {
            Mage::log(Mage::helper('auguria_sponsorship')->__("An error occured while saving points : %s",$e->getMessage()));
        }
    }

    /**
     * Get catalog fidelity or sponsor points depends on $type
     * @param date $date
     * @param integer $wId
     * @param integer $gId
     * @param integer $pId
     * @param String $type
     */
    public function getRulePoints($date, $wId, $gId, $pId, $type)
    {
        $resource = Mage::getSingleton('core/resource');
        $read= $resource->getConnection('core_read');
        $userTable = $resource->getTableName('auguria_sponsorship/catalog'.$type.'point');
        $select = $read->select()
        ->from($resource->getTableName('auguria_sponsorship/catalog'.$type.'point'), 'rule_point')
        ->where('rule_date=?', $this->formatDate($date))
        ->where('website_id=?', $wId)
        ->where('customer_group_id=?', $gId)
        ->where('product_id=?', $pId);
        return $read->fetchOne($select);
    }

    public function formatDate ($date)
    {
        $date = strtotime($date);
        return date('Y-m-d', $date);
    }

    /**
     * Add fidelity points to customer
     * @param Mage_Customer_Model_Customer $customer
     * @param float $tFPoints
     * @param String $recordType
     * @param Int $recordId
     * @param Datetime $datetime
     */
    protected function _addFidelityPoints($customer, $fidelityPoints, $recordType='order', $recordId=0, $datetime=null)
    {
        $customer = Mage::helper('auguria_sponsorship')->addFidelityPoints($customer, $fidelityPoints);
        //enregistrement dans les logs
        $data = array(
                'customer_id' => $customer->getId(),
                'record_id' => $recordId,
                'record_type' => $recordType,
                'datetime' => $datetime,
                'points' => $fidelityPoints
        );
        $this->_addAuguriaSponsorshipLog($data);
    }

    /**
     * Add sponsor points to sponsors
     * @param Mage_Customer_Model_Customer $sponsor
     * @param Mage_Customer_Model_Customer $godson
     * @param float $SPoints
     * @param String $recordType
     * @param Int $recordId
     * @param Datetime $datetime
     */
    protected function _addSponsorPoints($sponsor, $godson, $SPoints, $recordType='order', $recordId=0, $datetime=null)
    {
        $ratio = Mage::helper('auguria_sponsorship/config')->getSponsorPercent();
        $maxLevel = Mage::helper('auguria_sponsorship/config')->getSponsorLevels();
        $sponsorId = -1;
        $godsonId = $godson->getId();

        //Ajout des points tant que le niveau maximum n'est pas atteint et qu'un parrain est défini
        for ($level = 0; $level<$maxLevel AND $sponsorId!=0 AND round($SPoints,4)>0; $level++)
        {
            //définition du parrain
            if ($sponsorId>0) {
                $sponsor = Mage::getModel('customer/customer')->load($sponsorId);
            }
            else {
                $sponsorId = $sponsor->getId();
            }
            $special_rate = (int)$sponsor->getData('special_rate');

            //si parrain a un taux special : on met fin à la boucle
            if ($special_rate != 0) {
                $SPoints = 0;//mise à 0 des points de parrainage pour arrêter la boucle
                $sponsorId = 0;
            }
            else {
                $sponsor = Mage::helper('auguria_sponsorship')->addSponsorshipPoints($sponsor, $SPoints);
                $sponsor->save();

                //Save operation in logs table
                $data = array(
                        'godson_id' => $godsonId,
                        'sponsor_id' => $sponsorId,
                        'record_id' => $recordId,
                        'record_type' => $recordType,
                        'datetime' => $datetime,
                        'points' => $SPoints
                );
                $this->_addAuguriaSponsorshipLog($data);

                //Send notification
                $this->_sendSponsorNotification($sponsor, $godsonId, $SPoints, Mage::helper('auguria_sponsorship')->getSponsorshipPoints($sponsor));

                //incrémentation des points à ajouter
                $SPoints = ($SPoints*$ratio)/100;
                //le parrain devient le filleul
                $godsonId = $sponsorId;
                //définition du parrain du parrain
                $sponsorId = (int)$sponsor->getSponsor();
            }
        }
    }

    /**
     * Link customer with his sponsor on customer_save_before
     * @param Array $observer
     */
    public function setSponsorOnRegister($observer)
    {
        try
        {
            $customer = $observer->getCustomer();
            //if no get id : it's a creation
            if (!$customer->getId())
            {
                $this->_setSponsor($customer);
            }
        }
        catch (Exception $e)
        {
            Mage::log(Mage::helper('auguria_sponsorship')->__("An error occured while linking sponsor : %s",$e->getMessage()));
        }
    }

    /**
     * Link customer with his sponsor and update sponsor's sponsorship points validity
     * @param Mage_Customer_Model_Customer $customer
     */
    protected function _setSponsor($customer)
    {
        $sponsorId = (int)Mage::helper("auguria_sponsorship")->searchSponsorId($customer->getEmail());
        if ($sponsorId != 0)
        {
            //link
            $customer->setData('sponsor',$sponsorId);
            $cookie = Mage::getModel('core/cookie');
            //remove cookie
            if ($cookie->get('sponsorship_id'))
            {
                $cookie->delete('sponsorship_id');
                $cookie->delete('sponsorship_email');
                $cookie->delete('sponsorship_firstname');
                $cookie->delete('sponsorship_lastname');
            }

            //update sponsor points validity
            $sponsor = Mage::getModel('customer/customer')->load($sponsorId);
            $validityType = '';
            if (Mage::helper('auguria_sponsorship/config')->isAccumulatedEnabled()) {
                $validityType = 'points_validity';
            }
            else {
                $validityType = 'sponsor_points_validity';
            }
            $validity = Mage::helper('auguria_sponsorship')->getPointsValidity('sponsorship');
            if(isset($validity['sponsorship'])) {
                $sponsor->setData($validityType,$validity['sponsorship']);
                $sponsor->save();
            }
        }
    }

    /**
     * Link customer with his sponsor on checkout_submit_all_after
     * @param Array $observer
     */
    public function setSponsorOnOrder($observer)
    {
        //checkout_type_onepage_save_order_after
        $quote = $observer['quote'];
        $order = $observer['order'];

        if ($order) {
            //if it's a new customer or if we allow to sponsor a registred user
            if ($quote->getData('checkout_method') == Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER
                    || Mage::helper('auguria_sponsorship/config')->isRegistredUsersInvitationAllowed() == 1) {

                $customerId = $order->getCustomerId();
                if ($customerId != '')
                {
                    $customer = Mage::getModel("customer/customer")->load($customerId);
                    //check godchild has no order
                    //check godchild is not a sponsor
                    //check godchild has no sponsor
                    if (!Mage::helper('auguria_sponsorship')->haveOrder($customerId)
                            && !Mage::helper('auguria_sponsorship')->isASponsor($customerId)
                            && ((int)$customer->getSponsor()!=0)) {
                        $this->_setSponsor($customer);
                        $customer->save();
                    }
                }
            }
        }
    }

    /**
     * Add sponsor points for a sponsor with special rate
     */
    protected function _addSponsorSpecialPoints($sponsor, $godson, $specialratepoints, $recordType='order', $recordId=0, $datetime=null)
    {
        //recalcul de la commande & maj items de la commande
        try {
            $sponsor = Mage::helper('auguria_sponsorship')->addSponsorshipPoints($sponsor, $specialratepoints);
            $sponsor->save();

            //save operation in logs
            $data = array(
                    'godson_id' => $godson->getId(),
                    'sponsor_id' => $sponsor->getId(),
                    'record_id' => $recordId,
                    'record_type' => $recordType,
                    'datetime' => $datetime,
                    'points' => (float)$specialratepoints
            );
            $this->_addAuguriaSponsorshipLog($data);

            //Send notification to sponsor
            $this->_sendSponsorNotification($sponsor, $godsonId, $pointsToAdd, $tSPoints);
        }
        catch (Exception $e) {
            Mage::log(Mage::helper('auguria_sponsorship')->__("An error occured while saving special points : %s",$e->getMessage()));
        }

    }

    /**
     * Create cookie to link customer with his sponsor
     * @param Array() $observer
     */
    public function affiliate($observer)
    {
        $controller = $observer['controller_action'];
        /*
         * Transmission de l'id du parrain + nom + prenom dans l'url
        * base url / module / controller / action / parametres
        * http://www.inkonso.com/cms/index/index/sponsor_id/x/nom/xxx/prenom/xxx/email/xxx
        */
        $sponsorId = $controller->getRequest()->getParam('sponsor_id');
        if ($sponsorId!='')
        {
            $nom = $controller->getRequest()->getParam('nom');
            $prenom = $controller->getRequest()->getParam('prenom');
            $email = $controller->getRequest()->getParam('email');

            //stockage des variables dans la session
            $session = Mage::getSingleton('core/session');
            $session->setData('sponsor_id',$sponsorId);
            $session->setData('firstname',$prenom);
            $session->setData('lastname',$nom);
            $session->setData('email',$email);

            //stockage de l'id du parrain dans un cookie
            $sponsorInvitationValidity = Mage::helper('auguria_sponsorship/config')->getInvitationValidity();
            $period =3600*24*$sponsorInvitationValidity;

            $cookie = Mage::getModel('core/cookie');
            $cookie->set('sponsorship_id', $sponsorId, $period);
            $cookie->set('sponsorship_firstname', $prenom, $period);
            $cookie->set('sponsorship_lastname', $nom, $period);
            $cookie->set('sponsorship_email', $email, $period);

            $controller->getRequest()->setParam('sponsor_id', null);
        }
    }

    /**
     *
     * Add fidelity points if customer subscribe to newletter while registration
     * @param Varien_Event_Observer $observer
     */
    public function addNewsletterPoints($observer)
    {
        try
        {
            $newsletterPoints = (int)Mage::helper('auguria_sponsorship/config')->getFidelityNewsletterPoints();
            //Check if we must add points
            if ($newsletterPoints > 0)
            {
                //Check if it's an account creation
                $customer = $observer->getCustomer();
                if (!$customer->getId())
                {
                    //Check if he is subscribing to the newsletter
                    if ($customer->getIsSubscribed() == 1)
                    {
                        $this->_addFidelityPoints($customer, $newsletterPoints, 'newsletter');
                    }
                }
            }
        }
        catch (Exception $e)
        {
            Mage::log(Mage::helper('auguria_sponsorship')->__("An error occured while adding news letter points : %s",$e->getMessage()));
        }
    }

    /**
     *
     * Add fidelity points to customer and sponsorship points to sponsor on first order
     * @param Varien_Event_Observer $observer
     */
    public function addFirstOrderPoints($observer)
    {
        try
        {
            $godsonFirstOrderPoints = Mage::helper('auguria_sponsorship/config')->getFidelityFirstOrderPoints();
            $sponsorFirstOrderPoints =  Mage::helper('auguria_sponsorship/config')->getGodsonFirstOrderPoints();

            //Check if we have to add points on first order
            if ($godsonFirstOrderPoints>0 || $sponsorFirstOrderPoints>0)
            {
                $invoice = $observer->getInvoice();
                $customerId = $invoice->getCustomerId();

                //Get customer paid invoices
                $invoices = Mage::getResourceModel('sales/order_invoice_collection');
                $invoices->getSelect()->join(array('o'=>$invoices->getTable('sales/order')), 'main_table.order_id = o.entity_id','o.customer_id');

                $invoices->addAttributeToSelect('state')
                ->addAttributeToFilter('o.customer_id', $customerId)
                ->addAttributeToFilter('main_table.state', Mage_Sales_Model_Order_Invoice::STATE_PAID);

                if ($invoices->count() == 0)
                {
                    $customer = Mage::getModel('customer/customer')->load($customerId);

                    //Add fidelity points to customer
                    if ($godsonFirstOrderPoints >0)
                    {
                        $this->_addFidelityPoints($customer, $godsonFirstOrderPoints, 'first', $invoice->getOrderId());
                        $customer->save();
                    }

                    //Add sponsorship points
                    $sponsorId = $customer->getSponsor();
                    if (isset($sponsorId) && $sponsorId>0 && $sponsorFirstOrderPoints>0)
                    {
                        $sponsor = Mage::getModel('customer/customer')->load($sponsorId);
                        $this->_addSponsorPoints($sponsor, $customer, $sponsorFirstOrderPoints, 'first', $invoice->getOrderId());

                    }
                }
            }
        }
        catch (Exception $e)
        {
            Mage::log(Mage::helper('auguria_sponsorship')->__("An error occured while adding first order points :".$e->getMessage()));
        }
    }

    /**
     * Update sponsorship tab customer fields from adminhtml customer form on customer_save_before
     * @param Varien_Event_Observer $observer
     */
    public function adminUpdateSponsorshipFields($observer)
    {
        //@TODO check each parameters
        $form = Mage::app()->getRequest()->getParam('sponsorship');
        if (is_array($form) && !empty($form)) {
            foreach ($form as $key=>$value) {
                $observer->getCustomer()->setData($key, $value);
            }
        }
        return $observer;
    }

    /**
     * Insert log if points are modified on customer_save_after
     * @param Varien_Event_Observer $observer
     */
    public function adminUpdatePoints($observer)
    {
        if (Mage::app()->getRequest()->getControllerName()=='customer') {
            $customer = $observer->getCustomer();
            if ($customer->hasDataChanges()) {
                $keys = Array('accumulated_points','sponsor_points','fidelity_points');
                foreach ($keys as $key) {
                    if ($customer->dataHasChangedFor($key)) {
                        $points = (float)$customer->getData($key) - (float)$customer->getOrigData($key);
                        $data = Array('points'=>$points,
                                'customer_id'=>$customer->getId(),
                                'record_type'=>'admin');
                        $this->_addAuguriaSponsorshipLog($data);
                    }
                }
            }
        }
    }

    /**
     * Update sponsorship fields from frontend customer form on customer_save_before
     * @param Varien_Event_Observer $observer
     */
    public function frontUpdateSponsorshipFields($observer)
    {
        $iban = Mage::app()->getRequest()->getParam('iban');
        $bic = Mage::app()->getRequest()->getParam('bic');
        $siret =  Mage::app()->getRequest()->getParam('siret');
        $customer = $observer->getCustomer();
        /*Edit action*/
        if ($customer->getId()) {
            if (isset($iban)) {
                $customerIban = str_replace(CHR(32),"",$iban);
                $customerIban = str_replace("-","",$customerIban);
                if (Zend_Validate::is( trim($iban) , 'NotEmpty')) {
                    if (!Zend_Validate::is( trim($iban) , 'Iban')) {
                        Mage::throwException(Mage::helper('auguria_sponsorship')->__('Invalid IBAN code "%s"', $iban));
                    }
                }
                $customer->setData('iban', $customerIban);
            }

            if (isset($bic)) {
                $customerBic = str_replace(CHR(32),"", $bic);
                $customerBic = str_replace("-","", $customerBic);
                $validator = new Zend_Validate_Regex(array('pattern' => '/^([a-zA-Z]){4}([a-zA-Z]){2}([0-9a-zA-Z]){2}([0-9a-zA-Z]{3})?$/'));
                if(!$validator->isValid($customerBic) && !empty($customerBic)){
                    Mage::throwException(Mage::helper('auguria_sponsorship')->__('Invalid BIC code "%s"', $bic));
                }
                $customer->setData('bic', $customerBic);
            }

            if (isset($siret)) {
                $customerSiret = str_replace(CHR(32),"",$siret);
                $customerSiret = str_replace("-","",$customerSiret);
                /*desactivated for internationalization
                 if (!Mage::helper('auguria_sponsorship')->isSiret(trim($siret))) {
                Mage::throwException(Mage::helper('auguria_sponsorship')->__('Invalid SIRET code "%s"', $siret));
                }
                */
                $customer->setData('siret', $customerSiret);
            }
        }
        /*Create action*/
        else {
            if ($sponsorId = Mage::helper("auguria_sponsorship")->searchSponsorId($customer->getEmail())) {
                $customer->setData('sponsor',$sponsorId);
                $cookie = Mage::getSingleton('core/cookie');
                if ($cookie->get('sponsorship_id')) {
                    $cookie->delete('sponsorship_id');
                    $cookie->delete('sponsorship_email');
                    $cookie->delete('sponsorship_firstname');
                    $cookie->delete('sponsorship_lastname');
                }
            }
        }
        return $observer;
    }

    /**
     * Send notification to sponsor when godson make him earn points
     * @param Mage_Customer_Model_Customer $sponsor
     * @param Int $godchildId
     * @param Float $addedPoints
     * @param Float $totalPoints
     */
    protected function _sendSponsorNotification($sponsor, $godchildId, $addedPoints, $totalPoints)
    {
        if (Mage::helper('auguria_sponsorship/config')->isSponsorshipNotificationEnabled()==1) {
            $godchild = Mage::getModel('customer/customer')->load($godchildId);
            $mailTemplate = Mage::getModel('auguria_sponsorship/Core_Email_Template');

            $sender_name = Mage::getStoreConfig('trans_email/ident_sales/name');
            $sender_email = Mage::getStoreConfig('trans_email/ident_sales/email');

            $subject = Mage::helper('auguria_sponsorship')->__('%s sponsor points', Mage::helper('auguria_sponsorship/mail')->getStoreName());

            $sender  = array("name"=>$sender_name, "email"=>$sender_email);
            $recipient_email = $sponsor->getData('email');

            $postObject = new Varien_Object();
            $postObject->setData(Array ("sender_name" => $sender_name,
                    "sender_email" => $sender_email,
                    "recipient_firstname" => $sponsor->getFirstname(),
                    "recipient_lastname" => $sponsor->getLastname(),
                    "subject" => $subject,
                    "store_name" => Mage::helper('auguria_sponsorship/mail')->getStoreName(),
                    "godchild_firstname" =>$godchild->getFirstname(),
                    "godchild_lastname" =>$godchild->getLastname(),
                    "added_points" =>$addedPoints,
                    "total_points" =>$totalPoints
            ));
            $mailTemplate->setDesignConfig(array('area' => 'frontend'))
            ->setReplyTo($sender_email)
            ->sendTransactional(
                    Mage::helper('auguria_sponsorship/config')->getSponsorshipNotificationTemplate(),
                    $sender,
                    $recipient_email,
                    $sponsor->getFirstname().' '.$sponsor->getLastname(),
                    array('data' => $postObject));
            if (!$mailTemplate->getSentSuccess())
            {
                Mage::log(Mage::helper('auguria_sponsorship')->__("An error occured while sending sponsor notification email."));
            }
        }
    }

    /**
     * Remove points from customer account if auguria_sponsorship_discount is set
     * on checkout_submit_all_after
     * @param array $observer
     */
    public function cartPointsExchange($observer)
    {
        $order = $observer['order'];
        if ($order) {
            if ((float)$order->getAuguriaSponsorshipDiscountAmount()<0) {
                $customerId = $order->getCustomerId();
                if ($customerId != '')
                {
                    $pointsToCash = Mage::helper('auguria_sponsorship/config')->getPointsToCash();
                    $orderAccumulated = 0;
                    $orderFidelity = 0;
                    $orderSponsor = 0;
                    if (isset($pointsToCash['accumulated']) && (float)$pointsToCash['accumulated']>0) {
                        $orderAccumulated = (float)$order->getAuguriaSponsorshipAccumulatedPointsUsed()/(float)$pointsToCash['accumulated'];
                    }
                    if (isset($pointsToCash['fidelity']) && (float)$pointsToCash['fidelity']>0) {
                        $orderFidelity = (float)$order->getAuguriaSponsorshipFidelityPointsUsed()/(float)$pointsToCash['fidelity'];
                    }
                    if (isset($pointsToCash['sponsorship']) && (float)$pointsToCash['sponsorship']>0) {
                        $orderSponsor = (float)$order->getAuguriaSponsorshipSponsorPointsUsed()/(float)$pointsToCash['sponsorship'];
                    }
                    $type = Array();

                    if ((float)$orderAccumulated > 0
                            ||(float)$orderSponsor > 0
                            ||(float)$orderFidelity > 0) {
                        $customer = Mage::getModel("customer/customer")->load($customerId);
                        $usedPoints = 0;
                        if ((float)$orderAccumulated > 0) {
                            $points = (float)$customer->getAccumulatedPoints();
                            $usedPoints = (float)$orderAccumulated;
                            $points = $points - $usedPoints;
                            $points = max(0,$points);
                            $customer->setAccumulatedPoints($points);
                            $type['accumulated'] = array($usedPoints,(float)$order->getAuguriaSponsorshipAccumulatedPointsUsed());
                        }
                        else {
                            if ((float)$orderSponsor > 0) {
                                $points = (float)$customer->getSponsorPoints();
                                $usedPoints += (float)$orderSponsor;
                                $points = $points - $usedPoints;
                                $points = max(0,$points);
                                $customer->setSponsorPoints($points);
                                $type['sponsor'] = array($usedPoints,(float)$order->getAuguriaSponsorshipSponsorPointsUsed());
                            }
                            if ((float)$orderFidelity > 0) {
                                $points = (float)$customer->getFidelityPoints();
                                $usedPoints += (float)$orderFidelity;
                                $points = $points - (float)$orderFidelity;
                                $points = max(0,$points);
                                $customer->setFidelityPoints($points);
                                $type['fidelity'] = array((float)$orderFidelity,(float)$order->getAuguriaSponsorshipFidelityPointsUsed());
                            }
                        }
                        $customer->save();

                        if ($usedPoints>0) {
                            $data = Array('points'=>-$usedPoints,
                                    'customer_id'=>$customerId,
                                    'record_type'=>'cart',
                                    'record_id'=>$order->getId(),
                                    'datetime'=>$order->getUpdatedAt());
                            $this->_addAuguriaSponsorshipLog($data);

                            if (count($type)>0) {
                                foreach($type as $key=>$dataExchange) {
                                    list($point, $value) = $dataExchange;
                                    $echange = Mage::getModel('auguria_sponsorship/change');
                                    $row = array(
                                            'customer_id' => $customerId,
                                            'type' => 'cart',
                                            'module' => $key,
                                            'statut' => 'waiting',
                                            'datetime' => Mage::getModel('core/date')->gmtDate(),
                                            'points' => $point,
                                            'value' => $value
                                    );
                                    $echange->setData($row);
                                    $echange->save();
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->_removeCartExchangeFromSession();
    }

    /**
     * Unset choose of use points parameter in session
     *
     */
    protected function _removeCartExchangeFromSession()
    {
        $pointsTypes = Mage::helper('auguria_sponsorship/config')->getPointsTypes();
        if ($pointsTypes && count($pointsTypes) >0) {
            foreach ($pointsTypes as $type) {
                if ($type == 'sponsor') {
                    $type = 'sponsorship';
                }
                Mage::getSingleton('checkout/session')->setData('use_sponsorship_points_'.$type, false);
            }
        }
    }

    /**
     * Insert data in points logs table
     * @param array $data
     * @return boolean
     */
    protected function _addAuguriaSponsorshipLog($data)
    {
        if (is_array($data) && isset($data['points'])) {
            $log = Mage::getModel('auguria_sponsorship/log');
            if (!isset($data['datetime'])) {
                $data['datetime'] = Mage::getModel('core/date')->gmtDate();
            }
            $log->setData($data);
            $log->save();
            return true;
        }
        return false;
    }

    /**
     * Add Auguria_Sponsorship points in quote according to defined rules on salesrule_validator_process event
     * @param array(
     'rule'    => $rule,
     'item'    => $item,
     'address' => $address,
     'quote'   => $quote,
     'qty'     => $qty,
     'result'  => $result,
     ) $observer
     */
    public function validatorPointsCalculationProcess($observer)
    {
        $item = $observer['item'];
        $rule = $observer['rule'];
        $result = $observer['result'];
        $address = $observer['address'];
        $qty = $observer['qty'];

        $rulePercent = min(100, $rule->getDiscountAmount());
        $price = $item->getDiscountCalculationPrice();
        $baseItemPrice = ($price !== null) ? $item->getBaseDiscountCalculationPrice() : $item->getBaseCalculationPrice();

        $cartFidelityPoints = 0;
        $cartSponsorPoints = 0;

        switch ($rule->getSimpleAction()) {
            case 'fidelity_points_by_fixed':
                if ($step = $rule->getDiscountStep()) {
                    $qty = floor($qty/$step)*$step;
                }
                $cartFidelityPoints = $qty*$rule->getDiscountAmount();
                break;

            case 'fidelity_points_by_percent':
                if ($step = $rule->getDiscountStep()) {
                    $qty = floor($qty/$step)*$step;
                }
                $cartFidelityPoints = ($qty*$baseItemPrice - $item->getBaseDiscountAmount()) * $rulePercent/100;
                break;

            case 'fidelity_points_cart_fixed':
                /** @TODO : prevent applying whole cart discount for every shipping order, but only for first order */
                $cartRules = $address->getCartFixedRules();
                if (!isset($cartRules[$rule->getId()])) {
                    $cartRules[$rule->getId()] = $rule->getDiscountAmount();
                }
                if ($cartRules[$rule->getId()] > 0) {
                    $cartFidelityPoints = $rule->getDiscountAmount();
                }
                $address->setCartFixedRules($cartRules);
                break;
                //sponsor
            case 'sponsor_points_by_fixed':
                if ($step = $rule->getDiscountStep()) {
                    $qty = floor($qty/$step)*$step;
                }
                $cartSponsorPoints = $qty*$rule->getDiscountAmount();
                break;

            case 'sponsor_points_by_percent':
                if ($step = $rule->getDiscountStep()) {
                    $qty = floor($qty/$step)*$step;
                }
                $cartSponsorPoints = ($qty*$baseItemPrice - $item->getBaseDiscountAmount()) * $rulePercent/100;
                break;

            case 'sponsor_points_cart_fixed':
                /** @TODO : prevent applying whole cart discount for every shipping order, but only for first order */
                $cartRules = $address->getCartFixedRules();
                if (!isset($cartRules[$rule->getId()])) {
                    $cartRules[$rule->getId()] = $rule->getDiscountAmount();
                }
                if ($cartRules[$rule->getId()] > 0) {
                    $cartSponsorPoints = $rule->getDiscountAmount();
                }
                $address->setCartFixedRules($cartRules);
                break;
        }
        $result->setData('cart_fidelity_points',$cartFidelityPoints);
        $result->setData('cart_sponsor_points',$cartSponsorPoints);

        $cartFidelityPoints = max((float)$item->getCartFidelityPoints(), (float)$cartFidelityPoints);
        $cartSponsorPoints = max((float)$item->getCartSponsorPoints(), (float)$cartSponsorPoints);

        $item->setCartFidelityPoints($cartFidelityPoints);
        $item->setCartSponsorPoints($cartSponsorPoints);

        return $this;
    }

    /**
     * Cancel used and gained points while credit memo creation using sales_order_creditmemo_save_before event
     * @param array $observer
     */
    public function cancelPointsOnCreditMemo($observer)
    {
        $creditmemo = $observer['creditmemo'];
        //if it is a credit memo creation
        if ((int)$creditmemo->getId()==0
                && (Mage::helper('auguria_sponsorship/config')->cancelFidelityEarnedPointsOnCreditMemo()
                        || Mage::helper('auguria_sponsorship/config')->cancelSponsorshipEarnedPointsOnCreditMemo())
        ) {
            $order = $creditmemo->getOrder();

            //Cancel used points
            //@TODO check credit memo sold the order ?
            $this->_cancelUsedPoints($order);

            //Remove winning points
            //calcul fidelity points to remove and ratio for sponsorship points
            $earnedFidelityPoints = 0;
            $backFidelityPoints = 0;
            $earnedSponsorPoints = 0;
            $backSponsorPoints = 0;
            $ratio = 0;
            $creditMemoItems = $creditmemo->getItemsCollection();
            if ($creditMemoItems->count()>0) {
                foreach ($creditMemoItems as $creditmemoItem) {
                    $orderItem = $creditmemoItem->getOrderItem();

                    //fidelity points
                    $earnedFidelityPoints += (float)$orderItem->getCatalogFidelityPoints()+(float)$orderItem->getCartFidelityPoints();
                    $tmpFidelityPoints = (float)$orderItem->getCatalogFidelityPoints()+(float)$orderItem->getCartFidelityPoints();
                    if($orderItem->getQtyInvoiced()!=0) {
                        $tmpFidelityPoints = ($tmpFidelityPoints*$creditmemoItem->getQty())/$orderItem->getQtyInvoiced();
                    }
                    $backFidelityPoints += $tmpFidelityPoints;

                    //sponsorship points
                    $earnedSponsorPoints += (float)$orderItem->getCatalogSponsorPoints()+(float)$orderItem->getCartSponsorPoints();
                    $tmpSponsorPoints = (float)$orderItem->getCatalogSponsorPoints()+(float)$orderItem->getCartSponsorPoints();
                    if($orderItem->getQtyInvoiced()!=0) {
                        $tmpSponsorPoints = ($tmpSponsorPoints*$creditmemoItem->getQty())/$orderItem->getQtyInvoiced();
                    }
                    $backSponsorPoints += $tmpSponsorPoints;
                }
                if ($earnedSponsorPoints>0) {
                    $ratio = $backSponsorPoints/$earnedSponsorPoints;
                }
            }

            //remove fidelity points
            if ($backFidelityPoints>0) {
                if (Mage::helper('auguria_sponsorship/config')->cancelFidelityEarnedPointsOnCreditMemo()){
                    $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
                    $points = Mage::helper('auguria_sponsorship')->getFidelityPoints($customer);
                    $points -= (float)$backFidelityPoints;
                    $points = max(0,$points);
                    Mage::helper('auguria_sponsorship')->setFidelityPoints($customer, $points);
                    $customer->save();
                    $data = array(
                            'customer_id' => $order->getCustomerId(),
                            'record_id' => $order->getId(),
                            'record_type' => 'order',
                            'points' => -$backFidelityPoints
                    );
                    $this->_addAuguriaSponsorshipLog($data);
                }
            }

            //remove sponsorship points according to logs and applying ratio
            if (Mage::helper('auguria_sponsorship/config')->cancelSponsorshipEarnedPointsOnCreditMemo()){
                $collection = Mage::getResourceModel('auguria_sponsorship/log_collection')
                ->addFieldToFilter('record_type', 'order')
                ->addFieldToFilter('record_id', $order->getId());
                if ($collection->count()>0){
                    foreach ($collection as $log) {
                        $points = 0;
                        if($log->getSponsorId()>0) {
                            $customer = Mage::getModel('customer/customer')->load($log->getSponsorId());
                            $points = Mage::helper('auguria_sponsorship')->getSponsorshipPoints($customer);
                            $points -= (float)$log->getPoints()*$ratio;
                            $points = max(0,$points);
                            Mage::helper('auguria_sponsorship')->setSponsorshipPoints($customer, $points);
                            $customer->save();
                        }

                        if ($points>0) {
                            $data = array(
                                    'godson_id' => $log->getGodsonId(),
                                    'sponsor_id' => $log->getSponsorId(),
                                    'record_id' => $order->getId(),
                                    'record_type' => 'order',
                                    'points' => -(float)$log->getPoints()*$ratio
                            );
                            $this->_addAuguriaSponsorshipLog($data);
                        }
                    }
                }
            }
        }
    }

    /**
     * Cancel used points while on order cancellation
     * As there is no event on cancellation we use sales_order_save_before event and we check status
     * @param array $observer
     */
    public function cancelPointsOnOrderCanceled($observer)
    {
        $order = $observer['order'];
        //Check status passed from any to canceled
        if ($order->getStatus()=='canceled'
                && $order->getOrigData('status')!='canceled') {
            $this->_cancelUsedPoints($order);
        }
    }

    /**
     * Add to customer used points in an order and log it
     * @param Mage_Sales_Model_Order $order
     */
    protected function _cancelUsedPoints($order)
    {
        $customerId = $order->getCustomerId();
        $customer = Mage::getModel('customer/customer')->load($customerId);

        $usedPoints = Array();
        $usedPoints['accumulated'] = $order->getAuguriaSponsorshipAccumulatedPointsUsed();
        $usedPoints['sponsorship'] = $order->getAuguriaSponsorshipSponsorPointsUsed();
        $usedPoints['fidelity'] = $order->getAuguriaSponsorshipFidelityPointsUsed();
        $toAdd = 0;
        //Add used points
        foreach ($usedPoints as $type=>$points) {
            if ($points>0) {
                $haveUsedPoints = true;
                if ($type=='accumulated') {
                    if (Mage::helper('auguria_sponsorship/config')->cancelUsedPointsOnCreditMemo($type)){
                        $toAdd += $points;
                        $customer->setAccumulatedPoints($customer->getAccumulatedPoints()+$points);
                    }
                }
                elseif ($type=='sponsorship') {
                    if (Mage::helper('auguria_sponsorship/config')->cancelUsedPointsOnCreditMemo($type)){
                        $toAdd += $points;
                        $customer->setSponsorPoints($customer->getSponsorPoints()+$points);
                    }
                }
                elseif ($type=='fidelity') {
                    if (Mage::helper('auguria_sponsorship/config')->cancelUsedPointsOnCreditMemo($type)){
                        $toAdd += $points;
                        $customer->setFidelityPoints($customer->getFidelityPoints()+$points);
                    }
                }
            }
        }
        if ($toAdd>0) {
            $customer->save();
            $data = array(
                    'customer_id' => $customer->getId(),
                    'record_id' => $order->getId(),
                    'record_type' => 'cart',
                    'points' => $toAdd
            );
            $this->_addAuguriaSponsorshipLog($data);
        }
    }

    /**
     * Add sponsorship and fidelity action on adminhtml_block_salesrule_actions_prepareform
     * @param array $observer
     */
    public function salesRulesAddAction($observer)
    {
        $field = $observer->getForm()->getElement('simple_action');
        $options = $field->getValues();
        $customOptions = Array(
                Array('value'=>'fidelity_points_by_percent','label'=>Mage::helper('auguria_sponsorship')->__('Fidelity points by percentage of the original price')),
                Array('value'=>'fidelity_points_by_fixed','label'=>Mage::helper('auguria_sponsorship')->__('Fidelity points by fixed amount')),
                Array('value'=>'fidelity_points_cart_fixed','label'=>Mage::helper('auguria_sponsorship')->__('Fidelity points by fixed amount for whole cart')),
                Array('value'=>'sponsor_points_by_percent','label'=>Mage::helper('auguria_sponsorship')->__('Sponsorship points by percentage of the original price')),
                Array('value'=>'sponsor_points_by_fixed','label'=>Mage::helper('auguria_sponsorship')->__('Sponsorship points by fixed amount')),
                Array('value'=>'sponsor_points_cart_fixed','label'=>Mage::helper('auguria_sponsorship')->__('Sponsorship points by fixed amount for whole cart'))
        );
        $allOptions = array_merge($options, $customOptions);
        $field->setValues($allOptions);
    }

    /**
     * Update paypal cart
     *
     * @event paypal_prepare_line_items
     * @param Varien_Event_Observer $observer
     */
    public function updatePaypal($observer)
    {
        //récupération de la structure passée en paramètre par Paypal
        $event = $observer->getEvent();
        //récupération du panier Paypal
        $panierPaypal = $event->getPaypalCart();
        if ($panierPaypal)
        {
            //récupération de l'objet nécessaire pour les ventes
            $entity = $panierPaypal->getSalesEntity();
            if ($entity)
            {
                //tentative de récupération du montant Auguria
                $douRemisePointsFidelite = $entity->getAuguriaSponsorshipDiscountAmount();
                //si montant trouvé, on met à jour le total
                if ($douRemisePointsFidelite)
                    $panierPaypal->updateTotal(Mage_Paypal_Model_Cart::TOTAL_DISCOUNT, abs($douRemisePointsFidelite));
            }
        }
    }

    /**
     * Refill point when payment is canceled
     * @event sales_order_payment_cancel
     * @param Varien_Event_Observer $observer
     */
    public function paymentCanceled($observer)
    {
        //récupération de la structure passée en paramètre par Paypal
        $event = $observer->getEvent();
        //récupération de la commande associée
        $order = $event->getPayment()->getOrder();
        //action sur le compte fidélité (code dérivé de la fonction _cancelUsedPoints)

        $customerId = $order->getCustomerId();
        $customer = Mage::getModel('customer/customer')->load($customerId);

        $pointsToCash = Mage::helper('auguria_sponsorship/config')->getPointsToCash();

        $usedPoints = array();
        $usedPoints['accumulated'] = $order->getAuguriaSponsorshipAccumulatedPointsUsed();
        $usedPoints['sponsorship'] = $order->getAuguriaSponsorshipSponsorPointsUsed();
        $usedPoints['fidelity'] = $order->getAuguriaSponsorshipFidelityPointsUsed();
        $toAdd = 0;
        //Add used points
        foreach ($usedPoints as $type=>$points) {
            if ($points>0) {
                //conversion en nombre de points
                $douTaux = 1;
                if (isset($pointsToCash[$type]) && (float)$pointsToCash[$type]>0){
                    $douTaux = (float)$pointsToCash[$type];
                }
                //correction
                $haveUsedPoints = true;
                if ($type=='accumulated') {
                    $toAdd += $points/$douTaux;
                    $customer->setAccumulatedPoints($customer->getAccumulatedPoints()+$points/$douTaux);
                }
                elseif ($type=='sponsorship') {
                    $toAdd += $points/$douTaux;
                    $customer->setSponsorPoints($customer->getSponsorPoints()+$points/$douTaux);
                }
                elseif ($type=='fidelity') {
                    $toAdd += $points/$douTaux;
                    $customer->setFidelityPoints($customer->getFidelityPoints()+$points/$douTaux);
                }
            }
        }
        if ($toAdd>0) {
            $customer->save();
            $data = array(
                    'customer_id' => $customer->getId(),
                    'record_id' => $order->getId(),
                    'record_type' => 'cart',
                    'points' => $toAdd
            );
            $this->_addAuguriaSponsorshipLog($data);
        }
    }
}