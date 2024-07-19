<?php
class PdfMergemodel extends CI_Model {
	//mailmodel get it?

    function __construct()
    {
        parent::__construct();
    }

    public function processPDF2($startfile, $newfile, $skip_pages = 0, $loops = 0, $random_string = NULL, $debug = ''){

    	
    	$path = FCPATH.'uploads/pdfs/'.$random_string.'/';

	    $exit_recursion = false;
	    
	    $debug.="loop times:".$loops;

	    if($loops > 100){
	        die ("Something went wrong, too many recursions<br>");
	    }

	    ini_set('display_errors', 1);
	    ini_set('display_startup_errors', 1);
	    error_reporting(E_ALL);

	    $directory = FCPATH.'tmp/'.$random_string.'/';
	    if(!is_dir($directory)){
	    	mkdir($directory);
	    }

	    if(!file_exists($newfile)){
	        $pdffile = $startfile;
	    } else {
	        $pdffile = $newfile;
	    }
	    //get start orientation
	    $pdf_start1 = new \setasign\Fpdi\Fpdi();
        $pdf_start1x = $pdf_start1->setSourceFile($pdffile);
        log_message('error', '###'.$pdffile);
        $templateIdx = $pdf_start1->importPage(1);
        $size_start = $pdf_start1->getTemplateSize($templateIdx);
        $parentOrientation = $size_start['orientation'];
        log_message('error', '###'.$parentOrientation);
        //end get start orientation

	    $debug.= 'PDF File'.$pdffile.'<br>';

	    $files = glob("$directory/*.pdf");
	    //clean first
	    foreach ($files as $file) {
	        unlink($file);
	    }
	    $debug.= 'pdfseparate '.$pdffile.' '.$directory.'/'.basename($pdffile).'-%d.pdf';
	    exec('pdfseparate '.$pdffile.' '.$directory.'/'.basename($pdffile).'-%d.pdf');
	    //sleep(1);
	    //echo $debug;
	    //exit();
	    
	    $files = glob("$directory/*.pdf");
	    natsort($files);
	    if ($files === false) {
	        die("Failed to read the directory.");
	    }

	    $pages = count($files);

	    $tagPattern = '/\{(.+?)\}/'; // Matches any text enclosed in curly braces

	    $count = 1;
	    $splits = [];
	    $test_count = 0;
	    $tagText = '';

	    $debug.= 'Loop times'.$loops.'~~<br>';
	    foreach ($files as $file) {
	         $debug.= $file;
	        if($count > $skip_pages){
	        	 $debug.= '##PROCESSING FILE<br>';
		        if (is_file($file)) {
		            $text = \Ottosmops\Pdftotext\Extract::getText($file);
		             $debug.= $text;
		            if (preg_match($tagPattern, $text, $matches)) {
		                //print_r($matches);
		                $tagText = $matches[1];
		                $text = str_replace($matches[0], '', $text);
		                $splits[] = [$count, $tagText];
		                $test_count = $count;
		                $skip_pages = $count;
		                break;
		            }
		        }
	    	}
	        $count++;
	        $debug.= 'Count'.$count.'## Skip pages.'.$skip_pages.'@@<br>';
	    }

	    $additional_pages = 0;
	    if (!empty($tagText)) {
	        $pdf_file = $tagText;
	        $pdf_child = new \setasign\Fpdi\Fpdi('P', 'pt', 'A4');
	        $child_pages = $pdf_child->setSourceFile($path.$pdf_file);
	        if($child_pages > 1){
	        	$additional_pages = $child_pages - 1;
	        }
	        $skip_pages += $child_pages;
	    } else {

	        return;
	    }

	    $pdf = new \Clegginabox\PDFMerger\PDFMerger;

	    $debug.= 'add pages from '.$pdffile.' 1-'.($test_count-1).'<br>';
	   	
	   	$mergedOrientation = 'P'; // Default to portrait
	    $pdf_childx = new \setasign\Fpdi\Fpdi();
        $child_pagesx = $pdf_childx->setSourceFile($path.$pdf_file);
        //echo $path.$pdf_file.'##';
        // Adjust the page dimensions for landscape pages
        for ($pageNo = 1; $pageNo <= $child_pagesx; $pageNo++) {
            $templateId = $pdf_childx->importPage($pageNo);
            $size = $pdf_childx->getTemplateSize($templateId);
            //print_r($size);
            $mergedOrientation = $size['orientation'];
            break;
        }



	    if(($test_count-1) == 0){
	    	//first page	    
	    	$debug.= 'add file from '.$path.$pdf_file.' all<br>';
	    	//$pdf->setPageFormat($size['width'], $size['height']);
	    	//echo $path.$pdf_file.' '.$mergedOrientation.'@@';
	    	$pdf->addPDF($path.$pdf_file, 'all',  $mergedOrientation);
	    }else{
	    	$pdf->addPDF($pdffile, '1-'.($test_count-1));
	    	$debug.= 'add file from '.$path.$pdf_file.' all<br>';
	    	//$pdf->setPageFormat($size['width'], $size['height']);
	    	//echo $path.$pdf_file.' '.$mergedOrientation.'##';
	    	$pdf->addPDF($path.$pdf_file, 'all',  $mergedOrientation);
	    }
	    
	    if ($test_count == $pages) {
	        $exit_recursion = true;
	    } else {
	        $pdf->addPDF($pdffile, ($test_count+1).'-'.$pages);
	        $debug.= 'add file from '.$pdffile.' '.($test_count+1).'-'.$pages.'<br>';
	    }
	    
        //echo $newfile.'  '.$mergedOrientation;
	    $pdf->merge('file', $newfile, $parentOrientation);//$mergedOrientation);
	    
	    $skip_pages -= 1;
	    $debug.= 'skip pages:'.$skip_pages.' pages:'.$pages.' new pages:'.((int)$pages+(int)$additional_pages).'<br>';
	    $pages = $pages + $additional_pages;
	    
	    $loops++;

	    // Check if you've processed all the pages, and if not, continue recursion
	    if (!$exit_recursion && $skip_pages < $pages) {
	        $debug.= "looping ".$startfile." ".$newfile." ". $skip_pages." ".$loops.'<br>';
	        $this->processPDF2($startfile, $newfile, $skip_pages, $loops, $random_string, $debug);
	    } else {
	        $debug.= 'Task complete';
	        //return json_encode(array('newfile' => $newfile, 'debug' => $debug));
	    }
	}

}