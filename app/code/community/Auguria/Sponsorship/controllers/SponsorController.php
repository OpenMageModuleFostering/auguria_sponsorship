<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_SponsorController extends Mage_Core_Controller_Front_Action
{
	public function preDispatch()
    {
        parent::preDispatch();
    }
    
	public function indexAction()
    {
    	/*
    	 * Transmission de l'id du parrain + nom + prenom dans l'url
    	 * http://www.inkonso.com/index.php/sponsorship/sponsor_id/xxx/nom/xxx/prenom/xxx
        */
    	$sponsorId = $this->getRequest()->getParam('sponsor_id');    	
    	if ($sponsorId!='')
    	{
    		$nom = $this->getRequest()->getParam('nom');
        	$prenom = $this->getRequest()->getParam('prenom');
        	$email = $this->getRequest()->getParam('email');
    		
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
    	}
    	//redirection vers la page d'accueil
    	$this->_redirect('');
    }
}