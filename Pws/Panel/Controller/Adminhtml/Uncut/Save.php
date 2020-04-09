<?php

namespace Pws\Panel\Controller\Adminhtml\Uncut;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
class Save extends \Magento\Backend\App\Action
{

	protected $_fileSystem;
    /**
     * @param Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Backend\Helper\Js $jsHelper
        ) {
        $this->_fileSystem = $filesystem;
        $this->jsHelper = $jsHelper;
        parent::__construct($context);
    }
	
	/*public function __construct(Action\Context $context)
    {
        parent::__construct($context);
    }*/

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
    	return $this->_authorization->isAllowed('Pws_Panel::uncut_save');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
    	$data = $this->getRequest()->getPostValue();
    	/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
    	$resultRedirect = $this->resultRedirectFactory->create();
    	if ($data) {
			
			
			
    		$model = $this->_objectManager->create('Pws\Panel\Model\Uncut');

    		$id = $this->getRequest()->getParam('uncut_id');

			
			
			$panel_image = "";
            if ($id) {
                $oldcustomer = $data['panel_id'];
                $model->load($id);
				$newcustomer = $model->getPanelId();
				if($oldcustomer !=$newcustomer){
					$this->messageManager->addError('Project Id change not allowed');

					return $resultRedirect->setPath('*/*/edit', ['uncut_id' => $this->getRequest()->getParam('uncut_id')]);
				}
                
            }	
			

            if($data['url_key']=='')
            {
                $data['url_key'] = $data['name'];
            }
            $url_key = $this->_objectManager->create('Magento\Catalog\Model\Product\Url')->formatUrlKey($data['url_key']);
            $data['url_key'] = $url_key;
            
            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved this cutlist.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['uncut_id' => $model->getId(), '_current' => true]);
                }
                //return $resultRedirect->setPath('*/*/');
				return $resultRedirect->setPath('pwspanel/panel/edit', ['panel_id' => $data['panel_id'], '_current' => true]);
				
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Product.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['uncut_id' => $this->getRequest()->getParam('uncut_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
	
}