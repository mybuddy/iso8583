<?php

/**
 * 报文类
 *
 * User: yanfei
 * Date: 2017/08/07
 * Time: 17:54
 */
class Packet implements \JsonSerializable {

    private $tpdu;
    private $header;
    private $message_type;
    private $bitmap;
    private $data;

    public function __construct() {
        //parent::__construct();
    }

    /**
     * @return mixed
     */
    public function getTpdu() {
        return $this->tpdu;
    }

    /**
     * @param mixed $tpdu
     */
    public function setTpdu($tpdu) {
        $this->tpdu = $tpdu;
    }

    /**
     * @return mixed
     */
    public function getHeader() {
        return $this->header;
    }

    /**
     * @param mixed $header
     */
    public function setHeader($header) {
        $this->header = $header;
    }

    /**
     * @return mixed
     */
    public function getMessageType() {
        return $this->message_type;
    }

    /**
     * @param mixed $message_type
     */
    public function setMessageType($message_type) {
        $this->message_type = $message_type;
    }

    /**
     * @return mixed
     */
    public function getBitmap() {
        return $this->bitmap;
    }

    /**
     * @param mixed $bitmap
     */
    public function setBitmap($bitmap) {
        $this->bitmap = $bitmap;
    }

    /**
     * @return mixed
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data) {
        $this->data = $data;
    }

    /**
     * 校验数据包是否正确
     *
     * @return bool|string
     * 正确：TRUE；不正确：具体错误信息
     */
    public function check() {
        $result = '';
        if(empty($this->tpdu)) {
            $result .= "TPDU为空；";
        } else {
            foreach ($this->tpdu as $key => $value) {
                if($value === NULL) {
                    $result .= "TPDU：{$key}为空；";
                }
            }
        }

        if(empty($this->header)) {
            $result .= "报文头为空；";
        } else {
            foreach ($this->header as $key => $value) {
                if($value === NULL) {
                    $result .= "报文头：{$key}为空；";
                }
            }
        }

        if(empty($this->message_type)) {
            $result .= "消息类型为空；";
        }

        if(empty($this->bitmap)) {
            $result .= "位元表为空；";
        }

        if(empty($this->data)) {
            $result .= "应用数据为空；";
        } else {
            foreach ($this->data as $key => $value) {
                if($value === NULL) {
                    $result .= "应用数据：{$key}域为空；";
                }
            }
        }

        return empty($result) ? TRUE : $result;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize() {
        return get_object_vars($this);
    }
}