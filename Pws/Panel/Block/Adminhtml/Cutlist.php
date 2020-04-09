<?php

namespace Pws\Panel\Block\Adminhtml;

/**
 * Adminhtml cms pages content block
 */
class Cutlist extends \Magento\Backend\Block\Widget\Grid\Container
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
        $this->_headerText = __('Manage Cutlist');

        parent::_construct();

        if ($this->_isAllowedAction('Pws_Panel::cutlist_edit')) {
            $this->buttonList->update('add', 'label', __('Add New Cutlist'));
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
