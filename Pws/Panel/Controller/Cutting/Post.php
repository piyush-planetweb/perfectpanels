<?php
namespace Pws\Panel\Controller\Cutting;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
class Post extends \Pws\Panel\Controller\Index
{
        
   
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

    		$id = $this->getRequest()->getParam('id');
			$panel_image = "";
            if ($id) {
				$panel_image = $model->getImage();
                $model->load($id);
            }

			$mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
            ->getDirectoryRead(DirectoryList::MEDIA);
            $mediaFolder = 'panel/';
            $path = $mediaDirectory->getAbsolutePath($mediaFolder);

            // Delete, Upload Image
            $imagePath = $mediaDirectory->getAbsolutePath($model->getImage());
            if(isset($data['image']['delete']) && file_exists($imagePath)){
                unlink($imagePath);
                $data['image'] = '';
            }
            if(isset($data['image']) && is_array($data['image'])){
                unset($data['image']);
            }
            if($image = $this->uploadImage('image')){
				if(is_string($image)){
                $image = preg_replace('/[^A-Za-z0-9\. -]/', '_', $image);
                $data['image'] = str_replace('panel_','panel/',$image);;
				$data['imgname'] = str_replace('panel_','',$image);
				}else{
					
				}
            }

            
            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved this Cutting List.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/add', ['id' => $data['panel_id'], '_current' => true]);
                }
                 return $resultRedirect->setPath('*/*/add', ['id' => $data['panel_id'], '_current' => true]);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Cutting List.'));
            }

            //$this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/add', ['id' => $data['panel_id']]);
        }
        return $resultRedirect->setPath('*/*/add', ['id' => $data['panel_id']]);
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
            $path = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(
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
                    $this->messageManager->addError($e->getMessage());
                    return $resultRedirect->setPath('*/*/edit', ['panel_id' => $this->getRequest()->getParam('panel_id')]);
                }
            }
            return;
        }
}
