<?php

namespace Pws\Panel\Controller\Adminhtml\Panel;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface as PageRepository;

use Pws\Panel\Model\Panel as PanelModel;

class InlineEdit extends \Magento\Backend\App\Action
{

    /** @var PageRepository  */
    protected $panelRepository;

    /** @var JsonFactory  */
    protected $jsonFactory;

    /** @var panelModel */
    protected $panelModel;

    /**
     * @param Context $context
     * @param PageRepository $panelRepository
     * @param JsonFactory $jsonFactory
     * @param Pws\Panel\Model\Panel $panelModel
     */
    public function __construct(
        Context $context,
        PageRepository $panelRepository,
        JsonFactory $jsonFactory,
        PanelModel $panelModel
        ) {
        parent::__construct($context);
        $this->pageRepository = $panelRepository;
        $this->jsonFactory = $jsonFactory;
        $this->panelModel = $panelModel;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
                ]);
        }

        foreach (array_keys($postItems) as $panelId) {
            /** @var \Pws\Panel\Model\Group $panel */
            $panel = $this->_objectManager->create('Pws\Panel\Model\Panel');
            $panelData = $postItems[$panelId];

            try {
                $panel->load($panelId);
                $panel->setData(array_merge($panel->getData(), $panelData));
                $panel->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithgroupId($panel, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithgroupId($panel, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithPageId(
                    $page,
                    __('Something went wrong while saving the page.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => 'abc',
            'error' => 'def'
            ]);
    }

    /**
     * Add page title to error message
     *
     * @param PageInterface $panel
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithgroupId($panel, $errorText)
    {
        return '[Page ID: ' . $panel->getId() . '] ' . $errorText;
    }
}