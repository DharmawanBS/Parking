<?php
/**
 * Created by PhpStorm.
 * User: dharmawan
 * Date: 06/02/18
 * Time: 23:07
 */

class Model_place extends CI_Model
{
    function select($user = NULL,$id = NULL,$lat1 = NULL,$lng1 = NULL,$lat2 = NULL,$lng2 = NULL,$price1 = NULL,$price2 = NULL,$max1 = NULL,$max2 = NULL,$order = NULL,$limit = NULL) {
        $this->db->select('user.user_name,
                           park.*');
        if ( ! is_null($user)) {
            $this->db->where('park.park_user',$user);
        }
        if ( ! is_null($id)) {
            $this->db->where('park.park_id',$id);
        }
        if ( ! is_null($lat1) AND ! is_null($lng1) AND ! is_null($lat2) AND ! is_null($lng2) ) {
            $this->db->where('park.park_lat >=',$lat1);
            $this->db->where('park.park_lng >=',$lng1);
            $this->db->where('park.park_lat <=',$lat2);
            $this->db->where('park.park_lng <=',$lng2);
        }
        if ( ! is_null($price1) AND ! is_null($price2) ) {
            $this->db->where('park.park_price >=',$price1);
            $this->db->where('park.park_price <=',$price2);
        }
        if ( ! is_null($max1) AND ! is_null($max2) ) {
            $this->db->where('park.park_max >=',$max1);
            $this->db->where('park.park_max <=',$max2);
        }
        $this->db->where('park.park_is_active',TRUE);
        $this->db->where('user.user_is_active',TRUE);
        $this->db->where('park.park_user = user.user_id');
        if ( ! is_null($order) AND is_array($order)) {
            foreach($order as $key => $value) {
                $this->db->order_by($key,$value);
            }
        }
        $query = $this->db->get('park,user');
        $result = $query->result();
        if (sizeof($result) > 0) {
            return $result;
        }
        else {
            return NULL;
        }
    }

    function insert($data) {
        $this->db->insert('park',$data);
    }

    function update($id,$data) {
        $this->db->where('park_id',$id);
        $this->db->update('park',$data);
    }
}