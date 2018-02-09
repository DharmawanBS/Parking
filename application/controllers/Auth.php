<?php
require_once APPPATH.'/libraries/REST_Controller.php';
require_once APPPATH.'/libraries/JWT.php';
require_once 'Reusable.php';
require_once 'Constant.php';
use \Firebase\JWT\JWT;
/**
 * Created by PhpStorm.
 * User: dharmawan
 * Date: 04/02/18
 * Time: 11:20
 */

/**
 * Authentication
 *
 * @category    Controller
 * @author      Gede Wayan Dharmawan
 */

/**
 * @property Model_user $Model_user
 * @property Generate $Generate
 */
class Auth extends REST_Controller {
    public function __construct()
    {
        parent::__construct();

        //  instance class Reusable
        $this->reusable = new Reusable();

        $this->reusable->access();

        //  load model
        $this->load->model(array('Model_user','Generate'));

        //  set default date time
        $this->reusable->date_time();

        //  get input token
        $this->token = $this->reusable->get_token();

        $this->date_time = date("Y-m-d H:i:s");
        $this->date = date("Y-m-d");
    }

    function login_post() {
        $user = $this->input->post('user');
        $password = $this->input->post('password');
        $device = $this->input->post('device');

        if ( ! $this->reusable->validate($device)) $device = NULL;

        if ( ! $this->reusable->validate($user) OR
             ! $this->reusable->validate($password)) {

            //  give response invalid
            $this->response(
                $this->reusable->output(
                    Constant::MSG_INVALID,
                    NULL),
                REST_Controller::HTTP_NOT_FOUND);
        }
        else {
            $user_data = $this->Model_user->auth($user,$password);
            if (is_null($user_data)) {

                //  give response failed (-)
                $this->response(
                    $this->reusable->output(
                        Constant::MSG_FAILED,
                        NULL),
                    REST_Controller::HTTP_NOT_FOUND);
            }
            else {
                $user_data = $user_data[0];
                $id_user = $user_data->user_id;
                $device_user = $user_data->user_device;

                if($device_user !== $device) {

                    //  give response failed (+)
                    $this->response(
                        $this->reusable->output(
                            Constant::MSG_FAILED,
                            NULL),
                        REST_Controller::HTTP_OK);
                }
                else {
                    $date = new DateTime();
                    $iat = $date->getTimestamp();
                    $exp = $iat + 60 * Constant::DURATION;

                    //  generate token
                    $data = array(
                        'id' => $id_user,
                        'iat' => $iat,
                        'exp' => $exp
                    );
                    $token = JWT::encode($data, $this->reusable->generate_random_string());

                    //  update user's token
                    $data_user = array(
                        'user_token' => $token,
                        'user_token_start' => $this->date_time,
                        'user_token_end' => $this->reusable->inc_dec_datetime($this->date_time, "Y-m-d H:i:s", '+', 0, 0, 0, 0, Constant::DURATION, 0)
                    );
                    $this->Model_user->update($id_user, $data_user);

                    //  preparing output
                    $out = array(
                        'user_id' => $id_user,
                        'user_device' => $device_user,
                        'user_is_customer' => $this->reusable->check_boolean($user_data->user_is_customer),
                        'user_is_supplier' => $this->reusable->check_boolean($user_data->user_is_supplier),
                        'user_is_admin' => $this->reusable->check_boolean($user_data->user_is_admin),
                        'user_name' => $user_data->user_name,
                        'user_phone' => $user_data->user_phone,
                        'user_email' => $user_data->user_email,
                        'user_ktp' => $user_data->user_ktp,
                        'user_photo' => $user_data->user_photo,
                        'user_qr' => $user_data->user_qr,
                        'user_token' => $token
                    );

                    //  give response ok
                    $this->response(
                        $this->reusable->output(
                            Constant::MSG_OK,
                            $out),
                        REST_Controller::HTTP_OK);
                }
            }
        }
    }

    function logout_get() {
        if (! $this->reusable->validate($this->token)) {

            //  give response unauthorized
            $this->response(
                $this->reusable->output(
                    Constant::MSG_UNAUTHORIZED,
                    NULL),
                REST_Controller::HTTP_UNAUTHORIZED);
        }
        else {
            $id_user = $this->Model_user->validate_token($this->token);
            if (!is_null($id_user)) {

                //  update user's token
                $this->Model_user->update($id_user, array('user_token' => NULL));

                //  give response ok
                $this->response(
                    $this->reusable->output(
                        Constant::MSG_OK,
                        NULL),
                    REST_Controller::HTTP_OK);
            }
            else {

                //  give response unauthorized
                $this->response(
                    $this->reusable->output(
                        Constant::MSG_UNAUTHORIZED,
                        NULL),
                    REST_Controller::HTTP_UNAUTHORIZED);
            }
        }
    }

    function is_valid_post() {
        $text = $this->input->post('text');

        if (! $this->reusable->validate($text)) {

            //  give response invalid
            $this->response(
                $this->reusable->output(
                    Constant::MSG_INVALID,
                    NULL),
                REST_Controller::HTTP_NOT_FOUND);
        }
        else {
            if ($this->Model_user->validate_phone_email_ktp($text) > 0) {

                //  give response failed (-)
                $this->response(
                    $this->reusable->output(
                        Constant::MSG_FAILED,
                        NULL),
                    REST_Controller::HTTP_NOT_FOUND);
            }
            else {

                //  give response ok
                $this->response(
                    $this->reusable->output(
                        Constant::MSG_OK,
                        NULL),
                    REST_Controller::HTTP_OK);
            }
        }
    }

    function register_post() {
        $name = $this->input->post('name');
        $phone = $this->input->post('phone');
        $email = $this->input->post('email');
        $ktp = $this->input->post('ktp');
        $password = $this->input->post('password');
        $privilege = $this->input->post('privilege');
        $device = $this->input->post('device');

        if (! $this->reusable->validate($name) OR
            ! $this->reusable->validate($phone) OR
            $this->Model_user->validate_phone_email_ktp($phone) > 0 OR
            ! $this->reusable->validate($email) OR
            $this->Model_user->validate_phone_email_ktp($email) > 0 OR
            ! $this->reusable->validate($ktp) OR
            $this->Model_user->validate_phone_email_ktp($ktp) > 0 OR
            ! $this->reusable->validate($password) OR
            ! $this->reusable->validate($privilege,FALSE,TRUE) OR
            ! $this->reusable->validate($device)) {

            //  give response invalid
            $this->response(
                $this->reusable->output(
                    Constant::MSG_INVALID,
                    NULL),
                REST_Controller::HTTP_NOT_FOUND);
        }
        else {
            $customer = FALSE;
            $supplier = FALSE;
            foreach($privilege as $key => $value) {
                if ($key == 'customer') $customer = $value;
                else if ($key == 'supplier') $supplier = $value;
            }

            $id = $this->Generate->generate_id('user');

            $data = array(
                'user_id' => $id,
                'user_name' => $name,
                'user_phone' => $phone,
                'user_email' => $email,
                'user_ktp' => $ktp,
                'user_password' => md5($password),
                'user_is_customer' => $customer,
                'user_is_supplier' => $supplier,
                'user_is_admin' => FALSE,
                'user_is_active' => TRUE,
                'user_device' => $device
            );
            $this->Model_user->insert($data);

            //  give response ok
            $this->response(
                $this->reusable->output(
                    Constant::MSG_OK,
                    $id),
                REST_Controller::HTTP_OK);
        }
    }

    function profil_get() {
        if (! $this->reusable->validate($this->token)) {

            //  give response unauthorized
            $this->response(
                $this->reusable->output(
                    Constant::MSG_UNAUTHORIZED,
                    NULL),
                REST_Controller::HTTP_UNAUTHORIZED);
        }
        else {
            $id_user = $this->Model_user->validate_token($this->token);
            if (!is_null($id_user)) {

                //  get profil user
                $user = $this->Model_user->select($id_user);
                if (!is_null($user)) {

                    //  give response ok
                    $this->response(
                        $this->reusable->output(
                            Constant::MSG_OK,
                            $user[0]),
                        REST_Controller::HTTP_OK);
                }
                else {

                    //  give response failed (-)
                    $this->response(
                        $this->reusable->output(
                            Constant::MSG_FAILED,
                            NULL),
                        REST_Controller::HTTP_NOT_FOUND);
                }
            }
            else {

                //  give response unauthorized
                $this->response(
                    $this->reusable->output(
                        Constant::MSG_UNAUTHORIZED,
                        NULL),
                    REST_Controller::HTTP_UNAUTHORIZED);
            }
        }
    }
}