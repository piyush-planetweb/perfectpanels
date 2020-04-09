<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Pws_Panel
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Pws\Panel\Block;
use Magento\Customer\Model\Context as CustomerContext;

class PanelList extends \Magento\Framework\View\Element\Template
{
    /**
     * Group Collection
     */
    protected $_panelCollection;

    protected $_collection = null;

	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_panelHelper;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context         
     * @param \Magento\Framework\Registry                      $registry        
     * @param \Pws\Panel\Helper\Data                           $panelHelper     
     * @param \Pws\Panel\Model\Panel                           $panelCollection 
     * @param array                                            $data            
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Pws\Panel\Helper\Data $panelHelper,
        \Pws\Panel\Model\Panel $panelCollection,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
        ) {
        $this->_panelCollection = $panelCollection;
        $this->_panelHelper = $panelHelper;
        $this->_coreRegistry = $registry;
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
    }

    public function _construct(){
        if(!$this->getConfig('general_settings/enable') || !$this->getConfig('panel_block/enable')) return;
        parent::_construct();
        $carousel_layout = $this->getConfig('panel_block/carousel_layout');
        $template = '';
        if($carousel_layout == 'owl_carousel'){
            $template = 'block/panel_list_owl.phtml';
        }else{
            $template = 'block/panel_list_bootstrap.phtml';
        }
        if(!$this->getTemplate() && $template!=''){
            $this->setTemplate($template);
        }
    }

    public function getConfig($key, $default = '')
    {   
        $widget_key = explode('/', $key);
        if( (count($widget_key)==2) && ($resultData = $this->hasData($widget_key[1])) )
        {
            return $this->getData($widget_key[1]);
        }
        $result = $this->_panelHelper->getConfig($key);
        if($result == ""){
            return $default;
        }
        return $result;
    }

    public function getPanelCollection()
    {
        if(!$this->_collection) {
            $number_item = $this->getConfig('panel_block/number_item');
            $panelGroups = $this->getConfig('panel_block/panel_groups');
            $store = $this->_storeManager->getStore();
            $collection = $this->_panelCollection->getCollection()
            ->setOrder('position','ASC')
            ->addStoreFilter($store)
            ->addFieldToFilter('status',1);
            $panelGroups = explode(',', $panelGroups);
            if(is_array($panelGroups) && count($panelGroups)>0)
            {
                $collection->addFieldToFilter('group_id',array('in' => $panelGroups));
            }
            $collection->setPageSize($number_item)
            ->setCurPage(1)
            ->setOrder('position','ASC');
            $this->_collection = $collection;
        }
        return $this->_collection;
    }


    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return [
        'VES_BRAND_LIST',
        $this->_storeManager->getStore()->getId(),
        $this->_design->getDesignTheme()->getId(),
        $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP),
        'template' => $this->getTemplate(),
        $this->getProductsCount()
        ];
    }

    public function _toHtml()
    {
        return parent::_toHtml();
    }
}