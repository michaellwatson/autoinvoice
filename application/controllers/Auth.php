<?php defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends My_Controller {

    public function __construct() {
        parent::__construct();
    }

	public function check() {
        // Get the requested file path from the URL parameter
        $file_path = $this->input->get('path');

        // Split the path to get the (x) directory and filename
        $path_segments = explode('/', $file_path);
        $num_segments = count($path_segments);

        // Validate that there are at least two segments (x directory and filename)
        if ($num_segments < 2) {
            // Invalid file path format
            show_404();
            return;
        }

        $x_directory = $path_segments[0];
        $filename = $path_segments[$num_segments - 1];

        // Validate that $x_directory is a number
        if (!is_numeric($x_directory)) {
            // Invalid (x) directory format
            show_404();
            return;
        }

        // Validate the file path to ensure it is within the allowed directory
        $allowed_directory = FCPATH . 'assets/uploads/files/';
        $real_path = realpath($allowed_directory . $x_directory . '/' . implode('/', array_slice($path_segments, 1)));

        if ($real_path === false || strpos($real_path, $allowed_directory) !== 0) {
            // The file path is invalid or not within the allowed directory
            show_404();
            return;
        }

        // Get the file's MIME type
        $mime_type = mime_content_type($real_path);

        // Set the headers to stream the file
        header('Content-Type: ' . $mime_type);
        header('Content-Length: ' . filesize($real_path));

        // Prevent caching of the streamed file
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Expires: 0');

        // Set the Content-Disposition header to control the file name when downloaded
        header('Content-Disposition: attachment; filename="' . $x_directory . '/' . $filename . '"');

        // Stream the file content
        readfile($real_path);
    }

}