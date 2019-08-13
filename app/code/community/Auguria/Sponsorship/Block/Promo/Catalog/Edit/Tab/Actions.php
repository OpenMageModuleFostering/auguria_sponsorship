<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Auguria_Sponsorship_Block_Promo_Catalog_Edit_Tab_Actions extends Mage_Adminhtml_Block_Promo_Catalog_Edit_Tab_Actions
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_promo_catalog_rule');

        //$form = new Varien_Data_Form(array('id' => 'edit_form1', 'action' => $this->getData('action'), 'method' => 'post'));
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('action_fieldset', array('legend'=>Mage::helper('salesrule')->__('Update prices using the following information')));

        $fieldset->addField('simple_action', 'select', array(
            'label'     => Mage::helper('salesrule')->__('Apply'),
            'name'      => 'simple_action',
            'options'    => array(
                'by_percent' => Mage::helper('salesrule')->__('By Percentage of the original price'),
                'by_fixed' => Mage::helper('salesrule')->__('By Fixed Amount'),
                'to_percent' => Mage::helper('salesrule')->__('To Percentage of the original price'),
                'to_fixed' => Mage::helper('salesrule')->__('To Fixed Amount'),
        		'fidelity_points_to_percent' => $this->__('Fidelity points by percentage of the original price'),
                'fidelity_points_to_fixed' => $this->__('Fidelity points by fixed amount'),
        		'sponsor_points_to_percent' => $this->__('Sponsorship points by percentage of the original price'),
                'sponsor_points_to_fixed' => $this->__('Sponsorship points by fixed amount'),
            ),
        ));

        $fieldset->addField('discount_amount', 'text', array(
            'name' => 'discount_amount',
            'required' => true,
            'class' => 'validate-not-negative-number',
            'label' => Mage::helper('salesrule')->__('Discount amount'),
        ));

        $fieldset->addField('stop_rules_processing', 'select', array(
            'label'     => Mage::helper('salesrule')->__('Stop further rules processing'),
            'title'     => Mage::helper('salesrule')->__('Stop further rules processing'),
            'name'      => 'stop_rules_processing',
            'options'    => array(
                '1' => Mage::helper('salesrule')->__('Yes'),
                '0' => Mage::helper('salesrule')->__('No'),
            ),
        ));

        $form->setValues($model->getData());

        //$form->setUseContainer(true);

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);

        //return parent::_prepareForm();
        return Mage_Adminhtml_Block_Widget_Form::_prepareForm();
        
    }
}
