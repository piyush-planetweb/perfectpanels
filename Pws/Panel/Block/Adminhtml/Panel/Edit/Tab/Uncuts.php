<?php

namespace Pws\Panel\Block\Adminhtml\Panel\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class Uncuts extends \Magento\Backend\Block\Widget\Grid\Extended
	{
		/**
		 * @var \Webkul\Hello\Model\GridFactory
		 */
		protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Model\Product\LinkFactory
     */
    protected $_linkFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory]
     */
    protected $_setsFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_type;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_status;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_visibility;
    
    protected $_uncutCollection;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\Product\LinkFactory $linkFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product\Type $type
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $status
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\Product\LinkFactory $linkFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Pws\Panel\Model\Uncut $uncutCollection,
        \Magento\Catalog\Model\Product\Type $type,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_linkFactory = $linkFactory;
        $this->_setsFactory = $setsFactory;
        $this->_productFactory = $productFactory;
        $this->_uncutCollection = $uncutCollection;
        $this->_type = $type;
        $this->_status = $status;
        $this->_visibility = $visibility;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('uncut_grid');
        $this->setDefaultSort('uncut_id');
        $this->setUseAjax(true);
        //if ($this->getPanel()->getId()) {
        //    $this->setDefaultFilter(['panel_id' => $this->getPanel()->getId()]);
        //}
        if ($this->isReadonly()) {
            $this->setFilterVisibility(false);
        }
    }


		public function getPanel()
    {
        return $this->_coreRegistry->registry('current_panel');
    }

    /**
     * Add filter
     *
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare collection
     *
     * @return Extended
     */
    protected function _prepareCollection()
    {
        
        $currentpanel = $this->_coreRegistry->registry('current_panel');
        if($currentpanel){
            $panelid = $currentpanel->getId();
        }else{
            $panelid = 0;
        }
        $collection = $this->_uncutCollection->getCollection()
        ->addFieldToFilter('panel_id', $panelid);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Checks when this block is readonly
     *
     * @return bool
     */
    public function isReadonly()
    {
        return $this->getPanel() && $this->getPanel()->getRelatedReadonly();
    }

    /**
     * Add columns to grid
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
       

        $this->addColumn(
            'uncut_id',
            [
                'header' => __('UT-ID'),
                'filter' => false,
                'sortable' => false,
                'index' => 'uncut_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        
        $this->addColumn(
            'material',
            [
                'header' => __('Material'),
                'filter' => false,
                'sortable' => false,
                'index' => 'material',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
		
		$this->addColumn(
            'pricefull',
            [
                'header' => __('Price'),
                'filter' => false,
                'sortable' => false,
                'index' => 'pricefull',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        
        $this->addColumn(
            'cqty',
            [
                'header' => __('Qty'),
                'filter' => false,
                'sortable' => false,
                'index' => 'cqty',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        
        $this->addColumn(
            'materialcode',
            [
                'header' => __('Material Code'),
                'filter' => false,
                'sortable' => false,
                'index' => 'materialcode',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        
        $this->addColumn(
            'clength',
            [
                'header' => __('Length'),
                'filter' => false,
                'sortable' => false,
                'index' => 'clength',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        
        $this->addColumn(
            'cwidth',
            [
                'header' => __('Width'),
                'filter' => false,
                'sortable' => false,
                'index' => 'cwidth',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'thickness',
            [
                'header' => __('Thickness'),
                'filter' => false,
                'sortable' => false,
                'index' => 'thickness',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        
        $this->addColumn(
            'cpaneldescription',
            [
                'header' => __('Part Description'),
                'filter' => false,
                'sortable' => false,
                'index' => 'cpaneldescription',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        
        
        
        $this->addColumn('action', array(
            'header' => __('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getUncutId',
            //'renderer' => 'Vendor\Module\Block\Adminhtml\Data\Grid\Renderer\Link',
            'actions' => array(
                array(
                    'caption' => __('Edit/View'),
                    //'url' => array('base' => 'vendor_module/Data/edit'),
                    'url' => array('base' => 'pwspanel/uncut/edit'),
                    'field' => 'uncut_id'
                )
            ), 
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));
        

        return parent::_prepareColumns();
    }

    /**
     * Rerieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getData(
            'grid_url'
        ) ? $this->getData(
            'grid_url'
        ) : $this->getUrl(
            'pwspanel/*/uncutsGrid',
            ['_current' => true]
        );
    }

    /**
     * Retrieve selected related products
     *
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getProductsRelated();
        if (!is_array($products)) {
            $products = array_keys($this->getSelectedPanelProducts());
        }
        return $products;
    }

    /**
     * Retrieve related products
     *
     * @return array
     */
    public function getSelectedPanelProducts()
    {
        $products = [];
        if(!empty($this->_coreRegistry->registry('current_panel')->getData('products'))){
        foreach ($this->_coreRegistry->registry('current_panel')->getData('products') as $product) {
            $products[$product['product_id']] = ['product_position' => $product['position']];
        }
    }
        return $products;
    }
}
