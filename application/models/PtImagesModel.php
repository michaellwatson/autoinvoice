<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class PtImagesModel extends CI_Model{
    private $tableName;
    private $primaryKey;
	public function __construct(){
        parent::__construct();
        $this->tableName = 'pt_images';
        $this->primaryKey = 'up_id';
    }
    public function getInfoById($id){
        $this->db->select('*');
        $this->db->from($this->tableName);
        $this->db->where($this->primaryKey, $id);
        $response_data = $this->db->get()->row();
        return $response_data;
    }
    public function getData($whereArray=array(), $returnType='', $order='desc'){
        $this->db->select('*');
        $this->db->from($this->tableName);
        if(!empty($whereArray)){
            $this->db->where($whereArray);
        }
        $this->db->order_by($this->primaryKey, $order);
        if(!empty($returnType)){
            $response_data = $this->db->get()->row();
            if(!empty($response_data)){
                $response_data = $this->_modifyData($response_data);
            }
        }
        else{
            $response_data = $this->db->get()->result();
            if($response_data){
                foreach ($response_data as $key => $value) {
                    $valueModified = $this->_modifyData($value);
                    $response_data[$key] = $valueModified;
                }
            }
        }
        return $response_data;
    }
    private function _modifyData($value){
        return $value;
    }
    public function insertData($data = array()){
        $insert = $this->db->insert($this->tableName, $data);
        if($insert){
            return $this->db->insert_id();
        }
        else{
            return false;
        }
    }
    public function insertBatch($data = array()){
        $insert = $this->db->insert_batch($this->tableName, $data);
        return $insert?true:false;
    }
    public function updateData($data, $whereArray){
        if(!empty($data) && !empty($whereArray)){
            $update = $this->db->update($this->tableName, $data, $whereArray);
            return $update?true:false;
        }
        else{
            return false;
        }
    }
    public function deleteData($whereArray){
        $delete = $this->db->delete($this->tableName, $whereArray);
        return $delete?true:false;
    }
}