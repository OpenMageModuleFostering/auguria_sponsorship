<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Helper_Mail extends Mage_Core_Helper_Abstract
{
	public function getStoreName()
	{
		return Mage::app()->getGroup()->getName();
	}

	public function getSubject ()
	{
		return $this->__("Invitation to %s",$this->getStoreName());
	}

	public function getHeaderMessage ($prenom, $nom)
	{
		return $this->__('Dear %s %s,',$prenom, $nom);
	}

	public function getMessage ()
	{
		return $this->__('You should visit this website. It offers interesting products.');
	}

	public function getSponsorUrl ($id, $prenom, $nom, $email)
	{
		return  Mage::getUrl('sponsorship/sponsor',Array ('sponsor_id'=> $id, 'nom'=>$nom , 'prenom'=>$prenom, 'email'=>$email));

	}

	public function getFooterMessage ($id='',$prenom='', $nom='', $email='')
	{
		$url = $this->getSponsorUrl($id, $prenom, $nom, $email);
		return $this->__("<a href='%s'>%s</a>", $url, $this->getUrlWtHttp());
	}

	public function getUrlWtHttp()
	{
		$patterns = array();
		$patterns[0] = '/^http:\/\//i';
		$patterns[1] = '/index.php\/*/';
		$patterns[2] = '/\/$/';
		$url = preg_replace($patterns, "", Mage::getUrl());
		return $url;
	}

	public function recipientMailIsCustomer($mail)
	{
		//check if email is registred
		$customer = mage::getModel("customer/customer")
		->getCollection()
		->addAttributeToFilter("email",$mail);
		if (count($customer)==1)
		{
			//check if recipient is already a sponsor
			//check if we can invit registred user
			if (Mage::getStoreConfig('auguria_sponsorship/sponsor/allow_invit_registred_users')) {
				//check if registred user has already ordered
				if (Mage::helper('auguria_sponsorship')->haveOrder($customer->getFirstItem()->getId())) {
					return true;
				}
				//check if registred user has godchild
				elseif(Mage::helper('auguria_sponsorship')->isASponsor($customer->getFirstItem()->getId())) {
					return true;
				}
			}
			else {
				return true;
			}
		}
		return false;
	}

	public function processMail($post)
	{
		//recupération des données fixes pour tous les mails
		//sender
		$sender_name = ucwords(strtolower($post["sender"]["name"]));
		$sender_email = $post['sender']['email'];
		$sender_id = $post['sender']['id'];

		//message
		$subject = $post['message']['subject'];
		$body = $post['message']['body'];

		$date = now();

		//Boucle pour traiter les champs, les valider et les intégrer dans tableau mail
		$i = 0;
		$mails = Array();
		$valid = true;
		if (isset($post['recipient']['email']))
			foreach ($post['recipient']['email'] as $recipient_email)
			{
				$mails[$i]["sender_name"] = $sender_name;
				$mails[$i]["sender_email"] = $sender_email;
				$mails[$i]["sender_id"] = $sender_id;

				$recipient_firstname = ucwords(strtolower($post['recipient']['firstname'][$i]));
				$recipient_lastname = ucwords(strtolower($post['recipient']['lastname'][$i]));
				$mails[$i]["recipient_firstname"] = $recipient_firstname;
				$mails[$i]["recipient_lastname"] = $recipient_lastname;
				$mails[$i]["recipient_email"] = $recipient_email;

				//si header indiqué : construction du header et intégration au message
				$mails[$i]["subject"] = $subject;
				$htmlMessage = "";
				//si un id est envoyé : c'est une modification
				if (isset ($post['sponsorship_id']))
				{
					$mails[$i]["sponsorship_id"] = $post['sponsorship_id'];
					$htmlMessage = $body;
					$mails[$i]["datetime_boost"] = $date;
				}
				else //creation
				{
					$mails[$i]["datetime"] = $date;
					$header = $this->getHeaderMessage ($recipient_firstname, $recipient_lastname);
					$htmlMessage = $header."<br/><br/>".$body;
				}

				$mails[$i]["html_message"] = $htmlMessage;
				$htmlFooter = $this->getFooterMessage($sender_id, $recipient_firstname, $recipient_lastname, $recipient_email);
				$mails[$i]["sponsorship_url"] = $this->getSponsorUrl($sender_id, $recipient_firstname, $recipient_lastname, $recipient_email);
				$mails[$i]["html_footer"] = $htmlFooter;

				$i++;
			}
			return $mails;
	}
	public function validateMail($mails)
	{
		$valid = true;
		foreach ($mails as $mail)
		{
			//Sender
			$sender_name = $mail['sender_name'];
			$sender_email = $mail['sender_email'];

			//Recipient
			$recipient_email = $mail['recipient_email'];
			$recipient_firstname = $mail['recipient_firstname'];
			$recipient_lastname = $mail['recipient_lastname'];

			//Message
			$htmlMessage = $mail['html_message'];
			$subject = $mail['subject'];

			if (!Zend_Validate::is(trim($sender_name) , 'NotEmpty')) {
				$valid = false;
			}

			if (!Zend_Validate::is(trim($sender_email) , 'NotEmpty')) {
				$valid = false;
			}

			if (!Zend_Validate::is(trim($sender_email), 'EmailAddress')) {
				$valid = false;
			}

			if (!Zend_Validate::is(trim($htmlMessage) , 'NotEmpty')) {
				$valid = false;
			}

			if (!Zend_Validate::is(trim($subject) , 'NotEmpty')) {
				$valid = false;
			}

			if (!Zend_Validate::is(trim($recipient_email), 'EmailAddress')) {
				$valid = false;
			}

			if (!Zend_Validate::is(trim($recipient_firstname) , 'NotEmpty')) {

				$valid = false;
			}

			if (!Zend_Validate::is(trim($recipient_lastname) , 'NotEmpty')) {

				$valid = false;
			}
		}
		return $valid;
	}

	public function saveMail($mail)
	{
		try
		{
			$invit = "";
			$data = Array();
			//si sponsorship_id est envoyé : c'est un update
			if (isset ($mail['sponsorship_id']))
			{
				$invit = mage::getModel("auguria_sponsorship/sponsorship")->load($mail["sponsorship_id"]);
				$data = $invit->getData();
				$data["datetime_boost"] = $mail['datetime_boost'];
			}
			else
			{
				$invit = mage::getModel("auguria_sponsorship/sponsorship");
				$data["datetime"] = $mail['datetime'];
			}
			$data["parent_id"] = $mail["sender_id"];
			$data["child_mail"] = $mail["recipient_email"];
			$data["child_firstname"] = $mail["recipient_firstname"];
			$data["child_lastname"] = $mail["recipient_lastname"];
			$data["message"] = $mail["html_message"];
			$data["parent_mail"] = $mail["sender_email"];
			$data["parent_name"] = $mail["sender_name"];
			$data["subject"] = $mail["subject"];

			$invit->setData($data);
			$invit->save();
			return true;
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	public function sendMail($mail)
	{
		//generation du message en texte
		$htmlMessage = $mail["html_message"];
		$htmlFooter = $mail["html_footer"];
		$sender_name = $mail["sender_name"];
		$sender_email = $mail["sender_email"];
		$sender = array("name"=>$sender_name, "email"=>$sender_email);
		$recipient_email = $mail["recipient_email"];
		$recipient = $mail["recipient_firstname"]." ".$mail["recipient_lastname"];

		$mailTemplate = Mage::getModel('auguria_sponsorship/Core_Email_Template');

		$postObject = new Varien_Object();
		$postObject->setData(Array ("sender_name" => $sender_name,
				"sender_email" => $sender_email,
				"subject" => $mail["subject"],
				"html_message" => $htmlMessage,
				"html_footer" => $htmlFooter,
				"recipient_email" => $recipient_email,
				"sponsorship_url" => $mail["sponsorship_url"]
		));
		$mailTemplate->setDesignConfig(array('area' => 'fronted'))
		->setReplyTo($sender_email)
		->setReturnPath($sender_email)
		->sendTransactional(
				Mage::getStoreConfig('auguria_sponsorship/invitation/template'),
				$sender,
				$recipient_email,
				$recipient,
				array('data' => $postObject
				));
		if ($mailTemplate->getSentSuccess())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
