<?php
/**
 * Created by PhpStorm.
 * User: dharmawan
 * Date: 04/02/18
 * Time: 11:34
 */

class Constant
{
    // path profile picture
    const PICTURE_PATH = 'assets/images/profil/';
    const MISSING_PICTURE = 'assets/images/profile_image_missing.png';
    // output success
    const MSG_OK = 'OK';
    // ouput failed
    const MSG_FAILED = 'FAILED';
    // some input were not sent
    const MSG_INVALID = 'INVALID';
    // token was expired or access API without token
    const MSG_UNAUTHORIZED = 'UNAUTHORIZED';
    //there is no data
    const MSG_EMPTY = 'EMPTY';
    // token duration
    const DURATION = 30;
    // sort descending
    const SORT_DESC = 'DESC';
    // sort ascending
    const SORT_ASC = 'ASC';
}