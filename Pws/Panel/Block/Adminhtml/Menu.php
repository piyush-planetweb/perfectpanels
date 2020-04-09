<?php

namespace Pws\Panel\Block\Adminhtml;

use Pws\All\Model\Config;

class Menu extends \Magento\Backend\Block\Template
{
    /**
     * @var null|array
     */
    protected $items = null;

    /**
     * Block template filename
     *
     * @var string
     */
    //protected $_template = 'Pws_All::menu.phtml';


    public function __construct(\Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context);

    }//end __construct()


    public function getMenuItems()
    {
        if ($this->items === null) {
            $items = [
                      'panel' => [
                            'title' => __('Manage Panels'),
                            'url' => $this->getUrl('*/panel/index'),
                            'resource' => 'Pws_Panel::panel',
                            'child' => [
                                'panel/new/' => [
                                    'title' => __('New Panel'),
                                    'url' => $this->getUrl('*/panel/new/'),
                                    'resource' => 'Pws_Panel::panel_edit',
                                ]
                            ]
                        ],
                        'group' => [
                            'title' => __('Manage Groups'),
                            'url' => $this->getUrl('*/group/index'),
                            'resource' => 'Pws_Panel::group',
                            'child' => [
                                'group/new' => [
                                    'title' => __('New Group'),
                                    'url' => $this->getUrl('*/group/new'),
                                    'resource' => 'Pws_Panel::group_edit',
                                ]
                            ]
                        ],
                        'import' => [
                                     'title'    => __('Import Panels Products'),
                                     'url'      => $this->getUrl('*/import/index'),
                                     'resource' => 'Pws_Panel::import',
                                    ],
                      'settings' => [
                                     'title'    => __('Settings'),
                                     'url'      => $this->getUrl('adminhtml/system_config/edit/section/pwspanel'),
                                     'resource' => 'Pws_Panel::config_panel',
                                    ],
                      'support'  => [
                                     'title' => __('Get Support'),
                                     'url'   => Config::LANDOFCODER_TICKET,
                                     'attr'  => ['target' => '_blank'],
                                     'separator' => true,
                                    ],
                     ];
            foreach ($items as $index => $item) {
                if (array_key_exists('resource', $item)) {
                    if (!$this->_authorization->isAllowed($item['resource'])) {
                        unset($items[$index]);
                    }
                }
            }

            $this->items = $items;
        }//end if

        return $this->items;

    }//end getMenuItems()


    /**
     * @return array
     */
    public function getCurrentItem()
    {
        $items          = $this->getMenuItems();
        $controllerName = $this->getRequest()->getControllerName();
        $actionName     = $this->getRequest()->getActionName();

        $key = $controllerName . '/' . $actionName;
        if (array_key_exists($key, $items)) {
            return $items[$key];
        }

        if (array_key_exists($controllerName, $items)) {
            return $items[$controllerName];
        }

        return $items['page'];

    }//end getCurrentItem()


    /**
     * @param array $item
     * @return string
     */
    public function renderAttributes(array $item)
    {
        $result = '';
        if (isset($item['attr'])) {
            foreach ($item['attr'] as $attrName => $attrValue) {
                $result .= sprintf(' %s=\'%s\'', $attrName, $attrValue);
            }
        }

        return $result;

    }//end renderAttributes()


    /**
     * @param $itemIndex
     * @return bool
     */
    public function isCurrent($itemIndex)
    {
        $controllerName = $this->getRequest()->getControllerName();
        $actionName     = $this->getRequest()->getActionName();
        $key = $controllerName . '/' . $actionName;
        if ($key == $itemIndex) {
            return true;
        }
        return $itemIndex == $this->getRequest()->getControllerName();

    }//end isCurrent()


}//end class
