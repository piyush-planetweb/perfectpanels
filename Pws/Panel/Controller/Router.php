<?php

namespace Pws\Panel\Controller;

use Magento\Framework\App\RouterInterface;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Url;

class Router implements RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Event manager
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * Response
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $response;

    /**
     * @var bool
     */
    protected $dispatched;

    /**
     * Panel Factory
     *
     * @var \Pws\Panel\Model\Panel $panelCollection
     */
    protected $_panelCollection;

    /**
     * Panel Factory
     *
     * @var \Pws\Panel\Model\Group $groupCollection
     */
    protected $_groupCollection;

    /**
     * Panel Helper
     */
    protected $_panelHelper;

    /**
     * Store manager
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param ActionFactory          $actionFactory   
     * @param ResponseInterface      $response        
     * @param ManagerInterface       $eventManager    
     * @param \Pws\Panel\Model\Panel $panelCollection 
     * @param \Pws\Panel\Model\Group $groupCollection 
     * @param \Pws\Panel\Helper\Data $panelHelper     
     * @param StoreManagerInterface  $storeManager    
     */
    public function __construct(
    	ActionFactory $actionFactory,
    	ResponseInterface $response,
        ManagerInterface $eventManager,
        \Pws\Panel\Model\Panel $panelCollection,
        \Pws\Panel\Model\Group $groupCollection,
        \Pws\Panel\Helper\Data $panelHelper,
        StoreManagerInterface $storeManager
        )
    {
    	$this->actionFactory = $actionFactory;
        $this->eventManager = $eventManager;
        $this->response = $response;
        $this->_panelHelper = $panelHelper;
        $this->_panelCollection = $panelCollection;
        $this->_groupCollection = $groupCollection;
        $this->storeManager = $storeManager;
    }
    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface
     */
    public function match(RequestInterface $request)
    {
        $_panelHelper = $this->_panelHelper;
        if (!$this->dispatched) {
            $urlKey = trim($request->getPathInfo(), '/');
            $origUrlKey = $urlKey;
            /** @var Object $condition */
            $condition = new DataObject(['url_key' => $urlKey, 'continue' => true]);
            $this->eventManager->dispatch(
                'pws_panel_controller_router_match_before',
                ['router' => $this, 'condition' => $condition]
                );
            $urlKey = $condition->getUrlKey();
            if ($condition->getRedirectUrl()) {
                $this->response->setRedirect($condition->getRedirectUrl());
                $request->setDispatched(true);
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Redirect',
                    ['request' => $request]
                    );
            }
            if (!$condition->getContinue()) {
                return null;
            }
            $route = $_panelHelper->getConfig('general_settings/route');
            $urlKeyArr = explode(".",$urlKey);
            if(count($urlKeyArr) > 1) {
                $urlKey = $urlKeyArr[0];
            }
            $routeArr = explode(".",$route);
            if(count($routeArr) > 1) {
                $route = $routeArr[0];
            }
            if( $route !='' && $urlKey == $route )
            {
                $request->setModuleName('panel')
                ->setControllerName('index')
                ->setActionName('index');
                $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $urlKey);
                $this->dispatched = true;
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward',
                    ['request' => $request]
                    );
            }
            $url_prefix = $_panelHelper->getConfig('general_settings/url_prefix');
            $url_suffix = $_panelHelper->getConfig('general_settings/url_suffix');
            $url_prefix = $url_prefix?$url_prefix:$route;
            $identifiers = explode('/',$urlKey);

            // SEARCH PAGE
            if(count($identifiers)==2 && $url_prefix == $identifiers[0] && $identifiers[1]=='search'){
                $request->setModuleName('panel')
                ->setControllerName('search')
                ->setActionName('result');
                $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
                $request->setDispatched(true);
                $this->dispatched = true;
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward',
                    ['request' => $request]
                    );
            }
            //Check Group Url
            if( (count($identifiers) == 2 && $identifiers[0] == $url_prefix) || (trim($url_prefix) == '' && count($identifiers) == 1)){
                $panelUrl = '';
                if(trim($url_prefix) == '' && count($identifiers) == 1){
                    $panelUrl = str_replace($url_suffix, '', $identifiers[0]);
                }
                if(count($identifiers) == 2){
                    $panelUrl = str_replace($url_suffix, '', $identifiers[1]);
                }
                if ($panelUrl) {
                    $group = $this->_groupCollection->getCollection()
                    ->addFieldToFilter('status', array('eq' => 1))
                    ->addFieldToFilter('url_key', array('eq' => $panelUrl))
                    ->getFirstItem();

                    if($group && $group->getId()){
                        $request->setModuleName('panel')
                        ->setControllerName('group')
                        ->setActionName('view')
                        ->setParam('group_id', $group->getId());
                        $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
                        $request->setDispatched(true);
                        $this->dispatched = true;
                        return $this->actionFactory->create(
                            'Magento\Framework\App\Action\Forward',
                            ['request' => $request]
                            );
                    } else {
                        $panel = $this->_panelCollection->getCollection()
                                ->addFieldToFilter('status', array('eq' => 1))
                                ->addFieldToFilter('url_key', array('eq' => $panelUrl))
                                ->addStoreFilter([0,$this->storeManager->getStore()->getId()])
                                ->getFirstItem();

                        if($panel && $panel->getId()){
                            $request->setModuleName('panel')
                            ->setControllerName('panel')
                            ->setActionName('view')
                            ->setParam('panel_id', $panel->getId());
                            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
                            $request->setDispatched(true);
                            $this->dispatched = true;
                            return $this->actionFactory->create(
                                'Magento\Framework\App\Action\Forward',
                                ['request' => $request]
                                );
                        }

                    }
                }
            }
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
            $request->setDispatched(true);
            $this->dispatched = true;
            return $this->actionFactory->create(
                'Magento\Framework\App\Action\Forward',
                ['request' => $request]
                );
        }
    }
}