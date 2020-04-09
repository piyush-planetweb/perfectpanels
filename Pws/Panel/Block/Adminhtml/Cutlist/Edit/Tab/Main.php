<?php
namespace Pws\Panel\Block\Adminhtml\Cutlist\Edit\Tab;

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
    	if ($this->_isAllowedAction('Pws_Panel::cutlist_edit')) {
    		$isElementDisabled = false;
    	} else {
    		$isElementDisabled = true;
    	}

    	/** @var \Magento\Framework\Data\Form $form */
    	$form = $this->_formFactory->create();

    	$form->setHtmlIdPrefix('panel_');

    	$fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Cutlist Information')]);

    	if ($model->getId()) {
    		$fieldset->addField('cutline_id', 'hidden', ['name' => 'cutline_id']);
    	}
		/*$fieldset->addField('cutline_id', 'hidden', ['name' => 'cutline_id']);*/
		$fieldset->addField('panel_id', 'text', ['name' => 'panel_id', 'label' => __('Project Id'), 'required' => true]);
		$fieldset->addField('sku', 'text', ['name' => 'sku', 'label' => __('SKU'), 'required' => true]);
		$fieldset->addField('qty', 'text', ['name' => 'qty', 'label' => __('Qty'), 'required' => true]);
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
    		'length',
    		'text',
    		[
	    		'name' => 'length',
	    		'label' => __('Lenght(mm)'),
	    		'title' => __('Lenght(mm)'),
	    		'required' => true,
	    		'disabled' => $isElementDisabled
    		]
    		);
			
			$fieldset->addField(
    		'width',
    		'text',
    		[
	    		'name' => 'width',
	    		'label' => __('Widht(mm)'),
	    		'title' => __('Width(mm)'),
	    		'required' => true,
	    		'disabled' => $isElementDisabled
    		]
    		);
			
			$fieldset->addField(
    		'paneldescription',
    		'text',
    		[
	    		'name' => 'paneldescription',
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
			
			$fieldset->addField(
    		'specialnotes',
    		'text',
    		[
	    		'name' => 'specialnotes',
	    		'label' => __('Special Notes/Extra Information'),
	    		'title' => __('Special Notes/Extra Information'),
	    		'required' => false,
	    		'disabled' => $isElementDisabled
    		]
    		);
			
			$fieldset->addField(
    		'cabinetno',
    		'text',
    		[
	    		'name' => 'cabinetno',
	    		'label' => __('Cabinet No'),
	    		'title' => __('Cabinet No'),
	    		'required' => false,
	    		'disabled' => $isElementDisabled
    		]
    		);
			 $select1[] = array('value' => 'Edge Top', 'label' => 'Edge Top');
			 $select1[] = array('value' => 'Edge Bottom', 'label' => 'Edge Bottom');
			 $select1[] = array('value' => 'Edge Left', 'label' => 'Edge Left');
			 $select1[] = array('value' => 'Edge Right', 'label' => 'Edge Right');
			$fieldset->addField(
    		'edgingbanding',
    		'multiselect',
    		[
	    		'name' => 'edgingbanding[]',
	    		'label' => __('Edging Banding'),
	    		'title' => __('Edging Banding'),
				'values' => $select1,

				//'values' => array('Edge Top' => 'Edge Top', 'Edge Bottom' => 'Edge Bottom', 'Edge Left' => 'Edge Left', 'Edge Right' =>'Edge Right'),
	    		'disabled' => $isElementDisabled
    		]
			);
			
			$fieldset->addField(
    		'edgingbandingop',
    		'select',
    		[
	    		'name' => 'edgingbandingop',
	    		'label' => __('Edging Option'),
	    		'title' => __('Edging Option'),
				'values' => array('1' => '0.6mm MDF preparation tape', '2' => '1mm Standard ABS matching', '3' => '2mm Standard ABS matching', '4' =>'1mm Airtec Matching', '5' =>'2mm Airtec Matching','6' => '1mm Wood Veneer Matching','7' => '2mm Wood Veneer Matching'),
	    		'disabled' => $isElementDisabled
    		]
			);
			
			
			
			$fieldset->addField(
    		'edgingprofile',
    		'select',
    		[
	    		'name' => 'edgingprofile',
	    		'label' => __('Edging Radius'),
	    		'title' => __('Edging Radius'),
				'values' => array('1' => 'Square', '2' => '1mm Radius','3' => '2mm Radius'),
	    		'disabled' => $isElementDisabled
    		]
    		);
			
			
			$fieldset->addField(
    		'grainmatching',
    		'select',
    		[
	    		'name' => 'grainmatching',
	    		'label' => __('Grain Matching'),
	    		'title' => __('Grain Matching'),
				'values' => array('Yes' => 'Yes', '' => 'No'),
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
        return __('Cutlist Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Cutlist Information');
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