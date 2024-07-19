<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Recordaccess extends My_Controller {

    public function __construct() {
        parent::__construct();
        // Load necessary libraries and helpers
        $this->load->library('session');
        $this->load->database();
        $this->load->library('form_validation');
    }

    public function lockRecord($recordId) {
        $userId = $this->data['user']['us_id'];

        // Set validation rules for the record ID
        $this->form_validation->set_rules('formID', 'Form ID', 'required|integer');

        if ($this->form_validation->run() === FALSE) {
            // Validation failed, return error response
            $response = array('status' => 'error', 'message' => validation_errors());
        } else {
            $formID = $this->input->post('formID');
            // Check if the record is already locked by another user
            $existingLock = $this->db->get_where('record_locks', array('record_id' => $recordId, 'table_id'=>$formID, 'unlocked_at' => NULL))->row();

            if ($existingLock) {
                if ($existingLock->user_id != $userId) {
                    // Record is already locked by another user, return error response
                    $response = array('status' => 'error', 'message' => 'Record is already locked by another user');
                } else {
                    // User already has the lock on the record, return success response
                    $response = array('status' => 'success', 'message' => 'User already has the lock on the record');
                }
            } else {
                // Lock the record for the current user
                $this->db->insert('record_locks', array('record_id' => $recordId, 'user_id' => $userId, 'table_id' => $formID));
                //echo $this->db->last_query();

                // Return success response
                $response = array('status' => 'success', 'message' => 'Record locked successfully');
            }
        }

        // Send JSON response
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function unlockRecord($recordId) {

        $userId = $this->data['user']['us_id'];

        // Set validation rules for the record ID
        $this->form_validation->set_rules('formID', 'Form ID', 'required|integer');

        if ($this->form_validation->run() === FALSE) {
            // Validation failed, return error response
            $response = array('status' => 'error', 'message' => validation_errors());
        } else {
            $formID = $this->input->post('formID');
            $userId = $this->data['user']['us_id'];

            // Unlock the record for the current user
            $this->db->where(array('record_id' => $recordId, 'user_id' => $userId, 'table_id'=>$formID, 'unlocked_at' => NULL));
            $this->db->update('record_locks', array('unlocked_at' => date('Y-m-d H:i:s')));

            // Return success response
            $response = array('status' => 'success', 'message' => 'Record unlocked successfully');

            // Send JSON response
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    }

    public function checkLock($recordId) {

        $userId = $this->data['user']['us_id'];

        // Set validation rules for the record ID
        $this->form_validation->set_rules('formID', 'Form ID', 'required|integer');

        if ($this->form_validation->run() === FALSE) {
            // Validation failed, return error response
            $response = array('status' => 'error', 'message' => validation_errors());
        } else {
             $formID = $this->input->post('formID');
            // Check if the record is locked by another user
            $existingLock = $this->db->get_where('record_locks', array('record_id' => $recordId, 'table_id'=>$formID, 'unlocked_at' => NULL))->row();

            if ($existingLock) {
                // Record is locked by another user
                $user = UserEl::find($existingLock->user_id);
                if($existingLock->user_id == $userId){
                    
                    $this->db->where('id', $existingLock->id);
                    $this->db->delete('record_locks');

                    $response = array('status' => 'unlocked', 'message' => 'Record is unlocked');
                }else{
                    $response = array('status' => 'locked', 'message' => 'Record is locked by another user ('.$user->us_firstName.' '.$user->us_surname.')');
                }
                
            } else {
                // Record is unlocked
                $response = array('status' => 'unlocked', 'message' => 'Record is unlocked');
            }
        }

        // Send JSON response
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function delete($recordId) {
        // Set validation rules for the record ID
        $this->form_validation->set_rules('formID', 'Form ID', 'required|integer');

        if ($this->form_validation->run() === FALSE) {
            // Validation failed, return error response
            $response = array('status' => 'error', 'message' => validation_errors());
        } else {
            // Perform the deletion of the record based on the record ID
            $this->db->where('record_id', $recordId);
            $this->db->delete('record_locks');

            // Check if the deletion was successful
            if ($this->db->affected_rows() > 0) {
                // Record deleted successfully
                $response = array('status' => 'success', 'message' => 'Record deleted successfully');
            } else {
                // Failed to delete the record
                $response = array('status' => 'error', 'message' => 'Failed to delete the record');
            }
        }

        // Send JSON response
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function deleteExpiredLocks() {
        // Calculate the timestamp for 30 minutes ago
        $expirationTime = strtotime('-30 minutes');

        // Convert the timestamp to MySQL datetime format
        $expirationDateTime = date('Y-m-d H:i:s', $expirationTime);

        // Delete expired locks from the database
        $this->db->where('locked_at <=', $expirationDateTime);
        $this->db->delete('record_locks');
        echo $this->db->last_query();

        // Log the number of deleted locks
        $numDeletedLocks = $this->db->affected_rows();
        log_message('info', 'Deleted '.$numDeletedLocks.' expired locks');
    }
}
?>