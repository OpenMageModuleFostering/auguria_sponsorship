<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Helper_Data extends Mage_Core_Helper_Abstract
{
    /*
    public function versionIsgreaterThanOneThree()
    {
        $currentVersion = Mage::getVersion();
        if (version_compare($currentVersion, '1.4.0')>=0)
            return true;
        else
            return false;
    }
    */
    
    public function haveOrder ()
    {
    	$cId = $this->getCustomerId();
    	$commande = Mage::getModel("sales/order")
                    ->getCollection()
                    ->addAttributeToFilter('customer_id',$cId);
    	if ($commande->getData()) {
    		return true;
    	}
    	else {
    		return false;
    	}
    }

    //Ajout de la methode pour rechercher l'id d'un parrain eventuel
    public function searchSponsorId($emailCustomer)
    {
        $idParrain = '';
        $cookie = new Mage_Core_Model_Cookie;
        $sponsorInvitationValidity = Mage::getStoreConfig('sponsorship/sponsor/sponsor_invitation_validity');
        //recherche du mail dans les logs actifs
        $parrainageLog = Mage::getModel("sponsorship/sponsorship")
                        ->getCollection()
                                ->addAttributeToFilter('child_mail', $emailCustomer)
                                ->addDateToFilter('datetime', $sponsorInvitationValidity)
                                ->setOrder('datetime','asc')
                                ->getLastItem();
        //si mail dans les logs parrain selection du log actif le plus ancien
        $parrain = $parrainageLog->getData('parent_id');
        if ($parrain != '') {
                $idParrain = $parrain['parent_id'];
        }
        //sinon, si il existe on prend l'id stocké dans le cookie
        else {
                if ($cookie->get('sponsorship_id')) {
                        $idParrain = $cookie->get('sponsorship_id');
                }
        //sinon, si il existe on prend l'id stocké dans la session : au cas où les cookies sont désactivés.
                else {
                        if (Mage::getSingleton('core/session')->getData('sponsor_id')) {
                                $idParrain = Mage::getSingleton('core/session')->getData('sponsor_id');
                        }
                }
        }
        return $idParrain;
    }
}