<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Model_Customer_Customer extends Mage_Customer_Model_Customer
{
    public function validate()
    {
        $errors = array();
        if (Zend_Validate::is( trim($this->getIban()) , 'NotEmpty'))
        if (!Zend_Validate::is( trim($this->getIban()) , 'Iban')) {
            $errors[] = Mage::helper('sponsorship')->__('Invalid IBAN code "%s"', $this->getIban());
        }
        /*
         * Vérification du SIRET désactivé pour internationalisation
        if ($siret = $this->getSiret()) {
        	if (!$this->isSiret(trim($siret))) {
	            $errors[] = Mage::helper('customer')->__('Invalid SIRET code');
	        }
        }
         *
         */
                
        if (!Zend_Validate::is( trim($this->getFirstname()) , 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('First name can\'t be empty');
        }

        if (!Zend_Validate::is( trim($this->getLastname()) , 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('Last name can\'t be empty');
        }

        if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
            $errors[] = Mage::helper('customer')->__('Invalid email address "%s"', $this->getEmail());
        }

        $password = $this->getPassword();
        if (!$this->getId() && !Zend_Validate::is($password , 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('Password can\'t be empty');
        }
        if ($password && !Zend_Validate::is($password, 'StringLength', array(6))) {
            $errors[] = Mage::helper('customer')->__('Password minimal length must be more %s', 6);
        }
        $confirmation = $this->getConfirmation();
        if ($password != $confirmation) {
            $errors[] = Mage::helper('customer')->__('Please make sure your passwords match.');
        }

        if (('req' === Mage::helper('customer/address')->getConfig('dob_show'))
            && '' == trim($this->getDob())) {
            $errors[] = Mage::helper('customer')->__('Date of Birth is required.');
        }
        if (('req' === Mage::helper('customer/address')->getConfig('taxvat_show'))
            && '' == trim($this->getTaxvat())) {
            $errors[] = Mage::helper('customer')->__('TAX/VAT number is required.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }
    /*
     * Vérification du SIRET désactivé pour internationalisation
    public function isSiret ($siret)
    {
    	$siret = str_replace ( ' ', '', $siret );
    	if (strlen ( $siret ) != 14 || !is_numeric ( $siret )) {
			return false;
		}
		$siren = substr ( $siret, 0, 9 );
		if (! $this->isSiren ( $siren )) {
			return false;
		}
		$total = 0;
		for($i = 0; $i < 14; $i++) {
			$temp = substr ( $siret, $i, 1 );
			if ($i % 2 == 0) {
				$temp *= 2;
				if ($temp > 9) {
					$temp -= 9;
				}
			}
			$total += $temp;
		}
		return (($total % 10) == 0);
	}
	
	function isSiren($siren)
	{
		$siren = str_replace ( ' ', '', $siren );
		if (strlen ( $siren ) != 9 || !is_numeric ( $siren )) {
			return false;
		}
		$total = 0;
		for($i = 0; $i < 9; $i++) {
			$temp = substr ( $siren, $i, 1 );
			if ($i % 2 == 1) {
				$temp *= 2;
				if ($temp > 9) {
					$temp -= 9;
				}
			}
			$total += $temp;
		}
		return (($total % 10) == 0);
	}
     *
     */
}