<?php

namespace Pws\Panel\Controller\Adminhtml\Panel;

class Delete extends \Magento\Backend\App\Action
{

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Pws_Panel::panel_delete');
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('panel_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = "";
            try {
                // init model and delete
                $model = $this->_objectManager->create('Pws\Panel\Model\Panel');
                $model->load($id);
                $title = $model->getTitle();
                if($model->getStatus()){
                    $this->messageManager->addError(__('Only Disabled project can be deleted'));
                    return $resultRedirect->setPath('*/*/');
                }
                
                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('The project has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['panel_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a project to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }

}