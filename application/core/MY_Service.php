<?php

/**
 * Created by PhpStorm.
 * User: yanfei
 * Date: 17/8/7
 * Time: 17:32
 */
class MY_Service {
    public function __construct() {
        log_message('debug', "Service Class Initialized");
    }

    function __get($key) {
        $CI = &get_instance();
        return $CI->$key;
    }
}