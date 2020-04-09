<?php

namespace Pws\Panel\Controller\Adminhtml\Panel;

use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{
	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Pws_Panel::panel_edit');
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Pws_Panel::panel')
            ->addBreadcrumb(__('Panel'), __('Panel'))
            ->addBreadcrumb(__('Manage Panels'), __('Manage Panels'));
        return $resultPage;
    }

    /**
     * Edit Panel Page
     */
    public function execute()
    {
    	// 1. Get ID and create model
    	$id = $this->getRequest()->getParam('panel_id');
    	$model = $this->_objectManager->create('Pws\Panel\Model\Panel');

    	// 2. Initial checking
    	if($id){
    		$model->load($id);
    		if(!$model->getId()){
    			$this->messageManager->addError(__('This project no longer exits. '));
    			/** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
    			$resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
    		}
    	}

    	// 3. Set entered data if was error when we do save
    	$data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        $this->_coreRegistry->register('pws_panel', $model);

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Panel') : __('New Project'),
            $id ? __('Edit Panel') : __('New Project')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Projects'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getname() : __('New Project'));

        return $resultPage;
    }
}