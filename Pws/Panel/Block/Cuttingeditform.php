<?php
namespace Pws\Panel\Block;

use Magento\Framework\View\Element\Template;
 
class Cuttingeditform extends Template
{
        
        
    protected $scopeConfig;
    
    protected $_productCollectionFactory;  
        
    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
                                \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,        
        array $data = [])
    {
        
        $this->scopeConfig = $context->getScopeConfig();
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);
    }
    
    public function getProductCollection()
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $collection->setPageSize(9); // fetching only 10 products
        return $collection;
    }
    
    public function getProductCollectionAll()
    {
        $collection = $this->_productCollectionFactory->create();
       $collection->addAttributeToSelect(['name','sku','id']);
        //Need attribute only selected
        //$collection->setPageSize(10); // fetching only 10 products
        return $collection;
    }
        
        
               
    public function getFormAction()
    {
        return $this->getUrl('pwspanel/cutting/post', ['_secure' => true]);
    }
    
    public function getFormQuoteAction()
    {
        return $this->getUrl('pwspanel/panel/post', ['_secure' => true]);
    }
        
        
        
    public function getAllowedFileExtensions()
    {
                
        $ext = $this->scopeConfig->getValue(self::CONFIG_FILE_EXT_UPLOAD);
        return $ext;
    }
    
}
