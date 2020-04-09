<?php

namespace Pws\Panel\Controller\Adminhtml\Panel;

class Uncuts extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
		 * @var \Magento\Framework\View\Result\LayoutFactory
		 */
		protected $resultLayoutFactory;

		public function __construct(
			\Magento\Backend\App\Action\Context $context,
			\Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
			\Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
		) {
			parent::__construct($context,$productBuilder);
			$this->resultLayoutFactory = $resultLayoutFactory;
		}

		/**
		 * @return \Magento\Framework\View\Result\Layout
		 */
		public function execute()
		{
           
			$id = $this->getRequest()->getparam('panel_id');
			$panel = $this->_objectManager->create('Pws\Panel\Model\Panel');
			$panel->load($id);
			$registry = $this->_objectManager->get('Magento\Framework\Registry');
			$registry->register("current_panel", $panel);
			$resultLayout = $this->resultLayoutFactory->create();
			$resultLayout->getLayout()->getBlock('panel.edit.tab.uncuts');
			return $resultLayout;
		}

}
