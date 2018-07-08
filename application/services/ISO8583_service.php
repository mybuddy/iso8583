<?php

/**
 * 报文解析服务类
 *
 * User: yanfei
 * Date: 2017/08/07
 * Time: 17:45
 */
require_once APPPATH . 'models/Packet.php';
require_once APPPATH . 'util/Packet_util.php';

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

class ISO8583_service extends MY_Service{
    public function __construct() {
        parent::__construct();
    }

    public function pack($array) {

    }

    public function unpack($string, $length_prefix = false) {
        $result = array();

        if($length_prefix) {
            if(strlen($string) > 4) {
                $length = substr($string, 0, 4); // 长度前缀
                $string = substr($string, 4); // 报文

                $length_dec = hexdec($length); // 长度前缀转换成十进制
                $string_size = strlen($string) / 2; // 报文长度（所占字节数）

                if($length_dec != $string_size) {
                    $result['status'] = false;
                    $result['err_msg'] = "报文长度错误。预期长度：{$length_dec}(0X{$length})字节，实际长度：{$string_size}字节";
                    return $result;
                }
            } else {
                $result['status'] = false;
                $result['err_msg'] = '报文长度错误（< 4个十六进制位）';
                return $result;
            }
        }

        $this->config->load('iso8583_packet');

        $packet = new Packet();
        $packet->setTpdu($this->getTPDU($string));
        $packet->setHeader($this->getHeader($string));
        $packet->setMessageType($this->getMessageType($string));
        $packet->setBitmap($this->getBitmap($string));
        $packet->setData($this->getData($string, Packet_util::convertToFieldArray($packet->getBitmap())));

        $check_result = $packet->check();
        if($check_result === TRUE) {
            $result['status'] = true;
            $result['data'] = $packet;
        } else {
            $result['status'] = false;
            $result['err_msg'] = $check_result;
            $result['data'] = $packet;
        }

        return $result;
    }

    /**
     * 获取TPDU
     *
     * @param $string
     * @return array
     */
    private function getTPDU(&$string) {
        $tpdu = $this->config->item('tpdu');
        foreach($tpdu as $key => $value) {
            $field_data = $this->getField($string, $value);
            if($field_data != null) {
                if($field_data['processed'] != null) {
                    $tpdu[$key] = $field_data;
                } else {
                    $tpdu[$key] = $field_data['original'];
                }
            } else {
                $tpdu[$key] = null;
            }
        }
        return $tpdu;
    }

    /**
     * 获取报文头
     *
     * @param $string
     * @return array
     */
    private function getHeader(&$string) {
        $header = $this->config->item('header');
        foreach($header as $key => $value) {
            $field_data = $this->getField($string, $value);
            if($field_data != null) {
                if($field_data['processed'] != null) {
                    $header[$key] = $field_data;
                } else {
                    $header[$key] = $field_data['original'];
                }
            } else {
                $header[$key] = null;
            }
        }
        return $header;
    }

    /**
     * 获取消息类型
     *
     * @param $string
     * @return string
     */
    private function getMessageType(&$string) {
        $message_type = substr($string, 0, 4);
        $string = substr($string, 4);
        return $message_type;
    }

    /**
     * 获取位元表
     *
     * @param $string
     * @return string
     */
    private function getBitmap(&$string) {
        // 位元表，这里暂时只支持64位
        $hex_bitmap = substr($string, 0, 16);
        $string = substr($string, 16);
        return $hex_bitmap;
    }

    /**
     * 获取应用数据
     *
     * @param $string
     * @param $field_array
     * @return array
     */
    private function getData(&$string, $field_array) {
        $packet = $this->config->item('packet');

        $result = array();
        foreach($field_array as $field) {
            // 该域的配置信息
            $config = $packet[$field];
            // 解析出该域数据
            $field_data = $this->getField($string, $config['type']);

            // 返回的数据处理成相关格式
            $data = null;
            if($field_data != null) {
                if($field_data['processed'] != null) {
                    $data = $field_data;
                } else {
                    $data = $field_data['original'];
                }
            }

            // 返回的类型处理成相关格式
            $type = $config['type'];
            if(is_array($type)) {
                $type = $config['type']['type'];
                if(array_key_exists('align', $config['type'])) {
                    $type .= '，' . $config['type']['align'];
                }
                if(array_key_exists('encode', $config['type'])) {
                    $type .= '，' . $config['type']['encode'];
                }
            }

            $result[$field] = array(
                'desc' => $config['desc'] . ' | ' . $type,
                'data' => $data,
            );
        }
        return $result;
    }

    /**
     * 获取域数据
     *
     * @param $string
     * @param $type
     * @return null|array
     */
    private function getField(&$string, $type) {
        $original = null; // 原始数据
        $processed = null; // 处理后的数据

        if(is_array($type)) {
            if(array_key_exists('align', $type)) {
                $align = $type['align'];
            }
            if(array_key_exists('encode', $type)) {
                $encode = $type['encode'];
            }
            $type = $type['type'];
        }

        $pattern = '/^(ANS|AN|AS|NS|N|A|S|B|Z)+(\.*)(\d+)$/';
        if(preg_match($pattern, $type, $matches)) {
            $letter = $matches[1];
            $dot = $matches[2];
            $number = $matches[3];

            if(strlen($dot) == 0) {
                // 定长
                if ($letter == 'N' || $letter == 'Z') {
                    // BCD
                    if ($number % 2 == 0) {
                        // 偶数个
                        $original = substr($string, 0, $number);
                        $string = substr($string, $number);
                    } else {
                        // 奇数个
                        if(!empty($align)) {
                            if($align == 'left') {
                                // 靠左
                                $original = substr($string, 0, $number);
                            } else if ($align == 'right') {
                                // 靠右
                                $original = substr($string, 1, $number);
                            }
                            $string = substr($string, $number + 1);
                        } else {
                            $original = substr($string, 0, $number);
                            $string = substr($string, $number);
                        }
                    }
                } else if ($letter == 'B') {
                    // bit
                    $hex_num = $number / 4;
                    $original = substr($string, 0, $hex_num);
                    $string = substr($string, $hex_num);
                } else {
                    // ASCII
                    $hex_num = $number * 2;
                    $original = substr($string, 0, $hex_num);
                    $string = substr($string, $hex_num);

                    // 将ASCII转换为实际字符
                    if(!empty($encode) && 'GBK' == $encode) {
                        $processed = Packet_util::gbkHexToStr($original);
                    } else {
                        $processed = hex2bin($original);
                    }
                }
            } else {
                // 不定长
                $dot_num = strlen($dot);
                if($dot_num % 2 == 1) {
                    // 长度
                    $dot_num += 1;
                }

                $number = substr($string, 0, $dot_num);
                $dec_number = Packet_util::bcdHexToDec($number);

                if($letter == 'N' || $letter == 'Z') {
                    if($dec_number % 2 == 0) {
                        // 偶数个
                        $original = substr($string, $dot_num, $dec_number);
                        $string = substr($string, $dot_num + $dec_number);
                    } else {
                        // 奇数个
                        $original = substr($string, $dot_num, $dec_number);
                        $string = substr($string, $dot_num + $dec_number + 1);
                    }
                } else {
                    // ASCII
                    $hex_num = $dec_number * 2;
                    $original = substr($string, $dot_num, $hex_num);
                    $string = substr($string, $dot_num + $hex_num);

                    // 将ASCII转换为实际字符
                    if($letter != 'B') {
                        if(!empty($encode) && 'GBK' == $encode) {
                            $processed = Packet_util::gbkHexToStr($original);
                        } else {
                            $processed = hex2bin($original);
                        }
                    }
                }
            }
        }

        return $original !== false ? array('original' => $original, 'processed' => $processed) : null;
    }
}