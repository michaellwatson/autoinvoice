<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class PtFieldTypeModel extends CI_Model{
    private $tableName;
    private $primaryKey;
	public function __construct(){
        parent::__construct();
        $this->tableName = 'pt_field_type';
        $this->primaryKey = 'fi_id';
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
        }
        else{
            $response_data = $this->db->get()->result();
        }
        return $response_data;
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