<?php

namespace Pws\Panel\Controller\Adminhtml\Cutlist;

class Index extends \Pws\Panel\Controller\Adminhtml\Cutlist
{
	/**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Pws_Panel::cutlist');
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
		$resultPage->getConfig()->getTitle()->prepend(__('Cutlist'));

		/**
		 * Add breadcrumb item
		 */
		$resultPage->addBreadcrumb(__('Panels'),__('Panels'));
		$resultPage->addBreadcrumb(__('Manage Panels'),__('Manage Cutlist'));

		return $resultPage;
	}
}