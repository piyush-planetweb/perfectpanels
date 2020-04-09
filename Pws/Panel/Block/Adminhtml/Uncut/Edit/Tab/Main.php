<?php
namespace Pws\Panel\Block\Adminhtml\Uncut\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	/**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
    protected $_wysiwygConfig1;

    /**
     * @param \Magento\Backend\Block\Template\Context $context       
     * @param \Magento\Framework\Registry             $registry      
     * @param \Magento\Framework\Data\FormFactory     $formFactory   
     * @param \Magento\Store\Model\System\Store       $systemStore   
     * @param \Magento\Cms\Model\Wysiwyg\Config       $wysiwygConfig 
     * @param array                                   $data          
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_wysiwygConfig1 = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm() {
    	/** @var $model \Pws\Panel\Model\Panel */
    	$model = $this->_coreRegistry->registry('pws_panel');

    	/**
    	 * Checking if user have permission to save information
    	 */
    	if ($this->_isAllowedAction('Pws_Panel::uncut_edit')) {
    		$isElementDisabled = false;
    	} else {
    		$isElementDisabled = true;
    	}

    	/** @var \Magento\Framework\Data\Form $form */
    	$form = $this->_formFactory->create();

    	$form->setHtmlIdPrefix('panel_');

    	$fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Product Information')]);

    	if ($model->getId()) {
    		$fieldset->addField('uncut_id', 'hidden', ['name' => 'uncut_id']);
    	}
		/*$fieldset->addField('cutline_id', 'hidden', ['name' => 'cutline_id']);*/
		$fieldset->addField('panel_id', 'text', ['name' => 'panel_id', 'label' => __('Project Id'), 'required' => true]);
		$fieldset->addField('sku', 'text', ['name' => 'sku', 'label' => __('SKU'), 'required' => true]);
		$fieldset->addField('cqty', 'text', ['name' => 'cqty', 'label' => __('Qty'), 'required' => true]);
		$fieldset->addField('proid', 'text', ['name' => 'proid', 'label' => __('Product Id/Material ID'), 'required' => true]);
		$fieldset->addField('url_key', 'text', ['name' => 'url_key', 'label' => __('Customer Id'), 'required' => true]);

			$fieldset->addField(
    		'materialcode',
    		'text',
    		[
	    		'name' => 'materialcode',
	    		'label' => __('Material Code'),
	    		'title' => __('Material Code'),
	    		'required' => true,
	    		'disabled' => $isElementDisabled
    		]
    		);

			$fieldset->addField(
    		'clength',
    		'text',
    		[
	    		'name' => 'clength',
	    		'label' => __('Lenght(mm)'),
	    		'title' => __('Lenght(mm)'),
	    		'required' => true,
	    		'disabled' => $isElementDisabled
    		]
    		);
			
			$fieldset->addField(
    		'cwidth',
    		'text',
    		[
	    		'name' => 'cwidth',
	    		'label' => __('Widht(mm)'),
	    		'title' => __('Width(mm)'),
	    		'required' => true,
	    		'disabled' => $isElementDisabled
    		]
    		);
			
			$fieldset->addField(
    		'cpaneldescription',
    		'text',
    		[
	    		'name' => 'cpaneldescription',
	    		'label' => __('Part Description'),
	    		'title' => __('Part Description'),
	    		'required' => false,
	    		'disabled' => $isElementDisabled
    		]
    		);
			
			$fieldset->addField(
    		'thickness',
    		'text',
    		[
	    		'name' => 'thickness',
	    		'label' => __('Material Thickness(mm)'),
	    		'title' => __('Material Thickness(mm)'),
	    		'required' => true,
	    		'disabled' => $isElementDisabled
    		]
    		);
			


    	$form->setValues($model->getData());
    	$this->setForm($form);

    	return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Product Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Product Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
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