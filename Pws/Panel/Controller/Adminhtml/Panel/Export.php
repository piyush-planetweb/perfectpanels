<?php

namespace Pws\Panel\Controller\Adminhtml\Panel;

class Export extends \Magento\Backend\App\Action
{

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Pws_Panel::panel_delete');
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('panel_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = "";
            try {
                // init model and delete
                $model = $this->_objectManager->create('Pws\Panel\Model\Panel');
                $model->load($id);
                $customerid = $model->getUrlKey();
				$projectname = '';
				$projectname = str_replace(' ','-',$model->getName());
				$quoteid = $model->getQuoteid();
				if($quoteid ==''){
					$quoteid = 0;
				}
                //$model->delete();
				
				
				
				
				$directory = $this->_objectManager->get('\Magento\Framework\Filesystem\DirectoryList');

				$rootPath  =  $directory->getRoot();

				$inputFileName = $rootPath.'/pub/media/project.xlsx';
				$dir_val = $rootPath.'/PHPExcel';
				require_once $dir_val.'/PHPExcel.php';
				require_once $dir_val.'/PHPExcel/IOFactory.php';
				$inputFileType = \PHPExcel_IOFactory::identify($inputFileName);
				$objReader = \PHPExcel_IOFactory::createReader($inputFileType);
				$objPHPExcel = $objReader->load($inputFileName);
				//$sheet = $objPHPExcel->getSheet(0);
				//$highestRow = $sheet->getHighestRow();
				//$highestColumn = $sheet->getHighestColumn();
				$objPHPExcel->getProperties()->setCreator("RDeveloper")
							 ->setLastModifiedBy("RD")
							 ->setTitle("Project Management")
							 ->setSubject("Project Management")
							 ->setDescription("Cutlist Information")
							 ->setKeywords("Perfect Panel")
							 ->setCategory("cutlist");


					// Add some data
					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('A1', 'customer reference')
								->setCellValue('B1', $customerid)
								->setCellValue('F1', 'project')
								->setCellValue('G1', $id);
					
					$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight('40');
					$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight('25');
					$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
					$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
					$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
					$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
					$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
					$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
					$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
					// Miscellaneous glyphs, UTF-8
					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('A2', 'part description')
								->setCellValue('B2', 'material')
								->setCellValue('C2', 'length')
								->setCellValue('D2', 'width')
								->setCellValue('E2', 'quantity')
								->setCellValue('F2', 'grain Y/N')
								->setCellValue('G2', 'egde btm')
								->setCellValue('H2', 'edge top')
								->setCellValue('I2', 'edge left')
								->setCellValue('J2', 'edge right')
								->setCellValue('K2', 'face laminate')
								->setCellValue('L2', 'back laminate')
								->setCellValue('M2', 'edge diagram')
								->setCellValue('N2', 'finished size')
								->setCellValue('O2', 'Part code & size')
								->setCellValue('P2', 'reference')
								->setCellValue('Q2', 'offcuts to keep Y/N')
								->setCellValue('R2', 'CLIENT')
								->setCellValue('S2', 'PROJECT')
								->setCellValue('T2', 'Cabinet NO.')
								->setCellValue('U2', 'EDGE MATERIAL')
								->setCellValue('V2', 'Profile')
								->setCellValue('W2', 'Grain matching')
								->setCellValue('X2', 'Special Notes');
					
					$helper = $this->_objectManager->get('pws\panel\Helper\Data');
					$cuttingcollection = $helper->getCuttingcollection($id);
					$partdescription = $material = $length = $width = $quantity = $grain = $edgebanding = $edgeoption  = $cabinetno = $edgeprofile = '';
					$count = 3;
					foreach ($cuttingcollection as $cutting) {
						$bottom = $top = $left = $right = '';
						$objPHPExcel->getActiveSheet()->getRowDimension($count)->setRowHeight('25');
						$partdescription = $cutting->getPaneldescription();
						$material = $cutting->getMaterial();
						$product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($material);
						//$material = $product->getMaterialCode();
						$material = $product->getResource()->getAttribute('material_code')->getFrontend()->getValue($product);
						$length = $cutting->getLength();
						$width = $cutting->getWidth();
						$quantity = $cutting->getQty();
						$grain = $cutting->getGrainmatching();
						$edgebanding = $cutting->getEdgingbanding();
						$edgebanding = strtolower($edgebanding);
						$edgeoption = $cutting->getEdgingbandingop();
						$edgeprofile = $cutting->getEdgingprofile();
						$specialnotes = $cutting->getSpecialnotes();
						$label = $slabel='';
						if($edgeoption==1){
							$label = '0.6MM-MDF-PREPARATION-TAPE';
						}elseif($edgeoption==2){
							$label = '1MM-STANDARD-ABS-MATCHING';
						}elseif($edgeoption==3){
							$label = '2MM-STANDARD-ABS-MATCHING';
						}elseif($edgeoption==4){
							$label = '1MM-AIRTEC-MATCHING';
						}elseif($edgeoption==5){
							$label = '2MM-AIRTEC-MATCHING';
						}elseif($edgeoption==6){
							$label = '1MM-WOOD-VENEER-MATCHING';
						}elseif($edgeoption==7){
							$label = '2MM-WOOD-VENEER-MATCHING';
						}
						if($edgeprofile==1){
							$slabel = 'SQUARE';
						}elseif($edgeprofile==2){
							$slabel = '1MM-RADIUS';
						}elseif($edgeprofile==3){
							$slabel = '2MM-RADIUS';
						}

						if (strpos($edgebanding, 'bottom') !== false) {
							$bottom = $label;
						}
						if (strpos($edgebanding, 'top') !== false) {
							$top = $label;
						}
						if (strpos($edgebanding, 'left') !== false) {
							$left = $label;
						}
						if (strpos($edgebanding, 'right') !== false) {
							$right = $label;
						}
						$cabinetno = $cutting->getCabinetno();
						$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('A'.$count, $partdescription)
								->setCellValue('B'.$count, $material)
								->setCellValue('C'.$count, $length)
								->setCellValue('D'.$count, $width)
								->setCellValue('E'.$count, $quantity)
								->setCellValue('F'.$count, $grain)
								->setCellValue('G'.$count, $bottom)
								->setCellValue('H'.$count, $top)
								->setCellValue('I'.$count, $left)
								->setCellValue('J'.$count, $right)
								->setCellValue('K'.$count, '')
								->setCellValue('L'.$count, '')
								->setCellValue('M'.$count, '')
								->setCellValue('N'.$count, '')
								->setCellValue('O'.$count, '')
								->setCellValue('P'.$count, '')
								->setCellValue('Q'.$count, '')
								->setCellValue('R'.$count, $customerid)
								->setCellValue('S'.$count, $id)
								->setCellValue('T'.$count, $cabinetno)
								->setCellValue('U'.$count, '')
								->setCellValue('V'.$count, $slabel)
								->setCellValue('W'.$count, $grain)
								->setCellValue('X'.$count, $specialnotes);
								
						$count++;
					}
					
					
					
					// Rename worksheet
					//$objPHPExcel->getActiveSheet()->setTitle('Simple');
					
					
					// Set active sheet index to the first sheet, so Excel opens this as the first sheet
					$objPHPExcel->setActiveSheetIndex(0);
					
					
					// Redirect output to a client’s web browser (Excel5)
					$filename = date('Ymd').'-'.$customerid.'-'.$id.'-'.$quoteid.'.xlsx';
					header('Content-Type: application/vnd.ms-excel');
					header('Content-Disposition: attachment;filename='.$filename);
					header('Cache-Control: max-age=0');
					// If you're serving to IE 9, then the following may be needed
					header('Cache-Control: max-age=1');
					
					// If you're serving to IE over SSL, then the following may be needed
					header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
					header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
					header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
					header ('Pragma: public'); // HTTP/1.0
					
					$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save('php://output');
				
				
				/*$firsthead = [
							  __('customer reference'),($customerid),
							  __(''),__(''),__(''),
							  __('project'),($id)
							 ];

				
				$outputFile = "pub/media/project/projectxls.xlsx";
				$handle = fopen($outputFile, 'w');				
				fputcsv($handle, $firsthead);
				$heading = [
								__('part description'),
								__('material'),
								__('length'),
								__('width'),
								__('quantity'),
								__('grain Y/N'),
								__('egde btm'),
								__('edge top'),
								__('edge left'),
								__('edge right'),
								__('face laminate'),
								__('back laminate'),
								__('edge diagram'),
								__('finished size'),
								__('Part code & size'),
								__('reference'),
								__('offcuts to keep Y/N'),
								__('CLIENT'),
								__('PROJECT'),
								__('Cabinet NO.'),
								__('EDGE MATERIAL')											

							];
				//$outputFile = "pub/media/inactive/ListProducts". date('Ymd_His').".csv";
				
				fputcsv($handle, $heading);
				//$helper = $this->helper('');
				$helper = $this->_objectManager->get('pws\panel\Helper\Data');
				$cuttingcollection = $helper->getCuttingcollection($id);
				$partdescription = $material = $length = $width = $quantity = $grain = $edgebanding = $edgeoption = $bottom = $top = $left = $right = $cabinetno = $edgeprofile = '';
				foreach ($cuttingcollection as $cutting) {
					$partdescription = $cutting->getPaneldescription();
					$material = $cutting->getMaterial();
					$product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($material);
					$material = $product->getName();
					$length = $cutting->getLength();
					$width = $cutting->getWidth();
					$quantity = $cutting->getQty();
					$grain = $cutting->getGrainmatching();
					$edgebanding = $cutting->getEdgingbanding();
					$edgebanding = strtolower($edgebanding);
					$edgeoption = $cutting->getEdgingbandingop();
					$edgeprofile = $cutting->getEdgingprofile();
					if (strpos($edgebanding, 'bottom') !== false) {
						$bottom = $edgeoption;
					}
					if (strpos($edgebanding, 'top') !== false) {
						$top = $edgeoption;
					}
					if (strpos($edgebanding, 'left') !== false) {
						$left = $edgeoption;
					}
					if (strpos($edgebanding, 'right') !== false) {
						$right = $edgeoption;
					}
					$cabinetno = $cutting->getCabinetno();
					$row = [
						$partdescription,
						$material,
						$length,
						$width,
						$quantity,
						$grain,
						$bottom,
						$top,
						$left,
						$right,
						'',
						'',
						'',
						'',
						'',
						'',
						'',
						$customerid,
						$id,
						$cabinetno,
						''
				
					];
					fputcsv($handle, $row);
				}
			
					$file = $outputFile;
				
					header('Content-Description: File Transfer');
					header('Content-Type: application/csv');
					header('Content-Disposition: attachment; filename='.$file);
					header('Expires: 0');
					header('Cache-Control: must-revalidate');
					header('Pragma: public');
					header('Content-Length: ' . filesize($file));
					ob_clean();flush();
					readfile($file);*/
					die();
                // display success message
                $this->messageManager->addSuccess(__('The project has been exported.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['panel_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a project to export.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }

}