<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Model_AutoBoost
{
    public function process ()
    {
        /*
         *  Selection des invitations n'ayant pas été relancées
         *  Suivant les paramètres de délai avant relance et de validité des invitations
         */
        $resource = Mage::getSingleton('core/resource');
	$read = $resource->getConnection('core_read');
        $select = $read->select()
		->from(Array("s"=>$resource->getTableName('sponsorship/sponsorship')),
                            Array("*"=>"s.*"))
                ->where('isnull(datetime_boost)')
                ->where('TO_DAYS(NOW()) - TO_DAYS(datetime) >= ?', Mage::getStoreConfig('sponsorship/invitation_email/time_before_boost'))
                ->where('TO_DAYS(NOW()) - TO_DAYS(datetime) <= ?', Mage::getStoreConfig('sponsorship/invitation_email/sponsor_invitation_validity'));

        $resultats = $read->fetchAll($select);
        foreach ($resultats as $resultat)
        {
            $mailHelper = mage::helper("sponsorship/mail");
            $mail["sponsorship_id"] = $resultat["sponsorship_id"];
            $mail["subject"] = $resultat["subject"];
            $mail["html_message"] = $resultat["message"];
            $mail["text_message"] =  $mailHelper->htmlToText($resultat["message"]);
            
            $mail["html_footer"] = $mailHelper->getFooterMessage($resultat["parent_id"],  $resultat["child_firstname"], $resultat["child_lastname"], $resultat["child_mail"]);
            $mail["text_footer"] = $mailHelper->htmlToText($mail["html_footer"]);

            $mail["sponsorship_url"] = $mailHelper->getSponsorUrl($resultat["parent_id"],  $resultat["child_firstname"], $resultat["child_lastname"], $resultat["child_mail"]);

            $mail["sender_name"] = $resultat["parent_name"];
            $mail["sender_email"] = $resultat["parent_mail"];
            $mail["sender_id"] = $resultat["parent_id"];
            $mail["recipient_email"] = $resultat["child_mail"];
            $mail["recipient_firstname"] = $resultat["child_firstname"];
            $mail["recipient_lastname"] = $resultat["child_lastname"];
            $mail['datetime_boost'] = now();

            if ($mailHelper->sendMail($mail))
            {
                try
                {
                    $sponsorship = mage::getModel("sponsorship/sponsorship")->load($mail["sponsorship_id"]);
                    $sponsorship->setDatetimeBoost($mail['datetime_boost']);
                    $sponsorship->save();
                    mage::log("1 mail sent by autoboost");
                }
                catch (Exception $e)
                {
                    mage::log($e);
                }
            }
        }
    }
}