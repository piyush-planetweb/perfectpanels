<?php

namespace Pws\Panel\Setup;

use Pws\Panel\Model\Panel;
use Pws\Panel\Model\PanelFactory;
use Pws\Panel\Model\Group;
use Pws\Panel\Model\GroupFactory;
use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;

class InstallData implements InstallDataInterface
{
	/**
	 * Panel Factory
	 *
	 * @var PanelFactory
	 */
	private $panelFactory;

	/**
	 * @param PanelFactory $panelFactory 
	 * @param GroupFactory $groupFactory 
	 */
	public function __construct(
		PanelFactory $panelFactory,
		GroupFactory $groupFactory,
		EavSetupFactory $eavSetupFactory
		)
	{
		$this->panelFactory = $panelFactory;
		$this->groupFactory = $groupFactory;
		$this->eavSetupFactory = $eavSetupFactory;
	}

	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
 		$data = array(
 			'group' => 'General',
 			'type' => 'varchar',
 			'input' => 'multiselect',
 			'default' => 1,
 			'label' => 'Product Panel',
 			'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
 			'frontend' => '',
 			'source' => 'Pws\Panel\Model\Panellist',
 			'visible' => 1,
 			'required' => 1,
 			'user_defined' => 1,
 			'used_for_price_rules' => 1,
 			'position' => 2,
 			'unique' => 0,
 			'default' => '',
 			'sort_order' => 100,
 			'is_global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
 			'is_required' => 0,
 			'is_configurable' => 1,
 			'is_searchable' => 0,
 			'is_visible_in_advanced_search' => 0,
 			'is_comparable' => 0,
 			'is_filterable' => 0,
 			'is_filterable_in_search' => 1,
 			'is_used_for_promo_rules' => 1,
 			'is_html_allowed_on_front' => 0,
 			'is_visible_on_front' => 1,
 			'used_in_product_listing' => 1,
 			'used_for_sort_by' => 0,
 			);
 		$eavSetup->addAttribute(
 			\Magento\Catalog\Model\Product::ENTITY,
 			'product_panel',
 			$data);
	}
}
