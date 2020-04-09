<?php

namespace Pws\Panel\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Pws\Panel\Block\Adminhtml\Panel\Grid\Renderer\Action\UrlBuilder;
use Magento\Framework\UrlInterface;

class PanelActions extends Column
{
	/** Url Path */
	const BRAND_URL_PATH_EDIT = 'pwspanel/panel/edit';
	const BRAND_URL_PATH_DELETE = 'pwspanel/panel/delete';
	const BRAND_URL_PATH_EXPORT = 'pwspanel/panel/export';

	/** @var UrlBuilder */
	protected $actionUrlBuilder;

	/** @var UrlInterface */
    protected $urlBuilder;

    /**
     * @var string
     */
    private $editUrl;

    /**
     * @param ContextInterface   $context            
     * @param UiComponentFactory $uiComponentFactory 
     * @param UrlBuilder         $actionUrlBuilder   
     * @param UrlInterface       $urlBuilder         
     * @param array              $components         
     * @param array              $data               
     * @param [type]             $editUrl            
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlBuilder $actionUrlBuilder,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $editUrl = self::BRAND_URL_PATH_EDIT
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->editUrl = $editUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return void
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['panel_id'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl($this->editUrl, ['panel_id' => $item['panel_id']]),
                        'label' => __('Edit')
                    ];
                }
               
            }
        }
        return $dataSource;
    }
}