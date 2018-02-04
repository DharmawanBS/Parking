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

class Admin extends REST_Controller {
    public function __construct()
    {
        parent::__construct();

        //  instance class Reusable
        $this->reusable = new Reusable();

        $this->reusable->access();

        //  load model
        $this->load->model(array());

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

    }

    function place_post() {

    }

    function transaction_post() {

    }

    function income_post() {

    }
}