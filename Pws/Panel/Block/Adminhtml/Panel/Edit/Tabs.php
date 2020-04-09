<?php

namespace Pws\Panel\Block\Adminhtml\Panel\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Project Information'));
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareLayout()
    {
        $this->addTab(
                'general',
                [
                    'label' => __('Project Information'),
                    'content' => $this->getLayout()->createBlock('Pws\Panel\Block\Adminhtml\Panel\Edit\Tab\Main')->toHtml()
                ]
            );

        $this->addTab(
                'products',
                [
                    'label' => __('Cutlists'),
                    'url' => $this->getUrl('pwspanel/*/products', ['_current' => true]),
                    'class' => 'ajax'
                ]
            );

        $this->addTab(
            'uncuts',
            [
                'label' => __('Full Sheets'),
                'url' => $this->getUrl('pwspanel/*/uncuts', ['_current' => true]),
                'class' => 'ajax'
            ]
        );

        return parent::_prepareLayout();
    }
}
