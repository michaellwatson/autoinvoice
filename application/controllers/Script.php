<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Script extends CI_Controller {
	
	public $menu = '';
		
	
	public function __construct()
    {
    	parent::__construct();
    	$this->load->helper(array('form', 'url'));
    }

    public function bootstrap(){
    	$this->load->view('common/bootstrap');
    }

    public function api_test(){

        ini_set('display_errors', 1); 
        ini_set('display_startup_errors', 1); 
        error_reporting(E_ALL);

        //echo 'TEST';
        
        // Set the API endpoint URL
        $url = 'https://cladding2.rowe.ai/Api/entries';

        // Set the API key
        $apiKey = '14f79084-14c2-4d9c-a7d6-6daf29cbd80a';

        //Auth credentials
        $username = "admin";
        $password = "1234";

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          curl_setopt($curl, CURLOPT_USERPWD, "$username:$password"),
          CURLOPT_HTTPHEADER => array(
            'X-API-KEY: '.$apiKey
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
        /*
        // Prepare the POST data
        $data = array(
            'param1' => 'value1',
            'param2' => 'value2'
        );

        // Initialize cURL
        $curl = curl_init($url);

        // Set the POST data
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

        // Set the API key in the request headers
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: X-API-KEY ' . $apiKey
        ));

        // Set other cURL options if needed
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Execute the request
        $response = curl_exec($curl);
        print_r($response);

        // Check for cURL errors
        if (curl_errno($curl)) {
            $error = curl_error($curl);
            // Handle the error accordingly
        } else {
            // Process the response
            $decodedResponse = json_decode($response, true);
            // Handle the response accordingly
        }

        // Close cURL
        curl_close($curl);
        */

    }
}