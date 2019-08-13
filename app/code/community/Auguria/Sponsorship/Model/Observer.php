<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Model_Observer
{
    public function calcPoints($observer)
    {
    	try {
            mage::log("début debug-----------------------------------------------------------------------");
    		//récupération de la commande et des articles
    		$order = $observer->getInvoice()->getOrder();
	    	$orderDate = $order->getUpdatedAt();
    		$orderId = $order->getEntityId();

                //définition du client
	        $cId = $order->getCustomerId();

	    	//definition du websiteid
	     	$wId = $order->getStore()->getWebsiteId();

	        //definition du groupe du client
	    	$gId = Mage::getModel('customer/customer')->load($cId)->getGroupId();

                mage::log($orderId." ".$cId);
                //definition du sponsor de premier niveau
                $sponsorId = $this->getSponsorId($cId);
                $sponsor = Mage::getModel('customer/customer')->load($sponsorId);
                $special_rate = $sponsor->getData('special_rate');
	    	//variable de points
	    	$tCatalogFidelityPoints=0;
	    	$tCatalogSponsorPoints=0;
	    	$tCartFidelityPoints=0;
	    	$tCartSponsorPoints=0;


	    	//modules actifs
	    	$moduleFidelity = Mage::getStoreConfig('sponsorship/fidelity/fidelity_enabled');
	    	$moduleSponsor = Mage::getStoreConfig('sponsorship/sponsor/sponsor_enabled');

                //calcul des points catalogue et mise à jour de lacommande pour chaque ligne
	    	foreach ($order->getAllItems() as $item)
	        {
	        	$date = $item->getData('updated_at');
	        	$pId = $item->getData('product_id');
	        	$qte = $item->getData('qty_ordered');
	        	$data = $item->getData();

	        	if ($moduleFidelity)
	        	{
                            mage::log("module fidélité ok");
	        		if ($item->getData('price') > 0) //vérification pour bundle : ne pas ajouter des points si le prix de l'article est 0 : idéalement, il faudrait revoir les règles de prix pour les articles packagés
	        		{
		        		//récupération et affectation des points catalog pour chaque article commandé
			        	$catalogFidelityPoints = $this->getRulePoints($date, $wId, $gId, $pId,'fidelity');
			        	//multiplication des points par la quantité
			        	$catalogFidelityPoints = $catalogFidelityPoints*$qte;

			        	//ajout des points aux items de commande
                                        $data['catalog_fidelity_points'] = $catalogFidelityPoints;

			        	//calcul du total de points catalogue
			        	$tCatalogFidelityPoints = $tCatalogFidelityPoints+$catalogFidelityPoints;
	        		}

		        	//calcul du total de points panier
		        	$tCartFidelityPoints = $tCartFidelityPoints+$item->getCartFidelityPoints();
	        	}

		        if ($moduleSponsor && $special_rate==null)
		        {
                            mage::log("module parrainage ok sans taux spé");
		        	if ($item->getData('price') > 0) //vérification pour bundle : ne pas ajouter des points si le prix de l'article est 0 : idéalement, il faudrait revoir les règles de prix pour les articles packagés
	        		{
		        		//récupération et affectation des points catalog pour chaque article commandé
			        	$catalogSponsorPoints = $this->getRulePoints($date, $wId, $gId, $pId, 'sponsor');

			        	//multiplication des points par la quantité
			        	$catalogSponsorPoints = $catalogSponsorPoints*$qte;

			        	//ajout des points aux items de commande
                                        $data['catalog_sponsor_points'] = $catalogSponsorPoints;
			        	//calcul du total de points catalogue
			        	$tCatalogSponsorPoints = $tCatalogSponsorPoints+$catalogSponsorPoints;
	        		}

		        	//calcul du total de points panier
		    		$tCartSponsorPoints = $tCartSponsorPoints+$item->getCartSponsorPoints();
	        	}
                        //si un taux spécial est défini pour le parrain direct
                        elseif ($moduleSponsor && $special_rate!=null)
                        {
                            mage::log("module parrainage ok avec taux spé");
                            //Redéfinition du taux à appliquer dans la commande
                            if ($item->getData('price') > 0) //vérification pour bundle : ne pas ajouter des points si le prix de l'article est 0 : idéalement, il faudrait revoir les règles de prix pour les articles packagés
                            {
                                    //ajout des points aux items de commande
                                    $data['catalog_sponsor_points'] = 0;
                                    $specialratepoints = $item->getData('price')*$qte*$special_rate/100;
                                    $data['cart_sponsor_points'] = $specialratepoints;
                            }

                        }

	        	if ($moduleFidelity||$moduleSponsor)
	        	{
	        		$item->setData($data);
		        	$item->save();
	        	}
	        }

	        $order->save();

	        //Ajout du total des points fidelite
	        if ($tCatalogFidelityPoints != 0 || $tCartFidelityPoints != 0 ) {
	        	$this->addFidelityPoints($cId,$tCatalogFidelityPoints+$tCartFidelityPoints,$orderDate,$orderId);
	        }

	        //Ajout du total des points de parrainage si le parrain n'a pas de taux spécial
	    	if (($tCatalogSponsorPoints != 0 || $tCartSponsorPoints != 0) && $special_rate==null) {
	        	$this->addSponsorsPoints($cId,$tCatalogSponsorPoints+$tCartSponsorPoints,$orderDate,$orderId);
	        }
                //Ajout des points à partir du taxu spécial au parrain direct uniquement
                elseif ($special_rate!=null && $moduleSponsor) {
	        	$this->addSponsorSpecialPoints($cId,$orderDate,$orderId,$order->subtotal, $special_rate);
	        }


                mage::log("fin debug-----------------------------------------------------------------------");
	        return $this;
    	}
    	catch (Exception $e) {
    		Mage::log("An error occured while saving points :".$e->getMessage());
    	}
    }

    public function getRulePoints($date, $wId, $gId, $pId, $type)
    {
    	$resource = Mage::getSingleton('core/resource');
		$read= $resource->getConnection('core_read');
		$userTable = $resource->getTableName('sponsorship/catalog'.$type.'point');
		$select = $read->select()
			->from($resource->getTableName('sponsorship/catalog'.$type.'point'), 'rule_point')
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

    public function addFidelityPoints($cId,$tFPoints,$orderDate,$orderId)
    {
    	//récupération des points fidélité existants
    	$customer = Mage::getModel('customer/customer')->load($cId);
    	$iFPoints = $customer->getData('fidelity_points');

    	//ajout des points fidélité
		$customer->setFidelityPoints($tFPoints+$iFPoints);
    	$customer->save();

    	//enregistrement dans les logs
    	$log = Mage::getModel('sponsorship/fidelitylog');
    	$data = array(
			    'customer_id' => $cId,
			    'record_id' => $orderId,
			    'record_type' => 'order',
			    'datetime' => $orderDate,
			    'points' => $tFPoints
    	);
		$log->setData($data);
		$log->save();
    }

    /*
     * Add sponsor points to sponsors
     */
    public function addSponsorsPoints($cId,$SPoints,$orderDate,$orderId)
    {
    	$ratio = Mage::getStoreConfig('sponsorship/sponsor/sponsor_percent');
    	$maxLevel = Mage::getStoreConfig('sponsorship/sponsor/sponsor_levels');
    	$sponsorId = $this->getSponsorId($cId);
    	$godsonId = $cId;

    	//Ajout des points tant que le niveau maximum n'est pas atteint et qu'un parrain est défini
    	for ($level = 0; $level<$maxLevel AND $sponsorId!=null AND round($SPoints,4)>0; $level++)
    	{
            //définition du parrain
            $sponsor = Mage::getModel('customer/customer')->load($sponsorId);

            //si parrain a un taux special : on met fin à la boucle
            $special_rate = $sponsor->getData('special_rate');
            if ($special_rate)
                $SPoints = 0;//mise à 0 des points de parrainage pour arrêter la boucle

            $iSPoints = $sponsor->getData('sponsor_points');

            //ajout des points fidélité au parrain
            //si il y a un taux special et qu'on est au premier niveau
            $pointsToAdd = 0;
            if ($special_rate && $level == 0)
            {
                $pointsToAdd = $specialSponsorPoints;
            }
            //si il n'y a pas de taux special
            elseif (!$special_rate)
            {
                $pointsToAdd = $SPoints;
            }
            $tSPoints = $pointsToAdd+$iSPoints;

            if (!$special_rate||($special_rate && $level == 0))
            {
                $sponsor->setSponsorPoints($tSPoints);
                $sponsor->save();

                //enregistrement dans les logs
                $log = Mage::getModel('sponsorship/sponsorlog');
                $data = array(
                        'godson_id' => $godsonId,
                        'sponsor_id' => $sponsorId,
                        'record_id' => $orderId,
                        'record_type' => 'order',
                        'datetime' => $orderDate,
                        'points' => $pointsToAdd
                );
                // affecter les attributs au nouveau produit
                $log->setData($data);
                // sauver le nouveau produit (insère en base de données)
                $log->save();
            }
            if (!$special_rate)
            {
                //incrémentation des points à ajouter
                $SPoints = ($SPoints*$ratio)/100;
                //le parrain devient le filleul
                $godsonId = $sponsorId;
                //définition du parrain du parrain
                $sponsorId = $this->getSponsorId($sponsorId);
            }
    	}
    }

    public function getSponsorId($cId)
    {
    	$customer = Mage::getModel('customer/customer')->load($cId);
    	$sponsorId = $customer->getSponsor();
    	if ($sponsorId != null && $sponsorId != 0)
    	{
    		return $sponsorId;
    	}
    	else {
    		return false;
    	}
    }

    public function setSponsor($observer)
    {
        //checkout_type_onepage_save_order_after
        $quote = $observer['quote'];
        $order = $observer['order'];

        //si c'est un enregistrement (nouveau client)
        if ($quote->getData('checkout_method')==Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER)
        {
            //recherche d'un parrain éventuel
            $customerId = $order->getCustomerId();
            if ($customerId != '')
            {
                $customer = mage::getModel("customer/customer")->load($customerId);
                $sponsorId = mage::helper("sponsorship/data")->searchSponsorId($customer->getEmail());
                if ($sponsorId != '')
                {
                    $customer->setData('sponsor',$sponsorId);
                    $cookie = new Mage_Core_Model_Cookie;
                    if ($cookie->get('sponsorship_id'))
                    {
                            $cookie->delete('sponsorship_id');
                            $cookie->delete('sponsorship_email');
                            $cookie->delete('sponsorship_firstname');
                            $cookie->delete('sponsorship_lastname');
                    }
                    $customer->save();
                }
            }
        }
    }

    /*
     * Add sponsor points for a sponsor with special rate
     */
    public function addSponsorSpecialPoints($cId, $orderDate, $orderId, $orderTotal, $special_rate)
    {
        //recalcul de la commande & maj items de la commande
        try {
                $sponsorId = $this->getSponsorId($cId);
                $godsonId = $cId;
                //définition du parrain
                $sponsor = Mage::getModel('customer/customer')->load($sponsorId);
                $iSPoints = $sponsor->getData('sponsor_points');
                $pointsToAdd = $orderTotal*$special_rate/100;
                $tSPoints = $pointsToAdd+$iSPoints;
                $sponsor->setSponsorPoints($tSPoints);
                $sponsor->save();

                //enregistrement dans les logs
                $log = Mage::getModel('sponsorship/sponsorlog');
                $data = array(
                        'godson_id' => $godsonId,
                        'sponsor_id' => $sponsorId,
                        'record_id' => $orderId,
                        'record_type' => 'order',
                        'datetime' => $orderDate,
                        'points' => $pointsToAdd
                );
                // affecter les attributs au nouveau produit
                $log->setData($data);
                // sauver le nouveau produit (insère en base de données)
                $log->save();
    	}
    	catch (Exception $e) {
    		Mage::log("An error occured while saving special points :".$e->getMessage());
    	}

    }
    
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
            $sponsorInvitationValidity = Mage::getStoreConfig('sponsorship/sponsor/sponsor_invitation_validity');
            $period =3600*24*$sponsorInvitationValidity;
                
        	$cookie = new Mage_Core_Model_Cookie;
        	$cookie->set('sponsorship_id', $sponsorId, $period);
        	$cookie->set('sponsorship_firstname', $prenom, $period);
        	$cookie->set('sponsorship_lastname', $nom, $period);
        	$cookie->set('sponsorship_email', $email, $period);
        	
        	$controller->getRequest()->setParam('sponsor_id', null); 
    	}
    }
}