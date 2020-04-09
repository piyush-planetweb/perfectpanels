<?php

namespace Pws\Panel\Controller\Adminhtml\Panel;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Excellence\EmailAttachment\Mail\Template\TransportBuilder;
use Magento\Framework\App\State;

class Sendmail extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;
	
	protected $_panelHelper;
	
	protected $customerRepository;
	protected $transportBuilder;
	protected $state;
	const TEST_FILE_CONTENT = 'Test file content.';
    const TEST_FILE_NAME = 'test';
    const TEST_FILE_TYPE = 'txt';

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
		TransportBuilder $transportBuilder, State $state,		
		\Pws\Panel\Helper\Data $panelHelper
        ) {
        $this->_fileSystem = $filesystem;
        $this->jsHelper = $jsHelper;
		$this->_storeManager = $storeManager;
		$this->transportBuilder = $transportBuilder;
        $this->state = $state;
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
$id = $this->getRequest()->getParam('panel_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {

            $model = $this->_objectManager->create('Pws\Panel\Model\Panel');

            $id = $this->getRequest()->getParam('panel_id');
            $panel_image = $panel_thumbnail = "";
			$quoteprice = $newquote = 0;
            if ($id) {
                $model->load($id);
				$newcustomer = $model->getUrlKey();
				
                $panel_image = $model->getImage();
                $panel_thumbnail = $model->getThumbnail();
				$quoteprice = $model->getQuoteprice();
            
			
			$custid = $model->getUrlKey();;
			$username = $quoteid = $projectid = $leadtime = $creationdate = '';
					
			$customerFactory = $this->_objectManager->get('\Magento\Customer\Model\CustomerFactory')->create();
			$customer = $customerFactory->load($custid);
			if($customer) {
				$username =  $customer->getName();
				$projectid = $id;
				$leadtime = $model->getLeadtime();
				$quoteid = $model->getQuoteid();
				$creationdate = $model->getCreationTime();
				$pdf = $model->getPdf();
				$pname= $model->getName();
				$creationdate = date("d-m-Y", strtotime($creationdate));
				$useremail = $customer->getEmail();
			}else{
					$this->messageManager->addError('Customer Not exist');
				
					$this->_getSession()->setFormData($data);
					return $resultRedirect->setPath('*/*/edit', ['panel_id' => $this->getRequest()->getParam('panel_id')]);
			}
			}else{
				$this->messageManager->addError('Project not exist');
				
					$this->_getSession()->setFormData($data);
					return $resultRedirect->setPath('*/*/edit', ['panel_id' => $this->getRequest()->getParam('panel_id')]);
			
			}
			
            //$model->setData($data);
			
            try {
				//$model->save();
				
					$scopeConfig =  $this->_objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
					$fromname = $scopeConfig->getValue('trans_email/ident_sales/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
					$fromemail = $scopeConfig->getValue('trans_email/ident_sales/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
					
					
					$templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' =>$this->_storeManager->getStore()->getId());
					$templateVars = array(
										'store' => $this->_storeManager->getStore(),
										'cname' => $username,
										'pname' => $pname,
										'projectid' => $projectid,
										'quoteid' => $quoteid,
										'leadtime' => $leadtime,
										'creationdate'=>$creationdate,
										'customerid' =>$custid
										
									);
					$from = [
                    'name' => 'Perfect Panel',
                    'email' => $fromemail,
                    ];
					$storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
					$currentStore = $storeManager->getStore();
					$mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
					$pdfFile = $mediaUrl.'/panel/pdf/'.$pdf;
					$file1 = $model->getFilecuttosize();
					$file2 = $model->getFilepatterncutting();
					$filea1 = $mediaUrl.'/'.$file1;
					$filea2 = $mediaUrl.'/'.$file2;
					$filea3 = $mediaUrl.'/panel/pdf/PP-Terms-and-conditions.pdf';
					$uncutonlycondition = $model->getMaterialcost();
					if($uncutonlycondition==0){
						$templeteid = 16;
						
					}else{
						$templeteid = 5;
						if($file1 =='' || $file2 =='' || $pdf = ''){
						$this->messageManager->addError('Please upload all files first');				
						$this->_getSession()->setFormData($data);
						return $resultRedirect->setPath('*/*/edit', ['panel_id' => $this->getRequest()->getParam('panel_id')]);
						}
					}
					
					/**/
					
					//echo $templeteid;
					//die();
					$adminemail = 'info@perfectpanels.online';
					if($file1 =='' && $file2 ==''){
						$transport = $this->transportBuilder->setTemplateIdentifier($templeteid)
										->setTemplateOptions($templateOptions)
										->setTemplateVars($templateVars)
										->setFrom($from)
										->addTo($useremail)
										->addTo($adminemail)
										//->addTo('alkabagga@gmail.com')
										->addAttachment(file_get_contents($pdfFile), 'Quote.pdf', 'application/pdf')
										->addAttachment(file_get_contents($filea3), 'Terms and Conditions.pdf', 'application/pdf')
										//->addAttachment(self::TEST_FILE_CONTENT, self::TEST_FILE_NAME, self::TEST_FILE_TYPE)
										->getTransport();
						$transport->sendMessage();
					}elseif($file1 ==''){
						$transport = $this->transportBuilder->setTemplateIdentifier($templeteid)
										->setTemplateOptions($templateOptions)
										->setTemplateVars($templateVars)
										->setFrom($from)
										->addTo($useremail)
										->addTo($adminemail)
										//->addTo('alkabagga@gmail.com')
										->addAttachment(file_get_contents($pdfFile), 'Quote.pdf', 'application/pdf')
										->addAttachment(file_get_contents($filea2), 'Pattern Preview.pdf', 'application/pdf')
										->addAttachment(file_get_contents($filea3), 'Terms and Conditions.pdf', 'application/pdf')
										//->addAttachment(self::TEST_FILE_CONTENT, self::TEST_FILE_NAME, self::TEST_FILE_TYPE)
										->getTransport();
						$transport->sendMessage();
						
					}elseif($file2 ==''){
						$transport = $this->transportBuilder->setTemplateIdentifier($templeteid)
										->setTemplateOptions($templateOptions)
										->setTemplateVars($templateVars)
										->setFrom($from)
										->addTo($useremail)
										->addTo($adminemail)
										->addAttachment(file_get_contents($pdfFile), 'Quote.pdf', 'application/pdf')
										->addAttachment(file_get_contents($filea1), 'Cut To Size Part List.pdf', 'application/pdf')
										->addAttachment(file_get_contents($filea3), 'Terms and Conditions.pdf', 'application/pdf')
										//->addAttachment(self::TEST_FILE_CONTENT, self::TEST_FILE_NAME, self::TEST_FILE_TYPE)
										->getTransport();
						$transport->sendMessage();
						
					}else{
						$transport = $this->transportBuilder->setTemplateIdentifier($templeteid)
										->setTemplateOptions($templateOptions)
										->setTemplateVars($templateVars)
										->setFrom($from)
										->addTo($useremail)
										->addTo($adminemail)
										//->addTo('alkabagga@gmail.com')
										->addAttachment(file_get_contents($pdfFile), 'Quote.pdf', 'application/pdf')
										->addAttachment(file_get_contents($filea1), 'Cut To Size Part List.pdf', 'application/pdf')
										->addAttachment(file_get_contents($filea2), 'Pattern Preview.pdf', 'application/pdf')
										->addAttachment(file_get_contents($filea3), 'Terms and Conditions.pdf', 'application/pdf')
										//->addAttachment(self::TEST_FILE_CONTENT, self::TEST_FILE_NAME, self::TEST_FILE_TYPE)
										->getTransport();
						$transport->sendMessage();
						
					}
					

				
                $this->messageManager->addSuccess(__('Email Sent Successfully.'));
                //$this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                //if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['panel_id' => $model->getId(), '_current' => true]);
                //}
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while sent the Email.'));
            }
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['panel_id' => $this->getRequest()->getParam('panel_id')]);
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
                    $uploader->setAllowedExtensions(array('dxf','dwg','iges','ai','pdf','stp')); 
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