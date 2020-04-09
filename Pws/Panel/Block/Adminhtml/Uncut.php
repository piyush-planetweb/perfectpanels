<?php

namespace Pws\Panel\Block\Adminhtml;

/**
 * Adminhtml cms pages content block
 */
class Uncut extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Block constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_panelcutlist';
        $this->_blockGroup = 'Pws_Panel';
        $this->_headerText = __('Manage Uncut Products');

        parent::_construct();

        if ($this->_isAllowedAction('Pws_Panel::uncut_edit')) {
            $this->buttonList->update('add', 'label', __('Add New Product'));
        } else {
            $this->buttonList->remove('add');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
