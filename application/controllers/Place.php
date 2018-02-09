<?php
require_once APPPATH.'/libraries/REST_Controller.php';
require_once APPPATH.'/libraries/JWT.php';
require_once 'Reusable.php';
require_once 'Constant.php';
/**
 * Created by PhpStorm.
 * User: dharmawan
 * Date: 04/02/18
 * Time: 11:32
 */

/**
 * Crrud Place
 *
 * @category    Controller
 * @author      Gede Wayan Dharmawan
 */

/**
 * @property Model_user $Model_user
 * @property Generate $Generate
 * @property Model_place $Model_place
 */
class Place extends REST_Controller {
    public function __construct()
    {
        parent::__construct();

        //  instance class Reusable
        $this->reusable = new Reusable();

        $this->reusable->access();

        //  load model
        $this->load->model(array('Model_user','Generate','Model_place'));

        //  set default date time
        $this->reusable->date_time();

        //  get input token
        $this->token = $this->reusable->get_token();

        $this->date_time = date("Y-m-d H:i:s");
        $this->date = date("Y-m-d");

        //  check value of $this->token,if is NULL set output value = unauthorized
        if (! $this->reusable->validate($this->token)) {

            //  give response unauthorized
            $this->response(
                $this->reusable->output(
                    Constant::MSG_UNAUTHORIZED,
                    NULL),
                REST_Controller::HTTP_UNAUTHORIZED);
        }

        //  check token is expired or not
        $this->user = $this->Model_user->validate_token($this->token);
        if (!is_null($this->user)) {

            //  give response unauthorized
            $this->response(
                $this->reusable->output(
                    Constant::MSG_UNAUTHORIZED,
                    NULL),
                REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    function create_post() {
        $lat = $this->input->post('lat');
        $lng = $this->input->post('lng');
        $price = $this->input->post('price');
        $max = $this->input->post('max');

        if (! $this->reusable->validate($lat) OR
            ! $this->reusable->validate($lng) OR
            ! $this->reusable->validate($price,TRUE,FALSE) OR
            ! $this->reusable->validate($max,TRUE,FALSE)) {

            //  give response invalid
            $this->response(
                $this->reusable->output(
                    Constant::MSG_INVALID,
                    NULL),
                REST_Controller::HTTP_NOT_FOUND);
        }
        else {
            $id = $this->Generate->generate_id('park');

            $data = array(
                'park_id' => $id,
                'park_user' => $this->user,
                'park_lat' => $lat,
                'park_lng' => $lng,
                'park_price' => $price,
                'park_max' => $max,
                'park_is_active' => TRUE,
                'park_created' => $this->date_time,
                'park_lastmodified' => $this->date_time
            );
            $this->Model_place->insert($data);

            //  give response ok
            $this->response(
                $this->reusable->output(
                    Constant::MSG_OK,
                    $id),
                REST_Controller::HTTP_OK);
        }
    }

    function read_post() {
        $user = $this->input->post('user');
        $id = $this->input->post('id');
        $lat1 = $this->input->post('lat1');
        $lng1 = $this->input->post('lng1');
        $lat2 = $this->input->post('lat2');
        $lng2 = $this->input->post('lng2');
        $price1 = $this->input->post('price1');
        $price2 = $this->input->post('price2');
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
        if (! $this->reusable->validate($max1,TRUE,FALSE) OR
            ! $this->reusable->validate($max2,TRUE,FALSE)) {
            $max1 = NULL;
            $max2 = NULL;
        }
        if (! $this->reusable->validate($order,FALSE,TRUE)) $order = NULL;
        if (! $this->reusable->validate($limit,TRUE,FALSE)) $limit = NULL;

        $data = $this->Model_place->select($user,$id,$lat1,$lng1,$lat2,$lng2,$price1,$price2,$max1,$max2,$order,$limit);
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

    function update_post() {
        $id = $this->input->post('id');
        $lat = $this->input->post('lat');
        $lng = $this->input->post('lng');
        $price = $this->input->post('price');
        $max = $this->input->post('max');

        if (! $this->reusable->validate($id,TRUE,FALSE) OR
            ! $this->reusable->validate($lat) OR
            ! $this->reusable->validate($lng) OR
            ! $this->reusable->validate($price,TRUE,FALSE) OR
            ! $this->reusable->validate($max,TRUE,FALSE)) {

            //  give response invalid
            $this->response(
                $this->reusable->output(
                    Constant::MSG_INVALID,
                    NULL),
                REST_Controller::HTTP_NOT_FOUND);
        }
        else {

            $data = array(
                'park_user' => $this->user,
                'park_lat' => $lat,
                'park_lng' => $lng,
                'park_price' => $price,
                'park_max' => $max,
                'park_lastmodified' => $this->date_time
            );
            $this->Model_place->update($id,$data);

            //  give response ok
            $this->response(
                $this->reusable->output(
                    Constant::MSG_OK,
                    $id),
                REST_Controller::HTTP_OK);
        }
    }

    function delete_post() {
        $id = $this->input->post('id');

        if (! $this->reusable->validate($id,TRUE,FALSE)) {

            //  give response invalid
            $this->response(
                $this->reusable->output(
                    Constant::MSG_INVALID,
                    NULL),
                REST_Controller::HTTP_NOT_FOUND);
        }
        else {
            $data = array(
                'park_is_active' => FALSE,
                'park_lastmodified' => $this->date_time
            );
            $this->Model_place->update($id,$data);

            //  give response ok
            $this->response(
                $this->reusable->output(
                    Constant::MSG_OK,
                    $id),
                REST_Controller::HTTP_OK);
        }
    }
}