<?php

namespace Pws\Panel\Controller\Adminhtml\Panel;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;

class Savepdf extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;
	
	protected $_panelHelper;
	
	protected $customerRepository;
	private $_transportBuilder;
	protected $fileFactory;
	protected $resource;
	protected $string;



    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\Stdlib\StringUtils $string,
		\Magento\Framework\App\Response\Http\FileFactory $fileFactory,
		\Magento\Framework\App\ResourceConnection $resource,
        \Magento\Backend\Helper\Js $jsHelper,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Mail\Template\TransportBuilder $_transportBuilder,		
		\Pws\Panel\Helper\Data $panelHelper
        ) {
        $this->_fileSystem = $filesystem;
		$this->string = $string;
		$this->fileFactory = $fileFactory;
		$this->_resource = $resource;
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
			$helper = $this->_panelHelper;
			$uncutcollection = $helper->getUncutcollection($id);
			$totalpage = 1;
			$totalpage =  ceil(count($uncutcollection)/37+1);
			$quoteprice = $newquote = 0;
            if ($id) {
                $model->load($id);
				$quoteprice = $model->getQuoteprice();
				$newquote = $data['quoteprice'];
				if($newquote ==0){
					$this->messageManager->addError('Customer Not exist');
				
					$this->_getSession()->setFormData($data);
					return $resultRedirect->setPath('*/*/edit', ['panel_id' => $this->getRequest()->getParam('panel_id')]);
				}
            }
			$offcuts = $model->getOffcuts();
			$quoteid = $model->getQuoteid();
			$panelname = $model->getName();
				if(!$model->getQuoteid()){
					$connection = $this->_resource->getConnection();
					$sql = "Select MAX(quoteid) as max FROM pws_panel";
					$result = $connection->fetchAll($sql);
						$quoteid = $result[0]['max'];
						$quoteid = $quoteid+1;
					if(!$quoteid){
						$quoteid = 1;
					}
				}
				$data['quoteid'] = $quoteid;
				
			//if($quoteprice!=$newquote){
			if(1==1){
				$urlkey = $data['url_key'];
				
				
				//$sql = "INSERT INTO `pws_panel_group` (`name`, `url_key`, `position`, `status`, `shown_in_sidebar`) VALUES ('1', $urlkey, $id, '1', '1')";
				//$connection->query($sql);
 
				//Insert Data into table
				//$sql = "Insert Into " . $tableName . " (emp_id, emp_name, emp_code, emp_salary) Values ('','XYZ','ABD20','50000')";

				$materialcost = '£'.number_format($data['materialcost'],2);
				$edgincost = '£'.number_format($data['edgincost'],2);
				$projectcost = '£'.number_format($data['projectcost'],2);
				
				
				
				$uncutv = $data['uncut'];
				if($uncutv==''){
					$uncutv=0;
				}
				$sprayingv = $data['spraying'];
				if($sprayingv==''){
					$sprayingv=0;
				}
				$laminatingv = $data['laminating'];
				if($laminatingv==''){
					$laminatingv=0;
				}
				$cncv = $data['cnc'];
				if($cncv==''){
					$cncv=0;
				}
				$ironmongeryv = $data['ironmongery'];
				if($ironmongeryv==''){
					$ironmongeryv=0;
				}
				$otheronev = $data['otherone'];
				if($otheronev==''){
					$otheronev=0;
				}
				$othertwov = $data['othertwo'];
				if($othertwov==''){
					$othertwov=0;
				}
				$deliveryv = $data['shippingcost'];
				if($deliveryv==''){
					$deliveryv=0;
				}
				$uncut = '£'.number_format($uncutv,2);
				$spraying = '£'.number_format($sprayingv,2);
				$laminating = '£'.number_format($laminatingv,2);
				$cnc = '£'.number_format($cncv,2);
				$ironmongery = '£'.number_format($ironmongeryv,2);
				$otherone = '£'.number_format($otheronev,2);
				$othertwo = '£'.number_format($othertwov,2);
				$delivery = '£'.number_format($deliveryv,2);		

				$discountcostvalf = $data['discountcost'];
				if($discountcostvalf==''){
					$discountcostvalf=0;
				}
				$discountcost=$discountcostvalf.'%';
				$discountable = $data['projectcost'];
				$discountcostval = $discountcostvalf/100*$discountable;
				$discountcostval = '-£'.number_format($discountcostval,2);
				$subtotalcost = '£'.number_format($data['subtotalcost'],2);
				$vatcost = '£'.number_format($data['vatcost'],2);
				$quoteprice = '£'.number_format($data['quoteprice'],2);
				$custid = $model->getUrlKey();
				$customerFactory = $this->_objectManager->get('\Magento\Customer\Model\CustomerFactory')->create();
				$username = $useremail = '';
				$customer = $customerFactory->load($custid);
				if($customer) {
					$username =  $customer->getName();
					$useremail = $customer->getEmail();
				}
					$pdf = new \Zend_Pdf();
					$pdf->pages[] = $pdf->newPage(\Zend_Pdf_Page::SIZE_A4);
					$page = $pdf->pages[0]; // this will get reference to the first page.
					$style = new \Zend_Pdf_Style();
					$style->setLineColor(new \Zend_Pdf_Color_Rgb(0,0,0));
					$font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES);
					$style->setFont($font,15);
					$page->setStyle($style);
					$width = $page->getWidth();
					$hight = $page->getHeight();
					$x = 30;
					$pageTopalign = 850; //default PDF page height
					$this->y = 850 - 100; //print table row from page top - 100px
					$upery = $this->y;
					//Draw table header row's
					$page->drawRectangle(25, $this->y -10, $page->getWidth()-25, $this->y-100, \Zend_Pdf_Page::SHAPE_DRAW_STROKE);
					//$style->setFont($font,15);
					//$page->setStyle($style);
					//$page->drawText(__("Perfect Panels Quote Details"), ($page->getWidth()/2)-50, $this->y+60);
					
					
					$imagePath = 'perfect-panels-logo2.png';
					$image = \Zend_Pdf_Image::imageWithPath($imagePath);
					$page->drawImage($image, 25, $this->y+20, 178, $this->y+70);
					
					$style->setFont($font,10);
					$page->setStyle($style);
					$page->drawText(__("Perfect Panels Limited"), $x + 425, $this->y+70);
					$page->drawText(__("42-60 Dalmain Road,"), $x + 425, $this->y+57);
					$page->drawText(__("London. SE23 1AT"), $x + 425, $this->y+44);
					$page->drawText(__("Tel. 020 8291 9694"), $x + 425, $this->y+31);
					$page->drawText(__("order@perfectpanels.online"), $x + 425, $this->y+18);
					
					
					$style->setFont($font,16);
					$page->setStyle($style);
					$page->drawText(__("Project Detail"), $x, $this->y-25);
					$style->setFont($font,11);
					$page->setStyle($style);
					$page->drawText(__("Quote Id : #%1","$quoteid"), $x, $this->y-45);
					$page->drawText(__("Customer Name : %1", "$username"), $x, $this->y-60);
					$page->drawText(__("Email : %1","$useremail"), $x, $this->y-75);
					$page->drawText(__("Project Name : %1","$panelname"), $x, $this->y-90);
				
				
					$style->setFont($font,16);
					$page->setStyle($style);
					$page->drawText(__("Project Totals"), $x + 390, $this->y-115);
					$style->setFont($font,12);
					$page->setStyle($style);
					$page->drawText(__("Service"), $x + 340, $this->y-135);
					$page->drawText(__("Cost"), $this->getAlignRight('Cost', $x+455, 65, $font, 12), $this->y-135);
			
					$style->setFont($font,10);
					$page->setStyle($style);
					$page->drawText($materialcost, $this->getAlignRight($materialcost, $x+460, 65, $font, 10), $this->y-155,'UTF-8');
					$page->drawText('Cut to Size Panels', $x + 340, $this->y-155);
					$page->drawText($edgincost, $this->getAlignRight($edgincost, $x+460, 65, $font, 10), $this->y-170,'UTF-8');
					$page->drawText('Edging & Edge Banding', $x + 340, $this->y-170);
					$this->y +=15;
					
					if($data['uncut']>0){
						$this->y = $this->y-15;
						$page->drawText($uncut, $this->getAlignRight($uncut, $x+460, 65, $font, 10), $this->y-185,'UTF-8');
						$page->drawText('Full Sheets', $x + 340, $this->y-185);
					}
					if($data['spraying']>0){
						$this->y = $this->y-15;
						$page->drawText($spraying, $this->getAlignRight($spraying, $x+460, 65, $font, 10), $this->y-185,'UTF-8');
						$page->drawText('Spraying', $x + 340, $this->y-185);
					}
					if($data['laminating']>0){
						$this->y = $this->y-15;
						$page->drawText($laminating, $this->getAlignRight($laminating, $x+460, 65, $font, 10), $this->y-185,'UTF-8');
						$page->drawText('Laminating & Pressing', $x + 340, $this->y-185);
					}
					if($data['cnc']>0){
						$this->y = $this->y-15;
						$page->drawText($cnc, $this->getAlignRight($cnc, $x+460, 65, $font, 10), $this->y-185,'UTF-8');
						$page->drawText('CNC', $x + 340, $this->y-185);
					}
					
					if($data['ironmongery']>0){
						$this->y = $this->y-15;
						$page->drawText($ironmongery, $this->getAlignRight($ironmongery, $x+460, 65, $font, 10), $this->y-185,'UTF-8');
						$page->drawText('Ironmongery', $x + 340, $this->y-185);
					}
					
					if($data['otherone']>0){
						$this->y = $this->y-15;
						$page->drawText($otherone, $this->getAlignRight($otherone, $x+460, 65, $font, 10), $this->y-185,'UTF-8');
						$page->drawText('Other (#1)', $x + 340, $this->y-185);
					}
					
					if($data['othertwo']>0){
						$this->y = $this->y-15;
						$page->drawText($othertwo, $this->getAlignRight($othertwo, $x+460, 65, $font, 10), $this->y-185,'UTF-8');
						$page->drawText('Other (#2)', $x + 340, $this->y-185);
					}
					
					$this->y = $this->y-15;
					$page->drawText($projectcost, $this->getAlignRight($projectcost, $x+460, 65, $font, 10), $this->y-185,'UTF-8');
					$page->drawText('Project Total', $x + 340, $this->y-185);
					
					
					$page->drawText($discountcostval, $this->getAlignRight($discountcostval, $x+460, 65, $font, 10), $this->y-200,'UTF-8');
					$page->drawText('Discount @ '.$discountcost, $x + 340, $this->y-200);
					
					$this->y = $this->y-15;
					$page->drawText($delivery, $this->getAlignRight($delivery, $x+460, 65, $font, 10), $this->y-200,'UTF-8');
					$page->drawText('Delivery Amount ', $x + 340, $this->y-200);
					
					
					$font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES_BOLD);
					$style->setFont($font,10);
					$page->setStyle($style);
					$page->drawText($subtotalcost, $this->getAlignRight($subtotalcost, $x+460, 65, $font, 10), $this->y-215,'UTF-8');
					$page->drawText('Subtotal', $x + 340, $this->y-215);
					$font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES);
					$style->setFont($font,10);
					$page->setStyle($style);
					$page->drawText($vatcost, $this->getAlignRight($vatcost, $x+460, 65, $font, 10), $this->y-230,'UTF-8');
					$page->drawText('VAT @ 20%', $x + 340, $this->y-230);
					$page->drawText('Offcuts required', $x, $this->y-230);
					$page->drawText($offcuts, $x + 95, $this->y-230);
					/*$page->drawText('Delivery', $x, $this->y-230);
					$page->drawText('Not included', $x + 95, $this->y-230);*/
					$page->drawRectangle(25, $upery -100, $page->getWidth()-25, $this->y - 240, \Zend_Pdf_Page::SHAPE_DRAW_STROKE);
					$page->drawRectangle(25, $this->y -240, $page->getWidth()-25, $this->y - 280, \Zend_Pdf_Page::SHAPE_DRAW_STROKE);
					$page->drawRectangle(25, $this->y -280, $page->getWidth()-25, $this->y - 590, \Zend_Pdf_Page::SHAPE_DRAW_STROKE);
					$font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES_BOLD);
					$style->setFont($font,14);
					$page->setStyle($style);
					
					//$page->drawText($quoteprice, $x + 460, $this->y-290);
					$page->drawText($quoteprice, $this->getAlignRight($quoteprice, $x+460, 65, $font, 14), $this->y-260,'UTF-8');
					$page->drawText('Total', $x + 340, $this->y-260,'UTF-8');
	
					//$page->drawText(__("Total : %1", "$quoteprice"), $x + 435, $this->y-85);
					$font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES_BOLD);
					$style->setFont($font,10);
					$page->setStyle($style);
					//$font = \Zend_Pdf_Font::fontWithPath(
					//	$this->_rootDirectory->getAbsolutePath('lib/internal/GnuFreeFont/FreeSerifBold.ttf')
					//);
					$page->drawText(__("NOTE: Please check the attached cut to size part list and ensure that all details and information are correct,"), $x, $this->y-305);
					$page->drawText(__("once the order has been placed we are unable to change part dimensions or materials."), $x, $this->y-320);
					$font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES);
					$style->setFont($font,10);
					$page->setStyle($style);
					//$page->drawText(__("The costs outlined above are for this project only, delivery charges and/or admin fees will  be itemized on the Invoice where"), $x, $this->y-345);
					//$page->drawText(__("applicable."), $x, $this->y-360);
					
					$pdfnotes = $data['pdfnotes'];
					$value = preg_replace('/<br[^>]*>/i', "\n", $pdfnotes);
					$top = $this->y-345;
					foreach ($this->string->split($value, 120, true, true) as $_value) {
						$page->drawText(
							trim(strip_tags($_value)),
							//$this->getAlignRight($_value, 130, 440, $font, 10),
							$x,
							$top,
							'UTF-8'
						);
						$top -= 10;
					}
					
					
					$font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES_BOLD);
					$style->setFont($font,10);
					$page->setStyle($style);
					$page->drawText('Bank Details', $x, $top-10);
					$top -=10;
					
					$font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES);
					$style->setFont($font,10);
					$page->setStyle($style);
					
					$page->drawText(__("Bank Name:"), $x, $top-13);
					$page->drawText(__("NatWest"), $x + 80, $top-13);
					$page->drawText(__("Account Name:"), $x, $top-28);
					$page->drawText(__("Perfect Panels Ltd"), $x + 80, $top-28);
					$page->drawText(__("Account Number:"), $x, $top-43);
					$page->drawText(__("22186689"), $x + 80, $top-43);
					$page->drawText(__("Sort Code:"), $x, $top-58);
					$page->drawText(__("60-05-29"), $x + 80, $top-58);
					$top +=18;
					$page->drawText(__("If paying by BACS please make a bank transfer to the account shown above."), $x, $top-93);					
					$page->drawText(__("Please use your quote reference number (top left of the page) as the payment reference."), $x, $top-108);
					$page->drawText(__("Lead time begins once we have received cleared funds."), $x, $top-123);
					$page->drawText(__("We also accept card payment via WorldPay."), $x, $top-138);
					$page->drawText(__("This quote is valid for 30 days."), $x, $top-153);
								
					
					$font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES);
					$style->setFont($font,9);
					$page->setStyle($style);
					$page->drawText(__("Perfect Panels Limited"), $x, $upery-720);
					$page->drawText(__("Page 1 of $totalpage"), $x+245, $upery-735);
					$page->drawText(__("Registration in England and Wales No. 11729479"), $x, $upery-735);
					$page->drawText(__("www.perfectpanels.online"), $x + 225, $upery-720);
					$page->drawText(__("Vat No. 314 1845 25"), $x + 418, $upery-720);
					$page->drawText(__("Registered at the above address"), $x + 418, $upery-735);
					
					//create new page for uncut boards
					
					
					if(count($uncutcollection)>0){
						$pdf->pages[] = $pdf->newPage(\Zend_Pdf_Page::SIZE_A4);
						$page = $pdf->pages[1]; // this will get reference to the first page.
						$style = new \Zend_Pdf_Style();
						$style->setLineColor(new \Zend_Pdf_Color_Rgb(0,0,0));
						$font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES);
						$style->setFont($font,15);
						$page->setStyle($style);
						$width = $page->getWidth();
						$hight = $page->getHeight();
						$x = 10;
						$pageTopalign = 850; //default PDF page height
						$this->y = 850; //print table row from page top - 100px
						$imagePath = 'perfect-panels-logo21.png';
						$image = \Zend_Pdf_Image::imageWithPath($imagePath);			
						$page->drawImage($image, 10, $this->y-50, 105, $this->y-16);
						$page->drawText(__("Full Sheets"), $x + 240, $this->y-35);
						$this->y -= 70;
						//Draw table header row's
						$page->drawRectangle(10, $this->y - 8, $page->getWidth()-10, $this->y + 12, \Zend_Pdf_Page::SHAPE_DRAW_STROKE);
						$font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES);
						$style->setFont($font,9);
						$page->setStyle($style);
						//Move text left to right - Header values ($x)
						$page->drawText('S. No.', $x + 3, $this->y);
						$page->drawText('Part Description', $x + 30, $this->y);
						$page->drawText('Material Code', $x + 265, $this->y);
						$page->drawText('Length(mm)', $x + 389, $this->y);
						$page->drawText('Width(mm)', $x + 442, $this->y);
						$page->drawText('Thickness(mm)', $x + 490, $this->y);
						$page->drawText('Qty', $x + 553, $this->y);
						//above row having 4 column's headers so we have declare 3 line's between the columns header
						$page->drawLine($x + 27, $this->y + 12, $x + 27, $this->y - 8);
						$page->drawLine($x + 262, $this->y + 12, $x + 262, $this->y - 8);
						$page->drawLine($x + 386, $this->y + 12, $x + 386, $this->y - 8);
						$page->drawLine($x + 439, $this->y + 12, $x + 439, $this->y - 8);
						$page->drawLine($x + 487, $this->y + 12, $x + 487, $this->y - 8);
						$page->drawLine($x + 550, $this->y + 12, $x + 550, $this->y - 8);
						
						//https://jutesenthil.wordpress.com/2015/12/01/how-to-create-table-in-magento-zend-pdf/
						$counter=1;
						$pageno=2;
						
						$page->drawText(__("Perfect Panels Limited"), $x, $upery-720);
						$page->drawText(__("Page $pageno of $totalpage"), $x + 245, $upery-735);
						$page->drawText(__("Registration in England and Wales No. 11729479"), $x, $upery-735);
						$page->drawText(__("www.perfectpanels.online"), $x + 225, $upery-720);
						$page->drawText(__("Vat No. 314 1845 25"), $x + 418, $upery-720);
						$page->drawText(__("Registered at the above address"), $x + 418, $upery-735);
						foreach($uncutcollection as $result){
							$cpaneldescription = $result->getCpaneldescription();
							$materialcode = $result->getMaterialcode();
							$clength = $result->getClength();
							$cwidth = $result->getCwidth();
							$thickness = $result->getThickness();
							$cqty = $result->getCqty();
							//$this->y -= 20;
							
							if($counter%37==0){
								$pdf->pages[] = $pdf->newPage(\Zend_Pdf_Page::SIZE_A4);
								$page = $pdf->pages[$pageno]; // this will get reference to the first page.
								$style = new \Zend_Pdf_Style();
								$style->setLineColor(new \Zend_Pdf_Color_Rgb(0,0,0));
								$font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES);
								$style->setFont($font,15);
								$page->setStyle($style);
								$width = $page->getWidth();
								$hight = $page->getHeight();
								$x = 10;
								$pageTopalign = 850; //default PDF page height
								$this->y = 850; //print table row from page top - 100px
								$imagePath = 'perfect-panels-logo21.png';
								$image = \Zend_Pdf_Image::imageWithPath($imagePath);			
								$page->drawImage($image, 10, $this->y-50, 105, $this->y-16);
								$page->drawText(__("Full Sheets"), $x + 240, $this->y-35);
								$this->y -= 70;
								$page->drawRectangle(10, $this->y - 8, $page->getWidth()-10, $this->y + 12, \Zend_Pdf_Page::SHAPE_DRAW_STROKE);
								$font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES);
								$style->setFont($font,9);
								$page->setStyle($style);
								//Move text left to right - Header values ($x)
								$page->drawText('S. No.', $x + 3, $this->y);
								$page->drawText('Part Description', $x + 30, $this->y);
								$page->drawText('Material Code', $x + 265, $this->y);
								$page->drawText('Length(mm)', $x + 389, $this->y);
								$page->drawText('Width(mm)', $x + 442, $this->y);
								$page->drawText('Thickness(mm)', $x + 490, $this->y);
								$page->drawText('Qty', $x + 553, $this->y);
								$page->drawLine($x + 27, $this->y + 12, $x + 27, $this->y - 8);
								$page->drawLine($x + 262, $this->y + 12, $x + 262, $this->y - 8);
								$page->drawLine($x + 386, $this->y + 12, $x + 386, $this->y - 8);
								$page->drawLine($x + 439, $this->y + 12, $x + 439, $this->y - 8);
								$page->drawLine($x + 487, $this->y + 12, $x + 487, $this->y - 8);
								$page->drawLine($x + 550, $this->y + 12, $x + 550, $this->y - 8);
								$mypage = $pageno+1;
								$page->drawText(__("Perfect Panels Limited"), $x, $upery-720);
								$page->drawText(__("Page $mypage of $totalpage"), $x + 245, $upery-735);
								$page->drawText(__("Registration in England and Wales No. 11729479"), $x, $upery-735);
								$page->drawText(__("www.perfectpanels.online"), $x + 225, $upery-720);
								$page->drawText(__("Vat No. 314 1845 25"), $x + 418, $upery-720);
								$page->drawText(__("Registered at the above address"), $x + 418, $upery-735);
								$pageno++;
							}								

							
							
							$this->y -= 20;
							$page->drawRectangle(10, $this->y - 8, $page->getWidth()-10, $this->y + 12, \Zend_Pdf_Page::SHAPE_DRAW_STROKE);
							$page->drawText("$counter", $x + 3, $this->y);
							$page->drawText("$cpaneldescription", $x + 30, $this->y);
							$page->drawText("$materialcode", $x + 265, $this->y);
							$page->drawText("$clength", $x + 389, $this->y);
							$page->drawText("$cwidth", $x + 442, $this->y);
							$page->drawText("$thickness", $x + 490, $this->y);
							$page->drawText("$cqty", $x + 553, $this->y);
							//above row having 4 column's headers so we have declare 3 line's between the columns header
							$page->drawLine($x + 27, $this->y + 12, $x + 27, $this->y - 8);
							$page->drawLine($x + 262, $this->y + 12, $x + 262, $this->y - 8);
							$page->drawLine($x + 386, $this->y + 12, $x + 386, $this->y - 8);
							$page->drawLine($x + 439, $this->y + 12, $x + 439, $this->y - 8);
							$page->drawLine($x + 487, $this->y + 12, $x + 487, $this->y - 8);
							$page->drawLine($x + 550, $this->y + 12, $x + 550, $this->y - 8);
							$counter++;
						}
						
					}
					
					$datetime = date("dmYHis");
					$fileName = 'quote'.$custid.'-'.$id.'-'.$quoteid.'_'.$datetime.'.pdf';
					$data['pdf'] = 'quote'.$custid.'-'.$id.'-'.$quoteid.'_'.$datetime.'.pdf';
					$this->fileFactory->createnew(
					   $fileName,
					   $pdf->render(),
					   DirectoryList::MEDIAPDF,
					   'application/pdf'
					);
				
			}
			


            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
            ->getDirectoryRead(DirectoryList::MEDIA);
            $mediaFolder = 'panel/';
          

			if(isset($data['filecuttosize']) && is_array($data['filecuttosize'])){
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
            }

			if($id && $newquote>0 && $newquote != $quoteprice){
				$data['quotestatus'] = 'Price Quoted';
			}
			
            $model->setData($data);
			
            try {
				$model->save(); 
				$this->messageManager->addSuccess(__('PDF created successfully. You Can download pdf from below form'));
				 
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                return $resultRedirect->setPath('*/*/edit', ['panel_id' => $model->getId(), '_current' => true]);
                //}
                //return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Project.'));
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
                    $uploader->setAllowedExtensions(array('dxf','dwg','iges','ai','pdf','stp','doc','docx')); 
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
		public function getAlignRight($string, $x, $columnWidth, \Zend_Pdf_Resource_Font $font, $fontSize, $padding = 5)
		{
			$width = $this->widthForStringUsingFontSize($string, $font, $fontSize);
			return $x + $columnWidth - $width - $padding;
		}
		public function widthForStringUsingFontSize($string, $font, $fontSize)
    {
        $drawingString = '"libiconv"' == ICONV_IMPL ? iconv(
            'UTF-8',
            'UTF-16BE//IGNORE',
            $string
        ) : @iconv(
            'UTF-8',
            'UTF-16BE',
            $string
        );

        $characters = [];
        for ($i = 0; $i < strlen($drawingString); $i++) {
            $characters[] = ord($drawingString[$i++]) << 8 | ord($drawingString[$i]);
        }
        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);
        $stringWidth = array_sum($widths) / $font->getUnitsPerEm() * $fontSize;
        return $stringWidth;
    }
    }