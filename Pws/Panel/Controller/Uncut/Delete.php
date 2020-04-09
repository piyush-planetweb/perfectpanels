<?php

namespace Pws\Panel\Controller\Uncut;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
class Delete extends \Pws\Panel\Controller\Index
{

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
       $id = $this->getRequest()->getParam('id');
       $pid = $this->getRequest()->getParam('pid');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id && $pid) {
            $title = "";
            try {
                
                // init model and delete
                $model = $this->_objectManager->create('Pws\Panel\Model\Uncut');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('The Product has been deleted.'));
                return $resultRedirect->setPath('*/cutting/add', ['id' => $pid]);
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/add', ['id' => $pid]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a Product to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }

}