<?php
class Engage extends CI_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->helper('cookie');
        $this->load->database();
    } 


    public function image(){
        if(($this->input->get('id'))&&($this->input->get('tableid'))){

            $FormsTablesmodel   = FormsTablesmodel::find($this->input->get('tableid'));
            //this means its been clicked
            //get the flowchart id from the click
            $this->db->where('ad_id', $this->input->get('id'));
            $update['ad_isread'] = 1;
            $update['ad_flag'] = 3;
            $this->db->update($FormsTablesmodel->ft_database_table, $update);
            //echo $this->db->last_query();
            
            $graphic_http = base_url().'assets/images/pixel.png';
            $filesize = filesize( 'assets/images/pixel.png' );

            header( 'Pragma: public' );
            header( 'Expires: 0' );
            header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
            header( 'Cache-Control: private',false );
            header( 'Content-Disposition: attachment; filename="a_unique_image_name_' . $this->input->get('id') . '.gif"' );
            header( 'Content-Transfer-Encoding: binary' );
            header( 'Content-Length: '.$filesize );
            readfile( $graphic_http );
            exit();
            
        }
    }

}