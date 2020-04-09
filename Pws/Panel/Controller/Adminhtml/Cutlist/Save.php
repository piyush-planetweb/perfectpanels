<?php

namespace Pws\Panel\Controller\Adminhtml\Cutlist;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
class Save extends \Magento\Backend\App\Action
{

	protected $_fileSystem;
    /**
     * @param Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Backend\Helper\Js $jsHelper
        ) {
        $this->_fileSystem = $filesystem;
        $this->jsHelper = $jsHelper;
        parent::__construct($context);
    }
	
	/*public function __construct(Action\Context $context)
    {
        parent::__construct($context);
    }*/

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
    	return $this->_authorization->isAllowed('Pws_Panel::cutlist_save');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
    	$data = $this->getRequest()->getPostValue();
    	/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
    	$resultRedirect = $this->resultRedirectFactory->create();
    	if ($data) {
			
			$edgingarray = array();
			if(isset($data['edgingbanding'])){
				$edgingarray = $data['edgingbanding'];
			}
			$chk="";
			if(count($edgingarray)>0){
				
				foreach($edgingarray as $edge){
					$chk .= $edge.",";
				}
			}
			$data['edgingbanding'] = $chk;
			
			
    		$model = $this->_objectManager->create('Pws\Panel\Model\Cutlist');

    		$id = $this->getRequest()->getParam('cutline_id');

			
			
			$panel_image = "";
            if ($id) {
                $oldcustomer = $data['panel_id'];
                $model->load($id);
				$newcustomer = $model->getPanelId();
				if($oldcustomer !=$newcustomer){
					$this->messageManager->addError('Project Id change not allowed');

					return $resultRedirect->setPath('*/*/edit', ['cutline_id' => $this->getRequest()->getParam('cutline_id')]);
				}
                $panel_image = $model->getImage();
            }

            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
            ->getDirectoryRead(DirectoryList::MEDIA);
            $mediaFolder = 'panel/';
            $path = $mediaDirectory->getAbsolutePath($mediaFolder);

            // Delete, Upload Image
            $imagePath = $mediaDirectory->getAbsolutePath($model->getImage());
            if(isset($data['image']['delete']) && file_exists($imagePath)){
                unlink($imagePath);
                $data['image'] = '';
				$data['imgname'] = '';
                if($panel_image && $panel_thumbnail && $panel_image == $panel_thumbnail){
                    $data['thumbnail'] = '';
                }
            }
            if(isset($data['image']) && is_array($data['image'])){
                unset($data['image']);
            }
            if($image = $this->uploadImage('image')){
                $image = preg_replace('/[^A-Za-z0-9\. -]/', '_', $image);
                $data['image'] = str_replace('panel_','panel/',$image);;
				$data['imgname'] = str_replace('panel_','',$image);
            }
			
			

            if($data['url_key']=='')
            {
                $data['url_key'] = $data['name'];
            }
            $url_key = $this->_objectManager->create('Magento\Catalog\Model\Product\Url')->formatUrlKey($data['url_key']);
            $data['url_key'] = $url_key;
            
            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved this cutlist.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['cutline_id' => $model->getId(), '_current' => true]);
                }
                //return $resultRedirect->setPath('*/*/');
				return $resultRedirect->setPath('pwspanel/panel/edit', ['panel_id' => $data['panel_id'], '_current' => true]);
				
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Cutlist.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['cutline_id' => $this->getRequest()->getParam('cutline_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
	
	public function uploadImage($fieldId = 'image')
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if (isset($_FILES[$fieldId]) && $_FILES[$fieldId]['name']!='') 
        {
			$_FILES[$fieldId]['name'] = date("dmYHis").$_FILES[$fieldId]['name'];
			$_FILES[$fieldId]['name'] = str_replace(' ','_',$_FILES[$fieldId]['name']);
            $uploader = $this->_objectManager->create(
                'Magento\Framework\File\Uploader',
                array('fileId' => $fieldId)
                );
            $path = $this->_fileSystem->getDirectoryRead(
                DirectoryList::MEDIA
                )->getAbsolutePath(
                'catalog/category/'
                );

                /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
                $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
                $mediaFolder = 'panel/';
                try {
                    $uploader->setAllowedExtensions(array('dxf')); 
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $result = $uploader->save($mediaDirectory->getAbsolutePath($mediaFolder)
                        );
                    return $mediaFolder.$result['name'];
                } catch (\Exception $e) {
                    $this->_logger->critical($e);
                    $this->messageManager->addError($e->getMessage());
                    return $resultRedirect->setPath('*/*/edit', ['cutline_id' => $this->getRequest()->getParam('cutline_id')]);
                }
            }
            return;
    }
}