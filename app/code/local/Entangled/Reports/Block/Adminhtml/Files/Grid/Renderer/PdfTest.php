<?php
require_once "Watermark/fpdf/FPDF.php";
require_once "Watermark/fpdi/FPDI.php";

class Entangled_Reports_Block_Adminhtml_Files_Grid_Renderer_PdfTest
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row)
    {
        $pdf = $row;
        $originalPdf = Mage::getModel('downloadable/link')->getBasePath() . DS . str_replace('/',DS,$row->getPdf());
        $fpdi = new FPDI();
        try{
            $works = true;
            $fpdi->setSourceFile($originalPdf);
        }catch (Exception $e){
            $works = false;
        }

        return $works ? "Yes" : "No";
    }
}