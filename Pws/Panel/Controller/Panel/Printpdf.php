<?php

namespace Pws\Panel\Controller\Panel;

use Magento\Customer\Controller\AccountInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Pws\Panel\Model\Layer\Resolver;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Filesystem\DirectoryList;

class Printpdf extends \Magento\Framework\App\Action\Action
{

	protected $_fileSystem;

	protected $fileFactory;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Pws\Panel\Model\Panel
     */
    protected $_panelModel;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Catalog Layer Resolver
     *
     * @var Resolver
     */
    private $layerResolver;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Pws\Panel\Helper\Data
     */
    protected $_panelHelper;

    /**
     * @param Context                                             $context              [description]
     * @param \Magento\Store\Model\StoreManager                   $storeManager         [description]
     * @param \Magento\Framework\View\Result\PageFactory          $resultPageFactory    [description]
     * @param \Pws\Panel\Model\Panel                              $panelModel           [description]
     * @param \Magento\Framework\Registry                         $coreRegistry         [description]
     * @param Resolver                                            $layerResolver        [description]
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory [description]
     * @param \Pws\Panel\Helper\Data                              $panelHelper          [description]
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\Stdlib\StringUtils $string,
		\Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Pws\Panel\Model\Panel $panelModel,
        \Magento\Framework\Registry $coreRegistry,
        Resolver $layerResolver,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Pws\Panel\Helper\Data $panelHelper
        ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
		$this->_fileSystem = $filesystem;
		$this->string = $string;
		$this->fileFactory = $fileFactory;
        $this->_panelModel = $panelModel;
        $this->layerResolver = $layerResolver;
        $this->_coreRegistry = $coreRegistry;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_panelHelper = $panelHelper;
    }

   

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
		//echo 'Under construction';die();
		$date = "Printed on: ".date("d M Y");
        $id = $this->getRequest()->getParam('id');
		$model = $this->_objectManager->create('Pws\Panel\Model\Panel')->load($id);
		$panelname = $model->getName();
		$helper = $this->_panelHelper;
		$uncutcollection = $helper->getUncutcollection($id);
		$cutlistcollection = $helper->getCuttingcollection($id);		
		$totalpage = 1;
		$totalpage =  ceil(count($cutlistcollection)/13)+ceil(count($uncutcollection)/31);
		
		$pageno = 0;
		$mypage = 1;
		$pdf = new \Zend_Pdf();
		if(count($cutlistcollection)>0){
			
			$pdf->pages[] = $pdf->newPage(\Zend_Pdf_Page::SIZE_A4);
			$page = $pdf->pages[$pageno];
			$style = new \Zend_Pdf_Style();
			$style->setLineColor(new \Zend_Pdf_Color_Rgb(0,0,0));
			$font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES);
			$style->setFont($font,15);
			$page->setStyle($style);
			$width = $page->getWidth();
			$hight = $page->getHeight();
			$x = 3;
			
			$pageTopalign = 850; //default PDF page height
			$this->y = 850 - 100; //print table row from page top - 100px
			$this->newmarlopage($page,$date,$panelname,$mypage,$totalpage);
			$page->drawText(__("Cutting List"), $x + 270, $this->y);
			$this->y -=25;
			$page->drawRectangle(5, $this->y - 8, $page->getWidth()-5, $this->y + 12, \Zend_Pdf_Page::SHAPE_DRAW_STROKE);
			$font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES);
			$style->setFont($font,9);
			$page->setStyle($style);
			//Move text left to right - Header values ($x)
			$page->drawText('S. N.', $x + 5, $this->y);
			$page->drawText('Part Description', $x + 28, $this->y);
			$page->drawText('Material Code', $x + 110, $this->y);
			$page->drawText('LxWxT (mm)', $x + 189, $this->y);
			$page->drawText('Qty', $x + 245, $this->y);
			$page->drawText('Edge Top', $x + 265, $this->y);
			$page->drawText('Edge Bottom', $x + 338, $this->y);
			$page->drawText('Edge Left', $x + 402, $this->y);
			$page->drawText('Edge Right', $x + 470, $this->y);
			$page->drawText('Cabinet No.', $x + 540, $this->y);
			//above row having 4 column's headers so we have declare 3 line's between the columns header
			$page->drawLine($x + 26, $this->y + 12, $x + 26, $this->y - 8);
			$page->drawLine($x + 108, $this->y + 12, $x + 108, $this->y - 8);
			$page->drawLine($x + 187, $this->y + 12, $x + 187, $this->y - 8);
			$page->drawLine($x + 243, $this->y + 12, $x + 243, $this->y - 8);
			$page->drawLine($x + 263, $this->y + 12, $x + 263, $this->y - 8);
			$page->drawLine($x + 332, $this->y + 12, $x + 332, $this->y - 8);
			$page->drawLine($x + 400, $this->y + 12, $x + 400, $this->y - 8);
			$page->drawLine($x + 468, $this->y + 12, $x + 468, $this->y - 8);
			$page->drawLine($x + 536, $this->y + 12, $x + 536, $this->y - 8);
			$counter=0;
			$font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES);
			$style->setFont($font,8);
			$page->setStyle($style);
			foreach($cutlistcollection as $result){
				$sno = $counter+1;
				if($counter%13==0 && $counter!=0){
					$pageno++;
					$pdf->pages[] = $pdf->newPage(\Zend_Pdf_Page::SIZE_A4);
					$page = $pdf->pages[$pageno]; // this will get reference to the first page.
					$style = new \Zend_Pdf_Style();
					$style->setLineColor(new \Zend_Pdf_Color_Rgb(0,0,0));
					$font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES);
					$style->setFont($font,15);
					$page->setStyle($style);
					$width = $page->getWidth();
					$hight = $page->getHeight();
					$x = 3;
					$pageTopalign = 850; //default PDF page height
					$this->y = 850; //print table row from page top - 100px
					$mypage = $pageno+1;
					$this->newmarlopage($page,$date,$panelname,$mypage,$totalpage);
					$page->drawText(__("Cutting List"), $x + 270, $this->y);
					$this->y -=25;
					$page->drawRectangle(5, $this->y - 8, $page->getWidth()-5, $this->y + 12, \Zend_Pdf_Page::SHAPE_DRAW_STROKE);
					$font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES);
					$style->setFont($font,9);
					$page->setStyle($style);
					$page->drawText('S. N.', $x + 5, $this->y);
					$page->drawText('Part Description', $x + 28, $this->y);
					$page->drawText('Material Code', $x + 110, $this->y);
					$page->drawText('LxWxT (mm)', $x + 189, $this->y);
					$page->drawText('Qty', $x + 245, $this->y);
					$page->drawText('Edge Top', $x + 265, $this->y);
					$page->drawText('Edge Bottom', $x + 338, $this->y);
					$page->drawText('Edge Left', $x + 402, $this->y);
					$page->drawText('Edge Right', $x + 470, $this->y);
					$page->drawText('Cabinet No.', $x + 540, $this->y);
					//above row having 4 column's headers so we have declare 3 line's between the columns header
					$page->drawLine($x + 26, $this->y + 12, $x + 26, $this->y - 8);
					$page->drawLine($x + 108, $this->y + 12, $x + 108, $this->y - 8);
					$page->drawLine($x + 187, $this->y + 12, $x + 187, $this->y - 8);
					$page->drawLine($x + 243, $this->y + 12, $x + 243, $this->y - 8);
					$page->drawLine($x + 263, $this->y + 12, $x + 263, $this->y - 8);
					$page->drawLine($x + 332, $this->y + 12, $x + 332, $this->y - 8);
					$page->drawLine($x + 400, $this->y + 12, $x + 400, $this->y - 8);
					$page->drawLine($x + 468, $this->y + 12, $x + 468, $this->y - 8);
					$page->drawLine($x + 536, $this->y + 12, $x + 536, $this->y - 8);
					
				}
				$font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES);
				$style->setFont($font,8);
				$page->setStyle($style);
				$paneldescription = $result->getPaneldescription();
				$materialcode = $result->getMaterialcode();
				$length = $result->getLength();
				$width = $result->getWidth();
				$thickness = $result->getThickness();
				$qty = $result->getQty();
				$cabinetno = $result->getCabinetno();
				$edgingbanding = $result->getEdgingbanding();
				
				$bottom = $top = $left = $right = '';
				$edgebanding = strtolower($edgingbanding);
				$edgeoption = $result->getEdgingbandingop();
					 if($edgeoption==1){
						$label = '0.6mm MDF preparation tape';
					 }else if($edgeoption==2){
						  $label = '1mm Standard ABS matching';
					 }else if($edgeoption==3){
						  $label = '2mm Standard ABS matching';
					 }else if($edgeoption==4){
						  $label = '1mm Airtec Matching';
					 }else if($edgeoption==5){
						  $label = '2mm Airtec Matching';
					 }else if($edgeoption==6){
						  $label = '1mm Wood Veneer Matching';
					 }else if($edgeoption==7){
						  $label = '2mm Wood Veneer Matching';
					 }
				$edgeprofile = $result->getEdgingprofile();
					 
					 if($edgeprofile==1){
						$slabel = 'Square';
					 }else if($edgeprofile==2){
						  $slabel = '1mm Radius';
					 }else if($edgeprofile==3){
						  $slabel = '2mm Radius';
					 }
					 
				if (strpos($edgebanding, 'bottom') !== false) {
					$bottom = $label.', '.$slabel;
				}
				if (strpos($edgebanding, 'top') !== false) {
					$top = $label.', '.$slabel;
				}
				if (strpos($edgebanding, 'left') !== false) {
					$left = $label.', '.$slabel;
				}
				if (strpos($edgebanding, 'right') !== false) {
					$right = $label.', '.$slabel;
				}
				$this->y -= 48;
				$page->drawRectangle(5, $this->y - 8, $page->getWidth()-5, $this->y + 40, \Zend_Pdf_Page::SHAPE_DRAW_STROKE);
				$size = '';
				$size = $length.'x'.$width.'x'.$thickness;
				$page->drawText("$sno", $x + 5, $this->y+30);
				//$page->drawText("$paneldescription", $x + 28, $this->y+20);
				$paneldescription = ucwords($paneldescription);
				$paneldescription = preg_replace('/<br[^>]*>/i', "\n", $paneldescription);
				$ylocation = $this->y+28;
				foreach ($this->string->split($paneldescription, 19, true, true) as $_value) {
						$page->drawText(trim(strip_tags($_value)), $x + 28, $ylocation,'UTF-8');
						$ylocation -= 10;
						}
				//$page->drawText("$materialcode", $x + 110, $this->y+20);
				
				$materialcode = preg_replace('/<br[^>]*>/i', "\n", $materialcode);
				$ylocation = $this->y+28;
				foreach ($this->string->split($materialcode, 14, true, true) as $_value) {
						$page->drawText(trim(strip_tags($_value)), $x + 110, $ylocation,'UTF-8');
						$ylocation -= 10;
						}
				
				$page->drawText("$size", $x + 189, $this->y+30);
				$page->drawText("$qty", $x + 245, $this->y+30);
				$top = preg_replace('/<br[^>]*>/i', "\n", $top);
				$bottom = preg_replace('/<br[^>]*>/i', "\n", $bottom);
				$left = preg_replace('/<br[^>]*>/i', "\n", $left);
				$right = preg_replace('/<br[^>]*>/i', "\n", $right);
				$ylocation = $this->y+33;
				foreach ($this->string->split($top, 16, true, true) as $_value) {
						$page->drawText(trim(strip_tags($_value)), $x + 265, $ylocation-3,'UTF-8');
						$ylocation -= 10;
						}
				$ylocation = $this->y+33;
				foreach ($this->string->split($bottom, 16, true, true) as $_value) {
						$page->drawText(trim(strip_tags($_value)), $x + 334, $ylocation-3,'UTF-8');
						$ylocation -= 10;
						}
				$ylocation = $this->y+33;
				foreach ($this->string->split($left, 16, true, true) as $_value) {
						$page->drawText(trim(strip_tags($_value)), $x + 402, $ylocation-3,'UTF-8');
						$ylocation -= 10;
						}
				$ylocation = $this->y+33;
				foreach ($this->string->split($right, 16, true, true) as $_value) {
						$page->drawText(trim(strip_tags($_value)), $x + 470, $ylocation-3,'UTF-8');
						$ylocation -= 10;
						}
				//$page->drawText("$top", $x + 330, $this->y);
				//$page->drawText("$bottom", $x + 338, $this->y);
				//$page->drawText("$left", $x + 406, $this->y);
				//$page->drawText("$right", $x + 474, $this->y);
				//$page->drawText("$cabinetno", $x + 542, $this->y+20);
				$cabinetno = preg_replace('/<br[^>]*>/i', "\n", $cabinetno);
				$ylocation = $this->y+28;
				foreach ($this->string->split($cabinetno, 12, true, true) as $_value) {
						$page->drawText(trim(strip_tags($_value)), $x + 538, $ylocation,'UTF-8');
						$ylocation -= 10;
						}
				//above row having 4 column's headers so we have declare 3 line's between the columns header
				$page->drawLine($x + 26, $this->y + 40, $x + 26, $this->y - 8);
				$page->drawLine($x + 108, $this->y + 40, $x + 108, $this->y - 8);
				$page->drawLine($x + 187, $this->y + 40, $x + 187, $this->y - 8);
				$page->drawLine($x + 243, $this->y + 40, $x + 243, $this->y - 8);
				$page->drawLine($x + 263, $this->y + 40, $x + 263, $this->y - 8);
				$page->drawLine($x + 332, $this->y + 40, $x + 332, $this->y - 8);
				$page->drawLine($x + 400, $this->y + 40, $x + 400, $this->y - 8);
				$page->drawLine($x + 468, $this->y + 40, $x + 468, $this->y - 8);
				$page->drawLine($x + 536, $this->y + 40, $x + 536, $this->y - 8);
				$counter++;				
			
			}
			
		}else{
			$mypage=0;
		}

			if(count($uncutcollection)>0){
				$pageno = $mypage;
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
				$this->y = 850 - 100; //print table row from page top - 100px
				$mypage = $pageno+1;
				$this->newmarlopage($page,$date,$panelname,$mypage,$totalpage);
				$page->drawText(__("Full Sheets"), $x + 270, $this->y);
				$this->y -= 25;
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
				$counter=0;
				
				foreach($uncutcollection as $result){
					$sno =$counter+1;
					$cpaneldescription = $result->getCpaneldescription();
					$materialcode = $result->getMaterialcode();
					$clength = $result->getClength();
					$cwidth = $result->getCwidth();
					$thickness = $result->getThickness();
					$cqty = $result->getCqty();
					
					if($counter%31==0 && $counter!=0){
						$pageno++;
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
						$this->y = 850 - 100; //print table row from page top - 100px
						$mypage = $pageno+1;
						$this->newmarlopage($page,$date,$panelname,$mypage,$totalpage);
						$page->drawText(__("Full Sheets"), $x + 270, $this->y);
						$this->y -= 25;
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
						
						
					}
					
					
					$this->y -= 20;
					$page->drawRectangle(10, $this->y - 8, $page->getWidth()-10, $this->y + 12, \Zend_Pdf_Page::SHAPE_DRAW_STROKE);
					$page->drawText("$sno", $x + 3, $this->y);
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
		if(count($uncutcollection)>0 || count($cutlistcollection)>0){						
			$fileName = str_replace(' ','-',$panelname).'_'.$id.'_'.date("dmY").'.pdf';
			$this->fileFactory->createnew(
			   $fileName,
			   $pdf->render(),
			   DirectoryList::MEDIAPDF,
			   'application/pdf'
			);
			
			$path = "pub/media/panel/pdf/".$fileName;
			$filename = $fileName;
			header('Content-Transfer-Encoding: binary');  // For Gecko browsers mainly
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
			header('Accept-Ranges: bytes');  // For download resume
			header('Content-Length: ' . filesize($path));  // File size
			header('Content-Encoding: none');
			header('Content-Type: application/pdf');  // Change this mime type if the file is not PDF
			header('Content-Disposition: attachment; filename=' . $filename);  // Make the browser display the Save As dialog
			readfile($path);
		}else{
			$this->messageManager->addError(__('Add Cutlist of full sheets first.'));
			$resultRedirect = $this->resultRedirectFactory->create();
			return $resultRedirect->setPath('projects.html');
		}
		
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
	public function newmarlopage($page,$date,$panelname,$mypage,$totalpage){
		$style = new \Zend_Pdf_Style();
		$style->setLineColor(new \Zend_Pdf_Color_Rgb(0,0,0));
		$font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES);
		$style->setFont($font,15);
		$page->setStyle($style);
		$width = $page->getWidth();
		$hight = $page->getHeight();
		$x = 3;
		$pageTopalign = 850; //default PDF page height
		$this->y = 850 - 100; //print table row from page top - 100px
		if($mypage==1){
			$imagePath = 'perfect-panels-logo2.png';
			$image = \Zend_Pdf_Image::imageWithPath($imagePath);
			$page->drawImage($image, 5, $this->y+20, 178, $this->y+70);
			
		}else{
			$imagePath = 'perfect-panels-logo21.png';
			$image = \Zend_Pdf_Image::imageWithPath($imagePath);			
			$page->drawImage($image, 5, $this->y+20, 105, $this->y+54);
		}
		
		$style->setFont($font,10);
		$page->setStyle($style);
		if($mypage==1){
			$page->drawText(__("Perfect Panels Limited"), $x + 475, $this->y+70);
			$page->drawText(__("42-60 Dalmain Road,"), $x + 475, $this->y+57);
			$page->drawText(__("London. SE23 1AT"), $x + 475, $this->y+44);
			$page->drawText(__("Tel. 020 8291 9694"), $x + 475, $this->y+31);
			$page->drawText(__("order@perfectpanels.online"), $x + 475, $this->y+18);
		}
		
		$page->drawText(__("Perfect Panels Limited"), $x+3, 10);
		$page->drawText(__("Page $mypage of $totalpage"), $x + 530, 10);
		$page->drawText(__("www.perfectpanels.online"), $x+245, 10);
		
		$font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES_BOLD);
		$style->setFont($font,10);
		$page->setStyle($style);
		$page->drawText($panelname, $x + 5, $this->y-35);
		$page->drawText($date, $this->getAlignRight($date, $x+390, 200, $font, 10), $this->y-35,'UTF-8');
		$this->y -=55;
		
	}
}