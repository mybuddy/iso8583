<?php

/**
 * 报文解析控制器类
 *
 * User: yanfei
 * Date: 2017/08/08
 * Time: 00:08
 */
require_once APPPATH . 'libraries/REST_Controller.php';

class ISO8583 extends REST_Controller {
    public function unpack_cli_get($packet, $length_prefix = 1) {
        $this->load->service('ISO8583_service');
        $result = $this->iso8583_service->unpack($packet, intval($length_prefix) == 1);

        $this->response($result);
    }

    public function unpack_get() {
        $packet = $this->get('packet');
        $length_prefix = $this->get('length_prefix');
        if(empty($packet)) {
            $this->response(array(
                'status' => false,
                'err_msg' => '未提供报文信息（packet）',
            ));
        }

        // 若未提供 $length_prefix 参数，则默认为1
        $length_prefix = !isset($length_prefix) ? 1 : $length_prefix;

        $this->load->service('ISO8583_service');
        $result = $this->iso8583_service->unpack($packet, intval($length_prefix) == 1);

        $this->response($result);
    }
}