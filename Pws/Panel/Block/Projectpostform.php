<?php
namespace Pws\Panel\Block;

use Magento\Framework\View\Element\Template;
 
class Projectpostform extends Template
{
        
        
    protected $scopeConfig;
        
        
    public function __construct(\Magento\Framework\View\Element\Template\Context $context)
    {
        
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
    }
        
        
               
    public function getFormAction()
    {
        return $this->getUrl('pwspanel/group/post', ['_secure' => true]);
    }
        
        
        
    public function getAllowedFileExtensions()
    {
                
        $ext = $this->scopeConfig->getValue(self::CONFIG_FILE_EXT_UPLOAD);
        return $ext;
    }
    
}
