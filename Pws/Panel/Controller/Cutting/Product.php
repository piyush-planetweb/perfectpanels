<?php
namespace Pws\Panel\Controller\Cutting;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
class Product extends \Pws\Panel\Controller\Index
{
        
   
    public function execute()
    {
                
        $data = $this->getRequest()->getPostValue();

		$resultRedirect = $this->resultRedirectFactory->create();
    	if ($data) {
			$qty = 1;
			$price = $data['price'];
			
			$cutlistid = '';
			$projectid = '';
			$projectname = '';
			if($data['projectid'] !==NULL){
				$projectid = $data['projectid'];
			}
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$createdload = $objectManager->create('Pws\Panel\Model\Panel')->load($projectid);
			$projectname = $createdload->getName();
			if($createdload->getDelivery()=='collection'){
				$productid = 4759;
			}else{
				$productid = 4762;
			}

			
			if($createdload->getSubtotalcost() !== $price){
				return $resultRedirect->setPath('');
			}

			
			$product = $objectManager->create('Magento\Catalog\Model\Product')->load($productid);
			$listBlock = $objectManager->get('\Magento\Catalog\Block\Product\ListProduct');
			$cart = $objectManager->create('Magento\Checkout\Model\Cart');
			$session = $objectManager->get('Magento\Checkout\Model\Session');
			$product->setPrice($price);
			
			$customOptions = $objectManager->get('Magento\Catalog\Model\Product\Option')->getProductOptionCollection($product);

			$customOptions = $customOptions->getData();
			$customOptionval= $customOptions[0]['option_id'];
			$customOptionval2= $customOptions[1]['option_id'];
			$cutttinginformation = $projectid;
			$cart->addProduct($product,array('qty' => $qty, 'product' => $productid,
                'options' => array(
                    $customOptionval  => $cutttinginformation, $customOptionval2 => $projectname
                )));
			$cart->save();
			$session->setCartWasUpdated(true);
			

			// retrieve quote items collection
			$itemsCollection = $cart->getQuote()->getItemsCollection();
			
			// get array of all items what can be display directly
			$itemsVisible = $cart->getQuote()->getAllVisibleItems();
			
			// retrieve quote items array
			 $items = $cart->getQuote()->getAllItems();
			
			foreach($items as $item) {
				if($item->getProductId() == $productid && $item->getConvertedPrice() ==$price){
					$item->setCustomPrice($price);
					
					$item->setOriginalCustomPrice($price);
					$item->getProduct()->setIsSuperMode(true);
					$item->save();
				}
			  }

			return $resultRedirect->setPath('checkout/cart/index');
			exit;
						
        }
    }
}
