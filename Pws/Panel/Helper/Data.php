<?php

namespace Pws\Panel\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Group Collection
     */
    protected $_groupCollection;
    
    protected $_cutlineCollection;
    
    protected $_uncutCollection;
    
    //protected $_panelCollection;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * Panel config node per website
     *
     * @var array
     */
    protected $_config = [];

    /**
     * Template filter factory
     *
     * @var \Magento\Catalog\Model\Template\Filter\Factory
     */
    protected $_templateFilterFactory;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    protected $_request;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Pws\Panel\Model\Group $groupCollection,
        \Pws\Panel\Model\Cutlist $cutlineCollection,
        \Pws\Panel\Model\Uncut $uncutCollection,
        //\Pws\Panel\Model\Panellist $panelCollection,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider
        ) {
        parent::__construct($context);
        $this->_filterProvider = $filterProvider;
        $this->_storeManager = $storeManager;
        $this->_groupCollection = $groupCollection;
        $this->_cutlineCollection = $cutlineCollection;
        $this->_uncutCollection = $uncutCollection;
        //$this->_panelCollection = $panelCollection;
        $this->_request            = $context->getRequest();
    }

    public function getGroupList(){
        $result = array();
        $collection = $this->_groupCollection->getCollection()
        ->addFieldToFilter('status', '1');
        foreach ($collection as $panelGroup) {
            $result[$panelGroup->getId()] = $panelGroup->getName();
        }
        return $result;
    }
    
    
    public function getGroupListbynamecus($name,$cusid){
        $result = array();
        $collection = $this->_groupCollection->getCollection()
        ->addFieldToFilter('name', $name)
        ->addFieldToFilter('url_key', $cusid);
        return count($collection);
    }
    
    
    
    public function getPanelListbynamecus($name,$cusid){
        echo $name; die();
        $result = array();
        $collection = $this->_panelCollection->getCollection()
        ->addFieldToFilter('name', $name)
        ->addFieldToFilter('url_key', $cusid);
        return count($collection);
    }
    
    public function getGroupListById($id){
        $result = array();
        $collection = $this->_groupCollection->getCollection()
        ->addFieldToFilter('status', '1')
        ->addFieldToFilter('url_key', $id);
        foreach ($collection as $panelGroup) {
            $result[$panelGroup->getId()] = $panelGroup->getName();
        }
        return $result;
    }
    
    public function getGroupNameById($id){
        $name = '';
        $collection = $this->_groupCollection->getCollection()
        ->addFieldToFilter('group_id', $id);
        foreach ($collection as $panelGroup) {
            $name = $panelGroup->getName();
            break;
        }
        return $name;
    }
    
    public function getCuttingcollection($pid){
        $collectioncut = $this->_cutlineCollection->getCollection()
        ->addFieldToFilter('panel_id', $pid);
        //foreach ($collectioncut as $cutline) {
        //    $cutlineid = $cutline->getCutlineId();
        //    break;
        //}
           return $collectioncut;
    }
    public function getUncutcollection($pid){
        $collectioncut = $this->_uncutCollection->getCollection()
        ->addFieldToFilter('panel_id', $pid);
           return $collectioncut;
    }
    
    
    public function getCuttingbyPid($pid){
        $cutlineid = '';
        $collectioncut = $this->_cutlineCollection->getCollection()
        ->addFieldToFilter('panel_id', $pid);
        foreach ($collectioncut as $cutline) {
            $cutlineid = $cutline->getCutlineId();
            break;
        }
           return $cutlineid;
    }
    

    /**
     * Return panel config value by key and store
     *
     * @param string $key
     * @param \Magento\Store\Model\Store|int|string $store
     * @return string|null
     */
    public function getConfig($key, $store = null)
    {
        $store = $this->_storeManager->getStore($store);
        $websiteId = $store->getWebsiteId();

        $result = $this->scopeConfig->getValue(
            'pwspanel/'.$key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
    }

    public function filter($str)
    {
        $html = $this->_filterProvider->getPageFilter()->filter($str);
        return $html;
    }

    public function getSearchFormUrl(){
        $url        = $this->_storeManager->getStore()->getBaseUrl();
        $url_prefix = $this->getConfig('general_settings/url_prefix');
        $url_suffix = $this->getConfig('general_settings/url_suffix');
        $urlPrefix  = '';
        if ($url_prefix) {
            $urlPrefix = $url_prefix . '/';
        }
        return $url . $urlPrefix . 'search';
    }
    public function getSearchKey(){
        return $this->_request->getParam('s');
    }

}