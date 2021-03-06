<?php

namespace Pws\Panel\Controller\Adminhtml\Panel;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;
	
	protected $_panelHelper;
	
	protected $customerRepository;
	private $_transportBuilder;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Backend\Helper\Js $jsHelper,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Mail\Template\TransportBuilder $_transportBuilder,		
		\Pws\Panel\Helper\Data $panelHelper
        ) {
        $this->_fileSystem = $filesystem;
        $this->jsHelper = $jsHelper;
		$this->_storeManager = $storeManager;
		$this->_transportBuilder = $_transportBuilder;
		$this->_panelHelper = $panelHelper;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
    	return $this->_authorization->isAllowed('Pws_Panel::panel_save');
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
            $model = $this->_objectManager->create('Pws\Panel\Model\Panel');

            $id = $this->getRequest()->getParam('panel_id');
            $panel_image = $panel_thumbnail = "";
			$quoteprice = $newquote = 0;
            if ($id) {
				$oldcustomer = $data['url_key'];
                $model->load($id);
				$newcustomer = $model->getUrlKey();
				if($oldcustomer !=$newcustomer){
					$this->messageManager->addError('Customer change not allowed');

					return $resultRedirect->setPath('*/*/edit', ['panel_id' => $this->getRequest()->getParam('panel_id')]);
				}
                $panel_image = $model->getImage();
                $panel_thumbnail = $model->getThumbnail();
				$quoteprice = $model->getQuoteprice();
				$data['customerid'] = $model->getUrlKey();
				//$newquote = $data['quoteprice'];
            }else{
				$data['customerid'] = $data['url_key'];
			}
			
			/*$name = $data['name'];
            $urlkey = $data['url_key'];
			echo $countsamerow = $this->_panelHelper->getPanelListbynamecus($name,$urlkey);
			die();
			if($countsamerow>0){
				$this->messageManager->addError('Same Panel Name already exist. Please try again with different Panel Name.');
				$this->_getSession()->setFormData($data);
				return $resultRedirect->setPath('* /* /edit', ['panel_id' => $this->getRequest()->getParam('panel_id')]);
			}*/

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
			/*if(isset($data['filecuttosize']) && is_array($data['filecuttosize'])){
                unset($data['filecuttosize']);
            }
			if($filecuttosize = $this->uploadImage('filecuttosize')){
                $filecuttosize = preg_replace('/[^A-Za-z0-9\. -]/', '_', $filecuttosize);
                $data['filecuttosize'] = str_replace('panel_','panel/',$filecuttosize);
            }
			if(isset($data['filepatterncutting']) && is_array($data['filepatterncutting'])){
                unset($data['filepatterncutting']);
            }
			
			if($filepatterncutting = $this->uploadImage('filepatterncutting')){
                $filepatterncutting = preg_replace('/[^A-Za-z0-9\. -]/', '_', $filepatterncutting);
                $data['filepatterncutting'] = str_replace('panel_','panel/',$filepatterncutting);
            }*/

            // Delete, Upload Thumbnail
            $thumbnailPath = $mediaDirectory->getAbsolutePath($model->getThumbnail());
            if(isset($data['thumbnail']['delete']) && file_exists($thumbnailPath)){
                unlink($thumbnailPath);
                $data['thumbnail'] = '';
                if($panel_image && $panel_thumbnail && $panel_image == $panel_thumbnail){
                    $data['image'] = '';
                }
            }
            if(isset($data['thumbnail']) && is_array($data['thumbnail'])){
                unset($data['thumbnail']);
            }
            if($thumbnail = $this->uploadImage('thumbnail')){
                $data['thumbnail'] = $thumbnail;
            }

            if($data['url_key']=='')
            {
                $data['url_key'] = $data['name'];
            }
            $url_key = $this->_objectManager->create('Magento\Catalog\Model\Product\Url')->formatUrlKey($data['url_key']);
            $data['url_key'] = $url_key;

            $links = $this->getRequest()->getPost('links');
            $links = is_array($links) ? $links : [];
            if(!empty($links) && isset($links['related'])){
                $products = $this->jsHelper->decodeGridSerializedInput($links['related']);
                $data['products'] = $products;
            }
			//if($id && $newquote>0 && $newquote != $quoteprice){
			//	$data['quotestatus'] = 'Price Quoted';
			//}
			
			$custid = $data['url_key'];
			$username = $useremail ='';
					
			$customerFactory = $this->_objectManager->get('\Magento\Customer\Model\CustomerFactory')->create();
			$customer = $customerFactory->load($custid);
			if($customer) {
				$username =  $customer->getName();
				$useremail = $customer->getEmail();
			}else{
					$this->messageManager->addError('Customer Not exist');
				
					$this->_getSession()->setFormData($data);
					return $resultRedirect->setPath('*/*/edit', ['panel_id' => $this->getRequest()->getParam('panel_id')]);
					
			}
			
            $model->setData($data);
			
            try {
				$model->save();
				/*if($id && $newquote>0 &&$newquote != $quoteprice){
					
					$scopeConfig =  $this->_objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
					$fromname = $scopeConfig->getValue('trans_email/ident_sales/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
					$fromemail = $scopeConfig->getValue('trans_email/ident_sales/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
					
					
					$templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' =>$this->_storeManager->getStore()->getId());
					$templateVars = array(
										'store' => $this->_storeManager->getStore(),
										'cname' => $username,
										'cemail' => $useremail,
										'price' => $newquote,
										'pid' => $id,
										
									);
					$from = [
                    'name' => 'Perfect Panel',
                    'email' => $fromemail,
                    ];
					$transport = $this->_transportBuilder->setTemplateIdentifier(5)
									->setTemplateOptions($templateOptions)
									->setTemplateVars($templateVars)
									->setFrom($from)
									->addTo($useremail)
									->getTransport();
					$transport->sendMessage();
					
					
					
					
					
				}*/
				
                $this->messageManager->addSuccess(__('You saved this project.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['panel_id' => $model->getId(), '_current' => true]);
                }
                //return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Project.'));
            }
            $this->_getSession()->setFormData($data);
           // return $resultRedirect->setPath('*/*/edit', ['panel_id' => $this->getRequest()->getParam('panel_id')]);
			return $resultRedirect->setPath('*/*/');
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
                    //$this->_logger->critical($e);
                    $this->messageManager->addError($e->getMessage());
                    return $resultRedirect->setPath('*/*/edit', ['panel_id' => $this->getRequest()->getParam('panel_id')]);
                }
            }
            return;
        }
    }