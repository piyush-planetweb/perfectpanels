<?php

namespace Pws\Panel\Block\Adminhtml;

/**
 * Adminhtml cms pages content block
 */
class Panel extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Block constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_panel';
        $this->_blockGroup = 'Pws_Panel';
        $this->_headerText = __('Manage Project');

        parent::_construct();

        if ($this->_isAllowedAction('Pws_Panel::panel_save')) {
            $this->buttonList->update('add', 'label', __('Add New Project'));
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
