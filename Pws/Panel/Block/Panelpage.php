<?php

namespace Pws\Panel\Block;

class Panelpage extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Pws\Panel\Helper\Data
     */
    protected $_panelHelper;

    /**
     * @var \Pws\Panel\Model\Panel
     */
    protected $_panel;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context      
     * @param \Magento\Framework\Registry                      $registry     
     * @param \Pws\Panel\Helper\Data                           $panelHelper  
     * @param \Pws\Panel\Model\Panel                           $panel        
     * @param \Magento\Store\Model\StoreManagerInterface       $storeManager 
     * @param array                                            $data         
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Pws\Panel\Helper\Data $panelHelper,
        \Pws\Panel\Model\Panel $panel,
        array $data = []
        ) {
        $this->_panel = $panel;
        $this->_coreRegistry = $registry;
        $this->_panelHelper = $panelHelper;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        if(!$this->getConfig('general_settings/enable')) return;
        parent::_construct();

        $store = $this->_storeManager->getStore();
        $itemsperpage = (int)$this->getConfig('panel_list_page/item_per_page',12);
        $panel = $this->_panel;
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$customerSession = $om->create('Magento\Customer\Model\Session');
		if($customerSession->isLoggedIn()) {
			$cusid =  $customerSession->getCustomer()->getId();
		}else{
			$cusid= 0;
		}
        $panelCollection = $panel->getCollection()
        ->addFieldToFilter('status',1)
		->addFieldToFilter('url_key',$cusid)
        ->addStoreFilter($store)
        ->setOrder('panel_id','DESC');
        $this->setCollection($panelCollection);

        $template = '';
        $layout = $this->getConfig('panel_list_page/layout');
        if($layout == 'grid'){
            $template = 'panellistpage_grid.phtml';
        }else{
            $template = 'panellistpage_list.phtml';
        }
        if(!$this->hasData('template')){
            $this->setTemplate($template);
        }
    }

	/**
     * Prepare breadcrumbs
     *
     * @param \Magento\Cms\Model\Page $panel
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $panelRoute = $this->_panelHelper->getConfig('general_settings/route');
        $page_title = $this->_panelHelper->getConfig('panel_list_page/page_title');

        if($breadcrumbsBlock){

        $breadcrumbsBlock->addCrumb(
            'home',
            [
            'label' => __('Home'),
            'title' => __('Go to Home Page'),
            'link' => $baseUrl
            ]
            );
        $breadcrumbsBlock->addCrumb(
            'pwspanel',
            [
            'label' => $page_title,
            'title' => $page_title,
            'link' => ''
            ]
            );
        }
    }

    /**
     * Set panel collection
     * @param \Pws\Panel\Model\Panel
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this->_collection;
    }

    /**
     * Retrive panel collection
     * @param \Pws\Panel\Model\Panel
     */
    public function getCollection()
    {
        $this->_collection->getSelect()->reset(\Magento\Framework\DB\Select::ORDER);
        $this->_collection->setOrder('panel_id','DESC');
        return $this->_collection;
    }

    public function getConfig($key, $default = '')
    {
        $result = $this->_panelHelper->getConfig($key);
        if(!$result){

            return $default;
        }
        return $result;
    }

    /**
     * Prepare global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $page_title = $this->getConfig('panel_list_page/page_title');
        $meta_description = $this->getConfig('panel_list_page/meta_description');
        $meta_keywords = $this->getConfig('panel_list_page/meta_keywords');
        $this->_addBreadcrumbs();
        $this->pageConfig->addBodyClass('pws-panellist');
        if($page_title){
            $this->pageConfig->getTitle()->set($page_title);   
        }
        if($meta_keywords){
            $this->pageConfig->setKeywords($meta_keywords);   
        }
        if($meta_description){
            $this->pageConfig->setDescription($meta_description);   
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrieve Toolbar block
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function getToolbarBlock()
    {
        $block = $this->getLayout()->getBlock('pwspanel_toolbar');
        if ($block) {
            $block->setDefaultOrder("panel_id");
            $block->removeOrderFromAvailableOrders("price");
            return $block;
        }
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $collection = $this->getCollection();
        $toolbar = $this->getToolbarBlock();

        // set collection to toolbar and apply sort
        if($toolbar){
            $itemsperpage = (int)$this->getConfig('panel_list_page/item_per_page',12);
            $toolbar->setData('_current_limit',$itemsperpage)->setCollection($collection);
            $this->setChild('toolbar', $toolbar);
        }
        return parent::_beforeToHtml();
    }
}