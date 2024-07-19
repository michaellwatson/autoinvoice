<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Security extends MY_Controller {
    public function __construct(){
        parent::__construct();
    }
    
    public function index($fileName) {
        // Remove any path elements, preventing traversal attacks.
        $fileName = basename($fileName);

        // Allow only PDF files.
        $allowed_extensions = array('pdf');
        $file_extension = pathinfo($fileName, PATHINFO_EXTENSION);

        if (in_array($file_extension, $allowed_extensions, TRUE) && !empty($fileName)) {
            $filePath = FCPATH.'assets/quotes/'.$fileName;

            if (file_exists($filePath)) { 
                // Define headers specific to PDFs
                header("Cache-Control: public"); 
                header("Content-Description: File Transfer"); 
                header("Content-Disposition: attachment; filename=$fileName"); 
                header("Content-Type: application/pdf"); 
                header("Content-Transfer-Encoding: binary"); 
                
                // Read the file 
                readfile($filePath); 
                exit; 
            } else {
                // Handle the error with a CodeIgniter function.
                show_error('The file does not exist.', 404); 
            }
        } else {
            // The file extension is not allowed, or the filename is empty.
            show_error('The file does not exist or is not accessible.', 403);
        }
        exit;
    }

}
