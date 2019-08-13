<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Model_Core_Email_Template extends Mage_Core_Model_Email_Template
{
	public function send($email, $name=null, array $variables = array())
	{
		if(!$this->isValidForSend()) {
			return false;
		}

		if (is_null($name)) {
			$name = substr($email, 0, strpos($email, '@'));
		}

		$variables['email'] = $email;
		$variables['name'] = $name;

		ini_set('SMTP', Mage::getStoreConfig('system/smtp/host'));
		ini_set('smtp_port', Mage::getStoreConfig('system/smtp/port'));

		$mail = $this->getMail();
		if (is_array($email)) {
			foreach ($email as $emailOne) {
				$mail->addTo($emailOne, $name);
			}
		} else {
			$mail->addTo($email, '=?utf-8?B?'.base64_encode($name).'?=');
		}

		$this->setUseAbsoluteLinks(true);
		
		$html = $this->getProcessedTemplate($variables, true);
		$mail->setBodyHTML($html);

		$mail->setSubject('=?utf-8?B?'.base64_encode($this->getProcessedTemplateSubject($variables)).'?=');
		$mail->setFrom($this->getSenderEmail(), $this->getSenderName());

		try {
			$mail->send(); // Zend_Mail warning..
			$this->_mail = null;
		}
		catch (Exception $e)
		{
			Mage::log('An error occured while sending mail : '.$e->getMessage());
			return false;
		}

		return true;
	}
}