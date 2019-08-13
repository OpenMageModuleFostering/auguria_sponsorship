<?php
/**
 * @category   Auguria
 * @package    Auguria_Sponsorship
 * @author     Auguria
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;
$installer->startSetup();
$date = now();
$storeId = Mage::app()->getStore()->getId();
$installer->run("

CREATE TABLE IF NOT EXISTS {$this->getTable('sponsorship')} (
  `sponsorship_id` int(11) unsigned NOT NULL auto_increment,
  `parent_id` int(10) unsigned NOT NULL,
  `child_mail` varchar(255) NOT NULL default '',
  `child_firstname` varchar(255) NOT NULL default '',
  `child_lastname` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`sponsorship_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$this->getTable('cms_page')}`
    (`title`, `root_template`, `meta_keywords`, `meta_description`, `identifier`, `content_heading`, `content`, `creation_time`,
        `update_time`, `is_active`, `sort_order`, `layout_update_xml`, `custom_theme`, `custom_theme_from`,
        `custom_theme_to`)
VALUES (
'Sample of sponsorship principles', 'two_columns_right', 'Sponsorship', 'Information on sponsorship', 'sponsorship_info',
'Sample of sponsorship principles',
 '<p>By sponsoring friends you can earn:</p>\r\n<ul class=\"disc\">\r\n<li>Cash</li>\r\n<li>Vouchers</li>\r\n</ul>\r\n<p>Every time one of your godson order, you win 5% of its order !</p>\r\n<p>But that\'s not all, if your godson sponsors too, you earn 50% of what your godson has won...</p>\r\n<p>For example,</p>\r\n<ul class=\"disc\">\r\n<li>your godson place an order of 100 euros (you win 5 points)</li>\r\n<li>your godson sponsors 2 friends who order 100 euros each (you win 5 points)</li>\r\n<li>the godchildren of your godson each sponsor 2 people who order 100 euros each (you win 5 points)...</li>\r\n</ul>\r\n<p>Then you can exchange your points into cash or vouchers.</p>\r\n<p style=\"text-align:right;\"><a href=\"../sponsorship\">Yes I want sponsors friends to earn cash or vouchers !</a></p>',
 '$date', '$date', 1, 0, '', '', NULL , NULL
);

INSERT INTO `{$this->getTable('cms/page_store')}` (`page_id`, `store_id`) VALUES
(LAST_INSERT_ID(), $storeId);
    ");
    
$installer->endSetup(); 
