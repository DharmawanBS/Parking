<?php
require_once APPPATH.'/libraries/REST_Controller.php';
require_once APPPATH.'/libraries/JWT.php';
require_once 'Reusable.php';
require_once 'Constant.php';
/**
 * Created by PhpStorm.
 * User: dharmawan
 * Date: 04/02/18
 * Time: 11:43
 */

/**
 * @property User $User
 */
class Admin extends REST_Controller {
    public function __construct()
    {
        parent::__construct();

        //  instance class Reusable
        $this->reusable = new Reusable();

        $this->reusable->access();

        //  load model
        $this->load->model(array('User'));

        //  set default date time
        $this->reusable->date_time();

        //  get input token
        $this->token = $this->reusable->get_token();

        $this->date_time = date("Y-m-d H:i:s");
        $this->date = date("Y-m-d");

        //  check value of $this->token,if is NULL set output value = unauthorized
        if ($this->token == NULL) {

            //  give response unauthorized
            $this->response(
                $this->reusable->output(
                    Constant::MSG_UNAUTHORIZED,
                    NULL),
                REST_Controller::HTTP_UNAUTHORIZED);
        }

        //  check token is expired or not
        $this->user = $this->Model_auth->is_exp($this->token, $this->date_time);
        if (!$this->user) {

            //  give response unauthorized
            $this->response(
                $this->reusable->output(
                    Constant::MSG_UNAUTHORIZED,
                    NULL),
                REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    function user_post() {
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $phone = $this->input->post('phone');
        $email = $this->input->post('email');
        $ktp = $this->input->post('ktp');
        $privilege = $this->input->post('privilege');
        $device = $this->input->post('device');
        $order = $this->input->post('order');
        $limit = $this->input->post('limit');

        if( ! $this->reusable->validate($id)) $id = NULL;
        if( ! $this->reusable->validate($name)) $name = NULL;
        if( ! $this->reusable->validate($phone)) $phone = NULL;
        if( ! $this->reusable->validate($email)) $email = NULL;
        if( ! $this->reusable->validate($ktp)) $ktp = NULL;
        if( ! $this->reusable->validate($privilege,FALSE,TRUE)) $privilege = NULL;
        if( ! $this->reusable->validate($device)) $devie = NULL;
        if( ! $this->reusable->validate($order,FALSE,TRUE)) $order = NULL;
        if( ! $this->reusable->validate($limit,TRUE,FALSE)) $limit = NULL;

        $user = $this->User->select($id,$privilege,$name,$email,$phone,$ktp,$device,$order,$limit);
        if (!is_null($user)) {

            //  give response ok
            $this->response(
                $this->reusable->output(
                    Constant::MSG_OK,
                    $user[0]),
                REST_Controller::HTTP_OK);
        }
        else {

            //  give response empty
            $this->response(
                $this->reusable->output(
                    Constant::MSG_EMPTY,
                    NULL),
                REST_Controller::HTTP_OK);
        }
    }

    function place_post() {
        $user = $this->input->post('user');
        $id = $this->input->post('id');
        $lat1 = $this->input->post('lat1');
        $lng1 = $this->input->post('lng1');
        $lat2 = $this->input->post('lat2');
        $lng2 = $this->input->post('lng2');
        $price1 = $this->input->post('price1');
        $price2 = $this->input->post('price2');
        $per = $this->input->post('per');
        $max1 = $this->input->post('max1');
        $max2 = $this->input->post('max2');
        $order = $this->input->post('order');
        $limit = $this->input->post('limit');

        if (! $this->reusable->validate($user,TRUE,FALSE)) $user = NULL;
        if (! $this->reusable->validate($id,TRUE,FALSE)) $id = NULL;
        if (! $this->reusable->validate($lat1) OR
            ! $this->reusable->validate($lng1) OR
            ! $this->reusable->validate($lat2) OR
            ! $this->reusable->validate($lng2)) {
            $lat1 = NULL;
            $lng1 = NULL;
            $lat2 = NULL;
            $lng2 = NULL;
        }
        if (! $this->reusable->validate($price1,TRUE,FALSE) OR
            ! $this->reusable->validate($price2,TRUE,FALSE)) {
            $price1 = NULL;
            $price2 = NULL;
        }
        if (! $this->reusable->validate($per)) $per = NULL;
        if (! $this->reusable->validate($max1,TRUE,FALSE) OR
            ! $this->reusable->validate($max2,TRUE,FALSE)) {
            $max1 = NULL;
            $max2 = NULL;
        }
        if (! $this->reusable->validate($order,FALSE,TRUE)) $order = NULL;
        if (! $this->reusable->validate($limit,TRUE,FALSE)) $limit = NULL;

        $data = $this->Park->select($user,$id,$lat1,$lng1,$lat2,$lng2,$price1,$price2,$per,$max1,$max2,$order,$limit);
        if( ! is_null($data)) {

            //  give response ok
            $this->response(
                $this->reusable->output(
                    Constant::MSG_OK,
                    $data),
                REST_Controller::HTTP_OK);
        }
        else {

            //  give response empty
            $this->response(
                $this->reusable->output(
                    Constant::MSG_EMPTY,
                    NULL),
                REST_Controller::HTTP_OK);
        }
    }

    function transaction_post() {

    }

    function income_post() {

    }
}