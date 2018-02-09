<?php
/**
 * Created by PhpStorm.
 * User: dharmawan
 * Date: 06/02/18
 * Time: 22:06
 */

class Generate extends CI_Model
{
    public function generate_id($table)
    {
        return $this->db->count_all_results($table)+1;
    }
}