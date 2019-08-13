<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Model_ApplyValidity
{
    public function process ()
    {
    	$pointsTypes = Mage::helper('auguria_sponsorship/config')->getPointsTypes();
    	if (count($pointsTypes)>0) {
    		
	    	$mode = Mage::helper('auguria_sponsorship/config')->getModuleMode();
	    	$validity = Mage::helper('auguria_sponsorship/config')->getPointsValidity();
	    	$date = new Zend_Date();
	    	$datetime = Mage::getModel('core/date')->gmtDate();
	    	$date->subDay(1);
	    	$tomorrow = $date->toString('YYYY-MM-dd');
	    	
    		foreach ($pointsTypes as $type) {
    			$pointsValidity = '';
    			$validityLabel = '';
    			if ($type=='accumulated') {
    				$validityLabel = 'points_validity';
    				$pointsValidity = $validity['accumulated'];
    			}
    			elseif($type=='sponsor') {
    				$validityLabel = 'sponsorship_points_validity';
    				$pointsValidity = $validity['sponsorship'];
    			}
    			elseif($type=='fidelity') {
    				$validityLabel = 'fidelity_points_validity';
    				$pointsValidity = $validity['fidelity'];
    			}
    			else {
    				continue;
    			}
    			
    			if ($pointsValidity > 0) {
	    			//select users with points and outdated validity
		    		$customers = Mage::getResourceModel('customer/customer_collection')
		    						->addAttributeToFilter($type.'_points', array('gt' => 0))
		    						->addAttributeToFilter($validityLabel, array('to' => $tomorrow));
		    								    						
		    		if ($customers->count() > 0) {
		    			foreach ($customers as $customer) {
		    				$points = $customer->getData($type.'_points');	    				
		    				$customer->setData($type.'_points', 0);
		    				$customer->save();
		    				//insert in log and change    				
		    				$log = Mage::getModel('auguria_sponsorship/log');    				
					    	$data = array(
								    'customer_id' => $customer->getId(),
								    'record_type' => 'validity',
								    'datetime' => $datetime,
								    'points' => -$points
					    	);
							$log->setData($data);
							$log->save();
		    			}
		    		}
    			}
    		}
    	}
    }
}