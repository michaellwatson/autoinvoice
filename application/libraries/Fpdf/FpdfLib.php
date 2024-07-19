<?php
use setasign\Fpdi\Fpdi;
class FpdfLib {
    public function __construct() {
    }
    public function getPdfData($data) {
        $status = false; $message = ''; $responseData = array();

        $upFilepath = $data['upFilepath'];

        $fileTypeData = getFileType($upFilepath);
        // _pre($fileTypeData);

        if($fileTypeData['status']){
            if($fileTypeData['data']['fileType'] == 'pdf'){
                $status = true;
                $pdf = new Fpdi();
                $pagesCount = $pdf->setSourceFile($upFilepath);
                $responseData['pagesCount'] = $pagesCount;
            }
            else{
                $message = "Not a pdf file";
            }
        }
        else{
            $message = $fileTypeData['message'];
        }

        $resultData = array(
            'status' => $status,
            'message' => $message,
            'data' => $responseData,
        );
        return $resultData;
    }
    public function modifyPdf($data){
        $status = false; $message = ''; $responseData = array();

        // _pre($data);

        $allDocuments = $data['allDocuments'];
        $totalPages = $data['totalPages'];

        foreach ($allDocuments as $key => $value) {
            $destinationFilepath = $value['destinationFilepath'];
            
            $pdfPath = $value['filePath'];

            $numberOfPages = $value['up_number_of_pages'];

            $currentStartingPage = $value['currentStartingPage'];

            $modifiedName = $value['up_filename'];

            $pdf = new Fpdi();

            $numberOfPages = $pdf->setSourceFile($pdfPath);

            for ($i = 1; $i <= $numberOfPages; $i++) {

                $tplIdx = $pdf->importPage($i);

                $specs = $pdf->getTemplateSize($tplIdx);
                // _pre($specs);

                $pdf->AddPage($specs['orientation']);

                $pdf->useTemplate($tplIdx);
                
                $tpl = $pdf->importPage($i);
                
                $size = $pdf->getTemplateSize($tpl);

                if($specs['orientation'] == 'L'){
                    $pdf->SetXY(250, 180);
                }
                else if($specs['orientation'] == 'P'){
                    $pdf->SetXY(160, 270);
                }

                $pdf->SetFont('Arial');

                $pdf->Cell(0, 0, "Page ".$currentStartingPage++." of ".$totalPages, 0, 0, 'L');
            }
            
            // $modifiedPath = __DIR__."/".$modifiedName;
            // $pdf->Output($modifiedPath, 'F');
            
            $pdf->Output($destinationFilepath, 'F');

            // $pdf->Output($pdf1PathModified, 'I');
        }

        // exit;

        $status = true;

        $resultData = array(
            'status' => $status,
            'message' => $message,
            'data' => $responseData,
        );
        return $resultData;
    }
    private function _pageIsBetweenAnyRange($i, $afterAdditionalPdfPageNumber){
        $pageIsBetweenAnyRange = false;
        if(!empty($afterAdditionalPdfPageNumber)){
            foreach ($afterAdditionalPdfPageNumber as $key => $rangeArray) {
                if ($i >= $rangeArray['start'] && $i <= $rangeArray['end']) {
                    $pageIsBetweenAnyRange = true;
                    break;
                }
            }
        }
        return $pageIsBetweenAnyRange;
    }
    public function modifyFinalPdf($data){
        $status = false; $message = ''; $responseData = array();

        $dompdfPageGlobal = $data['dompdfPageGlobal'];
        $afterAdditionalPdfPageNumber = @$dompdfPageGlobal['afterAdditionalPdfPageNumber'];

        $destinationFilepath = $data['destinationFilepath'];
        $pdfPath = $data['filePath'];
        $modifiedName = $data['up_filename'];

        $pdf = new Fpdi();

        $numberOfPages = $pdf->setSourceFile($pdfPath);
        // _pre($numberOfPages);

        for ($i = 1; $i <= $numberOfPages; $i++) {

            $tplIdx = $pdf->importPage($i);

            $specs = $pdf->getTemplateSize($tplIdx);
            // _pre($specs);

            $pdf->AddPage($specs['orientation'], array($specs['width'], $specs['height']));

            $pdf->useTemplate($tplIdx);
            
            $tpl = $pdf->importPage($i);

            $pageIsBetweenAnyRange = $this->_pageIsBetweenAnyRange($i, $afterAdditionalPdfPageNumber);

            if(!$pageIsBetweenAnyRange){
                // right bottom 
                $rightX = $specs['width'] - 30; // Adjust 50 as needed
                $bottomY = $specs['height'] - 22; // Adjust 50 as needed
                $pdf->SetXY($rightX, $bottomY);

                $pdf->SetFont('Arial');
                $pdf->setFontSize(8);

                $pdf->Cell(0, 0, "Page ".$i." of ".$numberOfPages, 0, 0, 'L');
            }
        }
        
        $pdf->Output($destinationFilepath, 'F');

        $status = true;

        $resultData = array(
            'status' => $status,
            'message' => $message,
            'data' => $responseData,
        );
        return $resultData;
    }
}
?>