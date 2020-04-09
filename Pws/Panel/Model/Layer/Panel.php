<?php

namespace Pws\Panel\Model\Layer;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Resource;

class Panel extends \Magento\Catalog\Model\Layer
{
    /**
     * Retrieve current layer product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection()
    {
    	$panel = $this->getCurrentPanel();
    	if(isset($this->_productCollections[$panel->getId()])){
    		$collection = $this->_productCollections;
    	}else{
    		$collection = $panel->getProductCollection();
    		$this->prepareProductCollection($collection);
            $this->_productCollections[$panel->getId()] = $collection;
    	} 
    	return $collection;
    }

    /**
     * Retrieve current category model
     * If no category found in registry, the root will be taken
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentPanel()
    {
    	$panel = $this->getData('current_panel');
    	if ($panel === null) {
    		$panel = $this->registry->registry('current_panel');
    		if ($panel) {
    			$this->setData('current_panel', $panel);
    		}
    	}
    	return $panel;
    }
}