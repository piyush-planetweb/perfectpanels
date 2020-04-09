<?php

namespace Pws\Panel\Controller\Adminhtml\Uncut;

class Delete extends \Pws\Panel\Controller\Adminhtml\Uncut
{
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Pws_Panel::uncut_delete');
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('uncut_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = "";
            try {
                // init model and delete
                $model = $this->_objectManager->create('Pws\Panel\Model\Uncut');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('The Products has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['uncut_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a Product to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }

}