<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
require_once (Mage::getModuleDir('', 'Auguria_Sponsorship').'/Lib/OpenInviter/openinviter.php');
class Auguria_Sponsorship_Model_Openinviter
{
	public $inviter;
	
	public function __construct()
	{
		$this->inviter = new OpenInviter();
	}
    
    public function getOpenIniviterPlugins()
    {
		return $this->inviter->getPlugins();
    }
    
    public function getOpenInviterTypes()
    {
    	return $this->inviter->pluginTypes;
    }
    
    public function startPlugin($provider_box, $getPlugins=false)
    {
    	return $this->inviter->startPlugin($provider_box, $getPlugins);
    }
    
    public function getInternalError()
    {
    	return $this->inviter->getInternalError();
    }
    
    public function getSessionID()
    {
    	return $this->inviter->plugin->getSessionID();
    }
    
    public function getMyContacts()
    {
    	return $this->inviter->getMyContacts();
    }
    
    public function login($email_box ,$password_box)
    {
    	return $this->inviter->login($email_box, $password_box);
    }
}