<?php
/**
 * Created by PhpStorm.
 * User: dharmawan
 * Date: 06/02/18
 * Time: 17:06
 */

class Model_user extends CI_Model
{
    private $user_data = 'user_id,
                          user_device,
                          user_is_customer,
                          user_is_supplier,
                          user_is_admin,
                          user_name,
                          user_phone,
                          user_email,
                          user_photo,
                          user_qr,
                          user_ktp';

    function auth($user,$password) {
        $this->db->select($this->user_data);
        $this->db->group_start();
        $this->db->where('user_email',$user);
        $this->db->or_where('user_phone',$user);
        $this->db->group_end();
        $this->db->where('user_password',md5($password));
        $this->db->where('user_is_active',TRUE);
        $this->db->limit(1);
        $query = $this->db->get('user');
        $result = $query->result();
        if (sizeof($result) == 1) {
            return $result;
        }
        else {
            return NULL;
        }
    }

    function validate_token($token) {
        $this->db->select('user_id');
        $this->db->where('user_token',$token);
        $this->db->where('user_token_start !=',NULL);
        $this->db->where('user_token_start <=',$this->date_time);
        $this->db->where('user_token_end !=',NULL);
        $this->db->where('user_token_end >=',$this->date_time);
        $this->db->where('user_is_active',TRUE);
        $this->db->limit(1);
        $query = $this->db->get('user');
        $result = $query->result();
        if (sizeof($result) == 1) {
            $result = $result[0];
            return $result->user_id;
        }
        else {
            return NULL;
        }
    }

    function validate_phone_email_ktp($text) {
        $this->db->where('user_email',$text);
        $this->db->or_where('user_phone',$text);
        $this->db->or_where('user_ktp',$text);
        $this->db->where('user_is_active',TRUE);
        return $this->db->count_all_results('user');
    }

    function select($id = NULL,$privilege = NULL,$name = NULL,$email = NULL,$phone = NULL,$ktp = NULL,$device = NULL,$order = NULL,$limit = NULL) {
        $this->db->select($this->user_data);
        if ( ! is_null($id)) {
            $this->db->where('user_id', $id);
        }
        if ( ! is_null($privilege) AND is_array($privilege)) {
            $i = 0;
            foreach($privilege as $key => $value) {
                if($i == 0) {
                    $this->db->where('user_is_' . $key, $value);
                }
                else {
                    $this->db->or_where('user_is_' . $key, $value);
                }
                $i++;
            }
        }
        if ( ! is_null($name)) {
            $this->db->like('user_name',$name);
        }
        if ( ! is_null($email)) {
            $this->db->like('user_email',$email);
        }
        if ( ! is_null($phone)) {
            $this->db->like('user_phone',$phone);
        }
        if ( ! is_null($ktp)) {
            $this->db->like('user_ktp',$ktp);
        }
        if ( ! is_null($device)) {
            $this->db->like('user_device',$device);
        }
        $this->db->where('user_is_active',TRUE);
        if ( ! is_null($limit)) {
            $this->db->limit($limit);
        }
        if ( ! is_null($order) AND is_array($order)) {
            foreach($order as $key => $value) {
                $this->db->order_by($key,$value);
            }
        }
        $query = $this->db->get('user');
        $result = $query->result();
        if (sizeof($result) > 0) {
            return $result;
        }
        else {
            return NULL;
        }
    }

    function insert($data) {
        $this->db->insert('user',$data);
    }

    function update($id,$data) {
        $this->db->where('user_id',$id);
        $this->db->update('user',$data);
    }
}