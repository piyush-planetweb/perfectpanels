<?php
namespace Pws\Panel\Controller\Uncut;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
class Post extends \Pws\Panel\Controller\Index
{
        
   
    public function execute()
    {
                
        $data = $this->getRequest()->getPostValue();
    	$resultRedirect = $this->resultRedirectFactory->create();
    	if ($data) {

    		$model = $this->_objectManager->create('Pws\Panel\Model\Uncut');

    		$id = $this->getRequest()->getParam('id');
			$panel_image = "";
            if ($id) {
				$panel_image = $model->getImage();
                $model->load($id);
            }

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved this Product.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/cutting/add', ['id' => $data['panel_id'], '_current' => true]);
                }
                 return $resultRedirect->setPath('*/cutting/add', ['id' => $data['panel_id'], '_current' => true]);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Product.'));
            }

            return $resultRedirect->setPath('*/cutting/add', ['id' => $data['panel_id']]);
        }
        return $resultRedirect->setPath('*/cutting/add', ['id' => $data['panel_id']]);
    }
	
}
