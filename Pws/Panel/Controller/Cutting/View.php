<?php

namespace Pws\Panel\Controller\Cutting;

use Magento\Customer\Controller\AccountInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Pws\Panel\Model\Layer\Resolver;

class View extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Pws\Panel\Model\Panel
     */
    protected $_panelModel;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Catalog Layer Resolver
     *
     * @var Resolver
     */
    private $layerResolver;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Pws\Panel\Helper\Data
     */
    protected $_panelHelper;

    /**
     * @param Context                                             $context              [description]
     * @param \Magento\Store\Model\StoreManager                   $storeManager         [description]
     * @param \Magento\Framework\View\Result\PageFactory          $resultPageFactory    [description]
     * @param \Pws\Panel\Model\Panel                              $panelModel           [description]
     * @param \Magento\Framework\Registry                         $coreRegistry         [description]
     * @param Resolver                                            $layerResolver        [description]
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory [description]
     * @param \Pws\Panel\Helper\Data                              $panelHelper          [description]
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Pws\Panel\Model\Panel $panelModel,
        \Magento\Framework\Registry $coreRegistry,
        Resolver $layerResolver,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Pws\Panel\Helper\Data $panelHelper
        ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_panelModel = $panelModel;
        $this->layerResolver = $layerResolver;
        $this->_coreRegistry = $coreRegistry;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_panelHelper = $panelHelper;
    }

    public function _initPanel()
    {
        $panelId = (int)$this->getRequest()->getParam('panel_id', false);
        if (!$panelId) {
            return false;
        }
        try{
            $panel = $this->_panelModel->load($panelId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
        $this->_coreRegistry->register('current_panel', $panel);
        return $panel;
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if(!$this->_panelHelper->getConfig('general_settings/enable')){
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $panel = $this->_initPanel();
        if ($panel) {
            $this->layerResolver->create('panel');
            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $page = $this->resultPageFactory->create();
            // apply custom layout (page) template once the blocks are generated
            if ($panel->getPageLayout()) {
                $page->getConfig()->setPageLayout($panel->getPageLayout());
            }
            $page->addHandle(['type' => 'VES_BRAND_'.$panel->getId()]);
            if (($layoutUpdate = $panel->getLayoutUpdateXml()) && trim($layoutUpdate)!='') {
                $page->addUpdate($layoutUpdate);
            }

            $page->getConfig()->addBodyClass('page-products')
            ->addBodyClass('panel-' . $panel->getUrlKey());
            return $page;
        }elseif (!$this->getResponse()->isRedirect()) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
    }
}