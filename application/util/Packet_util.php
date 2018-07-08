<?php

/**
 * 报文工具类
 *
 * User: yanfei
 * Date: 2017/8/8
 * Time: 17:29
 */
class Packet_util {
    /**
     * 十六进制位元表转换为对应的域数组
     *
     * @param $hex_bitmap
     * @return array
     */
    public static function convertToFieldArray($hex_bitmap) {
        $binary_bitmap = Packet_util::bitmapHexTobin($hex_bitmap);

        $field_array = array();
        $bitmap_length = strlen($binary_bitmap);
        for($i = 1; $i < $bitmap_length; $i++) {
            if($binary_bitmap[$i]) {
                $field_array[] = $i + 1;
            }
        }
        return $field_array;
    }

    /**
     * 十六进制位元表转二进制
     *
     * @param $hex_string
     * @return string
     */
    public static function bitmapHexTobin($hex_string) {
        $hex_length = strlen($hex_string);
        $bin_string = '';

        for($i = 0; $i < $hex_length; $i++) {
            $bin_string .= sprintf('%04b', hexdec($hex_string[$i]));
        }
        return $bin_string;
    }

    /**
     * 十六进制BCD码转十进制
     *
     * @param $bcd_hex_string
     * @return int
     */
    public static function bcdHexToDec($bcd_hex_string) {
        return intval($bcd_hex_string);
    }

    /**
     * GBK编码的文本对应的十六进制转换为可读的字符串
     *
     * @param $hex_string
     * @return string
     */
    public static function gbkHexToStr($hex_string) {
        if(strlen($hex_string) % 2 != 0) {
            $hex_string = $hex_string . '0';
        }

        $string = '';
        $arr = str_split($hex_string, 2);
        foreach ($arr as $byte) {
            $string .= chr(hexdec($byte));
        }

        // 需要将字符串由GBK编码转换为UTF8编码才能正常显示
        $utf8_string = mb_convert_encoding($string, 'UTF8', 'GBK');
        return $utf8_string;
    }

    /**
     * 文本字符串转换为对应的十六进制串
     *
     * @param $string
     * @return string
     */
    public static function strToHex($string){
        $hex_string = '';
        for($i=0, $length = mb_strlen($string); $i < $length; $i++){
            $hex_string .= dechex(ord($string[$i]));
        }
        return $hex_string;
    }
}