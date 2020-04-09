<?php

namespace Pws\Panel\Block\Adminhtml\Panel\Edit\Tab;

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

    /**
     * @var \Pws\Panel\Helper\Data
     */
    protected $_viewHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context       
     * @param \Magento\Framework\Registry             $registry      
     * @param \Magento\Framework\Data\FormFactory     $formFactory   
     * @param \Magento\Store\Model\System\Store       $systemStore   
     * @param \Magento\Cms\Model\Wysiwyg\Config       $wysiwygConfig 
     * @param \Pws\Panel\Helper\Data                  $viewHelper    
     * @param array                                   $data          
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Pws\Panel\Helper\Data $viewHelper,
        array $data = []
    ) {
        $this->_viewHelper = $viewHelper;
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
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
        
        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);
    	/**
    	 * Checking if user have permission to save information
    	 */
    	if($this->_isAllowedAction('Pws_Panel::panel_edit')){
    		$isElementDisabled = false;
    	}else {
    		$isElementDisabled = true;
    	}
    	/** @var \Magento\Framework\Data\Form $form */
    	$form = $this->_formFactory->create();

    	$form->setHtmlIdPrefix('panel_');

    	$fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Project Information')]);


    	if ($model->getId()) {
    		$fieldset->addField('panel_id', 'hidden', ['name' => 'panel_id']);
    	}
		$fieldset->addField('store_id', 'hidden', ['name' => 'stores[]', 'value' => 0]);


    	$fieldset->addField(
    		'name',
    		'text',
    		[
                'name'     => 'name',
                'label'    => __('Project Name'),
                'title'    => __('Project Name'),
                'required' => true,
                'disabled' => $isElementDisabled
    		]
    		);

    	$fieldset->addField(
    		'url_key',
    		'text',
    		[
                'name'     => 'url_key',
                'label'    => __('Customer Id'),
                'title'    => __('Customer Id'),
                //'note'     => __('Empty to auto create url key'),
                'disabled' => $isElementDisabled
    		]
    		);
		$fieldset->addField(
    		'customer_name',
    		'text',
    		[
                'name'     => 'customer_name',
                'label'    => __('Customer Name'),
                'title'    => __('Customer Name'),
                //'note'     => __('Empty to auto create url key'),
                'disabled' => $isElementDisabled
    		]
    		);
		
		$fieldset->addField(
    		'offcuts',
    		'text',
    		[
                'name'     => 'offcuts',
                'label'    => __('Off-Cuts'),
                'title'    => __('Off-Cuts'),
                'note'     => __('Please Enter Yes Or No only'),
                'disabled' => $isElementDisabled
    		]
    		);



    	$fieldset->addField(
    		'image',
    		'image',
    		[
                'name'     => 'image',
                'label'    => __('Dxf file'),
                'title'    => __('Dxf file'),
                'disabled' => $isElementDisabled
    		]
    		);

    //	$fieldset->addField(
    //		'thumbnail',
    //		'image',
    //		[
    //            'name'     => 'thumbnail',
    //            'label'    => __('Thumbnail'),
    //            'title'    => __('Thumbnail'),
    //            'disabled' => $isElementDisabled
    //		]
    //		);

    	/*$fieldset->addField(
            'description',
            'editor',
            [
                'name'     => 'description',
                'style'    => 'height:200px;',
                'label'    => __('Description'),
                'title'    => __('Description'),
                'disabled' => $isElementDisabled,
                'config'   => $wysiwygConfig
            ]
        );*/

    	

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Page Status'),
                'name' => 'status',
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
		
		$fieldset->addField(
    		'quotestatus',
    		'select',
    		[
	    		'name' => 'quotestatus',
	    		'label' => __('Quote Status'),
	    		'title' => __('Quote Status'),
				'values' => array('Created' => 'Created', 'Request Sent' => 'Request Sent', 'Price Quoted' => 'Price Quoted', 'Price Added' =>'Price Added'),
	    		'disabled' => $isElementDisabled
    		]
    	);
		$fieldset->addField(
    		'project_type',
    		'select',
    		[
	    		'name' => 'project_type',
	    		'label' => __('Project Type'),
	    		'title' => __('Project Type'),
				'values' => array('Normal' => 'Normal', 'Excel Quoted' => 'Excel Quoted'),
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
        return __('Project Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Project Information');
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