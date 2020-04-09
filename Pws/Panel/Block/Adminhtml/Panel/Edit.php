<?php

namespace Pws\Panel\Block\Adminhtml\Panel;

/**
 * Panel edit block
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize panel edit block
     *
     * @return void
     */
    protected function _construct(){
    	$this->_objectId = 'panel_id';
    	$this->_blockGroup = 'Pws_Panel';
    	$this->_controller = 'adminhtml_panel';

    	parent::_construct();

    	if($this->_isAllowedAction('Pws_Panel::panel_save')){
    		$this->buttonList->update('save','label',__('Save Project'));
    		/*$this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );*/
    	}else{
    		$this->buttonList->remove('save');
    	}
		
		$message = __('Are you sure to you wish to EXPORT the Cutlist to xls format for Magicut?');
		$this->buttonList->add(
            'export',
            [
				'label' => __('Export Project'),
				'class' => 'save',
				//'onclick' => 'setLocation(\'' . $this->getUrl('*/*/export') . '\')',
				'on_click' => "confirmSetLocation('{$message}', '{$this->getExportUrl()}')",
				'style' => 'background-color: #eb5202; border-color: #b84002; color: #fff;text-decoration: none;'
            ]
		);
		
		
		$message = __('Are you sure you wish to send an email with the quote to the customer? <br/>Please make sure you have added all the details before sending.');
		$this->buttonList->add(
            'email',
            [
				'label' => __('Send Email'),
				'class' => 'save',
				//'onclick' => 'setLocation(\'' . $this->getUrl('*/*/export') . '\')',
				'on_click' => "confirmSetLocation('{$message}', '{$this->getEmailUrl()}')",
				'style' => 'background-color: #eb5202; border-color: #b84002;color: #fff;text-decoration: none;'
            ]
		);
		
		
		
    	if ($this->_isAllowedAction('Pws_Panel::panel_delete')) {
            //$this->buttonList->update('delete', 'label', __('Delete Project'));
			//$this->buttonList->remove('delete');
        } else {
            $this->buttonList->remove('delete');
        }
    }
	public function getExportUrl()
	{
		return $this->getUrl('*/*/export', ['panel_id' => $this->_coreRegistry->registry('pws_panel')->getId()]);
	}
	
	public function getEmailUrl()
	{
		return $this->getUrl('*/*/sendmail', ['panel_id' => $this->_coreRegistry->registry('pws_panel')->getId()]);
	}

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('pws_panel')->getId()) {
            return __("Edit Project '%1'", $this->escapeHtml($this->_coreRegistry->registry('pws_panel')->getName()));
        } else {
            return __('New Project');
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

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('cms/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            };
        ";
        return parent::_prepareLayout();
    }
}
