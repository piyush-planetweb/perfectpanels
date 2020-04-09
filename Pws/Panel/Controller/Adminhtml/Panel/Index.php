<?php

namespace Pws\Panel\Controller\Adminhtml\Panel;

class Index extends \Pws\Panel\Controller\Adminhtml\Panel
{
	/**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Pws_Panel::panel');
    }

	/**
	 * Panel list action
	 *
	 * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Forward
	 */
	public function execute()
	{

		$resultPage = $this->resultPageFactory->create();

		/**
		 * Set active menu item
		 */
		$resultPage->setActiveMenu("Pws_Panel::panel");
		$resultPage->getConfig()->getTitle()->prepend(__('Projects'));

		/**
		 * Add breadcrumb item
		 */
		$resultPage->addBreadcrumb(__('Projects'),__('Projects'));
		$resultPage->addBreadcrumb(__('Manage Projects'),__('Manage Projects'));

		return $resultPage;
	}
	
}